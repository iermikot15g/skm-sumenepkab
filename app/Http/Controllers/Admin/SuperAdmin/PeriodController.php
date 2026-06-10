<?php

namespace App\Http\Controllers\Admin\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Period;
use Illuminate\Http\Request;

class PeriodController extends Controller
{
    /**
     * Display a listing of Periods.
     */
    public function index()
    {
        $periods = Period::orderBy('start_date', 'desc')->paginate(10);
        return view('admin.super-admin.periods.index', compact('periods'));
    }

    /**
     * Show form to create new Period.
     */
    public function create()
    {
        return view('admin.super-admin.periods.create');
    }

    /**
     * Store a newly created Period.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'is_active' => 'boolean',
        ]);

        // Jika periode baru diaktifkan, nonaktifkan periode lain
        if ($request->has('is_active') && $request->is_active == 1) {
            Period::where('is_active', true)->update(['is_active' => false]);
        }

        Period::create($validated);

        return redirect()->route('super-admin.periods.index')
            ->with('success', 'Periode survei berhasil ditambahkan.');
    }

    /**
     * Show form to edit Period.
     */
    public function edit(Period $period)
    {
        return view('admin.super-admin.periods.edit', compact('period'));
    }

    /**
     * Update the specified Period.
     */
    public function update(Request $request, Period $period)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'is_active' => 'boolean',
        ]);

        // Jika periode ini diaktifkan, nonaktifkan periode lain
        if ($request->has('is_active') && $request->is_active == 1) {
            Period::where('id', '!=', $period->id)
                ->where('is_active', true)
                ->update(['is_active' => false]);
        }

        $period->update($validated);

        return redirect()->route('super-admin.periods.index')
            ->with('success', 'Periode survei berhasil diupdate.');
    }

    /**
     * Remove the specified Period.
     */
    public function destroy(Period $period)
    {
        // Cek apakah periode sudah memiliki responden
        if ($period->respondents()->count() > 0) {
            return redirect()->route('super-admin.periods.index')
                ->with('error', 'Periode tidak bisa dihapus karena sudah memiliki data survei.');
        }

        $period->delete();

        return redirect()->route('super-admin.periods.index')
            ->with('success', 'Periode survei berhasil dihapus.');
    }

    /**
     * Toggle active status (additional feature)
     */
    public function toggleActive(Period $period)
    {
        // Nonaktifkan semua periode
        Period::where('is_active', true)->update(['is_active' => false]);
        
        // Aktifkan periode yang dipilih
        $period->update(['is_active' => true]);

        return redirect()->route('super-admin.periods.index')
            ->with('success', 'Periode aktif berhasil diubah.');
    }
}