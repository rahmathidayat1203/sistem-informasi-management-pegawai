@extends('layouts.app')

@section('title', 'Tambah Sisa Cuti')

@section('content')
<div class="card">
    <h5 class="card-header">Tambah Data Sisa Cuti</h5>
    <div class="card-body">
        <form action="{{ route('admin.sisa_cuti.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="pegawai_id" class="form-label">Nama Pegawai</label>
                        <select class="form-control @error('pegawai_id') is-invalid @enderror" id="pegawai_id" name="pegawai_id" required>
                            <option value="">Pilih Pegawai</option>
                            @foreach($pegawaiList as $pegawai)
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
                        <label for="tahun" class="form-label">Tahun</label>
                        <input type="number" class="form-control @error('tahun') is-invalid @enderror" id="tahun" name="tahun" value="{{ old('tahun') ?? date('Y') }}" min="1900" max="{{ date('Y') + 5 }}" required>
                        @error('tahun')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="jatah_cuti" class="form-label">Jatah Cuti</label>
                        <input type="number" class="form-control @error('jatah_cuti') is-invalid @enderror" id="jatah_cuti" name="jatah_cuti" value="{{ old('jatah_cuti') }}" min="0" required>
                        @error('jatah_cuti')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="sisa_cuti" class="form-label">Sisa Cuti</label>
                        <input type="number" class="form-control @error('sisa_cuti') is-invalid @enderror" id="sisa_cuti" name="sisa_cuti" value="{{ old('sisa_cuti') }}" min="0" required>
                        @error('sisa_cuti')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Sisa cuti tidak boleh melebihi jatah cuti.</small>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ route('admin.sisa_cuti.index') }}" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>
@endsection