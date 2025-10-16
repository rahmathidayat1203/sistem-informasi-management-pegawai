<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Data Pegawai</title>
    <style>
        @page {
            margin: 30px;
            size: A4 landscape;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            margin: 0;
            padding: 0;
        }
        .kop {
            text-align: center;
            margin-bottom: 20px;
        }
        .kop h1 {
            font-size: 16px;
            font-weight: bold;
            margin: 0;
            text-transform: uppercase;
        }
        .kop h2 {
            font-size: 12px;
            font-weight: normal;
            margin: 5px 0;
        }
        .kop hr {
            border: 1px solid black;
            margin: 10px 0;
        }
        .judul {
            text-align: center;
            margin-bottom: 20px;
        }
        .judul h3 {
            font-size: 14px;
            font-weight: bold;
            margin: 0;
        }
        .judul p {
            font-size: 11px;
            margin: 5px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 10px;
        }
        th, td {
            border: 1px solid #000;
            padding: 4px 6px;
            text-align: left;
            vertical-align: top;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: center;
            vertical-align: middle;
        }
        .text-center {
            text-align: center;
        }
        .footer {
            margin-top: 50px;
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
            margin: 5px 0;
        }
        .ttd-line {
            border-bottom: 1px solid #000;
            height: 30px;
            margin: 10px 0;
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
        <h3>LAPORAN DATA KEPEGAWAIAN</h3>
        <p>Tahun {{ date('Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 4%;">NO</th>
                <th style="width: 10%;">NIP</th>
                <th style="width: 18%;">NAMA PEGAWAI</th>
                <th style="width: 6%;">GOL.</th>
                <th style="width: 15%;">JABATAN</th>
                <th style="width: 12%;">UNIT KERJA</th>
                <th style="width: 8%;">TEMPAT/TGL LAHIR</th>
                <th style="width: 6%;">JK</th>
                <th style="width: 6%;">PENDIDIKAN</th>
                <th style="width: 10%;">ALAMAT</th>
                <th style="width: 5%;">TELEPON</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pegawais as $index => $pegawai)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $pegawai->NIP }}</td>
                <td>{{ $pegawai->nama_lengkap }}</td>
                <td>{{ $pegawai->golongan->nama ?? '-' }}</td>
                <td>{{ $pegawai->jabatan->nama ?? '-' }}</td>
                <td>{{ $pegawai->unitKerja->nama_unit ?? '-' }}</td>
                <td>{{ $pegawai->tempat_lahir }}, {{ \Carbon\Carbon::parse($pegawai->tgl_lahir)->format('d/m/Y') }}</td>
                <td class="text-center">{{ $pegawai->jenis_kelamin == 'L' ? 'L' : 'P' }}</td>
                <td>{{ $pegawai->pendidikan && $pegawai->pendidikan->first() ? $pegawai->pendidikan->first()->tingkat_pendidikan ?? $pegawai->pendidikan->first()->tingkat ?? '-' : '-' }}</td>
                <td>{{ $pegawai->alamat }}</td>
                <td>{{ $pegawai->no_telp }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="11" class="text-center"><strong>JUMLAH TOTAL: {{ $pegawais->count() }} ORANG</strong></td>
            </tr>
        </tfoot>
    </table>

    @if($pegawais->isEmpty())
    <p class="text-center">Tidak ada data yang ditemukan.</p>
    @endif

    <div class="footer">
        <div class="ttd-container">
            <div class="ttd-box">
                <div style="margin: 50px 0 10px;"></div>
                <p><strong>MENYETUJUI</strong></p>
                <p><strong>[Nama Pejabat Atasan]</strong></p>
                <p><strong>[Jabatan Pejabat Atasan]</strong></p>
                <p style="font-size: 9px;"><strong>NIP. [NIP Pejabat Atasan]</strong></p>
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
