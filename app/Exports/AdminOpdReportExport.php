<?php

namespace App\Exports;

use App\Models\Respondent;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AdminOpdReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $filters;
    
    public function __construct($filters)
    {
        $this->filters = $filters;
    }
    
    public function collection()
    {
        $query = Respondent::with(['unit', 'period', 'answers'])
            ->whereIn('unit_id', $this->filters['unit_ids']);
        
        if ($this->filters['period_id']) {
            $query->where('period_id', $this->filters['period_id']);
        }
        
        if ($this->filters['unit_id']) {
            $query->where('unit_id', $this->filters['unit_id']);
        }
        
        if ($this->filters['date_from']) {
            $query->whereDate('created_at', '>=', $this->filters['date_from']);
        }
        
        if ($this->filters['date_to']) {
            $query->whereDate('created_at', '<=', $this->filters['date_to']);
        }
        
        return $query->get();
    }
    
    public function headings(): array
    {
        return [
            'No',
            'Tanggal Survei',
            'NIK',
            'Nama Lengkap',
            'No HP',
            'Unit Layanan',
            'Jenis Layanan',
            'Periode',
            'Q1 - Kesesuaian Persyaratan',
            'Q2 - Kemudahan Prosedur',
            'Q3 - Kecepatan Pelayanan',
            'Q4 - Kewajaran Biaya',
            'Q5 - Kesesuaian Hasil',
            'Q6 - Kompetensi Petugas',
            'Q7 - Kesopanan & Keramahan',
            'Q8 - Sarana & Prasarana',
            'Q9 - Penanganan Pengaduan',
            'Rata-rata Skor',
            'IKM (%)',
            'Mutu',
        ];
    }
    
    public function map($respondent): array
    {
        static $rowNumber = 0;
        $rowNumber++;
        
        $answers = [];
        for ($i = 1; $i <= 9; $i++) {
            $answer = $respondent->answers->firstWhere('question_number', $i);
            $answers[] = $answer ? $answer->score : '-';
        }
        
        $avgScore = $respondent->answers->avg('score') ?? 0;
        $ikm = ($avgScore / 4) * 100;
        
        // Determine quality grade
        if ($ikm >= 88.31) {
            $mutu = 'A (Sangat Baik)';
        } elseif ($ikm >= 76.61) {
            $mutu = 'B (Baik)';
        } elseif ($ikm >= 65.00) {
            $mutu = 'C (Kurang Baik)';
        } else {
            $mutu = 'D (Tidak Baik)';
        }
        
        return [
            $rowNumber,
            $respondent->created_at->format('d/m/Y H:i'),
            $respondent->nik,
            $respondent->full_name,
            $respondent->phone,
            $respondent->unit->name ?? '-',
            $respondent->selected_service,
            $respondent->period->name ?? '-',
            $answers[0],
            $answers[1],
            $answers[2],
            $answers[3],
            $answers[4],
            $answers[5],
            $answers[6],
            $answers[7],
            $answers[8],
            number_format($avgScore, 2),
            number_format($ikm, 2),
            $mutu,
        ];
    }
    
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 11]],
            'A1:U1' => ['fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '22C55E']]],
        ];
    }
}