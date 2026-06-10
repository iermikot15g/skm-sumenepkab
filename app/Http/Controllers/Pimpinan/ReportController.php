<?php

namespace App\Http\Controllers\Pimpinan;

use App\Http\Controllers\Controller;
use App\Models\Respondent;
use App\Models\Period;
use App\Models\Unit;
use App\Models\Answer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    /**
     * Display reports page with filters (Read Only).
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
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        
        // Build query for preview and statistics
        $query = Respondent::whereIn('unit_id', $unitIds)
            ->with(['unit', 'period', 'answers']);
        
        if ($selectedPeriod) {
            $query->where('period_id', $selectedPeriod);
        }
        
        if ($selectedUnit) {
            $query->where('unit_id', $selectedUnit);
        }
        
        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        
        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }
        
        // Get preview data
        $previews = $query->latest()->paginate(20);
        
        // Get all respondents for statistics (without pagination)
        $allRespondents = $query->get();
        
        // Calculate summary statistics
        $totalRespondents = $allRespondents->count();
        
        // Calculate average IKM
        $totalIkm = 0;
        $scoreDistribution = [1 => 0, 2 => 0, 3 => 0, 4 => 0];
        $ikmByUnit = [];
        $ikmByPeriod = [];
        
        foreach ($allRespondents as $respondent) {
            $avgScore = $respondent->answers->avg('score') ?? 0;
            $ikm = ($avgScore / 4) * 100;
            $totalIkm += $ikm;
            
            // Score distribution
            foreach ($respondent->answers as $answer) {
                $scoreDistribution[$answer->score]++;
            }
            
            // IKM per unit
            $unitName = $respondent->unit->name ?? 'Unknown';
            if (!isset($ikmByUnit[$unitName])) {
                $ikmByUnit[$unitName] = ['total' => 0, 'count' => 0];
            }
            $ikmByUnit[$unitName]['total'] += $ikm;
            $ikmByUnit[$unitName]['count']++;
            
            // IKM per period
            $periodName = $respondent->period->name ?? 'Unknown';
            if (!isset($ikmByPeriod[$periodName])) {
                $ikmByPeriod[$periodName] = ['total' => 0, 'count' => 0];
            }
            $ikmByPeriod[$periodName]['total'] += $ikm;
            $ikmByPeriod[$periodName]['count']++;
        }
        
        $averageIkm = $totalRespondents > 0 ? round($totalIkm / $totalRespondents, 2) : 0;
        
        // Prepare chart data
        $unitChartLabels = [];
        $unitChartData = [];
        foreach ($ikmByUnit as $unitName => $data) {
            $unitChartLabels[] = $unitName;
            $unitChartData[] = $data['count'] > 0 ? round($data['total'] / $data['count'], 2) : 0;
        }
        
        $periodChartLabels = [];
        $periodChartData = [];
        foreach ($ikmByPeriod as $periodName => $data) {
            $periodChartLabels[] = $periodName;
            $periodChartData[] = $data['count'] > 0 ? round($data['total'] / $data['count'], 2) : 0;
        }
        
        // Question analysis
        $questions = Answer::getQuestions();
        $questionAnalysis = [];
        foreach ($questions as $num => $question) {
            $avgScore = Answer::whereIn('respondent_id', function($q) use ($allRespondents) {
                    $q->select('id')->from('respondents')->whereIn('id', $allRespondents->pluck('id'));
                })
                ->where('question_number', $num)
                ->avg('score') ?? 0;
            $questionAnalysis[$num] = [
                'question' => $question,
                'avg_score' => round($avgScore, 2),
                'ikm' => round(($avgScore / 4) * 100, 2)
            ];
        }
        
        // Determine quality grade
        if ($averageIkm >= 88.31) {
            $grade = 'A (Sangat Baik)';
            $gradeColor = 'green';
        } elseif ($averageIkm >= 76.61) {
            $grade = 'B (Baik)';
            $gradeColor = 'blue';
        } elseif ($averageIkm >= 65.00) {
            $grade = 'C (Kurang Baik)';
            $gradeColor = 'yellow';
        } else {
            $grade = 'D (Tidak Baik)';
            $gradeColor = 'red';
        }
        
        return view('pimpinan.reports.index', compact(
            'opd', 'units', 'periods', 'previews',
            'selectedPeriod', 'selectedUnit', 'dateFrom', 'dateTo',
            'totalRespondents', 'averageIkm', 'grade', 'gradeColor',
            'scoreDistribution', 'unitChartLabels', 'unitChartData',
            'periodChartLabels', 'periodChartData', 'questionAnalysis'
        ));
    }
    
    /**
     * Export to Excel (Read Only).
     */
    public function exportExcel(Request $request)
    {
        $user = Auth::user();
        $opdId = $user->opd_id;
        $opd = $user->opd;
        $unitIds = Unit::where('opd_id', $opdId)->pluck('id')->toArray();
        
        $filters = [
            'unit_ids' => $unitIds,
            'period_id' => $request->get('period_id'),
            'unit_id' => $request->get('unit_id'),
            'date_from' => $request->get('date_from'),
            'date_to' => $request->get('date_to'),
        ];
        
        $fileName = 'laporan-pimpinan-' . $opd->short_name . '-' . now()->format('Y-m-d-His') . '.xlsx';
        
        return Excel::download(new \App\Exports\PimpinanReportExport($filters), $fileName);
    }
    
    /**
     * Export to PDF (Read Only).
     */
    public function exportPdf(Request $request)
    {
        $user = Auth::user();
        $opdId = $user->opd_id;
        $opd = $user->opd;
        $unitIds = Unit::where('opd_id', $opdId)->pluck('id')->toArray();
        
        // Get filter values
        $periodId = $request->get('period_id');
        $unitId = $request->get('unit_id');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        
        // Build query
        $query = Respondent::whereIn('unit_id', $unitIds)
            ->with(['unit', 'period', 'answers']);
        
        if ($periodId) {
            $query->where('period_id', $periodId);
        }
        
        if ($unitId) {
            $query->where('unit_id', $unitId);
        }
        
        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        
        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }
        
        $respondents = $query->get();
        
        // Get filter labels
        $period = $periodId ? Period::find($periodId) : null;
        $unit = $unitId ? Unit::find($unitId) : null;
        
        // Calculate statistics
        $totalRespondents = $respondents->count();
        $totalIkm = 0;
        
        foreach ($respondents as $respondent) {
            $avgScore = $respondent->answers->avg('score') ?? 0;
            $ikm = ($avgScore / 4) * 100;
            $totalIkm += $ikm;
        }
        
        $averageIkm = $totalRespondents > 0 ? round($totalIkm / $totalRespondents, 2) : 0;
        
        // Determine quality grade
        if ($averageIkm >= 88.31) {
            $grade = 'A (Sangat Baik)';
            $gradeColor = '#22c55e';
        } elseif ($averageIkm >= 76.61) {
            $grade = 'B (Baik)';
            $gradeColor = '#3b82f6';
        } elseif ($averageIkm >= 65.00) {
            $grade = 'C (Kurang Baik)';
            $gradeColor = '#eab308';
        } else {
            $grade = 'D (Tidak Baik)';
            $gradeColor = '#ef4444';
        }
        
        // IKM per unit for PDF
        $ikmByUnit = [];
        foreach ($respondents->groupBy('unit_id') as $unitId => $unitRespondents) {
            $unitModel = $unitRespondents->first()->unit;
            $unitTotalIkm = 0;
            foreach ($unitRespondents as $resp) {
                $avgScore = $resp->answers->avg('score') ?? 0;
                $unitTotalIkm += ($avgScore / 4) * 100;
            }
            $ikmByUnit[] = [
                'name' => $unitModel->name ?? 'Unknown',
                'ikm' => $unitRespondents->count() > 0 ? round($unitTotalIkm / $unitRespondents->count(), 2) : 0,
                'respondents' => $unitRespondents->count()
            ];
        }
        
        $data = compact(
            'opd', 'respondents', 'period', 'unit',
            'dateFrom', 'dateTo', 'totalRespondents',
            'averageIkm', 'grade', 'gradeColor', 'ikmByUnit'
        );
        
        $pdf = Pdf::loadView('pimpinan.reports.pdf', $data);
        $pdf->setPaper('A4', 'landscape');
        
        return $pdf->download('laporan-pimpinan-' . $opd->short_name . '-' . now()->format('Y-m-d-His') . '.pdf');
    }
}