<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Unit;

class UnitSeeder extends Seeder
{
    public function run(): void
    {
        // Buat 3 unit layanan
        Unit::create([
            'name' => 'Dinas Pendidikan',
            'code' => 'DISPENDIK',
            'description' => 'Pelayanan pendidikan Kabupaten Sumenep',
            'is_active' => true,
        ]);

        Unit::create([
            'name' => 'Dinas Kesehatan',
            'code' => 'DINASKES',
            'description' => 'Pelayanan kesehatan Kabupaten Sumenep',
            'is_active' => true,
        ]);

        Unit::create([
            'name' => 'Dinas Kependudukan dan Pencatatan Sipil',
            'code' => 'DUKCAPIL',
            'description' => 'Pelayanan administrasi kependudukan',
            'is_active' => true,
        ]);
    }
}