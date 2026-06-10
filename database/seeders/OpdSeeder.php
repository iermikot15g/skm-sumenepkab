<?php

namespace Database\Seeders;

use App\Models\Opd;
use Illuminate\Database\Seeder;

class OpdSeeder extends Seeder
{
    public function run(): void
    {
        $opds = [
            [
                'code' => 'DISPENDIK',
                'name' => 'Dinas Pendidikan',
                'short_name' => 'Dispendik',
                'description' => 'Bertanggung jawab atas pendidikan di Kabupaten Sumenep',
                'head_name' => 'Dr. H. Moh. Ilyas, M.Pd',
                'head_nip' => '197001011995031001',
                'email' => 'dispendik@sumenepkab.go.id',
                'phone' => '(0328) 123456',
                'is_active' => true,
            ],
            [
                'code' => 'DINKES',
                'name' => 'Dinas Kesehatan',
                'short_name' => 'Dinkes',
                'description' => 'Bertanggung jawab atas kesehatan masyarakat',
                'head_name' => 'dr. Hj. Siti Aisyah, M.Kes',
                'head_nip' => '197502151998032002',
                'email' => 'dinkes@sumenepkab.go.id',
                'phone' => '(0328) 234567',
                'is_active' => true,
            ],
            [
                'code' => 'DUKCAPIL',
                'name' => 'Dinas Kependudukan dan Pencatatan Sipil',
                'short_name' => 'Dukcapil',
                'description' => 'Pelayanan administrasi kependudukan',
                'head_name' => 'Drs. H. Achmad Fauzi, M.Si',
                'head_nip' => '196812311994031012',
                'email' => 'dukcapil@sumenepkab.go.id',
                'phone' => '(0328) 345678',
                'is_active' => true,
            ],
            [
                'code' => 'DINSOS',
                'name' => 'Dinas Sosial',
                'short_name' => 'Dinsos',
                'description' => 'Pelayanan sosial dan pemberdayaan masyarakat',
                'head_name' => 'Hj. Nurul Hidayati, S.Sos, M.Si',
                'head_nip' => '197408152005012001',
                'email' => 'dinsos@sumenepkab.go.id',
                'phone' => '(0328) 456789',
                'is_active' => true,
            ],
        ];

        foreach ($opds as $opd) {
            Opd::create($opd);
        }
    }
}