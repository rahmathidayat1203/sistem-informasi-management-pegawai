@extends('layouts.app')

@section('title', 'Detail Pegawai')

@section('content')
<div class="card">
    <h5 class="card-header">Detail Pegawai</h5>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                @if($pegawai->foto_profil)
                    <img src="{{ asset('storage/' . $pegawai->foto_profil) }}" alt="Foto Profil" class="img-fluid rounded">
                @else
                    <div class="bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                        <span class="text-muted">Tidak ada foto</span>
                    </div>
                @endif
            </div>
            <div class="col-md-8">
                <table class="table table-borderless">
                    <tr>
                        <td><strong>NIP</strong></td>
                        <td>{{ $pegawai->NIP }}</td>
                    </tr>
                    <tr>
                        <td><strong>Nama Lengkap</strong></td>
                        <td>{{ $pegawai->nama_lengkap }}</td>
                    </tr>
                    <tr>
                        <td><strong>Tempat, Tanggal Lahir</strong></td>
                        <td>{{ $pegawai->tempat_lahir }}, {{ \Carbon\Carbon::parse($pegawai->tgl_lahir)->format('d-m-Y') }}</td>
                    </tr>
                    <tr>
                        <td><strong>Jenis Kelamin</strong></td>
                        <td>{{ $pegawai->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Agama</strong></td>
                        <td>{{ $pegawai->agama }}</td>
                    </tr>
                    <tr>
                        <td><strong>Alamat</strong></td>
                        <td>{{ $pegawai->alamat }}</td>
                    </tr>
                    <tr>
                        <td><strong>No. Telepon</strong></td>
                        <td>{{ $pegawai->no_telp }}</td>
                    </tr>
                    <tr>
                        <td><strong>Jabatan</strong></td>
                        <td>{{ $pegawai->jabatan->nama ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Golongan</strong></td>
                        <td>{{ $pegawai->golongan->nama ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Unit Kerja</strong></td>
                        <td>{{ $pegawai->unitKerja->nama ?? '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="mt-3">
            <a href="{{ route('admin.pegawai.index') }}" class="btn btn-secondary">Kembali</a>
            <a href="{{ route('admin.pegawai.edit', $pegawai->id) }}" class="btn btn-primary">Edit</a>
        </div>
    </div>
</div>
@endsection