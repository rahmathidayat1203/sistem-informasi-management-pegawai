@extends('layouts.app')

@section('title', 'Edit Riwayat Pangkat')

@section('content')
<div class="card">
    <h5 class="card-header">Edit Riwayat Pangkat</h5>
    <div class="card-body">
        <form action="{{ route('admin.riwayat_pangkat.update', $riwayatPangkat->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="pegawai_id" class="form-label">Nama Pegawai</label>
                        <select class="form-control @error('pegawai_id') is-invalid @enderror" id="pegawai_id" name="pegawai_id" required>
                            <option value="">Pilih Pegawai</option>
                            @foreach($pegawais as $pegawai)
                                <option value="{{ $pegawai->id }}" {{ old('pegawai_id', $riwayatPangkat->pegawai_id) == $pegawai->id ? 'selected' : '' }}>{{ $pegawai->nama_lengkap }}</option>
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
                                <option value="{{ $golongan->id }}" {{ old('golongan_id', $riwayatPangkat->golongan_id) == $golongan->id ? 'selected' : '' }}>{{ $golongan->nama }}</option>
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
                <input type="text" class="form-control @error('nomor_sk') is-invalid @enderror" id="nomor_sk" name="nomor_sk" value="{{ old('nomor_sk', $riwayatPangkat->nomor_sk) }}" required>
                @error('nomor_sk')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="tanggal_sk" class="form-label">Tanggal SK</label>
                        <input type="date" class="form-control @error('tanggal_sk') is-invalid @enderror" id="tanggal_sk" name="tanggal_sk" value="{{ old('tanggal_sk', $riwayatPangkat->tanggal_sk) }}" required>
                        @error('tanggal_sk')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="tmt_pangkat" class="form-label">TMT Pangkat</label>
                        <input type="date" class="form-control @error('tmt_pangkat') is-invalid @enderror" id="tmt_pangkat" name="tmt_pangkat" value="{{ old('tmt_pangkat', $riwayatPangkat->tmt_pangkat) }}" required>
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
                
                @if($riwayatPangkat->file_sk)
                    <div class="mt-2">
                        <label class="form-label">File SK Saat Ini:</label><br>
                        @if(pathinfo($riwayatPangkat->file_sk, PATHINFO_EXTENSION) === 'pdf')
                            <a href="{{ asset('storage/' . $riwayatPangkat->file_sk) }}" target="_blank" class="btn btn-sm btn-info">
                                Lihat File PDF
                            </a>
                        @else
                            <img src="{{ asset('storage/' . $riwayatPangkat->file_sk) }}" alt="File SK" width="100" class="img-thumbnail">
                        @endif
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" name="hapus_file" value="1" id="hapus_file">
                            <label class="form-check-label" for="hapus_file">
                                Hapus file saat ini
                            </label>
                        </div>
                    </div>
                @endif
            </div>
            
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="{{ route('admin.riwayat_pangkat.index') }}" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>
@endsection