<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Detail Survei - {{ $respondent->full_name }}</title>
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
        .section {
            margin-bottom: 20px;
        }
        .section-title {
            background: #22c55e;
            color: white;
            padding: 8px;
            margin-bottom: 10px;
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        td {
            padding: 6px;
            border: 1px solid #ddd;
        }
        .label {
            background: #f3f4f6;
            width: 30%;
            font-weight: bold;
        }
        .answer-table td {
            padding: 8px;
        }
        .answer-table tr:nth-child(even) {
            background: #f9fafb;
        }
        .grade-A { color: #22c55e; font-weight: bold; }
        .grade-B { color: #3b82f6; font-weight: bold; }
        .grade-C { color: #eab308; font-weight: bold; }
        .grade-D { color: #ef4444; font-weight: bold; }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>SURVEI KEPUASAN MASYARAKAT</h1>
        <p>Pemerintah Kabupaten Sumenep</p>
        <p>Tanggal Cetak: {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>
    
    <div class="section">
        <div class="section-title">Informasi Survei</div>
        <table>
            <tr>
                <td class="label">Tanggal Survei</td>
                <td>{{ $respondent->created_at->format('d/m/Y H:i:s') }}</td>
            </tr>
            <tr>
                <td class="label">Unit Layanan</td>
                <td>{{ $respondent->unit->name ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Jenis Layanan</td>
                <td>{{ $respondent->selected_service }}</td>
            </tr>
            <tr>
                <td class="label">Periode Survei</td>
                <td>{{ $respondent->period->name ?? '-' }}</td>
            </tr>
        </table>
    </div>
    
    <div class="section">
        <div class="section-title">Data Responden</div>
        <table>
            <tr><td class="label">NIK</td><td>{{ $respondent->nik }}</td></tr>
            <tr><td class="label">Nama Lengkap</td><td>{{ $respondent->full_name }}</td></tr>
            <tr><td class="label">Nomor HP</td><td>{{ $respondent->phone }}</td></tr>
            <tr><td class="label">Kelompok Usia</td><td>{{ $respondent->age_group }} tahun</td></tr>
            <tr><td class="label">Jenis Kelamin</td><td>{{ $respondent->gender == 'male' ? 'Laki-laki' : 'Perempuan' }}</td></tr>
            <tr><td class="label">Pendidikan</td><td>{{ strtoupper($respondent->education) }}</td></tr>
            <tr>
                <td class="label">Pekerjaan</td>
                <td>
                    @if($respondent->occupation == 'lainnya')
                        {{ $respondent->other_occupation }}
                    @else
                        {{ ucfirst(str_replace('_', ' ', $respondent->occupation)) }}
                    @endif
                </td>
            </tr>
        </table>
    </div>
    
    <div class="section">
        <div class="section-title">Hasil Penilaian</div>
        <table class="answer-table">
            @foreach($answers as $num => $answer)
            <tr>
                <td width="40">{{ $num }}.</td>
                <td>{{ $answer['question'] }}</td>
                <td width="120" align="center">{{ $answer['score'] }} - {{ $answer['label'] }}</td>
            </tr>
            @endforeach
        </table>
    </div>
    
    <div class="section">
        <div class="section-title">Ringkasan IKM</div>
        <table>
            <tr>
                <td class="label">Rata-rata Skor</td>
                <td>{{ number_format($respondent->answers->avg('score') ?? 0, 2) }} / 4</td>
            </tr>
            <tr>
                <td class="label">Nilai IKM</td>
                <td class="grade-{{ substr($grade, 0, 1) }}">{{ number_format($ikm, 2) }}%</td>
            </tr>
            <tr>
                <td class="label">Mutu Pelayanan</td>
                <td class="grade-{{ substr($grade, 0, 1) }}">{{ $grade }}</td>
            </tr>
        </table>
    </div>
    
    <div class="footer">
        <p>Dokumen ini dihasilkan secara otomatis oleh Sistem SKM Kabupaten Sumenep</p>
    </div>
</body>
</html>