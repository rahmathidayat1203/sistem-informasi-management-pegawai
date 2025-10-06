@extends('layouts.app')

@section('title', 'Detail Pendidikan')

@section('content')
<div class="card">
    <h5 class="card-header">Detail Data Pendidikan</h5>
    <div class="card-body">
        <table class="table table-borderless">
            <tr>
                <td><strong>Nama Pegawai</strong></td>
                <td>{{ $pendidikan->pegawai->nama_lengkap ?? '-' }}</td>
            </tr>
            <tr>
                <td><strong>Jenjang Pendidikan</strong></td>
                <td>{{ $pendidikan->jenjang }}</td>
            </tr>
            <tr>
                <td><strong>Nama Institusi</strong></td>
                <td>{{ $pendidikan->nama_institusi }}</td>
            </tr>
            <tr>
                <td><strong>Jurusan</strong></td>
                <td>{{ $pendidikan->jurusan ?? '-' }}</td>
            </tr>
            <tr>
                <td><strong>Tahun Lulus</strong></td>
                <td>{{ $pendidikan->tahun_lulus }}</td>
            </tr>
            <tr>
                <td><strong>Nomor Ijazah</strong></td>
                <td>{{ $pendidikan->nomor_ijazah ?? '-' }}</td>
            </tr>
            @if($pendidikan->file_ijazah)
            <tr>
                <td><strong>File Ijazah</strong></td>
                <td>
                    @if(pathinfo($pendidikan->file_ijazah, PATHINFO_EXTENSION) === 'pdf')
                        <a href="{{ asset('storage/' . $pendidikan->file_ijazah) }}" target="_blank" class="btn btn-sm btn-info">
                            Lihat File PDF
                        </a>
                    @else
                        <img src="{{ asset('storage/' . $pendidikan->file_ijazah) }}" alt="File Ijazah" width="200" class="img-thumbnail">
                    @endif
                </td>
            </tr>
            @endif
        </table>
        <div class="mt-3">
            <a href="{{ route('admin.pendidikan.index') }}" class="btn btn-secondary">Kembali</a>
            <a href="{{ route('admin.pendidikan.edit', $pendidikan->id) }}" class="btn btn-primary">Edit</a>
        </div>
    </div>
</div>
@endsection