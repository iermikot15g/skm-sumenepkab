<?php

namespace App\Http\Controllers\Admin\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Opd;
use Illuminate\Http\Request;

class OpdController extends Controller
{
    /**
     * Display a listing of OPDs.
     */
    public function index()
    {
        $opds = Opd::withCount('units')->paginate(10);
        return view('admin.super-admin.opds.index', compact('opds'));
    }

    /**
     * Show form to create new OPD.
     */
    public function create()
    {
        return view('admin.super-admin.opds.create');
    }

    /**
     * Store a newly created OPD.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:20|unique:opds,code',
            'name' => 'required|string|max:100',
            'short_name' => 'required|string|max:50',
            'description' => 'nullable|string',
            'head_name' => 'required|string|max:100',
            'head_nip' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:100',
            'phone' => 'nullable|string|max:20',
            'is_active' => 'boolean',
        ]);

        Opd::create($validated);

        return redirect()->route('super-admin.opds.index')
            ->with('success', 'OPD berhasil ditambahkan.');
    }

    /**
     * Show form to edit OPD.
     */
    public function edit(Opd $opd)
    {
        return view('admin.super-admin.opds.edit', compact('opd'));
    }

    /**
     * Update the specified OPD.
     */
    public function update(Request $request, Opd $opd)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:20|unique:opds,code,' . $opd->id,
            'name' => 'required|string|max:100',
            'short_name' => 'required|string|max:50',
            'description' => 'nullable|string',
            'head_name' => 'required|string|max:100',
            'head_nip' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:100',
            'phone' => 'nullable|string|max:20',
            'is_active' => 'boolean',
        ]);

        $opd->update($validated);

        return redirect()->route('super-admin.opds.index')
            ->with('success', 'OPD berhasil diupdate.');
    }

    /**
     * Remove the specified OPD (soft delete).
     */
    public function destroy(Opd $opd)
    {
        // Cek apakah OPD memiliki unit
        if ($opd->units()->count() > 0) {
            return redirect()->route('super-admin.opds.index')
                ->with('error', 'OPD tidak bisa dihapus karena masih memiliki unit layanan.');
        }

        $opd->delete();

        return redirect()->route('super-admin.opds.index')
            ->with('success', 'OPD berhasil dihapus.');
    }
}