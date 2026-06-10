<?php

namespace App\Http\Controllers\Admin\AdminOpd;

use App\Http\Controllers\Controller;
use App\Models\Respondent;
use App\Models\Unit;
use App\Models\Period;
use App\Models\Answer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SurveyController extends Controller
{
    /**
     * Display a listing of surveys for units under this OPD.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $opdId = $user->opd_id;
        
        // Get units under this OPD
        $units = Unit::where('opd_id', $opdId)->get();
        $unitIds = $units->pluck('id')->toArray();
        
        // Get periods for filter
        $periods = Period::orderBy('start_date', 'desc')->get();
        
        // Build query
        $query = Respondent::whereIn('unit_id', $unitIds)
            ->with(['unit', 'period', 'answers']);
        
        // Apply filters
        if ($request->filled('period_id')) {
            $query->where('period_id', $request->period_id);
        }
        
        if ($request->filled('unit_id')) {
            $query->where('unit_id', $request->unit_id);
        }
        
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }
        
        // Get paginated results
        $surveys = $query->latest()->paginate(20);
        
        // Calculate statistics
        $totalSurveys = $query->count();
        
        // Calculate average IKM
        $respondents = $query->get();
        $totalIkm = 0;
        foreach ($respondents as $respondent) {
            $avgScore = $respondent->answers->avg('score') ?? 0;
            $ikm = ($avgScore / 4) * 100;
            $totalIkm += $ikm;
        }
        $averageIkm = $totalSurveys > 0 ? round($totalIkm / $totalSurveys, 2) : 0;
        
        // Score distribution
        $scoreDistribution = [1 => 0, 2 => 0, 3 => 0, 4 => 0];
        foreach ($respondents as $respondent) {
            foreach ($respondent->answers as $answer) {
                $scoreDistribution[$answer->score]++;
            }
        }
        
        // Preserve filters for pagination
        $filters = $request->only(['period_id', 'unit_id', 'date_from', 'date_to', 'search']);
        
        return view('admin.admin-opd.surveys.index', compact(
            'surveys', 'units', 'periods', 'totalSurveys', 'averageIkm',
            'scoreDistribution', 'filters'
        ));
    }
    
    /**
     * Display detailed survey result.
     */
    public function show(Respondent $respondent)
    {
        $user = Auth::user();
        $opdId = $user->opd_id;
        
        // Verify this survey belongs to OPD
        if ($respondent->unit->opd_id !== $opdId) {
            abort(403, 'Anda tidak memiliki akses ke data ini.');
        }
        
        // Get all answers grouped by question
        $answers = [];
        $questions = Answer::getQuestions();
        
        foreach ($questions as $num => $question) {
            $answer = $respondent->answers->firstWhere('question_number', $num);
            $answers[$num] = [
                'question' => $question,
                'score' => $answer ? $answer->score : null,
                'label' => $answer ? $this->getScoreLabel($answer->score) : 'Belum dijawab',
            ];
        }
        
        // Calculate IKM
        $avgScore = $respondent->answers->avg('score') ?? 0;
        $ikm = ($avgScore / 4) * 100;
        
        // Determine grade
        if ($ikm >= 88.31) {
            $grade = 'A (Sangat Baik)';
            $gradeColor = 'green';
        } elseif ($ikm >= 76.61) {
            $grade = 'B (Baik)';
            $gradeColor = 'blue';
        } elseif ($ikm >= 65.00) {
            $grade = 'C (Kurang Baik)';
            $gradeColor = 'yellow';
        } else {
            $grade = 'D (Tidak Baik)';
            $gradeColor = 'red';
        }
        
        return view('admin.admin-opd.surveys.show', compact(
            'respondent', 'answers', 'ikm', 'grade', 'gradeColor'
        ));
    }
    
    /**
     * Get score label.
     */
    private function getScoreLabel($score)
    {
        return match($score) {
            1 => 'Tidak Baik',
            2 => 'Kurang Baik',
            3 => 'Baik',
            4 => 'Sangat Baik',
            default => 'Tidak Valid',
        };
    }
    
    /**
     * Export single survey to PDF.
     */
    public function exportPdf(Respondent $respondent)
    {
        $user = Auth::user();
        $opdId = $user->opd_id;
        
        // Verify this survey belongs to OPD
        if ($respondent->unit->opd_id !== $opdId) {
            abort(403, 'Anda tidak memiliki akses ke data ini.');
        }
        
        // Get answers
        $answers = [];
        $questions = Answer::getQuestions();
        
        foreach ($questions as $num => $question) {
            $answer = $respondent->answers->firstWhere('question_number', $num);
            $answers[$num] = [
                'question' => $question,
                'score' => $answer ? $answer->score : null,
                'label' => $answer ? $this->getScoreLabel($answer->score) : 'Belum dijawab',
            ];
        }
        
        // Calculate IKM
        $avgScore = $respondent->answers->avg('score') ?? 0;
        $ikm = ($avgScore / 4) * 100;
        
        // Determine grade
        if ($ikm >= 88.31) {
            $grade = 'A (Sangat Baik)';
        } elseif ($ikm >= 76.61) {
            $grade = 'B (Baik)';
        } elseif ($ikm >= 65.00) {
            $grade = 'C (Kurang Baik)';
        } else {
            $grade = 'D (Tidak Baik)';
        }
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.admin-opd.surveys.pdf', compact(
            'respondent', 'answers', 'ikm', 'grade'
        ));
        $pdf->setPaper('A4', 'portrait');
        
        return $pdf->download('survei-' . $respondent->nik . '-' . now()->format('Y-m-d') . '.pdf');
    }
}