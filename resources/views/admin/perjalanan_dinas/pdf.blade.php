<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Data Perjalanan Dinas</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
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
            padding: 6px;
            text-align: left;
            vertical-align: top;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: center;
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
        .pegawai-list {
            font-size: 10px;
            line-height: 1.2;
        }
        .pegawai-item {
            margin-bottom: 2px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>LAPORAN DATA PERJALANAN DINAS</h2>
        <p>Periode: {{ $perjalananDinas->isNotEmpty() ? $perjalananDinas->first()->tgl_berangkat->format('d F Y') . ' - ' . $perjalananDinas->last()->tgl_berangkat->format('d F Y') : '-' }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 12%;">Nomor Surat Tugas</th>
                <th style="width: 10%;">Tempat Tujuan</th>
                <th style="width: 25%;">Maksud Perjalanan</th>
                <th style="width: 15%;">Pegawai Ditugaskan</th>
                <th style="width: 10%;">Pimpinan</th>
                <th style="width: 8%;">Tgl Berangkat</th>
                <th style="width: 8%;">Tgl Kembali</th>
                <th style="width: 7%;">Lama (Hari)</th>
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
                                {{ $pegawai->nama_lengkap }}<br>
                                <small>{{ $pegawai->NIP }}</small><br>
                                <small>{{ $pegawai->unitKerja->nama_unit ?? '-' }}</small>
                            </div>
                        @endforeach
                    @else
                        -
                    @endif
                </td>
                <td>{{ $pd->pimpinanPemberiTugas->name ?? '-' }}</td>
                <td class="text-center">{{ $pd->tgl_berangkat->format('d-m-Y') }}</td>
                <td class="text-center">{{ $pd->tgl_kembali->format('d-m-Y') }}</td>
                <td class="text-center">{{ \Carbon\Carbon::parse($pd->tgl_berangkat)->diffInDays(\Carbon\Carbon::parse($pd->tgl_kembali)) + 1 }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    @if($perjalananDinas->isEmpty())
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
