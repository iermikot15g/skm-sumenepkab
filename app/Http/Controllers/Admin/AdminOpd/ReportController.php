<?php

namespace App\Http\Controllers\Admin\AdminOpd;

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
     * Display reports page with filters.
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
        
        // Calculate summary statistics
        $totalRespondents = $query->count();
        
        // Calculate average IKM
        $respondents = $query->get();
        $totalIkm = 0;
        $scoreDistribution = [1 => 0, 2 => 0, 3 => 0, 4 => 0];
        
        foreach ($respondents as $respondent) {
            $avgScore = $respondent->answers->avg('score') ?? 0;
            $ikm = ($avgScore / 4) * 100;
            $totalIkm += $ikm;
            
            foreach ($respondent->answers as $answer) {
                $scoreDistribution[$answer->score]++;
            }
        }
        
        $averageIkm = $totalRespondents > 0 ? round($totalIkm / $totalRespondents, 2) : 0;
        
        // Calculate IKM per unit
        $ikmPerUnit = [];
        foreach ($units as $unit) {
            $unitRespondents = $respondents->where('unit_id', $unit->id);
            $unitTotalIkm = 0;
            foreach ($unitRespondents as $resp) {
                $avgScore = $resp->answers->avg('score') ?? 0;
                $unitTotalIkm += ($avgScore / 4) * 100;
            }
            $ikmPerUnit[$unit->name] = $unitRespondents->count() > 0 
                ? round($unitTotalIkm / $unitRespondents->count(), 2) 
                : 0;
        }
        
        // Question analysis
        $questions = Answer::getQuestions();
        $questionAnalysis = [];
        foreach ($questions as $num => $question) {
            $avgScore = Answer::whereIn('respondent_id', function($q) use ($respondents) {
                    $q->select('id')->from('respondents')->whereIn('id', $respondents->pluck('id'));
                })
                ->where('question_number', $num)
                ->avg('score') ?? 0;
            $questionAnalysis[$num] = [
                'question' => $question,
                'avg_score' => round($avgScore, 2),
                'ikm' => round(($avgScore / 4) * 100, 2)
            ];
        }
        
        return view('admin.admin-opd.reports.index', compact(
            'opd', 'units', 'periods', 'previews',
            'selectedPeriod', 'selectedUnit', 'dateFrom', 'dateTo',
            'totalRespondents', 'averageIkm', 'scoreDistribution',
            'ikmPerUnit', 'questionAnalysis'
        ));
    }
    
    /**
     * Export to Excel.
     */
    public function exportExcel(Request $request)
    {
        $user = Auth::user();
        $opdId = $user->opd_id;
        $unitIds = Unit::where('opd_id', $opdId)->pluck('id')->toArray();
        
        $filters = [
            'unit_ids' => $unitIds,
            'period_id' => $request->get('period_id'),
            'unit_id' => $request->get('unit_id'),
            'date_from' => $request->get('date_from'),
            'date_to' => $request->get('date_to'),
        ];
        
        $fileName = 'laporan-opd-' . $user->opd->short_name . '-' . now()->format('Y-m-d-His') . '.xlsx';
        
        return Excel::download(new \App\Exports\AdminOpdReportExport($filters), $fileName);
    }
    
    /**
     * Export to PDF.
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
        
        $data = compact(
            'opd', 'respondents', 'period', 'unit',
            'dateFrom', 'dateTo', 'totalRespondents',
            'averageIkm', 'grade', 'gradeColor'
        );
        
        $pdf = Pdf::loadView('admin.admin-opd.reports.pdf', $data);
        $pdf->setPaper('A4', 'landscape');
        
        return $pdf->download('laporan-opd-' . $opd->short_name . '-' . now()->format('Y-m-d-His') . '.pdf');
    }
}