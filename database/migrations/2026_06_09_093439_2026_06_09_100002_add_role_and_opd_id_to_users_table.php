<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Tambah kolom role (hanya jika belum ada)
            if (!Schema::hasColumn('users', 'role')) {
                $table->enum('role', ['super_admin', 'admin_opd', 'pimpinan_opd'])->default('admin_opd')->after('email');
            }
            
            // Tambah kolom opd_id (hanya jika belum ada)
            if (!Schema::hasColumn('users', 'opd_id')) {
                $table->foreignId('opd_id')->nullable()->after('role')->constrained('opds')->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Hapus foreign key dulu
            if (Schema::hasColumn('users', 'opd_id')) {
                $table->dropForeign(['opd_id']);
                $table->dropColumn('opd_id');
            }
            
            // Hapus kolom role
            if (Schema::hasColumn('users', 'role')) {
                $table->dropColumn('role');
            }
        });
    }
};