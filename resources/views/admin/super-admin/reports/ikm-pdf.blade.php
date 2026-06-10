<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan IKM - Kabupaten Sumenep</title>
    <style>
        /* Reset margin dan padding */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.5;
            margin: 2.54cm; /* margin standar dokumen */
            background: white;
        }
        
        /* Header */
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
        
        /* Nilai IKM */
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
        
        /* Tabel Informasi */
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
        
        /* Section Title */
        .section-title {
            font-weight: bold;
            margin: 20px 0 10px 0;
            text-decoration: underline;
            font-size: 12pt;
        }
        
        /* Tabel Data */
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
        
        .data-table td:last-child {
            font-weight: normal;
        }
        
        /* Indent untuk pendidikan */
        .indent {
            padding-left: 20px;
        }
        
        /* Footer Terima Kasih */
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
        
        /* Footer Bawah */
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10pt;
            font-style: italic;
            color: #333333;
        }
        
        /* Spasi vertikal */
        .spacer-10 {
            margin-top: 10px;
        }
        .spacer-20 {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <!-- HEADER -->
    <div class="header">
        <div class="title">INDEKS KEPUASAN MASYARAKAT (IKM)</div>
        <div class="subtitle">PEMERINTAH KABUPATEN SUMENEP</div>
        
        <div class="subtitle">PERIODE : {{ $periodName }}</div>
        <div class="separator"></div>
    </div>
    
    <!-- NILAI IKM -->
    <div class="ikm-container">
        <div class="ikm-label">NILAI IKM</div>
        <div class="ikm-value">{{ number_format($averageIkm, 2) }}</div>
    </div>
    
    <!-- TABEL INFORMASI -->
    <table class="info-table">
        <tr>
            <td>NAMA UNIT :</td>
            <td>{{ $unitName }}</td>
        </tr>
        <tr>
            <td>PERIODE SURVEI :</td>
            <td>{{ $startDate }} s/d {{ $endDate }}</td>
        </tr>
    </table>
    
    <!-- RESPONDEN -->
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
    
    <!-- PENDIDIKAN -->
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
    
    <!-- TERIMA KASIH -->
    <div class="thankyou">
        <div class="thankyou-line">TERIMA KASIH ATAS PENILAIAN YANG TELAH ANDA BERIKAN</div>
        <div class="thankyou-line">MASUKAN ANDA SANGAT BERMANFAAT UNTUK KEMAJUAN UNIT KAMI</div>
        <div class="thankyou-line">AGAR TERUS MEMPERBAIKI DAN MENINGKATKAN KUALITAS PELAYANAN</div>
        <div class="thankyou-line">BAGI MASYARAKAT</div>
    </div>
    
    <!-- FOOTER -->
    <div class="footer">
        Laporan ini dihasilkan secara otomatis oleh Sistem SKM Kabupaten Sumenep<br>
        Berdasarkan Peraturan Menteri PANRB No. 14 Tahun 2017
    </div>
</body>
</html>