<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();  // Kode unik: DISPENDIK, DINKES
            $table->string('name', 100);           // Nama unit: Dinas Pendidikan
            $table->text('description')->nullable(); // Deskripsi unit
            $table->boolean('is_active')->default(true); // Status aktif/tidak
            $table->timestamps();                   // created_at, updated_at
            $table->softDeletes();                  // deleted_at (soft delete)
            
            // Index untuk performance
            $table->index('code');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};