<?php

namespace App\Http\Controllers\Admin\AdminOpd;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use App\Models\Period;
use App\Models\Respondent;
use App\Models\Answer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

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
        
        // Prepare chart data
        $chartLabels = [];
        $chartData = [];
        foreach ($ikmByUnit as $unitName => $data) {
            $chartLabels[] = $unitName;
            $chartData[] = $data['count'] > 0 ? round($data['total'] / $data['count'], 2) : 0;
        }
        
        // Monthly trend data
        $monthlyData = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthName = $month->format('M Y');
            $count = Respondent::whereIn('unit_id', $unitIds)
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();
            $monthlyData[$monthName] = $count;
        }
        
        // Recent surveys
        $recentSurveys = Respondent::whereIn('unit_id', $unitIds)
            ->with(['unit', 'period'])
            ->latest()
            ->take(10)
            ->get();
        
        // Question analysis (lowest scores)
        $questionAnalysis = [];
        $questions = Answer::getQuestions();
        
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
        
        // Sort by lowest IKM
        uasort($questionAnalysis, function($a, $b) {
            return $a['ikm'] <=> $b['ikm'];
        });
        $lowestAspects = array_slice($questionAnalysis, 0, 3, true);
        
        return view('admin.admin-opd.dashboard', compact(
            'opd', 'units', 'totalUnits', 'totalRespondents',
            'averageIkm', 'activePeriod', 'chartLabels', 'chartData',
            'monthlyData', 'recentSurveys', 'lowestAspects'
        ));
    }
}