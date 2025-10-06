@extends('layouts.app')

@section('title', 'Detail Riwayat Jabatan')

@section('content')
<div class="card">
    <h5 class="card-header">Detail Riwayat Jabatan</h5>
    <div class="card-body">
        <table class="table table-borderless">
            <tr>
                <td><strong>Nama Pegawai</strong></td>
                <td>{{ $riwayatJabatan->pegawai->nama_lengkap ?? '-' }}</td>
            </tr>
            <tr>
                <td><strong>Jabatan</strong></td>
                <td>{{ $riwayatJabatan->jabatan->nama ?? '-' }}</td>
            </tr>
            <tr>
                <td><strong>Unit Kerja</strong></td>
                <td>{{ $riwayatJabatan->unitKerja->nama ?? '-' }}</td>
            </tr>
            <tr>
                <td><strong>Jenis Jabatan</strong></td>
                <td>{{ $riwayatJabatan->jenis_jabatan }}</td>
            </tr>
            <tr>
                <td><strong>Nomor SK</strong></td>
                <td>{{ $riwayatJabatan->nomor_sk }}</td>
            </tr>
            <tr>
                <td><strong>Tanggal SK</strong></td>
                <td>{{ \Carbon\Carbon::parse($riwayatJabatan->tanggal_sk)->format('d-m-Y') }}</td>
            </tr>
            <tr>
                <td><strong>TMT Jabatan</strong></td>
                <td>{{ \Carbon\Carbon::parse($riwayatJabatan->tmt_jabatan)->format('d-m-Y') }}</td>
            </tr>
            @if($riwayatJabatan->file_sk)
            <tr>
                <td><strong>File SK</strong></td>
                <td>
                    @if(pathinfo($riwayatJabatan->file_sk, PATHINFO_EXTENSION) === 'pdf')
                        <a href="{{ asset('storage/' . $riwayatJabatan->file_sk) }}" target="_blank" class="btn btn-sm btn-info">
                            Lihat File PDF
                        </a>
                    @else
                        <img src="{{ asset('storage/' . $riwayatJabatan->file_sk) }}" alt="File SK" width="200" class="img-thumbnail">
                    @endif
                </td>
            </tr>
            @endif
        </table>
        <div class="mt-3">
            <a href="{{ route('admin.riwayat_jabatan.index') }}" class="btn btn-secondary">Kembali</a>
            <a href="{{ route('admin.riwayat_jabatan.edit', $riwayatJabatan->id) }}" class="btn btn-primary">Edit</a>
        </div>
    </div>
</div>
@endsection