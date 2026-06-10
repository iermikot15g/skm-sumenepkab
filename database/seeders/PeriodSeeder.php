<?php

namespace Database\Seeders;

use App\Models\Period;
use Illuminate\Database\Seeder;

class PeriodSeeder extends Seeder
{
    public function run(): void
    {
        // Periode aktif (Jan-Jun 2026)
        Period::create([
            'name' => 'Periode Januari - Juni 2026',
            'start_date' => '2026-01-01',
            'end_date' => '2026-06-30',
            'is_active' => true,
        ]);

        // Periode sebelumnya (arsip)
        Period::create([
            'name' => 'Periode Juli - Desember 2025',
            'start_date' => '2025-07-01',
            'end_date' => '2025-12-31',
            'is_active' => false,
        ]);
    }
}