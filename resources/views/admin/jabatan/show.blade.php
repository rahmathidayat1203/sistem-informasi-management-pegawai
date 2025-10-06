@extends('layouts.app')

@section('title', 'Detail Jabatan')

@section('content')
<div class="card">
    <h5 class="card-header">Detail Jabatan</h5>
    <div class="card-body">
        <table class="table table-borderless">
            <tr>
                <td><strong>Nama Jabatan</strong></td>
                <td>{{ $jabatan->nama }}</td>
            </tr>
            <tr>
                <td><strong>Deskripsi</strong></td>
                <td>{{ $jabatan->deskripsi }}</td>
            </tr>
        </table>
        <div class="mt-3">
            <a href="{{ route('admin.jabatan.index') }}" class="btn btn-secondary">Kembali</a>
            <a href="{{ route('admin.jabatan.edit', $jabatan->id) }}" class="btn btn-primary">Edit</a>
        </div>
    </div>
</div>
@endsection