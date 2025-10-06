@extends('layouts.app')

@section('title', 'Detail Perjalanan Dinas')

@section('content')
<div class="card">
    <h5 class="card-header">Detail Perjalanan Dinas</h5>
    <div class="card-body">
        <table class="table table-borderless">
            <tr>
                <td><strong>Nomor Surat Tugas</strong></td>
                <td>{{ $perjalananDinas->nomor_surat_tugas }}</td>
            </tr>
            <tr>
                <td><strong>Maksud Perjalanan</strong></td>
                <td>{{ $perjalananDinas->maksud_perjalanan }}</td>
            </tr>
            <tr>
                <td><strong>Tempat Tujuan</strong></td>
                <td>{{ $perjalananDinas->tempat_tujuan }}</td>
            </tr>
            <tr>
                <td><strong>Tanggal Berangkat</strong></td>
                <td>{{ \Carbon\Carbon::parse($perjalananDinas->tgl_berangkat)->format('d-m-Y') }}</td>
            </tr>
            <tr>
                <td><strong>Tanggal Kembali</strong></td>
                <td>{{ \Carbon\Carbon::parse($perjalananDinas->tgl_kembali)->format('d-m-Y') }}</td>
            </tr>
            <tr>
                <td><strong>Pimpinan Pemberi Tugas</strong></td>
                <td>{{ $perjalananDinas->pimpinanPemberiTugas->name ?? '-' }}</td>
            </tr>
            <tr>
                <td><strong>Pegawai yang Ikut</strong></td>
                <td>
                    <ul class="list-unstyled">
                        @foreach($perjalananDinas->pegawai as $pegawai)
                            <li>{{ $pegawai->nama_lengkap }}</li>
                        @endforeach
                    </ul>
                </td>
            </tr>
            <tr>
                <td><strong>Laporan Perjalanan Dinas</strong></td>
                <td>
                    @if($perjalananDinas->laporanPD)
                        <a href="{{ route('admin.laporan_pd.show', $perjalananDinas->laporanPD->id) }}" class="btn btn-sm btn-info">
                            Lihat Laporan
                        </a>
                    @else
                        <span class="text-muted">Belum ada laporan</span>
                    @endif
                </td>
            </tr>
        </table>
        <div class="mt-3">
            <a href="{{ route('admin.perjalanan_dinas.index') }}" class="btn btn-secondary">Kembali</a>
            <a href="{{ route('admin.perjalanan_dinas.edit', $perjalananDinas->id) }}" class="btn btn-primary">Edit</a>
        </div>
    </div>
</div>
@endsection