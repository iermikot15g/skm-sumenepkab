<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;  // ← Tambahkan ini

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;  // ← Tambahkan SoftDeletes

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'opd_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // Relasi ke OPD
    public function opd()
    {
        return $this->belongsTo(Opd::class);
    }

    // Role check methods
    public function isSuperAdmin()
    {
        return $this->role === 'super_admin';
    }

    public function isAdminOpd()
    {
        return $this->role === 'admin_opd';
    }

    public function isPimpinanOpd()
    {
        return $this->role === 'pimpinan_opd';
    }
}