@extends('layouts.app')

@section('title', 'Tambah Riwayat Pangkat')

@section('content')
<div class="card">
    <h5 class="card-header">Tambah Riwayat Pangkat</h5>
    <div class="card-body">
        <form action="{{ route('admin.riwayat_pangkat.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="pegawai_id" class="form-label">Nama Pegawai</label>
                        <select class="form-control @error('pegawai_id') is-invalid @enderror" id="pegawai_id" name="pegawai_id" required>
                            <option value="">Pilih Pegawai</option>
                            @foreach($pegawais as $pegawai)
                                <option value="{{ $pegawai->id }}" {{ old('pegawai_id') == $pegawai->id ? 'selected' : '' }}>{{ $pegawai->nama_lengkap }}</option>
                            @endforeach
                        </select>
                        @error('pegawai_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="golongan_id" class="form-label">Golongan</label>
                        <select class="form-control @error('golongan_id') is-invalid @enderror" id="golongan_id" name="golongan_id" required>
                            <option value="">Pilih Golongan</option>
                            @foreach($golongans as $golongan)
                                <option value="{{ $golongan->id }}" {{ old('golongan_id') == $golongan->id ? 'selected' : '' }}>{{ $golongan->nama }}</option>
                            @endforeach
                        </select>
                        @error('golongan_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="nomor_sk" class="form-label">Nomor SK</label>
                <input type="text" class="form-control @error('nomor_sk') is-invalid @enderror" id="nomor_sk" name="nomor_sk" value="{{ old('nomor_sk') }}" required>
                @error('nomor_sk')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="tanggal_sk" class="form-label">Tanggal SK</label>
                        <input type="date" class="form-control @error('tanggal_sk') is-invalid @enderror" id="tanggal_sk" name="tanggal_sk" value="{{ old('tanggal_sk') }}" required>
                        @error('tanggal_sk')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="tmt_pangkat" class="form-label">TMT Pangkat</label>
                        <input type="date" class="form-control @error('tmt_pangkat') is-invalid @enderror" id="tmt_pangkat" name="tmt_pangkat" value="{{ old('tmt_pangkat') }}" required>
                        @error('tmt_pangkat')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="file_sk" class="form-label">File SK</label>
                <input type="file" class="form-control @error('file_sk') is-invalid @enderror" id="file_sk" name="file_sk" accept=".pdf,.jpg,.jpeg,.png">
                @error('file_sk')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="form-text text-muted">Format: pdf, jpg, jpeg, png. Maksimal: 5MB</small>
            </div>
            
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ route('admin.riwayat_pangkat.index') }}" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>
@endsection