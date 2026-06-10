<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Respondent extends Model
{
    use HasFactory;

    protected $fillable = [
        'unit_id', 'period_id', 'nik', 'full_name', 'phone',
        'selected_service', 'age_group', 'gender', 'education',
        'occupation', 'other_occupation', 'ip_address', 'unique_hash'
    ];

    // Relasi ke unit
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    // Relasi ke periode
    public function period()
    {
        return $this->belongsTo(Period::class);
    }

    // Relasi ke jawaban (1 responden punya 9 jawaban)
    public function answers()
    {
        return $this->hasMany(Answer::class);
    }

    // Helper: hitung rata-rata skor
    public function getAverageScoreAttribute()
    {
        return $this->answers()->avg('score') ?? 0;
    }

    // Helper: hitung IKM
    public function getIKMAttribute()
    {
        $avg = $this->average_score;
        return ($avg / 4) * 100;
    }
}