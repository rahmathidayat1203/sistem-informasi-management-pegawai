@extends('layouts.app')

@section('title', 'Detail Laporan Perjalanan Dinas')

@section('content')
<div class="card">
    <h5 class="card-header">Detail Laporan Perjalanan Dinas</h5>
    <div class="card-body">
        <table class="table table-borderless">
            <tr>
                <td><strong>Nomor Surat Tugas</strong></td>
                <td>{{ $laporanPD->perjalananDinas->nomor_surat_tugas ?? '-' }}</td>
            </tr>
            <tr>
                <td><strong>Maksud Perjalanan</strong></td>
                <td>{{ $laporanPD->perjalananDinas->maksud_perjalanan ?? '-' }}</td>
            </tr>
            <tr>
                <td><strong>Tempat Tujuan</strong></td>
                <td>{{ $laporanPD->perjalananDinas->tempat_tujuan ?? '-' }}</td>
            </tr>
            <tr>
                <td><strong>Tanggal Upload</strong></td>
                <td>{{ $laporanPD->tgl_unggah ? \Carbon\Carbon::parse($laporanPD->tgl_unggah)->format('d-m-Y H:i') : '-' }}</td>
            </tr>
            <tr>
                <td><strong>Status Verifikasi</strong></td>
                <td>
                    @if($laporanPD->status_verifikasi == 'Belum Diverifikasi')
                        <span class="badge bg-warning">Belum Diverifikasi</span>
                    @elseif($laporanPD->status_verifikasi == 'Disetujui')
                        <span class="badge bg-success">Disetujui</span>
                    @elseif($laporanPD->status_verifikasi == 'Perbaikan')
                        <span class="badge bg-danger">Perbaikan</span>
                    @endif
                </td>
            </tr>
            <tr>
                <td><strong>Admin Keuangan Verifier</strong></td>
                <td>{{ $laporanPD->adminKeuanganVerifier->name ?? '-' }}</td>
            </tr>
            <tr>
                <td><strong>Catatan Verifikasi</strong></td>
                <td>{{ $laporanPD->catatan_verifikasi ?? '-' }}</td>
            </tr>
            <tr>
                <td><strong>File Laporan</strong></td>
                <td>
                    @if($laporanPD->file_laporan)
                        @if(pathinfo($laporanPD->file_laporan, PATHINFO_EXTENSION) === 'pdf')
                            <a href="{{ asset('storage/' . $laporanPD->file_laporan) }}" target="_blank" class="btn btn-sm btn-info">
                                Lihat File PDF
                            </a>
                        @else
                            <img src="{{ asset('storage/' . $laporanPD->file_laporan) }}" alt="File Laporan" width="200" class="img-thumbnail">
                        @endif
                    @else
                        <span class="text-muted">Tidak ada file</span>
                    @endif
                </td>
            </tr>
        </table>
        <div class="mt-3">
            <a href="{{ route('admin.laporan_pd.index') }}" class="btn btn-secondary">Kembali</a>
            <a href="{{ route('admin.laporan_pd.edit', $laporanPD->id) }}" class="btn btn-primary">Edit</a>
        </div>
    </div>
</div>
@endsection