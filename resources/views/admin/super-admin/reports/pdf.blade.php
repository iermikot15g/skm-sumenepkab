<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Survei Kepuasan Masyarakat</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #22c55e;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
            color: #166534;
        }
        .header p {
            margin: 5px 0 0;
            color: #666;
        }
        .filter-info {
            background: #f3f4f6;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .filter-info table {
            width: 100%;
        }
        .filter-info td {
            padding: 3px;
        }
        .summary {
            margin-bottom: 20px;
        }
        .summary table {
            width: 100%;
            border-collapse: collapse;
        }
        .summary td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: center;
        }
        .summary .label {
            background: #f3f4f6;
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
            padding: 8px;
            text-align: left;
            font-size: 11px;
        }
        table.data td {
            border: 1px solid #ddd;
            padding: 6px;
            font-size: 10px;
        }
        table.data tr:nth-child(even) {
            background: #f9fafb;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .grade-A { color: #22c55e; font-weight: bold; }
        .grade-B { color: #3b82f6; font-weight: bold; }
        .grade-C { color: #eab308; font-weight: bold; }
        .grade-D { color: #ef4444; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN SURVEI KEPUASAN MASYARAKAT</h1>
        <p>Pemerintah Kabupaten Sumenep - Berdasarkan Permen PANRB No. 14 Tahun 2017</p>
        <p>Tanggal Cetak: {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>
    
    <div class="filter-info">
        <table>
            <tr>
                <td width="150"><strong>Periode:</strong></td>
                <td>{{ $period->name ?? 'Semua Periode' }}</td>
            </tr>
            <tr>
                <td><strong>OPD:</strong></td>
                <td>{{ $opd->name ?? 'Semua OPD' }}</td>
            </tr>
            <tr>
                <td><strong>Unit:</strong></td>
                <td>{{ $unit->name ?? 'Semua Unit' }}</td>
            </tr>
            @if($dateFrom || $dateTo)
            <tr>
                <td><strong>Rentang Tanggal:</strong></td>
                <td>{{ $dateFrom ?? 'Awal' }} s/d {{ $dateTo ?? 'Sekarang' }}</td>
            </tr>
            @endif
        </table>
    </div>
    
    <div class="summary">
        <table>
            <tr>
                <td class="label">Total Responden</td>
                <td class="label">Rata-rata IKM</td>
                <td class="label">Mutu Pelayanan</td>
            </tr>
            <tr>
                <td>{{ $totalRespondents }}</td>
                <td>{{ number_format($averageIkm, 2) }}%</td>
                <td class="grade-{{ substr($grade, 0, 1) }}">{{ $grade }}</td>
            </tr>
        </table>
    </div>
    
    <h3 style="margin: 20px 0 10px;">Data Responden</h3>
    
    <table class="data">
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Nama</th>
                <th>NIK</th>
                <th>Unit</th>
                <th>Skor Rata-rata</th>
                <th>IKM</th>
                <th>Mutu</th>
            </tr>
        </thead>
        <tbody>
            @foreach($respondents as $index => $respondent)
            @php
                $avgScore = $respondent->answers->avg('score') ?? 0;
                $ikm = ($avgScore / 4) * 100;
                if ($ikm >= 88.31) {
                    $mutu = 'A';
                } elseif ($ikm >= 76.61) {
                    $mutu = 'B';
                } elseif ($ikm >= 65.00) {
                    $mutu = 'C';
                } else {
                    $mutu = 'D';
                }
            @endphp
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $respondent->created_at->format('d/m/Y') }}</td>
                <td>{{ $respondent->full_name }}</td>
                <td>{{ $respondent->nik }}</td>
                <td>{{ $respondent->unit->name ?? '-' }}</td>
                <td>{{ number_format($avgScore, 2) }}</td>
                <td>{{ number_format($ikm, 1) }}%</td>
                <td class="grade-{{ $mutu }}">{{ $mutu }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <div class="footer">
        <p>Dokumen ini dihasilkan secara otomatis oleh Sistem SKM Kabupaten Sumenep</p>
    </div>
</body>
</html>