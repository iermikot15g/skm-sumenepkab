<?php

namespace App\Http\Controllers\Admin\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use App\Models\Opd;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    /**
     * Display a listing of Units.
     */
    public function index()
    {
        $units = Unit::with('opd')->paginate(10);
        return view('admin.super-admin.units.index', compact('units'));
    }

    /**
     * Show form to create new Unit.
     */
    public function create()
    {
        $opds = Opd::where('is_active', true)->get();
        return view('admin.super-admin.units.create', compact('opds'));
    }

    /**
     * Store a newly created Unit.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'opd_id' => 'required|exists:opds,id',
            'code' => 'required|string|max:20|unique:units,code',
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        Unit::create($validated);

        return redirect()->route('super-admin.units.index')
            ->with('success', 'Unit layanan berhasil ditambahkan.');
    }

    /**
     * Show form to edit Unit.
     */
    public function edit(Unit $unit)
    {
        $opds = Opd::where('is_active', true)->get();
        return view('admin.super-admin.units.edit', compact('unit', 'opds'));
    }

    /**
     * Update the specified Unit.
     */
    public function update(Request $request, Unit $unit)
    {
        $validated = $request->validate([
            'opd_id' => 'required|exists:opds,id',
            'code' => 'required|string|max:20|unique:units,code,' . $unit->id,
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $unit->update($validated);

        return redirect()->route('super-admin.units.index')
            ->with('success', 'Unit layanan berhasil diupdate.');
    }

    /**
     * Remove the specified Unit (soft delete).
     */
    public function destroy(Unit $unit)
    {
        // Cek apakah unit sudah memiliki responden
        if ($unit->respondents()->count() > 0) {
            return redirect()->route('super-admin.units.index')
                ->with('error', 'Unit tidak bisa dihapus karena sudah memiliki data survei.');
        }

        $unit->delete();

        return redirect()->route('super-admin.units.index')
            ->with('success', 'Unit layanan berhasil dihapus.');
    }
}