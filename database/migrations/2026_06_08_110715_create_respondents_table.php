<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('respondents', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke unit dan periode
            $table->foreignId('unit_id')->constrained()->onDelete('restrict');
            $table->foreignId('period_id')->constrained()->onDelete('restrict');
            
            // Data diri responden
            $table->string('nik', 16);                      // NIK 16 digit
            $table->string('full_name', 100);               // Nama lengkap
            $table->string('phone', 15);                    // Nomor HP
            $table->string('selected_service', 100);        // Layanan yang dipilih
            
            // Demografi (sesuai Permen PANRB)
            $table->enum('age_group', ['<20', '20-30', '31-40', '41-50', '>50']);
            $table->enum('gender', ['male', 'female']);
            $table->enum('education', ['sd', 'smp', 'sma', 'd1', 'd2', 'd3', 's1', 's2', 's3']);
            $table->string('occupation', 50);               // Pekerjaan utama
            $table->string('other_occupation', 100)->nullable(); // Jika pilih "lainnya"
            
            // Keamanan & tracking
            $table->string('ip_address', 45)->nullable();   // IP responden
            $table->string('unique_hash', 64)->unique();    // Hash untuk cek duplikat
            
            $table->timestamps();
            
            // Unique constraint: 1 NIK hanya bisa 1 kali per unit per periode
            $table->unique(['nik', 'unit_id', 'period_id'], 'unique_respondent_per_period');
            
            // Index untuk query cepat
            $table->index('nik');
            $table->index('unique_hash');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('respondents');
    }
};