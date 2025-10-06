@extends('layouts.app')

@section('title', 'Edit Laporan Perjalanan Dinas')

@section('content')
<div class="card">
    <h5 class="card-header">Edit Laporan Perjalanan Dinas</h5>
    <div class="card-body">
        <form action="{{ route('admin.laporan_pd.update', $laporanPD->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label for="perjalanan_dinas_id" class="form-label">Perjalanan Dinas</label>
                <select class="form-control @error('perjalanan_dinas_id') is-invalid @enderror" id="perjalanan_dinas_id" name="perjalanan_dinas_id" required>
                    <option value="">Pilih Perjalanan Dinas</option>
                    @foreach($perjalananDinas as $pd)
                        <option value="{{ $pd->id }}" {{ old('perjalanan_dinas_id', $laporanPD->perjalanan_dinas_id) == $pd->id ? 'selected' : '' }}>
                            {{ $pd->nomor_surat_tugas }} - {{ Str::limit($pd->maksud_perjalanan, 30) }}
                        </option>
                    @endforeach
                </select>
                @error('perjalanan_dinas_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <label for="file_laporan" class="form-label">File Laporan</label>
                <input type="file" class="form-control @error('file_laporan') is-invalid @enderror" id="file_laporan" name="file_laporan" accept=".pdf,.jpg,.jpeg,.png">
                @error('file_laporan')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="form-text text-muted">Format: pdf, jpg, jpeg, png. Maksimal: 10MB</small>
                
                @if($laporanPD->file_laporan)
                    <div class="mt-2">
                        <label class="form-label">File Laporan Saat Ini:</label><br>
                        @if(pathinfo($laporanPD->file_laporan, PATHINFO_EXTENSION) === 'pdf')
                            <a href="{{ asset('storage/' . $laporanPD->file_laporan) }}" target="_blank" class="btn btn-sm btn-info">
                                Lihat File PDF
                            </a>
                        @else
                            <img src="{{ asset('storage/' . $laporanPD->file_laporan) }}" alt="File Laporan" width="100" class="img-thumbnail">
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
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="status_verifikasi" class="form-label">Status Verifikasi</label>
                        <select class="form-control @error('status_verifikasi') is-invalid @enderror" id="status_verifikasi" name="status_verifikasi" required>
                            <option value="Belum Diverifikasi" {{ old('status_verifikasi', $laporanPD->status_verifikasi) == 'Belum Diverifikasi' ? 'selected' : '' }}>Belum Diverifikasi</option>
                            <option value="Disetujui" {{ old('status_verifikasi', $laporanPD->status_verifikasi) == 'Disetujui' ? 'selected' : '' }}>Disetujui</option>
                            <option value="Perbaikan" {{ old('status_verifikasi', $laporanPD->status_verifikasi) == 'Perbaikan' ? 'selected' : '' }}>Perbaikan</option>
                        </select>
                        @error('status_verifikasi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="admin_keuangan_verifier_id" class="form-label">Admin Keuangan Verifier</label>
                        <select class="form-control @error('admin_keuangan_verifier_id') is-invalid @enderror" id="admin_keuangan_verifier_id" name="admin_keuangan_verifier_id">
                            <option value="">Pilih Admin Keuangan</option>
                            @foreach($adminKeuangan as $admin)
                                <option value="{{ $admin->id }}" {{ old('admin_keuangan_verifier_id', $laporanPD->admin_keuangan_verifier_id) == $admin->id ? 'selected' : '' }}>{{ $admin->name }}</option>
                            @endforeach
                        </select>
                        @error('admin_keuangan_verifier_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="catatan_verifikasi" class="form-label">Catatan Verifikasi</label>
                <textarea class="form-control @error('catatan_verifikasi') is-invalid @enderror" id="catatan_verifikasi" name="catatan_verifikasi" rows="3">{{ old('catatan_verifikasi', $laporanPD->catatan_verifikasi) }}</textarea>
                @error('catatan_verifikasi')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="{{ route('admin.laporan_pd.index') }}" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>
@endsection