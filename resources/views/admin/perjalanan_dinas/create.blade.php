@extends('layouts.app')

@section('title', 'Tambah Perjalanan Dinas')

@section('content')
<div class="card">
    <h5 class="card-header">Tambah Perjalanan Dinas</h5>
    <div class="card-body">
        <form action="{{ route('admin.perjalanan_dinas.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="nomor_surat_tugas" class="form-label">Nomor Surat Tugas</label>
                        <input type="text" class="form-control @error('nomor_surat_tugas') is-invalid @enderror" id="nomor_surat_tugas" name="nomor_surat_tugas" value="{{ old('nomor_surat_tugas') }}" required>
                        @error('nomor_surat_tugas')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="pimpinan_pemberi_tugas_id" class="form-label">Pimpinan Pemberi Tugas</label>
                        <select class="form-control @error('pimpinan_pemberi_tugas_id') is-invalid @enderror" id="pimpinan_pemberi_tugas_id" name="pimpinan_pemberi_tugas_id" required>
                            <option value="">Pilih Pimpinan</option>
                            @foreach($pimpinans as $pimpinan)
                                <option value="{{ $pimpinan->id }}" {{ old('pimpinan_pemberi_tugas_id') == $pimpinan->id ? 'selected' : '' }}>{{ $pimpinan->name }}</option>
                            @endforeach
                        </select>
                        @error('pimpinan_pemberi_tugas_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="maksud_perjalanan" class="form-label">Maksud Perjalanan</label>
                <textarea class="form-control @error('maksud_perjalanan') is-invalid @enderror" id="maksud_perjalanan" name="maksud_perjalanan" rows="3" required>{{ old('maksud_perjalanan') }}</textarea>
                @error('maksud_perjalanan')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="tempat_tujuan" class="form-label">Tempat Tujuan</label>
                        <input type="text" class="form-control @error('tempat_tujuan') is-invalid @enderror" id="tempat_tujuan" name="tempat_tujuan" value="{{ old('tempat_tujuan') }}" required>
                        @error('tempat_tujuan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="tgl_berangkat" class="form-label">Tanggal Berangkat</label>
                        <input type="date" class="form-control @error('tgl_berangkat') is-invalid @enderror" id="tgl_berangkat" name="tgl_berangkat" value="{{ old('tgl_berangkat') }}" required>
                        @error('tgl_berangkat')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="tgl_kembali" class="form-label">Tanggal Kembali</label>
                        <input type="date" class="form-control @error('tgl_kembali') is-invalid @enderror" id="tgl_kembali" name="tgl_kembali" value="{{ old('tgl_kembali') }}" required>
                        @error('tgl_kembali')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Pegawai yang Ikut</label>
                <div class="row">
                    @foreach($pegawais as $pegawai)
                        <div class="col-md-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="pegawai_ids[]" value="{{ $pegawai->id }}" id="pegawai_{{ $pegawai->id }}" {{ in_array($pegawai->id, old('pegawai_ids', [])) ? 'checked' : '' }}>
                                <label class="form-check-label" for="pegawai_{{ $pegawai->id }}">
                                    {{ $pegawai->nama_lengkap }}
                                </label>
                            </div>
                        </div>
                    @endforeach
                </div>
                @error('pegawai_ids')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ route('admin.perjalanan_dinas.index') }}" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>
@endsection