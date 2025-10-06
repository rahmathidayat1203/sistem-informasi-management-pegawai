@extends('layouts.app')

@section('title', 'Tambah Jenis Cuti')

@section('content')
<div class="card">
    <h5 class="card-header">Tambah Jenis Cuti</h5>
    <div class="card-body">
        <form action="{{ route('admin.jenis_cuti.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="nama" class="form-label">Nama Jenis Cuti</label>
                <input type="text" class="form-control @error('nama') is-invalid @enderror" id="nama" name="nama" value="{{ old('nama') }}" required>
                @error('nama')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ route('admin.jenis_cuti.index') }}" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>
@endsection