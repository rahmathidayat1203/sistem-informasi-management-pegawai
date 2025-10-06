@extends('layouts.app')

@section('title', 'Detail Keluarga')

@section('content')
<div class="card">
    <h5 class="card-header">Detail Data Keluarga</h5>
    <div class="card-body">
        <table class="table table-borderless">
            <tr>
                <td><strong>Nama Pegawai</strong></td>
                <td>{{ $keluarga->pegawai->nama_lengkap ?? '-' }}</td>
            </tr>
            <tr>
                <td><strong>Nama Lengkap</strong></td>
                <td>{{ $keluarga->nama_lengkap }}</td>
            </tr>
            <tr>
                <td><strong>Hubungan</strong></td>
                <td>{{ $keluarga->hubungan }}</td>
            </tr>
            <tr>
                <td><strong>Jenis Kelamin</strong></td>
                <td>{{ $keluarga->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
            </tr>
            <tr>
                <td><strong>NIK</strong></td>
                <td>{{ $keluarga->nik ?? '-' }}</td>
            </tr>
            <tr>
                <td><strong>Tempat Lahir</strong></td>
                <td>{{ $keluarga->tempat_lahir ?? '-' }}</td>
            </tr>
            <tr>
                <td><strong>Tanggal Lahir</strong></td>
                <td>{{ $keluarga->tgl_lahir ? \Carbon\Carbon::parse($keluarga->tgl_lahir)->format('d-m-Y') : '-' }}</td>
            </tr>
            <tr>
                <td><strong>Pekerjaan</strong></td>
                <td>{{ $keluarga->pekerjaan ?? '-' }}</td>
            </tr>
        </table>
        <div class="mt-3">
            <a href="{{ route('admin.keluarga.index') }}" class="btn btn-secondary">Kembali</a>
            <a href="{{ route('admin.keluarga.edit', $keluarga->id) }}" class="btn btn-primary">Edit</a>
        </div>
    </div>
</div>
@endsection