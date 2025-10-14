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
        <p>Periode: {{ $cutis->isNotEmpty() ? $cutis->first()->tgl_pengajuan->format('d F Y') . ' - ' . $cutis->last()->tgl_pengajuan->format('d F Y') : '-' }}</p>
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
                <td>{{ $cuti->tgl_pengajuan->format('d-m-Y') }}</td>
                <td>{{ $cuti->tgl_mulai->format('d-m-Y') }}</td>
                <td>{{ $cuti->tgl_selesai->format('d-m-Y') }}</td>
                <td>{{ Carbon\Carbon::parse($cuti->tgl_mulai)->diffInDays(Carbon\Carbon::parse($cuti->tgl_selesai)) + 1 }} hari</td>
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
