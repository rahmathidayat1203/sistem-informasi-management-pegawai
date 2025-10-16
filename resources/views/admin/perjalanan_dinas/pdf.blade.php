<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Data Perjalanan Dinas</title>
    <style>
        @page {
            margin: 25px;
            size: A4 landscape;
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
        .judul p {
            font-size: 10px;
            margin: 2px 0;
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
        .pegawai-list {
            font-size: 9px;
            line-height: 1.1;
        }
        .pegawai-item {
            margin-bottom: 2px;
            padding: 2px 0;
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
        <h3>LAPORAN REKAP PERJALANAN DINAS</h3>
        <p>Periode: {{ $perjalananDinas->isNotEmpty() ? \Carbon\Carbon::parse($perjalananDinas->first()->tgl_berangkat)->format('d F Y') . ' - ' . \Carbon\Carbon::parse($perjalananDinas->last()->tgl_berangkat)->format('d F Y') : 'Semua Data' }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 4%;">NO</th>
                <th style="width: 12%;">NO. SURAT TUGAS</th>
                <th style="width: 12%;">TEMPAT TUJUAN</th>
                <th style="width: 20%;">MAKSUD PERJALANAN</th>
                <th style="width: 18%;">PEGAWAI YANG DITUGASKAN</th>
                <th style="width: 10%;">PIMPINAN</th>
                <th style="width: 8%;">TGL. BERANGKAT</th>
                <th style="width: 8%;">TGL. KEMBALI</th>
                <th style="width: 8%;">LAMA (HARI)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($perjalananDinas as $index => $pd)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $pd->nomor_surat_tugas }}</td>
                <td>{{ $pd->tempat_tujuan }}</td>
                <td>{{ $pd->maksud_perjalanan }}</td>
                <td class="pegawai-list">
                    @if($pd->pegawai->isNotEmpty())
                        @foreach($pd->pegawai as $pegawai)
                            <div class="pegawai-item">
                                <strong>{{ $pegawai->nama_lengkap }}</strong><br>
                                NIP: {{ $pegawai->NIP }}<br>
                                {{ $pegawai->unitKerja->nama_unit ?? '-' }}
                            </div>
                        @endforeach
                    @else
                        -
                    @endif
                </td>
                <td>{{ $pd->pimpinanPemberiTugas->name ?? '-' }}</td>
                <td class="text-center">{{ \Carbon\Carbon::parse($pd->tgl_berangkat)->format('d/m/Y') }}</td>
                <td class="text-center">{{ \Carbon\Carbon::parse($pd->tgl_kembali)->format('d/m/Y') }}</td>
                <td class="text-center">{{ \Carbon\Carbon::parse($pd->tgl_berangkat)->diffInDays(\Carbon\Carbon::parse($pd->tgl_kembali)) + 1 }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="9" class="text-center"><strong>JUMLAH TOTAL: {{ $perjalananDinas->count() }} PERJALANAN DINAS</strong></td>
            </tr>
        </tfoot>
    </table>

    @if($perjalananDinas->isEmpty())
    <p class="text-center">Tidak ada data yang ditemukan.</p>
    @endif

    <div class="footer">
        <div style="text-align: center; margin-bottom: 30px;">
            <p style="font-size: 10px; margin: 3px 0;">Demikian laporan rekap perjalanan dinas ini dibuat untuk dapat dipergunakan sebagaimana mestinya.</p>
        </div>
        
        <div class="ttd-container">
            <div class="ttd-box">
                <p>&nbsp;</p>
                <p>&nbsp;</p>
                <p><strong>MENYETUJUI</strong></p>
                <p><strong>[Nama Pejabat Atasan]</strong></p>
                <p><strong>[Jabatan Pejabat Atasan]</strong></p>
                <p><strong>NIP. [NIP Pejabat Atasan]</strong></p>
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
