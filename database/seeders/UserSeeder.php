<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Opd;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Super Admin
        User::create([
            'name' => 'Super Admin Sumenep',
            'email' => 'superadmin@sumenepkab.go.id',
            'password' => Hash::make('SuperAdmin123!'),
            'role' => 'super_admin',
            'opd_id' => null,
        ]);

        // Admin OPD untuk Dinas Pendidikan
        $dispendik = Opd::where('code', 'DISPENDIK')->first();
        User::create([
            'name' => 'Admin Dispendik',
            'email' => 'admin@dispendik.sumenepkab.go.id',
            'password' => Hash::make('AdminDispendik123!'),
            'role' => 'admin_opd',
            'opd_id' => $dispendik->id,
        ]);

        // Admin OPD untuk Dinas Kesehatan
        $dinkes = Opd::where('code', 'DINKES')->first();
        User::create([
            'name' => 'Admin Dinkes',
            'email' => 'admin@dinkes.sumenepkab.go.id',
            'password' => Hash::make('AdminDinkes123!'),
            'role' => 'admin_opd',
            'opd_id' => $dinkes->id,
        ]);

        // Pimpinan OPD untuk Dinas Pendidikan
        User::create([
            'name' => 'Kepala Dinas Pendidikan',
            'email' => 'kadispendik@sumenepkab.go.id',
            'password' => Hash::make('Kadispendik123!'),
            'role' => 'pimpinan_opd',
            'opd_id' => $dispendik->id,
        ]);

        // Pimpinan OPD untuk Dinas Kesehatan
        User::create([
            'name' => 'Kepala Dinas Kesehatan',
            'email' => 'kadinkes@sumenepkab.go.id',
            'password' => Hash::make('Kadinkes123!'),
            'role' => 'pimpinan_opd',
            'opd_id' => $dinkes->id,
        ]);
    }
}