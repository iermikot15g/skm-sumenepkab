<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Unit extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code', 'name', 'description', 'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relasi: Satu unit punya banyak responden
    public function respondents()
    {
        return $this->hasMany(Respondent::class);
    }

    // Scope untuk hanya unit aktif
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Accessor: mengambil nama unit dengan format rapi
    public function getFormattedNameAttribute()
    {
        return "Unit: {$this->name}";
    }

    public function opd()
    {
        return $this->belongsTo(Opd::class);
    }
}