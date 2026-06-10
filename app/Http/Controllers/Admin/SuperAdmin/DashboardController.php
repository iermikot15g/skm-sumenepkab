<?php

namespace App\Http\Controllers\Admin\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Opd;
use App\Models\Unit;
use App\Models\Period;
use App\Models\User;
use App\Models\Respondent;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Statistik dasar
        $totalOpd = Opd::count();
        $totalUnits = Unit::count();
        $totalUsers = User::count();
        $totalRespondents = Respondent::count();
        $activePeriod = Period::where('is_active', true)->first();
        
        // 10 survei terbaru dengan relasi
        $recentSurveys = Respondent::with(['unit.opd', 'period'])
            ->latest()
            ->take(10)
            ->get();
        
        // Hitung IKM per OPD untuk grafik
        $ikmByOpd = $this->calculateIkmByOpd();
        
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
    
    private function calculateIkmByOpd()
    {
        $opds = Opd::with('units.respondents.answers')->get();
        $result = [];
        
        foreach ($opds as $opd) {
            $totalIkm = 0;
            $totalRespondents = 0;
            
            foreach ($opd->units as $unit) {
                foreach ($unit->respondents as $respondent) {
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