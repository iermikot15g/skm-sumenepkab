<?php

namespace App\Http\Controllers\Pimpinan;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use App\Models\Period;
use App\Models\Respondent;
use App\Models\Answer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnalysisController extends Controller
{
    /**
     * Display analysis page with insights and recommendations.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $opdId = $user->opd_id;
        
        // Get OPD data
        $opd = $user->opd;
        
        // Get units under this OPD
        $units = Unit::where('opd_id', $opdId)->get();
        $unitIds = $units->pluck('id')->toArray();
        
        // Get periods for filter
        $periods = Period::orderBy('start_date', 'desc')->get();
        
        // Get filter values
        $selectedPeriod = $request->get('period_id');
        $selectedUnit = $request->get('unit_id');
        
        // Build query for respondents
        $query = Respondent::whereIn('unit_id', $unitIds)
            ->with(['unit', 'period', 'answers']);
        
        if ($selectedPeriod) {
            $query->where('period_id', $selectedPeriod);
        }
        
        if ($selectedUnit) {
            $query->where('unit_id', $selectedUnit);
        }
        
        $respondents = $query->get();
        
        // Get all questions
        $questions = Answer::getQuestions();
        
        // Calculate IKM per question
        $questionAnalysis = [];
        foreach ($questions as $num => $question) {
            $avgScore = Answer::whereIn('respondent_id', function($q) use ($respondents) {
                    $q->select('id')->from('respondents')->whereIn('id', $respondents->pluck('id'));
                })
                ->where('question_number', $num)
                ->avg('score') ?? 0;
            
            $ikm = round(($avgScore / 4) * 100, 2);
            
            $questionAnalysis[$num] = [
                'number' => $num,
                'question' => $question,
                'avg_score' => round($avgScore, 2),
                'ikm' => $ikm,
                'grade' => $this->getGrade($ikm),
                'gradeColor' => $this->getGradeColor($ikm),
                'recommendation' => $this->getRecommendation($num, $ikm),
                'priority' => $this->getPriority($ikm)
            ];
        }
        
        // Sort by lowest IKM
        $sortedAnalysis = $questionAnalysis;
        uasort($sortedAnalysis, function($a, $b) {
            return $a['ikm'] <=> $b['ikm'];
        });
        
        // Get lowest 3 aspects
        $lowestAspects = array_slice($sortedAnalysis, 0, 3, true);
        
        // Get highest 3 aspects
        $highestAspects = array_slice(array_reverse($sortedAnalysis, true), 0, 3, true);
        
        // Calculate period comparison
        $periodComparison = $this->getPeriodComparison($unitIds, $selectedPeriod);
        
        // Calculate trend analysis (last 3 periods)
        $trendAnalysis = $this->getTrendAnalysis($unitIds, $selectedUnit);
        
        // Get complaints/suggestions (if any - from a 'complaint' field if exists)
        $complaints = $this->getComplaints($respondents);
        
        // Priority interventions based on lowest scores and volume
        $priorityInterventions = $this->getPriorityInterventions($questionAnalysis, $respondents->count());
        
        // Prepare chart data
        $chartLabels = [];
        $chartData = [];
        $chartColors = [];
        foreach ($sortedAnalysis as $num => $analysis) {
            $chartLabels[] = $num . '. ' . substr($analysis['question'], 0, 30) . (strlen($analysis['question']) > 30 ? '...' : '');
            $chartData[] = $analysis['ikm'];
            $chartColors[] = $analysis['gradeColor'];
        }
        
        // Radar chart data (all questions)
        $radarLabels = [];
        $radarData = [];
        foreach ($questions as $num => $question) {
            $radarLabels[] = 'Q' . $num;
            $radarData[] = $questionAnalysis[$num]['ikm'];
        }
        
        return view('pimpinan.analysis', compact(
            'opd', 'units', 'periods', 'questionAnalysis',
            'lowestAspects', 'highestAspects', 'periodComparison',
            'trendAnalysis', 'complaints', 'priorityInterventions',
            'selectedPeriod', 'selectedUnit', 'chartLabels', 'chartData',
            'chartColors', 'radarLabels', 'radarData'
        ));
    }
    
    /**
     * Get grade based on IKM score.
     */
    private function getGrade($ikm)
    {
        if ($ikm >= 88.31) return 'A (Sangat Baik)';
        if ($ikm >= 76.61) return 'B (Baik)';
        if ($ikm >= 65.00) return 'C (Kurang Baik)';
        return 'D (Tidak Baik)';
    }
    
    /**
     * Get grade color based on IKM score.
     */
    private function getGradeColor($ikm)
    {
        if ($ikm >= 88.31) return '#22c55e';
        if ($ikm >= 76.61) return '#3b82f6';
        if ($ikm >= 65.00) return '#eab308';
        return '#ef4444';
    }
    
    /**
     * Get recommendation for improvement.
     */
    private function getRecommendation($questionNumber, $score)
    {
        $recommendations = [
            1 => 'Perbaiki sosialisasi persyaratan pelayanan. Sediakan papan informasi dan brosur yang jelas. Manfaatkan website dan media sosial.',
            2 => 'Sederhanakan prosedur pelayanan. Identifikasi tahapan yang bisa dieliminasi atau digabung. Terapkan sistem online jika memungkinkan.',
            3 => 'Evaluasi waktu pelayanan setiap tahapan. Tambah petugas pada jam sibuk. Optimalkan sistem antrian digital.',
            4 => 'Pastikan tidak ada pungutan liar. Transparansikan biaya resmi. Sediakan informasi biaya di tempat strategis.',
            5 => 'Tetapkan standar hasil pelayanan yang jelas. Lakukan quality control berkala. Survey kepuasan pasca pelayanan.',
            6 => 'Adakan pelatihan rutin untuk petugas. Berikan reward untuk petugas berprestasi. Evaluasi kompetensi berkala.',
            7 => 'Terapkan standar pelayanan ramah (Senyum, Salam, Sapa). Berikan pelatihan soft skill. Sediakan meja pengaduan.',
            8 => 'Perbarui sarana dan prasarana yang rusak. Tingkatkan kenyamanan ruang tunggu. Sediakan fasilitas ramah disabilitas.',
            9 => 'Sediakan kanal pengaduan yang mudah diakses. Tindaklanjuti setiap pengaduan maksimal 3 hari. Transparansikan tindak lanjut.'
        ];
        
        if ($score < 50) {
            return '⚠️ **Prioritas Utama**: ' . ($recommendations[$questionNumber] ?? 'Lakukan evaluasi menyeluruh pada unsur ini.');
        } elseif ($score < 75) {
            return '📋 **Perlu Perbaikan**: ' . ($recommendations[$questionNumber] ?? 'Tingkatkan kualitas pelayanan pada unsur ini.');
        }
        return '✅ **Pertahankan**: ' . ($recommendations[$questionNumber] ?? 'Lakukan inovasi untuk mempertahankan kualitas.');
    }
    
    /**
     * Get priority level based on score.
     */
    private function getPriority($score)
    {
        if ($score < 50) return 'High';
        if ($score < 75) return 'Medium';
        return 'Low';
    }
    
    /**
     * Get period comparison data.
     */
    private function getPeriodComparison($unitIds, $selectedPeriod = null)
    {
        $periods = Period::orderBy('start_date', 'desc')->take(4)->get();
        $comparison = [];
        
        foreach ($periods as $period) {
            $respondents = Respondent::whereIn('unit_id', $unitIds)
                ->where('period_id', $period->id)
                ->with('answers')
                ->get();
            
            $totalIkm = 0;
            foreach ($respondents as $respondent) {
                $avgScore = $respondent->answers->avg('score') ?? 0;
                $totalIkm += ($avgScore / 4) * 100;
            }
            
            $averageIkm = $respondents->count() > 0 ? round($totalIkm / $respondents->count(), 2) : 0;
            
            $comparison[] = [
                'period_name' => $period->name,
                'respondents' => $respondents->count(),
                'ikm' => $averageIkm,
                'grade' => $this->getGrade($averageIkm),
                'gradeColor' => $this->getGradeColor($averageIkm)
            ];
        }
        
        return $comparison;
    }
    
    /**
     * Get trend analysis over periods.
     */
    private function getTrendAnalysis($unitIds, $selectedUnit = null)
    {
        $periods = Period::orderBy('start_date', 'desc')->take(6)->get();
        $questions = Answer::getQuestions();
        $trend = [];
        
        foreach ($questions as $num => $question) {
            $trendData = [];
            foreach ($periods as $period) {
                $respondents = Respondent::whereIn('unit_id', $unitIds)
                    ->where('period_id', $period->id)
                    ->get();
                
                $avgScore = Answer::whereIn('respondent_id', function($q) use ($respondents) {
                        $q->select('id')->from('respondents')->whereIn('id', $respondents->pluck('id'));
                    })
                    ->where('question_number', $num)
                    ->avg('score') ?? 0;
                
                $trendData[] = round(($avgScore / 4) * 100, 2);
            }
            
            // Calculate trend direction
            if (count($trendData) >= 2) {
                $change = $trendData[0] - $trendData[count($trendData)-1];
                $direction = $change > 0 ? 'up' : ($change < 0 ? 'down' : 'stable');
            } else {
                $direction = 'stable';
            }
            
            $trend[$num] = [
                'question' => $question,
                'periods' => $periods->pluck('short_name', 'id')->toArray(),
                'values' => $trendData,
                'direction' => $direction,
                'change' => isset($change) ? round($change, 2) : 0
            ];
        }
        
        return $trend;
    }
    
    /**
     * Get complaints and suggestions from respondents.
     */
    private function getComplaints($respondents)
    {
        // Note: This assumes there's a 'complaint' or 'suggestion' field in respondents table
        // For now, return empty collection. Can be extended later.
        $complaints = [];
        
        // Example if field exists:
        // foreach ($respondents as $respondent) {
        //     if ($respondent->complaint) {
        //         $complaints[] = [
        //             'date' => $respondent->created_at,
        //             'respondent_name' => $respondent->full_name,
        //             'unit_name' => $respondent->unit->name,
        //             'complaint' => $respondent->complaint
        //         ];
        //     }
        // }
        
        return $complaints;
    }
    
    /**
     * Get priority interventions based on low scores and volume.
     */
    private function getPriorityInterventions($questionAnalysis, $totalRespondents)
    {
        $interventions = [];
        
        foreach ($questionAnalysis as $num => $analysis) {
            if ($analysis['priority'] == 'High' || $analysis['priority'] == 'Medium') {
                $interventions[] = [
                    'question_number' => $num,
                    'question' => $analysis['question'],
                    'current_ikm' => $analysis['ikm'],
                    'priority' => $analysis['priority'],
                    'target_ikm' => min(100, $analysis['ikm'] + 15),
                    'estimated_impact' => $analysis['priority'] == 'High' ? 'Besar' : 'Sedang',
                    'timeframe' => $analysis['priority'] == 'High' ? '1-3 bulan' : '4-6 bulan',
                    'recommendation' => $analysis['recommendation']
                ];
            }
        }
        
        // Sort by priority
        usort($interventions, function($a, $b) {
            $priorityOrder = ['High' => 1, 'Medium' => 2, 'Low' => 3];
            return $priorityOrder[$a['priority']] <=> $priorityOrder[$b['priority']];
        });
        
        return $interventions;
    }
    
    /**
     * API endpoint to get lowest aspects (for AJAX).
     */
    public function lowestAspects(Request $request)
    {
        $user = Auth::user();
        $opdId = $user->opd_id;
        $unitIds = Unit::where('opd_id', $opdId)->pluck('id')->toArray();
        
        $selectedPeriod = $request->get('period_id');
        $selectedUnit = $request->get('unit_id');
        
        $query = Respondent::whereIn('unit_id', $unitIds);
        
        if ($selectedPeriod) {
            $query->where('period_id', $selectedPeriod);
        }
        
        if ($selectedUnit) {
            $query->where('unit_id', $selectedUnit);
        }
        
        $respondents = $query->get();
        $questions = Answer::getQuestions();
        
        $analysis = [];
        foreach ($questions as $num => $question) {
            $avgScore = Answer::whereIn('respondent_id', function($q) use ($respondents) {
                    $q->select('id')->from('respondents')->whereIn('id', $respondents->pluck('id'));
                })
                ->where('question_number', $num)
                ->avg('score') ?? 0;
            
            $analysis[$num] = [
                'question' => $question,
                'ikm' => round(($avgScore / 4) * 100, 2)
            ];
        }
        
        uasort($analysis, function($a, $b) {
            return $a['ikm'] <=> $b['ikm'];
        });
        
        return response()->json(array_slice($analysis, 0, 5, true));
    }
}