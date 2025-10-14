<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Data Sisa Cuti</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h2 {
            margin-bottom: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .footer {
            margin-top: 50px;
            text-align: right;
        }
        .tanggal {
            text-align: center;
            margin-bottom: 20px;
        }
        .text-center {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>LAPORAN DATA SISA CUTI</h2>
        <p>Tahun: {{ $sisaCutis->isNotEmpty() ? $sisaCutis->pluck('tahun')->unique()->sort()->implode(', ') : '-' }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Pegawai</th>
                <th>NIP</th>
                <th>Unit Kerja</th>
                <th>Tahun</th>
                <th>Jatah Cuti</th>
                <th>Sisa Cuti</th>
                <th>Cuti Terpakai</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sisaCutis as $index => $sisaCuti)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $sisaCuti->pegawai->nama_lengkap ?? '-' }}</td>
                <td>{{ $sisaCuti->pegawai->NIP ?? '-' }}</td>
                <td>{{ $sisaCuti->pegawai->unitKerja->nama_unit ?? '-' }}</td>
                <td class="text-center">{{ $sisaCuti->tahun }}</td>
                <td class="text-center">{{ number_format($sisaCuti->jatah_cuti) }} hari</td>
                <td class="text-center">{{ number_format($sisaCuti->sisa_cuti) }} hari</td>
                <td class="text-center">{{ number_format($sisaCuti->jatah_cuti - $sisaCuti->sisa_cuti) }} hari</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            @if($sisaCutis->isNotEmpty())
            <tr style="font-weight: bold;">
                <td colspan="5" class="text-center">TOTAL</td>
                <td class="text-center">{{ number_format($sisaCutis->sum('jatah_cuti')) }} hari</td>
                <td class="text-center">{{ number_format($sisaCutis->sum('sisa_cuti')) }} hari</td>
                <td class="text-center">{{ number_format($sisaCutis->sum('jatah_cuti') - $sisaCutis->sum('sisa_cuti')) }} hari</td>
            </tr>
            @endif
        </tfoot>
    </table>

    @if($sisaCutis->isEmpty())
    <p class="text-center">Tidak ada data yang ditemukan.</p>
    @endif

    <div class="footer">
        <div class="tanggal">
            <p>{{ \Carbon\Carbon::now()->locale('id')->translatedFormat('d F Y') }}</p>
        </div>
        <p>Mengetahui,</p>
        <br><br><br>
        <p>(_________________________)</p>
    </div>
</body>
</html>
