<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan IKM - {{ $opd->name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.5;
            margin: 2.54cm;
            background: white;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .title {
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 5px;
        }
        
        .subtitle {
            font-size: 12pt;
            margin-bottom: 3px;
        }
        
        .separator {
            border-top: 2px solid #000000;
            margin: 15px 0 10px 0;
        }
        
        .ikm-container {
            text-align: center;
            margin: 30px 0;
        }
        
        .ikm-label {
            font-size: 14pt;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .ikm-value {
            font-size: 18pt;
            font-weight: bold;
            letter-spacing: 1px;
        }
        
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        
        .info-table td {
            padding: 5px;
            vertical-align: top;
        }
        
        .info-table td:first-child {
            width: 180px;
            font-weight: bold;
        }
        
        .section-title {
            font-weight: bold;
            margin: 20px 0 10px 0;
            text-decoration: underline;
            font-size: 12pt;
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }
        
        .data-table td {
            padding: 5px;
            border-bottom: 1px dotted #cccccc;
        }
        
        .data-table td:first-child {
            width: 180px;
            font-weight: normal;
        }
        
        .thankyou {
            margin-top: 40px;
            text-align: center;
            font-weight: bold;
            font-size: 11pt;
            padding: 15px 0;
            border-top: 1px solid #000000;
            border-bottom: 1px solid #000000;
        }
        
        .thankyou-line {
            margin: 5px 0;
        }
        
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10pt;
            font-style: italic;
            color: #333333;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">INDEKS KEPUASAN MASYARAKAT (IKM)</div>
        <div class="subtitle">{{ $opd->name }}</div>
        <div class="subtitle">KABUPATEN SUMENEP</div>
        <div class="subtitle">PERIODE : {{ $periodName }}</div>
        <div class="separator"></div>
    </div>
    
    <div class="ikm-container">
        <div class="ikm-label">NILAI IKM</div>
        <div class="ikm-value">{{ number_format($averageIkm, 2) }}</div>
    </div>
    
    <table class="info-table">
        <tr>
            <td>NAMA LAYANAN :</td>
            <td>{{ $selectedService }}</td>
        </tr>
        <tr>
            <td>PERIODE SURVEI :</td>
            <td>{{ $startDate }} s/d {{ $endDate }}</td>
        </tr>
    </table>
    
    <div class="section-title">RESPONDEN</div>
    <table class="data-table">
        <tr>
            <td>JUMLAH RESPONDEN</td>
            <td>: <strong>{{ number_format($respondents->count()) }} orang</strong></td>
        </tr>
        <tr>
            <td>JENIS KELAMIN</td>
            <td>: L = {{ number_format($genderMale) }} orang / P = {{ number_format($genderFemale) }} orang</td>
        </tr>
    </table>
    
    <div class="section-title">PENDIDIKAN</div>
    <table class="data-table">
        <tr>
            <td>SD</td>
            <td>: {{ number_format($education['sd']) }} orang</td>
        </tr>
        <tr>
            <td>SMP</td>
            <td>: {{ number_format($education['smp']) }} orang</td>
        </tr>
        <tr>
            <td>SMA</td>
            <td>: {{ number_format($education['sma']) }} orang</td>
        </tr>
        <tr>
            <td>DIII</td>
            <td>: {{ number_format($education['d3']) }} orang</td>
        </tr>
        <tr>
            <td>S1</td>
            <td>: {{ number_format($education['s1']) }} orang</td>
        </tr>
        <tr>
            <td>S2</td>
            <td>: {{ number_format($education['s2']) }} orang</td>
        </tr>
    </table>
    
    <div class="thankyou">
        <div class="thankyou-line">TERIMA KASIH ATAS PENILAIAN YANG TELAH ANDA BERIKAN</div>
        <div class="thankyou-line">MASUKAN ANDA SANGAT BERMANFAAT UNTUK KEMAJUAN UNIT KAMI</div>
        <div class="thankyou-line">AGAR TERUS MEMPERBAIKI DAN MENINGKATKAN KUALITAS PELAYANAN</div>
        <div class="thankyou-line">BAGI MASYARAKAT</div>
    </div>
    
    <div class="footer">
        Laporan ini dihasilkan secara otomatis oleh Sistem SKM Kabupaten Sumenep<br>
        Berdasarkan Peraturan Menteri PANRB No. 14 Tahun 2017
    </div>
</body>
</html>