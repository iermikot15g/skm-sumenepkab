<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Survei - {{ $opd->short_name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 2px solid #22c55e;
        }
        .header h1 {
            margin: 0;
            font-size: 16px;
            color: #166534;
        }
        .header p {
            margin: 3px 0 0;
            color: #666;
        }
        .filter-info {
            background: #f3f4f6;
            padding: 8px;
            margin-bottom: 15px;
            border-radius: 4px;
            font-size: 9px;
        }
        .summary {
            margin-bottom: 15px;
        }
        .summary table {
            width: 100%;
            border-collapse: collapse;
        }
        .summary td {
            padding: 6px;
            border: 1px solid #ddd;
            text-align: center;
            background: #f9fafb;
        }
        .summary .label {
            background: #e5e7eb;
            font-weight: bold;
        }
        table.data {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table.data th {
            background: #22c55e;
            color: white;
            padding: 6px;
            text-align: left;
            font-size: 9px;
        }
        table.data td {
            border: 1px solid #ddd;
            padding: 5px;
            font-size: 8px;
        }
        .footer {
            margin-top: 15px;
            text-align: center;
            font-size: 8px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 8px;
        }
        .grade-A { color: #22c55e; font-weight: bold; }
        .grade-B { color: #3b82f6; font-weight: bold; }
        .grade-C { color: #eab308; font-weight: bold; }
        .grade-D { color: #ef4444; font-weight: bold; }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN SURVEI KEPUASAN MASYARAKAT</h1>
        <p>{{ $opd->name }}</p>
        <p>Pemerintah Kabupaten Sumenep</p>
        <p>Tanggal Cetak: {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>
    
    <div class="filter-info">
        <strong>Filter Laporan:</strong><br>
        Periode: {{ $period->name ?? 'Semua Periode' }}<br>
        Unit: {{ $unit->name ?? 'Semua Unit' }}<br>
        @if($dateFrom || $dateTo)
        Tanggal: {{ $dateFrom ?? 'Awal' }} s/d {{ $dateTo ?? 'Sekarang' }}
        @endif
    </div>
    
    <div class="summary">
        <table>
            <tr><td class="label">Total Responden</td><td>{{ $totalRespondents }}</td><td class="label">Rata-rata IKM</td><td class="grade-{{ substr($grade, 0, 1) }}">{{ $averageIkm }}%</td><td class="label">Mutu</td><td class="grade-{{ substr($grade, 0, 1) }}">{{ $grade }}</td></tr>
        </table>
    </div>
    
    <h3 style="margin: 10px 0;">Data Responden</h3>
    
    <table class="data">
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Nama</th>
                <th>NIK</th>
                <th>Unit</th>
                <th>Jenis Layanan</th>
                <th>IKM</th>
            </tr>
        </thead>
        <tbody>
            @foreach($respondents as $index => $respondent)
            @php
                $avgScore = $respondent->answers->avg('score') ?? 0;
                $ikm = ($avgScore / 4) * 100;
            @endphp
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $respondent->created_at->format('d/m/Y') }}</td>
                <td>{{ $respondent->full_name }}</td>
                <td>{{ $respondent->nik }}</td>
                <td>{{ $respondent->unit->name ?? '-' }}</td>
                <td>{{ $respondent->selected_service }}</td>
                <td class="grade-{{ $ikm >= 88.31 ? 'A' : ($ikm >= 76.61 ? 'B' : ($ikm >= 65 ? 'C' : 'D')) }}">{{ number_format($ikm, 1) }}%</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <div class="footer">
        <p>Dokumen ini dihasilkan secara otomatis oleh Sistem SKM Kabupaten Sumenep</p>
        <p>Berdasarkan Permen PANRB No. 14 Tahun 2017</p>
    </div>
</body>
</html>