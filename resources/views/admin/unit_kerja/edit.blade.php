@extends('layouts.app')

@section('title', 'Edit Unit Kerja')

@section('content')
<div class="card">
    <h5 class="card-header">Edit Unit Kerja</h5>
    <div class="card-body">
        <form action="{{ route('admin.unit_kerja.update', $unitKerja->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label for="nama" class="form-label">Nama Unit Kerja</label>
                <input type="text" class="form-control @error('nama') is-invalid @enderror" id="nama" name="nama" value="{{ old('nama', $unitKerja->nama) }}" required>
                @error('nama')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="deskripsi" class="form-label">Deskripsi</label>
                <textarea class="form-control @error('deskripsi') is-invalid @enderror" id="deskripsi" name="deskripsi" rows="3">{{ old('deskripsi', $unitKerja->deskripsi) }}</textarea>
                @error('deskripsi')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="{{ route('admin.unit_kerja.index') }}" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>
@endsection