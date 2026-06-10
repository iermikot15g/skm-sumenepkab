<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    use HasFactory;

    protected $fillable = [
        'respondent_id', 'question_number', 'score'
    ];

    protected $casts = [
        'question_number' => 'integer',
        'score' => 'integer',
    ];

    // Relasi ke responden
    public function respondent()
    {
        return $this->belongsTo(Respondent::class);
    }

    // Daftar pertanyaan (untuk ditampilkan di form)
    public static function getQuestions()
    {
        return [
            1 => 'Kesesuaian Persyaratan Pelayanan',
            2 => 'Kemudahan Prosedur',
            3 => 'Kecepatan Pelayanan',
            4 => 'Kewajaran Biaya',
            5 => 'Kesesuaian Hasil Pelayanan',
            6 => 'Kompetensi Petugas',
            7 => 'Kesopanan & Keramahan',
            8 => 'Sarana & Prasarana',
            9 => 'Penanganan Pengaduan',
        ];
    }

    // Konversi score ke label
    public function getScoreLabelAttribute()
    {
        return match($this->score) {
            1 => 'Tidak Baik',
            2 => 'Kurang Baik',
            3 => 'Baik',
            4 => 'Sangat Baik',
            default => 'Tidak Valid',
        };
    }
}