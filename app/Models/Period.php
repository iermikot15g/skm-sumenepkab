<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Period extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'start_date', 'end_date', 'is_active'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    // Relasi: Satu periode punya banyak responden
    public function respondents()
    {
        return $this->hasMany(Respondent::class);
    }

    // Scope periode aktif
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Cek apakah periode sedang berlangsung
    public function isOngoing()
    {
        $now = now();
        return $now->between($this->start_date, $this->end_date);
    }
}