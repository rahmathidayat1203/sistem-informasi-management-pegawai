@extends('layouts.app')

@section('title', 'Detail Golongan')

@section('content')
<div class="card">
    <h5 class="card-header">Detail Golongan</h5>
    <div class="card-body">
        <table class="table table-borderless">
            <tr>
                <td><strong>Nama Golongan</strong></td>
                <td>{{ $golongan->nama }}</td>
            </tr>
            <tr>
                <td><strong>Deskripsi</strong></td>
                <td>{{ $golongan->deskripsi }}</td>
            </tr>
        </table>
        <div class="mt-3">
            <a href="{{ route('admin.golongan.index') }}" class="btn btn-secondary">Kembali</a>
            <a href="{{ route('admin.golongan.edit', $golongan->id) }}" class="btn btn-primary">Edit</a>
        </div>
    </div>
</div>
@endsection