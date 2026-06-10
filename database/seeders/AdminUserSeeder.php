<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        \App\Models\User::create([
            'name' => 'Admin SKM Sumenep',
            'email' => 'admin@sumenepkab.go.id',
            'password' => bcrypt('password123'),
            'role' => 'admin',
        ]);
    }
}
