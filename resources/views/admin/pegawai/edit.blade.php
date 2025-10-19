@extends('layouts.app')

@section('title', 'Edit Pegawai')

@section('content')
<div class="card">
    <h5 class="card-header">Edit Pegawai</h5>
    <div class="card-body">
        <form action="{{ route('admin.pegawai.update', $pegawai->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="NIP" class="form-label">NIP</label>
                        <input type="text" class="form-control @error('NIP') is-invalid @enderror" id="NIP" name="NIP" value="{{ old('NIP', $pegawai->NIP) }}" required>
                        @error('NIP')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-control @error('nama_lengkap') is-invalid @enderror" id="nama_lengkap" name="nama_lengkap" value="{{ old('nama_lengkap', $pegawai->nama_lengkap) }}" required>
                        @error('nama_lengkap')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="tempat_lahir" class="form-label">Tempat Lahir</label>
                        <input type="text" class="form-control @error('tempat_lahir') is-invalid @enderror" id="tempat_lahir" name="tempat_lahir" value="{{ old('tempat_lahir', $pegawai->tempat_lahir) }}" required>
                        @error('tempat_lahir')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="tgl_lahir" class="form-label">Tanggal Lahir</label>
                        <input type="date" class="form-control @error('tgl_lahir') is-invalid @enderror" id="tgl_lahir" name="tgl_lahir" value="{{ old('tgl_lahir', $pegawai->tgl_lahir) }}" required>
                        @error('tgl_lahir')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="jenis_kelamin" class="form-label">Jenis Kelamin</label>
                        <select class="form-control @error('jenis_kelamin') is-invalid @enderror" id="jenis_kelamin" name="jenis_kelamin" required>
                            <option value="">Pilih Jenis Kelamin</option>
                            <option value="L" {{ old('jenis_kelamin', $pegawai->jenis_kelamin) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="P" {{ old('jenis_kelamin', $pegawai->jenis_kelamin) == 'P' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                        @error('jenis_kelamin')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="agama" class="form-label">Agama</label>
                        <input type="text" class="form-control @error('agama') is-invalid @enderror" id="agama" name="agama" value="{{ old('agama', $pegawai->agama) }}" required>
                        @error('agama')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="alamat" class="form-label">Alamat</label>
                <textarea class="form-control @error('alamat') is-invalid @enderror" id="alamat" name="alamat" rows="3" required>{{ old('alamat', $pegawai->alamat) }}</textarea>
                @error('alamat')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="no_telp" class="form-label">No. Telepon</label>
                        <input type="text" class="form-control @error('no_telp') is-invalid @enderror" id="no_telp" name="no_telp" value="{{ old('no_telp', $pegawai->no_telp) }}" required>
                        @error('no_telp')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="jabatan_id" class="form-label">Jabatan</label>
                        <select class="form-control @error('jabatan_id') is-invalid @enderror" id="jabatan_id" name="jabatan_id" required>
                            <option value="">Pilih Jabatan</option>
                            @foreach($jabatans as $jabatan)
                                <option value="{{ $jabatan->id }}" {{ old('jabatan_id', $pegawai->jabatan_id) == $jabatan->id ? 'selected' : '' }}>{{ $jabatan->nama }}</option>
                            @endforeach
                        </select>
                        @error('jabatan_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="golongan_id" class="form-label">Golongan</label>
                        <select class="form-control @error('golongan_id') is-invalid @enderror" id="golongan_id" name="golongan_id" required>
                            <option value="">Pilih Golongan</option>
                            @foreach($golongans as $golongan)
                                <option value="{{ $golongan->id }}" {{ old('golongan_id', $pegawai->golongan_id) == $golongan->id ? 'selected' : '' }}>{{ $golongan->nama }}</option>
                            @endforeach
                        </select>
                        @error('golongan_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="unit_kerja_id" class="form-label">Unit Kerja</label>
                        <select class="form-control @error('unit_kerja_id') is-invalid @enderror" id="unit_kerja_id" name="unit_kerja_id" required>
                            <option value="">Pilih Unit Kerja</option>
                            @foreach($unitKerjas as $unitKerja)
                                <option value="{{ $unitKerja->id }}" {{ old('unit_kerja_id', $pegawai->unit_kerja_id) == $unitKerja->id ? 'selected' : '' }}>{{ $unitKerja->nama }}</option>
                            @endforeach
                        </select>
                        @error('unit_kerja_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="foto_profil" class="form-label">Foto Profil</label>
                <input type="file" class="form-control @error('foto_profil') is-invalid @enderror" id="foto_profil" name="foto_profil" accept="image/*">
                @error('foto_profil')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="form-text text-muted">Format: jpeg, png, jpg. Maksimal: 2MB</small>
                
                @if($pegawai->foto_profil)
                    <div class="mt-2">
                        <label class="form-label">Foto Saat Ini:</label><br>
                        <img src="{{ asset('storage/' . $pegawai->foto_profil) }}" alt="Foto Profil" width="100" class="img-thumbnail">
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" name="hapus_foto" value="1" id="hapus_foto">
                            <label class="form-check-label" for="hapus_foto">
                                Hapus foto saat ini
                            </label>
                        </div>
                    </div>
                @endif
            </div>
            
            <!-- Form untuk mengelola akun user -->
            <div class="border rounded p-3 mb-4">
                <h6 class="mb-3 text-primary">Data Akun Pengguna</h6>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" value="{{ old('username', $pegawai->user ? $pegawai->user->username : '') }}" readonly>
                            <small class="form-text text-muted">Username tidak dapat diubah.</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $pegawai->user ? $pegawai->user->email : '') }}" readonly>
                            <small class="form-text text-muted">Email tidak dapat diubah.</small>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="password" class="form-label">Password Baru</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Kosongkan jika tidak ingin mengubah password.</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                        </div>
                    </div>
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="{{ route('admin.pegawai.index') }}" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>
@endsection