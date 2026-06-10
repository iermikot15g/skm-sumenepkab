<?php

namespace App\Http\Controllers;

use App\Models\Respondent;
use App\Models\Unit;
use App\Models\Period;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Get active period
        $activePeriod = Period::where('is_active', true)->first();
        
        // Build query for respondents in active period
        $query = Respondent::query();
        if ($activePeriod) {
            $query->where('period_id', $activePeriod->id);
        }
        
        // Statistics
        $totalRespondents = $query->count();
        $totalUnits = Unit::where('is_active', true)->count();
        
        // Calculate average IKM
        $respondents = $query->get();
        $totalIkm = 0;
        foreach ($respondents as $respondent) {
            $avgScore = $respondent->answers->avg('score') ?? 0;
            $totalIkm += ($avgScore / 4) * 100;
        }
        $averageIkm = $totalRespondents > 0 ? round($totalIkm / $totalRespondents, 2) : 0;
        
        return view('home', compact('totalRespondents', 'totalUnits', 'averageIkm'));
    }
}