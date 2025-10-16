<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Lembar Perjalanan Dinas</title>
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
        .value-no-border {
            flex: 1;
            padding: 2px 5px;
        }
        .pegawai-list {
            margin-top: 10px;
        }
        .pegawai-item {
            margin-bottom: 5px;
            padding: 5px;
            border: 1px solid #ddd;
            background-color: #f9f9f9;
        }
        .pegawai-name {
            font-weight: bold;
            margin-bottom: 2px;
        }
        .pegawai-detail {
            font-size: 10px;
            color: #555;
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
        .tanggal-box {
            text-align: right;
            margin-bottom: 15px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="kop">
        <h1>Pemerintah Daerah {{ App\Models\Pengaturan::get('nama_daerah', '[Nama Daerah]') }}</h1>
        <h2>{{ App\Models\Pengaturan::get('nama_opd', '[Nama OPD]') }} <br>
        Alamat: {{ App\Models\Pengaturan::get('alamat_opd', '[Alamat Lengkap]') }} Telp: {{ App\Models\Pengaturan::get('telepon_opd', '[Nomor Telepon]') }} <br>
        Website: {{ App\Models\Pengaturan::get('website_opd', 'www.websitekota.go.id') }}, Email: {{ App\Models\Pengaturan::get('email_opd', 'email@kota.go.id') }}</h2>
        <hr>
    </div>

    <div class="judul">
        <h3>LEMBAR PERJALANAN DINAS</h3>
        <p style="font-size: 10px; margin: 2px 0;">Nomor: {{ $perjalananDinas->nomor_surat_tugas }}</p>
    </div>

    <div class="tanggal-box">
        <!-- Tanggal: {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('d F Y') }} -->
    </div>

    <div class="form-container">
        <div class="row">
            <div class="label">Maksud Perjalanan</div>
            <div class="value">{{ $perjalananDinas->maksud_perjalanan }}</div>
        </div>
        
        <div class="row">
            <div class="label">Tempat Tujuan</div>
            <div class="value">{{ $perjalananDinas->tempat_tujuan }}</div>
        </div>
        
        <div class="row">
            <div class="label">Tanggal Berangkat</div>
            <div class="value">{{ \Carbon\Carbon::parse($perjalananDinas->tgl_berangkat)->format('d/m/Y') }}</div>
        </div>
        
        <div class="row">
            <div class="label">Tanggal Kembali</div>
            <div class="value">{{ \Carbon\Carbon::parse($perjalananDinas->tgl_kembali)->format('d/m/Y') }}</div>
        </div>
        
        <div class="row">
            <div class="label">Lama Perjalanan</div>
            <div class="value">{{ \Carbon\Carbon::parse($perjalananDinas->tgl_berangkat)->diffInDays(\Carbon\Carbon::parse($perjalananDinas->tgl_kembali)) + 1 }} Hari</div>
        </div>
        
        <div class="row">
            <div class="label">Pemberi Tugas</div>
            <div class="value">{{ $perjalananDinas->pimpinanPemberiTugas->name ?? '-' }}</div>
        </div>
    </div>

    <div class="form-container">
        <div style="font-weight: bold; margin-bottom: 8px;">Pegawai Yang Melaksanakan Tugas:</div>
        
        <div class="pegawai-list">
            @if($perjalananDinas->pegawai->isNotEmpty())
                @foreach($perjalananDinas->pegawai as $index => $pegawai)
                    <div class="pegawai-item">
                        <div class="pegawai-name">{{ $index + 1 }}. {{ $pegawai->nama_lengkap }}</div>
                        <div class="pegawai-detail">
                            NIP: {{ $pegawai->NIP }}<br>
                            Unit Kerja: {{ $pegawai->unitKerja->nama_unit ?? '-' }}<br>
                            Jabatan: {{ $pegawai->jabatan->nama ?? '-' }}
                        </div>
                    </div>
                @endforeach
            @else
                <div style="padding: 10px; text-align: center; font-style: italic;">
                    Tidak ada pegawai yang ditugaskan
                </div>
            @endif
        </div>
    </div>

    <div class="form-container">
        <div style="font-weight: bold; margin-bottom: 5px;">CATATAN:</div>
        <div style="border: 1px solid #000; padding: 8px; min-height: 60px;">
            <!-- Catatan dapat ditulis di sini -->
        </div>
    </div>

    <div class="footer">
        <div class="ttd-container">
            <div class="ttd-box">
                <div style="width: 120px; height: 150px; border: 1px solid #000; margin: 0 auto 10px; display: flex; align-items: center; justify-content: center; background-color: #f5f5f5; font-size: 9px; color: #666;">
                    PAS FOTO<br>(3x4)
                </div>
                <p><strong>PELAKSANA TUGAS</strong></p>
                @if($perjalananDinas->pegawai->isNotEmpty() && $perjalananDinas->pegawai->count() == 1)
                    <p>{{ $perjalananDinas->pegawai->first()->nama_lengkap }}</p>
                    <p style="font-size: 9px;">{{ $perjalananDinas->pegawai->first()->NIP }}</p>
                @else
                    <p style="min-height: 40px;"></p>
                @endif
                <div class="ttd-line"></div>
            </div>
            
            <div class="ttd-box">
                <div style="margin: 50px 0 10px;"></div>
                <p><strong>MENGETAHUI</strong></p>
                <p><strong>{{ App\Models\Pengaturan::get('nama_kepala_opd', '[Nama Kepala OPD]') }}</strong></p>
                <p><strong>Kepala {{ App\Models\Pengaturan::get('nama_opd', '[Nama OPD]') }}</strong></p>
                <p style="font-size: 9px;"><strong>NIP. {{ App\Models\Pengaturan::get('nip_kepala_opd', '[NIP Kepala OPD]') }}</strong></p>
                <div class="ttd-line"></div>
            </div>
        </div>
        
        <div style="text-align: center; margin-top: 30px;">
            <p style="font-size: 10px; margin: 3px 0;">Demikian lembar perjalanan dinas ini dibuat untuk dapat dipergunakan sebagaimana mestinya.</p>
        </div>
    </div>
</body>
</html>
