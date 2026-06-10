<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('periods', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);              // "Periode Januari-Juni 2026"
            $table->date('start_date');               // Tanggal mulai survei
            $table->date('end_date');                 // Tanggal selesai survei
            $table->boolean('is_active')->default(false); // Hanya 1 periode aktif
            $table->timestamps();
            
            $table->index('is_active');
            $table->index(['start_date', 'end_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('periods');
    }
};