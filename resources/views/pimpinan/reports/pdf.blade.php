<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Pimpinan - {{ $opd->short_name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 9px;
            line-height: 1.3;
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
            font-size: 8px;
        }
        .executive-summary {
            margin-bottom: 15px;
        }
        .executive-summary table {
            width: 100%;
            border-collapse: collapse;
        }
        .executive-summary td {
            padding: 6px;
            border: 1px solid #ddd;
            text-align: center;
            background: #f9fafb;
        }
        .executive-summary .label {
            background: #e5e7eb;
            font-weight: bold;
        }
        .section-title {
            background: #22c55e;
            color: white;
            padding: 5px;
            margin: 10px 0;
            font-weight: bold;
            font-size: 10px;
        }
        table.data {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }
        table.data th {
            background: #22c55e;
            color: white;
            padding: 5px;
            text-align: left;
            font-size: 8px;
        }
        table.data td {
            border: 1px solid #ddd;
            padding: 4px;
            font-size: 7px;
        }
        .ikm-unit-table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }
        .ikm-unit-table th {
            background: #e5e7eb;
            padding: 5px;
            text-align: left;
            font-size: 8px;
        }
        .ikm-unit-table td {
            border-bottom: 1px solid #ddd;
            padding: 4px;
            font-size: 8px;
        }
        .footer {
            margin-top: 15px;
            text-align: center;
            font-size: 7px;
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
        <h1>LAPORAN EKSEKUTIF</h1>
        <h2>SURVEI KEPUASAN MASYARAKAT</h2>
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
    
    <div class="executive-summary">
        <div class="section-title">RINGKASAN EKSEKUTIF</div>
        <table>
            <tr>
                <td class="label">Total Responden</td>
                <td>{{ number_format($totalRespondents) }}</td>
                <td class="label">Rata-rata IKM</td>
                <td class="grade-{{ substr($grade, 0, 1) }}">{{ number_format($averageIkm, 2) }}%</td>
                <td class="label">Mutu Pelayanan</td>
                <td class="grade-{{ substr($grade, 0, 1) }}">{{ $grade }}</td>
            </tr>
        </table>
    </div>
    
    <div class="section-title">IKM PER UNIT LAYANAN</div>
    <table class="ikm-unit-table">
        <thead>
            <tr><th>Unit Layanan</th><th>Jumlah Responden</th><th>Nilai IKM</th><th>Mutu</th></tr>
        </thead>
        <tbody>
            @foreach($ikmByUnit as $unit)
            @php
                $unitGrade = $unit['ikm'] >= 88.31 ? 'A' : ($unit['ikm'] >= 76.61 ? 'B' : ($unit['ikm'] >= 65 ? 'C' : 'D'));
            @endphp
            <tr>
                <td>{{ $unit['name'] }}</td>
                <td>{{ $unit['respondents'] }}</td>
                <td class="grade-{{ $unitGrade }}">{{ $unit['ikm'] }}%</td>
                <td class="grade-{{ $unitGrade }}">{{ $unitGrade }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <div class="section-title">DATA RESPONDEN</div>
    <table class="data">
        <thead>
            <tr>
                <th>No</th>
                <th>Tgl</th>
                <th>Nama</th>
                <th>NIK</th>
                <th>Unit</th>
                <th>Layanan</th>
                <th>IKM</th>
                <th>Mutu</th>
            </tr>
        </thead>
        <tbody>
            @foreach($respondents as $index => $respondent)
            @php
                $avgScore = $respondent->answers->avg('score') ?? 0;
                $ikm = ($avgScore / 4) * 100;
                $gradeLetter = $ikm >= 88.31 ? 'A' : ($ikm >= 76.61 ? 'B' : ($ikm >= 65 ? 'C' : 'D'));
            @endphp
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $respondent->created_at->format('d/m/y') }}</td>
                <td>{{ $respondent->full_name }}</td>
                <td>{{ $respondent->nik }}</td>
                <td>{{ $respondent->unit->name ?? '-' }}</td>
                <td>{{ Str::limit($respondent->selected_service, 20) }}</td>
                <td class="grade-{{ $gradeLetter }}">{{ number_format($ikm, 1) }}%</td>
                <td class="grade-{{ $gradeLetter }}">{{ $gradeLetter }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <div class="footer">
        <p>Dokumen ini dihasilkan secara otomatis oleh Sistem SKM Kabupaten Sumenep</p>
        <p>Berdasarkan Permen PANRB No. 14 Tahun 2017</p>
        <p>*Laporan ini bersifat read-only dan dapat diunduh kapan saja</p>
    </div>
</body>
</html>