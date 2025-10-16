<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Data Pegawai</title>
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
            width: 140px;
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
        .photo-box {
            float: right;
            width: 120px;
            height: 150px;
            border: 1px solid #000;
            margin-left: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f5f5f5;
            font-size: 9px;
            color: #666;
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
        <h3>DATA PEGAWAI</h3>
    </div>

    <div class="form-container">
        <div style="display: flex; justify-content: space-between;">
            <div style="flex: 1;">
                <div class="row" style="align-items: flex-end;">
                    <div class="label">NIP</div>
                    <div class="value">{{ $pegawai->NIP }}</div>
                </div>
                
                <div class="row">
                    <div class="label">Nama Lengkap</div>
                    <div class="value">{{ $pegawai->nama_lengkap }}</div>
                </div>
                
                <div class="row">
                    <div class="label">Tempat, Tgl Lahir</div>
                    <div class="value">{{ $pegawai->tempat_lahir }}, {{ \Carbon\Carbon::parse($pegawai->tgl_lahir)->format('d/m/Y') }}</div>
                </div>
                
                <div class="row">
                    <div class="label">Jenis Kelamin</div>
                    <div class="value">{{ $pegawai->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</div>
                </div>
                
                <div class="row">
                    <div class="label">Agama</div>
                    <div class="value">{{ $pegawai->agama }}</div>
                </div>
            </div>
            
            <div class="photo-box">
                @if($pegawai->foto_profil && file_exists(public_path('storage/' . $pegawai->foto_profil)))
                    <img src="{{ asset('storage/' . $pegawai->foto_profil) }}" alt="Foto Pegawai" style="width: 100%; height: 100%; object-fit: cover;">
                @else
                    PAS FOTO<br>(3x4)
                @endif
            </div>
        </div>
        
        <div style="clear: both;"></div>
        
        <div class="row">
            <div class="label">Alamat</div>
            <div class="value">{{ $pegawai->alamat }}</div>
        </div>
        
        <div class="row">
            <div class="label">No. Telepon</div>
            <div class="value">{{ $pegawai->no_telp }}</div>
        </div>
    </div>

    <div class="form-container">
        <div class="row">
            <div class="label">Jabatan</div>
            <div class="value">{{ $pegawai->jabatan ? $pegawai->jabatan->nama : '-' }}</div>
        </div>
        
        <div class="row">
            <div class="label">Golongan</div>
            <div class="value">{{ $pegawai->golongan ? $pegawai->golongan->nama : '-' }}</div>
        </div>
        
        <div class="row">
            <div class="label">Unit Kerja</div>
            <div class="value">{{ $pegawai->unitKerja ? $pegawai->unitKerja->nama_unit : '-' }}</div>
        </div>
        
        <div class="row">
            <div class="label">Kelas Jabatan</div>
            <div class="value">{{ $pegawai->jabatan && isset($pegawai->jabatan->nama_kelas_karir) ? $pegawai->jabatan->nama_kelas_karir : '-' }}</div>
        </div>
        
        <div class="row">
            <div class="label">Masa Kerja</div>
            <div class="value">@if($pegawai->tmt_pns){{ \Carbon\Carbon::parse($pegawai->tmt_pns)->diffInYears(\Carbon\Carbon::now()) }} Tahun @else - @endif</div>
        </div>
        
        <div class="row">
            <div class="label">Gaji Pokok</div>
            <div class="value">{{ $pegawai->jabatan ? ($pegawai->jabatan->jenis_jabatan ?? ($pegawai->jabatan->kelas_jabatan ?? '-')) : '-' }}</div>
        </div>
        
        <div class="row">
            <div class="label">Pendidikan Terakhir</div>
            <div class="value">
                @if($pegawai->pendidikan && $pegawai->pendidikan->last())
                    {{ $pegawai->pendidikan->last()->tingkat_pendidikan ?? ($pegawai->pendidikan->last()->tingkat ?? '-') }}
                @else
                    -
                @endif
            </div>
        </div>
    </div>

    <div class="form-container">
        <div class="row">
            <div class="label">Status Kepegawaian</div>
            <div class="value">Aktif</div>
        </div>
        
        <div class="row">
            <div class="label">Masa Kerja (PNS)</div>
            <div class="value">@if($pegawai->tmt_pns){{ \Carbon\Carbon::parse($pegawai->tmt_pns)->diffInYears(\Carbon\Carbon::now()) }} Tahun @else - @endif</div>
        </div>
        
        <div class="row">
            <div class="label">TMT PNS</div>
            <div class="value">{{ $pegawai->tmt_pns ? \Carbon\Carbon::parse($pegawai->tmt_pns)->format('d/m/Y') : '-' }}</div>
        </div>
    </div>

    <div class="footer">
        <div style="text-align: center; margin-bottom: 30px;">
            <p style="font-size: 10px; margin: 3px 0;">Demikian data pegawai ini dibuat untuk dapat dipergunakan sebagaimana mestinya.</p>
        </div>
        
        <div class="ttd-container">
            <div class="ttd-box">
                <div style="margin: 50px 0 10px;"></div>
                <p><strong>PEGAWAI YANG BERSANGKUTAN</strong></p>
                <p>{{ $pegawai->nama_lengkap }}</p>
                <p style="font-size: 9px;">NIP. {{ $pegawai->NIP }}</p>
                <div class="ttd-line"></div>
            </div>
            
            <div class="ttd-box">
                <p>{{ \Carbon\Carbon::now()->locale('id')->translatedFormat('d F Y') }}</p>
                <p><strong>MENGETAHUI</strong></p>
                <p><strong>{{ App\Models\Pengaturan::get('nama_kepala_opd', '[Nama Kepala OPD]') }}</strong></p>
                <p><strong>Kepala {{ App\Models\Pengaturan::get('nama_opd', '[Nama OPD]') }}</strong></p>
                <p><strong>NIP. {{ App\Models\Pengaturan::get('nip_kepala_opd', '[NIP Kepala OPD]') }}</strong></p>
                <div class="ttd-line"></div>
            </div>
        </div>
    </div>
</body>
</html>