<?php

namespace App\Http\Controllers\Admin\AdminOpd;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ServiceController extends Controller
{
    /**
     * Display a listing of services for units under this OPD.
     */
    public function index()
    {
        $user = Auth::user();
        $opdId = $user->opd_id;
        
        // Get all units under this OPD
        $units = Unit::where('opd_id', $opdId)->get();
        
        // Get services from all units (stored in session or database)
        // For now, we'll get from the getServicesByUnit method
        $services = [];
        foreach ($units as $unit) {
            $unitServices = $this->getServicesForUnit($unit->code);
            foreach ($unitServices as $service) {
                $services[] = [
                    'id' => $unit->id . '_' . md5($service),
                    'unit_id' => $unit->id,
                    'unit_name' => $unit->name,
                    'service_name' => $service,
                    'is_active' => true,
                ];
            }
        }
        
        return view('admin.admin-opd.services.index', compact('units', 'services'));
    }
    
    /**
     * Show form to create a new service for a unit.
     */
    public function create()
    {
        $user = Auth::user();
        $opdId = $user->opd_id;
        $units = Unit::where('opd_id', $opdId)->get();
        
        return view('admin.admin-opd.services.create', compact('units'));
    }
    
    /**
     * Store a newly created service.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'unit_id' => 'required|exists:units,id',
            'service_name' => 'required|string|min:3|max:100',
        ]);
        
        // Verify unit belongs to this OPD
        $user = Auth::user();
        $unit = Unit::find($validated['unit_id']);
        
        if ($unit->opd_id !== $user->opd_id) {
            return redirect()->route('admin-opd.services.index')
                ->with('error', 'Anda tidak memiliki akses ke unit tersebut.');
        }
        
        // Store service in session or database (temporary - can be moved to database)
        $services = session()->get('opd_services', []);
        $serviceKey = $validated['unit_id'] . '_' . md5($validated['service_name']);
        
        if (!isset($services[$serviceKey])) {
            $services[$serviceKey] = [
                'unit_id' => $validated['unit_id'],
                'service_name' => $validated['service_name'],
                'is_active' => true,
                'created_at' => now(),
            ];
            session()->put('opd_services', $services);
        }
        
        return redirect()->route('admin-opd.services.index')
            ->with('success', 'Jenis layanan berhasil ditambahkan.');
    }
    
    /**
     * Show form to edit a service.
     */
    public function edit($id)
    {
        $user = Auth::user();
        $opdId = $user->opd_id;
        $units = Unit::where('opd_id', $opdId)->get();
        
        // Get service from session
        $services = session()->get('opd_services', []);
        $service = $services[$id] ?? null;
        
        if (!$service) {
            return redirect()->route('admin-opd.services.index')
                ->with('error', 'Jenis layanan tidak ditemukan.');
        }
        
        return view('admin.admin-opd.services.edit', compact('units', 'service', 'id'));
    }
    
    /**
     * Update the specified service.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'unit_id' => 'required|exists:units,id',
            'service_name' => 'required|string|min:3|max:100',
        ]);
        
        // Verify unit belongs to this OPD
        $user = Auth::user();
        $unit = Unit::find($validated['unit_id']);
        
        if ($unit->opd_id !== $user->opd_id) {
            return redirect()->route('admin-opd.services.index')
                ->with('error', 'Anda tidak memiliki akses ke unit tersebut.');
        }
        
        // Update service in session
        $services = session()->get('opd_services', []);
        
        if (isset($services[$id])) {
            unset($services[$id]);
            $newKey = $validated['unit_id'] . '_' . md5($validated['service_name']);
            $services[$newKey] = [
                'unit_id' => $validated['unit_id'],
                'service_name' => $validated['service_name'],
                'is_active' => true,
                'updated_at' => now(),
            ];
            session()->put('opd_services', $services);
        }
        
        return redirect()->route('admin-opd.services.index')
            ->with('success', 'Jenis layanan berhasil diupdate.');
    }
    
    /**
     * Remove the specified service.
     */
    public function destroy($id)
    {
        $services = session()->get('opd_services', []);
        
        if (isset($services[$id])) {
            unset($services[$id]);
            session()->put('opd_services', $services);
        }
        
        return redirect()->route('admin-opd.services.index')
            ->with('success', 'Jenis layanan berhasil dihapus.');
    }
    
    /**
     * Toggle service active status.
     */
    public function toggleActive($id)
    {
        $services = session()->get('opd_services', []);
        
        if (isset($services[$id])) {
            $services[$id]['is_active'] = !$services[$id]['is_active'];
            session()->put('opd_services', $services);
        }
        
        return redirect()->route('admin-opd.services.index')
            ->with('success', 'Status layanan berhasil diubah.');
    }
    
    /**
     * Helper to get services for a unit.
     */
    private function getServicesForUnit($unitCode)
    {
        $services = [
            'DISPENDIK' => [
                'Pendaftaran Sekolah',
                'Ijazah & Transkrip Nilai',
                'Beasiswa Pendidikan',
                'Administrasi Guru',
                'Informasi Pendidikan'
            ],
            'DINKES' => [
                'Pendaftaran Berobat',
                'Kartu Sehat',
                'Imunisasi',
                'KB & Keluarga Berencana',
                'Informasi Kesehatan'
            ],
            'DUKCAPIL' => [
                'Pembuatan KTP',
                'Pembuatan KK',
                'Akta Kelahiran',
                'Akta Kematian',
                'Kartu Identitas Anak'
            ],
            'DINSOS' => [
                'Bantuan Sosial',
                'Rehabilitasi Sosial',
                'Pemberdayaan Masyarakat',
                'Penanganan PMKS',
                'Informasi Sosial'
            ],
            'DISPERIN' => [
                'Perizinan Usaha',
                'Sertifikasi Produk',
                'Pembinaan UMKM',
                'Informasi Perindustrian',
                'Kemitraan Bisnis'
            ],
        ];
        
        return $services[$unitCode] ?? [
            'Administrasi Umum',
            'Perizinan',
            'Informasi Publik',
            'Pengaduan Masyarakat',
            'Layanan Konsultasi'
        ];
    }
}