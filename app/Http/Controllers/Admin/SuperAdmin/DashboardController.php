<?php

namespace App\Http\Controllers\Admin\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Opd;
use App\Models\Unit;
use App\Models\Period;
use App\Models\User;
use App\Models\Respondent;

class DashboardController extends Controller
{
    public function index()
    {
        // Get active period
        $activePeriod = Period::where('is_active', true)->first();
        
        // If no active period, show empty stats
        if (!$activePeriod) {
            return view('admin.super-admin.dashboard', [
                'totalOpd' => Opd::count(),
                'totalUnits' => Unit::count(),
                'totalUsers' => User::count(),
                'totalRespondents' => 0,
                'activePeriod' => null,
                'recentSurveys' => collect(),
                'ikmByOpd' => [],
            ]);
        }
        
        // Statistics based on active period only
        $totalOpd = Opd::count();
        $totalUnits = Unit::count();
        $totalUsers = User::count();
        $totalRespondents = Respondent::where('period_id', $activePeriod->id)->count();
        
        // Recent surveys (only from active period)
        $recentSurveys = Respondent::with(['unit.opd', 'period'])
            ->where('period_id', $activePeriod->id)
            ->latest()
            ->take(10)
            ->get();
        
        // Calculate IKM per OPD (only from active period)
        $ikmByOpd = $this->calculateIkmByOpd($activePeriod->id);
        
        return view('admin.super-admin.dashboard', compact(
            'totalOpd',
            'totalUnits', 
            'totalUsers',
            'totalRespondents',
            'activePeriod',
            'recentSurveys',
            'ikmByOpd'
        ));
    }
    
    private function calculateIkmByOpd($periodId)
    {
        $opds = Opd::with('units.respondents.answers')->get();
        $result = [];
        
        foreach ($opds as $opd) {
            $totalIkm = 0;
            $totalRespondents = 0;
            
            foreach ($opd->units as $unit) {
                // Filter respondents by active period
                $respondents = $unit->respondents->where('period_id', $periodId);
                
                foreach ($respondents as $respondent) {
                    $avgScore = $respondent->answers->avg('score') ?? 0;
                    $ikm = ($avgScore / 4) * 100;
                    $totalIkm += $ikm;
                    $totalRespondents++;
                }
            }
            
            $result[$opd->short_name] = $totalRespondents > 0 
                ? round($totalIkm / $totalRespondents, 2) 
                : 0;
        }
        
        return $result;
    }
}