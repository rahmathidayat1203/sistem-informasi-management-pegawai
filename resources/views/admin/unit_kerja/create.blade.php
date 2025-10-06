@extends('layouts.app')

@section('title', 'Tambah Unit Kerja')

@section('content')
<div class="card">
    <h5 class="card-header">Tambah Unit Kerja</h5>
    <div class="card-body">
        <form action="{{ route('admin.unit_kerja.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="nama" class="form-label">Nama Unit Kerja</label>
                <input type="text" class="form-control @error('nama') is-invalid @enderror" id="nama" name="nama" value="{{ old('nama') }}" required>
                @error('nama')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="deskripsi" class="form-label">Deskripsi</label>
                <textarea class="form-control @error('deskripsi') is-invalid @enderror" id="deskripsi" name="deskripsi" rows="3">{{ old('deskripsi') }}</textarea>
                @error('deskripsi')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ route('admin.unit_kerja.index') }}" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>
@endsection