<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Data Cuti</title>
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
    </style>
</head>
<body>
    <div class="header">
        <h2>LAPORAN DATA CUTI</h2>
        <p>Periode: {{ $cutis->isNotEmpty() ? \Carbon\Carbon::parse($cutis->first()->tgl_pengajuan)->translatedFormat('d F Y') . ' - ' . \Carbon\Carbon::parse($cutis->last()->tgl_pengajuan)->translatedFormat('d F Y') : '-' }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Pegawai</th>
                <th>NIP</th>
                <th>Jenis Cuti</th>
                <th>Tanggal Pengajuan</th>
                <th>Tanggal Mulai</th>
                <th>Tanggal Selesai</th>
                <th>Jumlah Hari</th>
                <th>Status</th>
                <th>Pimpinan Approver</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($cutis as $index => $cuti)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $cuti->pegawai->nama_lengkap ?? '-' }}</td>
                <td>{{ $cuti->pegawai->NIP ?? '-' }}</td>
                <td>{{ $cuti->jenisCuti->nama ?? '-' }}</td>
                <td>{{ \Carbon\Carbon::parse($cuti->tgl_pengajuan)->format('d-m-Y') }}</td>
                <td>{{ \Carbon\Carbon::parse($cuti->tgl_mulai)->format('d-m-Y') }}</td>
                <td>{{ \Carbon\Carbon::parse($cuti->tgl_selesai)->format('d-m-Y') }}</td>
                <td>{{ \Carbon\Carbon::parse($cuti->tgl_mulai)->diffInDays(\Carbon\Carbon::parse($cuti->tgl_selesai)) + 1 }} hari</td>
                <td>{{ $cuti->status_persetujuan }}</td>
                <td>{{ $cuti->pimpinanApprover->name ?? '-' }}</td>
                <td>{{ $cuti->keterangan }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    @if($cutis->isEmpty())
    <p style="text-align: center;">Tidak ada data yang ditemukan.</p>
    @endif

    <style>
        .ttd-container {
            display: flex;
            justify-content: flex-end;
            margin-top: 50px;
        }
        .ttd-box {
            text-align: center;
            width: 300px;
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
    <div class="footer">
        <div class="ttd-container">
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
