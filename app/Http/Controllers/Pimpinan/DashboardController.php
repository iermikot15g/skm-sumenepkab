<?php

namespace App\Http\Controllers\Pimpinan;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use App\Models\Period;
use App\Models\Respondent;
use App\Models\Answer;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $opdId = $user->opd_id;
        
        // Get OPD data
        $opd = $user->opd;
        
        // Get units under this OPD
        $units = Unit::where('opd_id', $opdId)->get();
        $unitIds = $units->pluck('id')->toArray();
        
        // Get active period
        $activePeriod = Period::where('is_active', true)->first();
        
        // Get all periods for comparison
        $periods = Period::orderBy('start_date', 'desc')->take(4)->get();
        
        // Statistics
        $totalUnits = $units->count();
        $totalRespondents = Respondent::whereIn('unit_id', $unitIds)->count();
        
        // Calculate average IKM for this OPD
        $respondents = Respondent::whereIn('unit_id', $unitIds)->with('answers')->get();
        $totalIkm = 0;
        $ikmByUnit = [];
        
        foreach ($respondents as $respondent) {
            $avgScore = $respondent->answers->avg('score') ?? 0;
            $ikm = ($avgScore / 4) * 100;
            $totalIkm += $ikm;
            
            // Group by unit
            $unitName = $respondent->unit->name ?? 'Unknown';
            if (!isset($ikmByUnit[$unitName])) {
                $ikmByUnit[$unitName] = ['total' => 0, 'count' => 0];
            }
            $ikmByUnit[$unitName]['total'] += $ikm;
            $ikmByUnit[$unitName]['count']++;
        }
        
        $averageIkm = $totalRespondents > 0 ? round($totalIkm / $totalRespondents, 2) : 0;
        
        // Chart data
        $chartLabels = [];
        $chartData = [];
        foreach ($ikmByUnit as $unitName => $data) {
            $chartLabels[] = $unitName;
            $chartData[] = $data['count'] > 0 ? round($data['total'] / $data['count'], 2) : 0;
        }
        
        // Monthly trend data (last 12 months)
        $monthlyData = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthName = $month->format('M Y');
            $count = Respondent::whereIn('unit_id', $unitIds)
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();
            
            // Calculate average IKM for that month
            $monthRespondents = Respondent::whereIn('unit_id', $unitIds)
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->with('answers')
                ->get();
            
            $monthIkm = 0;
            foreach ($monthRespondents as $resp) {
                $avgScore = $resp->answers->avg('score') ?? 0;
                $monthIkm += ($avgScore / 4) * 100;
            }
            $avgMonthIkm = $monthRespondents->count() > 0 ? round($monthIkm / $monthRespondents->count(), 2) : 0;
            
            $monthlyData[$monthName] = [
                'count' => $count,
                'ikm' => $avgMonthIkm
            ];
        }
        
        // Get recent surveys for activity feed
        $recentSurveys = Respondent::whereIn('unit_id', $unitIds)
            ->with(['unit', 'period'])
            ->latest()
            ->take(10)
            ->get();
        
        // Question analysis for this OPD
        $questions = Answer::getQuestions();
        $questionAnalysis = [];
        foreach ($questions as $num => $question) {
            $avgScore = Answer::whereIn('respondent_id', function($q) use ($unitIds) {
                    $q->select('id')->from('respondents')->whereIn('unit_id', $unitIds);
                })
                ->where('question_number', $num)
                ->avg('score') ?? 0;
            $questionAnalysis[$num] = [
                'question' => $question,
                'avg_score' => round($avgScore, 2),
                'ikm' => round(($avgScore / 4) * 100, 2)
            ];
        }
        
        // Sort by lowest IKM to get areas for improvement
        uasort($questionAnalysis, function($a, $b) {
            return $a['ikm'] <=> $b['ikm'];
        });
        $lowestAspects = array_slice($questionAnalysis, 0, 3, true);
        $highestAspects = array_slice(array_reverse($questionAnalysis, true), 0, 3, true);
        
        // Performance comparison with previous period
        $currentPeriod = Period::where('is_active', true)->first();
        $previousPeriod = Period::where('is_active', false)
            ->orderBy('end_date', 'desc')
            ->first();
        
        $previousIkm = 0;
        if ($previousPeriod) {
            $prevRespondents = Respondent::whereIn('unit_id', $unitIds)
                ->where('period_id', $previousPeriod->id)
                ->with('answers')
                ->get();
            $prevTotalIkm = 0;
            foreach ($prevRespondents as $resp) {
                $avgScore = $resp->answers->avg('score') ?? 0;
                $prevTotalIkm += ($avgScore / 4) * 100;
            }
            $previousIkm = $prevRespondents->count() > 0 ? round($prevTotalIkm / $prevRespondents->count(), 2) : 0;
        }
        
        $ikmChange = $previousIkm > 0 ? round($averageIkm - $previousIkm, 2) : 0;
        $trendDirection = $ikmChange >= 0 ? 'up' : 'down';
        $trendColor = $ikmChange >= 0 ? 'text-green-600' : 'text-red-600';
        
        // Recommendations based on lowest aspects
        $recommendations = [];
        foreach ($lowestAspects as $num => $aspect) {
            if ($aspect['ikm'] < 75) {
                $recommendations[] = [
                    'aspect' => $aspect['question'],
                    'current_score' => $aspect['ikm'],
                    'recommendation' => $this->getRecommendation($num, $aspect['ikm'])
                ];
            }
        }
        
        return view('pimpinan.dashboard', compact(
            'opd', 'units', 'totalUnits', 'totalRespondents',
            'averageIkm', 'activePeriod', 'chartLabels', 'chartData',
            'monthlyData', 'recentSurveys', 'questionAnalysis',
            'lowestAspects', 'highestAspects', 'previousIkm',
            'ikmChange', 'trendDirection', 'trendColor', 'recommendations',
            'periods'
        ));
    }
    
    private function getRecommendation($questionNumber, $score)
    {
        $recommendations = [
            1 => 'Sosialisasikan persyaratan pelayanan melalui berbagai media. Sediakan papan informasi yang jelas.',
            2 => 'Sederhanakan prosedur pelayanan. Manfaatkan teknologi digital untuk mempercepat proses.',
            3 => 'Evaluasi waktu pelayanan. Tambah petugas pada jam sibuk. Optimalkan sistem antrian.',
            4 => 'Pastikan tidak ada biaya tidak resmi. Transparansikan biaya resmi yang diperlukan.',
            5 => 'Tetapkan standar hasil pelayanan. Lakukan evaluasi berkala terhadap output layanan.',
            6 => 'Tingkatkan kompetensi petugas melalui pelatihan. Adakan evaluasi kinerja berkala.',
            7 => 'Terapkan standar pelayanan ramah. Berikan reward kepada petugas dengan pelayanan terbaik.',
            8 => 'Perbarui sarana dan prasarana. Pastikan kenyamanan ruang tunggu dan fasilitas pendukung.',
            9 => 'Sediakan kanal pengaduan yang mudah diakses. Tindaklanjuti pengaduan dengan cepat.'
        ];
        
        if ($score < 50) {
            return 'Segera ambil tindakan: ' . ($recommendations[$questionNumber] ?? 'Lakukan evaluasi menyeluruh pada unsur ini.');
        } elseif ($score < 75) {
            return 'Perlu perbaikan: ' . ($recommendations[$questionNumber] ?? 'Tingkatkan kualitas pelayanan pada unsur ini.');
        }
        
        return 'Pertahankan dan tingkatkan: ' . ($recommendations[$questionNumber] ?? 'Lakukan inovasi untuk mempertahankan kualitas.');
    }
}