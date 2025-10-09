@extends('layouts.app')

@section('title', 'Detail Cuti')

@section('content')
<div class="card">
    <h5 class="card-header">Detail Data Cuti</h5>
    <div class="card-body">
        <table class="table table-borderless">
            <tr>
                <td><strong>Nama Pegawai</strong></td>
                <td>{{ $cuti->pegawai->nama_lengkap ?? '-' }}</td>
            </tr>
            <tr>
                <td><strong>Jenis Cuti</strong></td>
                <td>{{ $cuti->jenisCuti->nama ?? '-' }}</td>
            </tr>
            <tr>
                <td><strong>Tanggal Pengajuan</strong></td>
                <td>{{ \Carbon\Carbon::parse($cuti->tgl_pengajuan)->format('d-m-Y') }}</td>
            </tr>
            <tr>
                <td><strong>Tanggal Mulai</strong></td>
                <td>{{ \Carbon\Carbon::parse($cuti->tgl_mulai)->format('d-m-Y') }}</td>
            </tr>
            <tr>
                <td><strong>Tanggal Selesai</strong></td>
                <td>{{ \Carbon\Carbon::parse($cuti->tgl_selesai)->format('d-m-Y') }}</td>
            </tr>
            <tr>
                <td><strong>Status Persetujuan</strong></td>
                <td>
                    @if($cuti->status_persetujuan == 'Diajukan')
                        <span class="badge bg-warning">Diajukan</span>
                    @elseif($cuti->status_persetujuan == 'Disetujui')
                        <span class="badge bg-success">Disetujui</span>
                    @elseif($cuti->status_persetujuan == 'Ditolak')
                        <span class="badge bg-danger">Ditolak</span>
                    @endif
                </td>
            </tr>
            <tr>
                <td><strong>Pimpinan Approver</strong></td>
                <td>{{ $cuti->pimpinanApprover->name ?? '-' }}</td>
            </tr>
            <tr>
                <td><strong>Keterangan</strong></td>
                <td>{{ $cuti->keterangan }}</td>
            </tr>
            @if(!empty($cuti->alokasi_sisa_cuti))
            <tr>
                <td><strong>Pengurangan Sisa Cuti</strong></td>
                <td>
                    <ul class="mb-0">
                        @foreach($cuti->alokasi_sisa_cuti as $tahun => $hari)
                            <li>{{ $tahun }}: {{ $hari }} hari</li>
                        @endforeach
                    </ul>
                </td>
            </tr>
            @endif
            @if($cuti->dokumen_pendukung)
            <tr>
                <td><strong>Dokumen Pendukung</strong></td>
                <td>
                    @if(pathinfo($cuti->dokumen_pendukung, PATHINFO_EXTENSION) === 'pdf')
                        <a href="{{ asset('storage/' . $cuti->dokumen_pendukung) }}" target="_blank" class="btn btn-sm btn-info">
                            Lihat Dokumen PDF
                        </a>
                    @else
                        <img src="{{ asset('storage/' . $cuti->dokumen_pendukung) }}" alt="Dokumen Pendukung" width="200" class="img-thumbnail">
                    @endif
                </td>
            </tr>
            @endif
        </table>
        <div class="mt-3">
            <a href="{{ route('admin.cuti.index') }}" class="btn btn-secondary">Kembali</a>
            <a href="{{ route('admin.cuti.edit', $cuti->id) }}" class="btn btn-primary">Edit</a>
        </div>
    </div>
</div>
@endsection