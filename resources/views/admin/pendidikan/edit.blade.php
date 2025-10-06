@extends('layouts.app')

@section('title', 'Edit Pendidikan')

@section('content')
<div class="card">
    <h5 class="card-header">Edit Data Pendidikan</h5>
    <div class="card-body">
        <form action="{{ route('admin.pendidikan.update', $pendidikan->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="pegawai_id" class="form-label">Nama Pegawai</label>
                        <select class="form-control @error('pegawai_id') is-invalid @enderror" id="pegawai_id" name="pegawai_id" required>
                            <option value="">Pilih Pegawai</option>
                            @foreach($pegawais as $pegawai)
                                <option value="{{ $pegawai->id }}" {{ old('pegawai_id', $pendidikan->pegawai_id) == $pegawai->id ? 'selected' : '' }}>{{ $pegawai->nama_lengkap }}</option>
                            @endforeach
                        </select>
                        @error('pegawai_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="jenjang" class="form-label">Jenjang Pendidikan</label>
                        <select class="form-control @error('jenjang') is-invalid @enderror" id="jenjang" name="jenjang" required>
                            <option value="">Pilih Jenjang</option>
                            <option value="SD" {{ old('jenjang', $pendidikan->jenjang) == 'SD' ? 'selected' : '' }}>SD</option>
                            <option value="SMP" {{ old('jenjang', $pendidikan->jenjang) == 'SMP' ? 'selected' : '' }}>SMP</option>
                            <option value="SMA" {{ old('jenjang', $pendidikan->jenjang) == 'SMA' ? 'selected' : '' }}>SMA</option>
                            <option value="D3" {{ old('jenjang', $pendidikan->jenjang) == 'D3' ? 'selected' : '' }}>D3</option>
                            <option value="S1" {{ old('jenjang', $pendidikan->jenjang) == 'S1' ? 'selected' : '' }}>S1</option>
                            <option value="S2" {{ old('jenjang', $pendidikan->jenjang) == 'S2' ? 'selected' : '' }}>S2</option>
                            <option value="S3" {{ old('jenjang', $pendidikan->jenjang) == 'S3' ? 'selected' : '' }}>S3</option>
                        </select>
                        @error('jenjang')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="nama_institusi" class="form-label">Nama Institusi</label>
                <input type="text" class="form-control @error('nama_institusi') is-invalid @enderror" id="nama_institusi" name="nama_institusi" value="{{ old('nama_institusi', $pendidikan->nama_institusi) }}" required>
                @error('nama_institusi')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="jurusan" class="form-label">Jurusan</label>
                        <input type="text" class="form-control @error('jurusan') is-invalid @enderror" id="jurusan" name="jurusan" value="{{ old('jurusan', $pendidikan->jurusan) }}">
                        @error('jurusan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="tahun_lulus" class="form-label">Tahun Lulus</label>
                        <input type="number" class="form-control @error('tahun_lulus') is-invalid @enderror" id="tahun_lulus" name="tahun_lulus" value="{{ old('tahun_lulus', $pendidikan->tahun_lulus) }}" min="1900" max="{{ date('Y') + 1 }}" required>
                        @error('tahun_lulus')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="nomor_ijazah" class="form-label">Nomor Ijazah</label>
                        <input type="text" class="form-control @error('nomor_ijazah') is-invalid @enderror" id="nomor_ijazah" name="nomor_ijazah" value="{{ old('nomor_ijazah', $pendidikan->nomor_ijazah) }}">
                        @error('nomor_ijazah')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="file_ijazah" class="form-label">File Ijazah</label>
                        <input type="file" class="form-control @error('file_ijazah') is-invalid @enderror" id="file_ijazah" name="file_ijazah" accept=".pdf,.jpg,.jpeg,.png">
                        @error('file_ijazah')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Format: pdf, jpg, jpeg, png. Maksimal: 5MB</small>
                        
                        @if($pendidikan->file_ijazah)
                            <div class="mt-2">
                                <label class="form-label">File Saat Ini:</label><br>
                                @if(pathinfo($pendidikan->file_ijazah, PATHINFO_EXTENSION) === 'pdf')
                                    <a href="{{ asset('storage/' . $pendidikan->file_ijazah) }}" target="_blank" class="btn btn-sm btn-info">
                                        Lihat File PDF
                                    </a>
                                @else
                                    <img src="{{ asset('storage/' . $pendidikan->file_ijazah) }}" alt="File Ijazah" width="100" class="img-thumbnail">
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
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="{{ route('admin.pendidikan.index') }}" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>
@endsection