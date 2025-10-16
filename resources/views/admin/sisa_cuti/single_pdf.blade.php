<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sisa Cuti Pegawai</title>
    <style>
        @page {
            margin: 25px;
            size: A4 portrait;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            margin: 0;
            padding: 0;
            line-height: 1.3;
        }
        .kop {
            text-align: center;
            margin-bottom: 20px;
        }
        .kop h1 {
            font-size: 14px;
            font-weight: bold;
            margin: 0;
            text-transform: uppercase;
        }
        .kop h2 {
            font-size: 11px;
            font-weight: normal;
            margin: 3px 0;
        }
        .kop hr {
            border: 1px solid black;
            margin: 8px 0;
        }
        .judul {
            text-align: center;
            margin-bottom: 20px;
        }
        .judul h3 {
            font-size: 12px;
            font-weight: bold;
            margin: 5px 0;
            text-decoration: underline;
        }
        .form-container {
            border: 2px solid #000;
            padding: 15px;
            margin-bottom: 20px;
        }
        .row {
            display: flex;
            margin-bottom: 8px;
            align-items: flex-start;
        }
        .label {
            width: 150px;
            font-weight: bold;
            flex-shrink: 0;
        }
        .value {
            flex: 1;
            border-bottom: 1px solid #000;
            padding: 2px 5px;
            min-height: 16px;
        }
        .footer {
            margin-top: 40px;
        }
        .ttd-container {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
        }
        .ttd-box {
            text-align: center;
            width: 45%;
        }
        .ttd-box p {
            margin: 3px 0;
            font-size: 10px;
        }
        .ttd-line {
            border-bottom: 1px solid #000;
            height: 30px;
            margin: 5px 0;
        }
        .status-box {
            padding: 8px;
            margin-top: 10px;
            border: 1px solid #000;
            text-align: center;
            font-weight: bold;
        }
        .status-available {
            background-color: #d4edda;
            color: #155724;
        }
        .status-empty {
            background-color: #f8d7da;
            color: #721c24;
        }
        .cuti-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .cuti-table th, .cuti-table td {
            border: 1px solid #000;
            padding: 6px;
            text-align: center;
        }
        .cuti-table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="kop">
        <h1>Pemerintah Daerah Kabupaten/Kota [Nama Daerah]</h1>
        <h2>[Nama OPD] <br>
        Alamat: [Alamat Lengkap] Telp: [Nomor Telepon] <br>
        Website: www.websitekota.go.id, Email: email@kota.go.id</h2>
        <hr>
    </div>

    <div class="judul">
        <h3>LEMBAR REKAPITULASI CUTI</h3>
        <p style="font-size: 10px; margin: 2px 0;">Tahun {{ $sisaCuti->tahun }}</p>
    </div>

    <div class="form-container">
        <div class="row">
            <div class="label">NIP</div>
            <div class="value">{{ $sisaCuti->pegawai->NIP ?? '-' }}</div>
        </div>
        
        <div class="row">
            <div class="label">Nama Pegawai</div>
            <div class="value">{{ $sisaCuti->pegawai->nama_lengkap ?? '-' }}</div>
        </div>
        
        <div class="row">
            <div class="label">Unit Kerja</div>
            <div class="value">{{ $sisaCuti->pegawai->unitKerja->nama_unit ?? '-' }}</div>
        </div>
        
        <div class="row">
            <div class="label">Jabatan</div>
            <div class="value">{{ $sisaCuti->pegawai->jabatan->nama ?? '-' }}</div>
        </div>
        
        <div class="row">
            <div class="label">Golongan</div>
            <div class="value">{{ $sisaCuti->pegawai->golongan->nama ?? '-' }}</div>
        </div>
    </div>

    <div class="form-container">
        <table class="cuti-table">
            <thead>
                <tr>
                    <th style="width: 33%;">JATAH CUTI TAHUN {{ $sisaCuti->tahun }}</th>
                    <th style="width: 33%;">TERPAKAI</th>
                    <th style="width: 34%;">SISA CUTI</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>{{ $sisaCuti->jatah_cuti }}</strong></td>
                    <td><strong>{{ $sisaCuti->jatah_cuti - $sisaCuti->sisa_cuti }}</strong></td>
                    <td><strong>{{ $sisaCuti->sisa_cuti }}</strong></td>
                </tr>
            </tbody>
        </table>
        
        @if($sisaCuti->sisa_cuti > 0)
        <div class="status-box status-available">
            CUTI TAHUN {{ $sisaCuti->tahun }} MASIH TERSEDIA {{ $sisaCuti->sisa_cuti }} HARI
        </div>
        @else
        <div class="status-box status-empty">
            CUTI TAHUN {{ $sisaCuti->tahun }} TIDAK TERSEDIA (HABIS)
        </div>
        @endif
    </div>

    <div class="form-container">
        <div class="row">
            <div class="label">Tanggal Cetak</div>
            <div class="value">{{ \Carbon\Carbon::now()->format('d/m/Y') }}</div>
        </div>
        
        <div class="row">
            <div class="label">Dicetak Oleh</div>
            <div class="value">Sistem SIMPEG</div>
        </div>
    </div>

    <div class="footer">
        <div style="text-align: center; margin-bottom: 30px;">
            <p style="font-size: 10px; margin: 3px 0;">Demikian lembar sisa cuti ini dibuat untuk dapat dipergunakan sebagaimana mestinya.</p>
        </div>
        
        <div class="ttd-container">
            <div class="ttd-box">
                <div style="margin: 50px 0 10px;"></div>
                <p><strong>PEGAWAI YANG BERSANGKUTAN</strong></p>
                <p>{{ $sisaCuti->pegawai->nama_lengkap ?? '-' }}</p>
                <p style="font-size: 9px;">NIP. {{ $sisaCuti->pegawai->NIP ?? '-' }}</p>
                <div class="ttd-line"></div>
            </div>
            
            <div class="ttd-box">
                <p>{{ \Carbon\Carbon::now()->locale('id')->translatedFormat('d F Y') }}</p>
                <p><strong>MENGETAHUI</strong></p>
                <p><strong>{{ App\Models\Pengaturan::get('nama_kepala_opd', '[Nama Kepala OPD]') }}</strong></p>
                <p><strong>Kepala {{ App\Models\Pengaturan::get('nama_opd', '[Nama OPD]') }}</strong></p>
                <p style="font-size: 9px;"><strong>NIP. {{ App\Models\Pengaturan::get('nip_kepala_opd', '[NIP Kepala OPD]') }}</strong></p>
                <div class="ttd-line"></div>
            </div>
        </div>
    </div>
</body>
</html>
