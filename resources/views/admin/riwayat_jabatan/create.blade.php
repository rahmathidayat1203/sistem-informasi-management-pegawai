@extends('layouts.app')

@section('title', 'Tambah Riwayat Jabatan')

@section('content')
<div class="card">
    <h5 class="card-header">Tambah Riwayat Jabatan</h5>
    <div class="card-body">
        <form action="{{ route('admin.riwayat_jabatan.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-md-4">
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
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="jabatan_id" class="form-label">Jabatan</label>
                        <select class="form-control @error('jabatan_id') is-invalid @enderror" id="jabatan_id" name="jabatan_id" required>
                            <option value="">Pilih Jabatan</option>
                            @foreach($jabatans as $jabatan)
                                <option value="{{ $jabatan->id }}" {{ old('jabatan_id') == $jabatan->id ? 'selected' : '' }}>{{ $jabatan->nama }}</option>
                            @endforeach
                        </select>
                        @error('jabatan_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="unit_kerja_id" class="form-label">Unit Kerja</label>
                        <select class="form-control @error('unit_kerja_id') is-invalid @enderror" id="unit_kerja_id" name="unit_kerja_id" required>
                            <option value="">Pilih Unit Kerja</option>
                            @foreach($unitKerjas as $unitKerja)
                                <option value="{{ $unitKerja->id }}" {{ old('unit_kerja_id') == $unitKerja->id ? 'selected' : '' }}>{{ $unitKerja->nama }}</option>
                            @endforeach
                        </select>
                        @error('unit_kerja_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="jenis_jabatan" class="form-label">Jenis Jabatan</label>
                        <select class="form-control @error('jenis_jabatan') is-invalid @enderror" id="jenis_jabatan" name="jenis_jabatan" required>
                            <option value="">Pilih Jenis Jabatan</option>
                            <option value="Struktural" {{ old('jenis_jabatan') == 'Struktural' ? 'selected' : '' }}>Struktural</option>
                            <option value="Fungsional Tertentu" {{ old('jenis_jabatan') == 'Fungsional Tertentu' ? 'selected' : '' }}>Fungsional Tertentu</option>
                            <option value="Fungsional Umum" {{ old('jenis_jabatan') == 'Fungsional Umum' ? 'selected' : '' }}>Fungsional Umum</option>
                        </select>
                        @error('jenis_jabatan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="nomor_sk" class="form-label">Nomor SK</label>
                        <input type="text" class="form-control @error('nomor_sk') is-invalid @enderror" id="nomor_sk" name="nomor_sk" value="{{ old('nomor_sk') }}" required>
                        @error('nomor_sk')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
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
                        <label for="tmt_jabatan" class="form-label">TMT Jabatan</label>
                        <input type="date" class="form-control @error('tmt_jabatan') is-invalid @enderror" id="tmt_jabatan" name="tmt_jabatan" value="{{ old('tmt_jabatan') }}" required>
                        @error('tmt_jabatan')
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
            <a href="{{ route('admin.riwayat_jabatan.index') }}" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>
@endsection