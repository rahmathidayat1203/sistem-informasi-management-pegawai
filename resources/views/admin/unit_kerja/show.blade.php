@extends('layouts.app')

@section('title', 'Detail Unit Kerja')

@section('content')
<div class="card">
    <h5 class="card-header">Detail Unit Kerja</h5>
    <div class="card-body">
        <table class="table table-borderless">
            <tr>
                <td><strong>Nama Unit Kerja</strong></td>
                <td>{{ $unitKerja->nama }}</td>
            </tr>
            <tr>
                <td><strong>Deskripsi</strong></td>
                <td>{{ $unitKerja->deskripsi }}</td>
            </tr>
        </table>
        <div class="mt-3">
            <a href="{{ route('admin.unit_kerja.index') }}" class="btn btn-secondary">Kembali</a>
            <a href="{{ route('admin.unit_kerja.edit', $unitKerja->id) }}" class="btn btn-primary">Edit</a>
        </div>
    </div>
</div>
@endsection