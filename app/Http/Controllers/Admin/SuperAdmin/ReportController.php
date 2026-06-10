<?php

namespace App\Http\Controllers\Admin\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Respondent;
use App\Models\Period;
use App\Models\Opd;
use App\Models\Unit;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\SurveyReportExport;

class ReportController extends Controller
{
    /**
     * Display reports page with filters.
     */
    public function index(Request $request)
    {
        $periods = Period::orderBy('start_date', 'desc')->get();
        $opds = Opd::where('is_active', true)->get();
        
        // Get filter values
        $selectedPeriod = $request->get('period_id');
        $selectedOpd = $request->get('opd_id');
        $selectedUnit = $request->get('unit_id');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        
        // Get units based on selected OPD
        $units = [];
        if ($selectedOpd) {
            $units = Unit::where('opd_id', $selectedOpd)->get();
        }
        
        // Build query for preview
        $query = Respondent::with(['unit.opd', 'period']);
        
        if ($selectedPeriod) {
            $query->where('period_id', $selectedPeriod);
        }
        
        if ($selectedOpd) {
            $query->whereHas('unit', function($q) use ($selectedOpd) {
                $q->where('opd_id', $selectedOpd);
            });
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
        
        $previews = $query->latest()->paginate(20);
        
        // Calculate summary statistics
        $totalRespondents = $query->count();
        
        // Calculate average IKM
        $respondents = $query->get();
        $totalIkm = 0;
        foreach ($respondents as $respondent) {
            $avgScore = $respondent->answers()->avg('score') ?? 0;
            $ikm = ($avgScore / 4) * 100;
            $totalIkm += $ikm;
        }
        $averageIkm = $totalRespondents > 0 ? round($totalIkm / $totalRespondents, 2) : 0;
        
        return view('admin.super-admin.reports.index', compact(
            'periods', 'opds', 'units', 'previews',
            'selectedPeriod', 'selectedOpd', 'selectedUnit',
            'dateFrom', 'dateTo', 'totalRespondents', 'averageIkm'
        ));
    }
    
    /**
     * Export to Excel.
     */
    public function exportExcel(Request $request)
    {
        $filters = [
            'period_id' => $request->get('period_id'),
            'opd_id' => $request->get('opd_id'),
            'unit_id' => $request->get('unit_id'),
            'date_from' => $request->get('date_from'),
            'date_to' => $request->get('date_to'),
        ];
        
        $fileName = 'laporan-survei-' . now()->format('Y-m-d-His') . '.xlsx';
        
        return Excel::download(new SurveyReportExport($filters), $fileName);
    }
    
    /**
     * Export to PDF.
     */
    public function exportPdf(Request $request)
    {
        // Get filters
        $periodId = $request->get('period_id');
        $opdId = $request->get('opd_id');
        $unitId = $request->get('unit_id');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        
        // Build query
        $query = Respondent::with(['unit.opd', 'period', 'answers']);
        
        if ($periodId) {
            $query->where('period_id', $periodId);
        }
        
        if ($opdId) {
            $query->whereHas('unit', function($q) use ($opdId) {
                $q->where('opd_id', $opdId);
            });
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
        
        // Get filter labels for display
        $period = $periodId ? Period::find($periodId) : null;
        $opd = $opdId ? Opd::find($opdId) : null;
        $unit = $unitId ? Unit::find($unitId) : null;
        
        // Calculate statistics
        $totalRespondents = $respondents->count();
        $totalIkm = 0;
        $totalScores = [1 => 0, 2 => 0, 3 => 0, 4 => 0];
        
        foreach ($respondents as $respondent) {
            $avgScore = $respondent->answers->avg('score') ?? 0;
            $ikm = ($avgScore / 4) * 100;
            $totalIkm += $ikm;
            
            // Count score distribution
            foreach ($respondent->answers as $answer) {
                $totalScores[$answer->score]++;
            }
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
            'respondents', 'period', 'opd', 'unit',
            'dateFrom', 'dateTo', 'totalRespondents',
            'averageIkm', 'grade', 'gradeColor', 'totalScores'
        );
        
        $pdf = Pdf::loadView('admin.super-admin.reports.pdf', $data);
        $pdf->setPaper('A4', 'landscape');
        
        return $pdf->download('laporan-survei-' . now()->format('Y-m-d-His') . '.pdf');
    }
}