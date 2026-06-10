<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Period;

class ActivePeriodSeeder extends Seeder
{
    public function run(): void
    {
        // Non-aktifkan semua periode dulu
        Period::query()->update(['is_active' => false]);
        
        // Buat periode aktif baru
        Period::create([
            'name' => 'Periode Juli-Desember 2026',
            'start_date' => '2026-07-01',
            'end_date' => '2026-12-31',
            'is_active' => true,
        ]);
    }
}