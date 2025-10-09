@extends('layouts.app')

@section('title', 'Tambah Cuti')

@section('content')
<div class="card">
    <h5 class="card-header">Tambah Data Cuti</h5>
    <div class="card-body">
        <form action="{{ route('admin.cuti.store') }}" method="POST" enctype="multipart/form-data">
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
                        <div class="alert alert-info mt-3 d-none" id="sisaCutiContainer">
                            <strong>Sisa Cuti Tahunan Tersedia:</strong>
                            <div class="mt-2">Total: <span id="sisaCutiTotal">0</span> hari</div>
                            <ul class="mb-0" id="sisaCutiPerYear"></ul>
                        </div>
                        <div class="alert alert-warning mt-3 d-none" id="sisaCutiWarning">
                            Data sisa cuti tahunan belum tersedia untuk pegawai ini.
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="jenis_cuti_id" class="form-label">Jenis Cuti</label>
                        <select class="form-control @error('jenis_cuti_id') is-invalid @enderror" id="jenis_cuti_id" name="jenis_cuti_id" required>
                            <option value="">Pilih Jenis Cuti</option>
                            @foreach($jenisCutis as $jenisCuti)
                                <option value="{{ $jenisCuti->id }}" {{ old('jenis_cuti_id') == $jenisCuti->id ? 'selected' : '' }}>{{ $jenisCuti->nama }}</option>
                            @endforeach
                        </select>
                        @error('jenis_cuti_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="tgl_pengajuan" class="form-label">Tanggal Pengajuan</label>
                        <input type="date" class="form-control @error('tgl_pengajuan') is-invalid @enderror" id="tgl_pengajuan" name="tgl_pengajuan" value="{{ old('tgl_pengajuan') }}" required>
                        @error('tgl_pengajuan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="tgl_mulai" class="form-label">Tanggal Mulai</label>
                        <input type="date" class="form-control @error('tgl_mulai') is-invalid @enderror" id="tgl_mulai" name="tgl_mulai" value="{{ old('tgl_mulai') }}" required>
                        @error('tgl_mulai')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="tgl_selesai" class="form-label">Tanggal Selesai</label>
                        <input type="date" class="form-control @error('tgl_selesai') is-invalid @enderror" id="tgl_selesai" name="tgl_selesai" value="{{ old('tgl_selesai') }}" required>
                        @error('tgl_selesai')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="status_persetujuan" class="form-label">Status Persetujuan</label>
                        <select class="form-control @error('status_persetujuan') is-invalid @enderror" id="status_persetujuan" name="status_persetujuan" required>
                            <option value="Diajukan" {{ old('status_persetujuan') == 'Diajukan' ? 'selected' : '' }}>Diajukan</option>
                            <option value="Disetujui" {{ old('status_persetujuan') == 'Disetujui' ? 'selected' : '' }}>Disetujui</option>
                            <option value="Ditolak" {{ old('status_persetujuan') == 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
                        </select>
                        @error('status_persetujuan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="pimpinan_approver_id" class="form-label">Pimpinan Approver</label>
                        <select class="form-control @error('pimpinan_approver_id') is-invalid @enderror" id="pimpinan_approver_id" name="pimpinan_approver_id">
                            <option value="">Pilih Pimpinan Approver</option>
                            @foreach($pimpinans as $pimpinan)
                                <option value="{{ $pimpinan->id }}" {{ old('pimpinan_approver_id') == $pimpinan->id ? 'selected' : '' }}>{{ $pimpinan->name }}</option>
                            @endforeach
                        </select>
                        @error('pimpinan_approver_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="keterangan" class="form-label">Keterangan</label>
                <textarea class="form-control @error('keterangan') is-invalid @enderror" id="keterangan" name="keterangan" rows="3" required>{{ old('keterangan') }}</textarea>
                @error('keterangan')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <label for="dokumen_pendukung" class="form-label">Dokumen Pendukung</label>
                <input type="file" class="form-control @error('dokumen_pendukung') is-invalid @enderror" id="dokumen_pendukung" name="dokumen_pendukung" accept=".pdf,.jpg,.jpeg,.png">
                @error('dokumen_pendukung')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="form-text text-muted">Format: pdf, jpg, jpeg, png. Maksimal: 5MB</small>
            </div>
            
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ route('admin.cuti.index') }}" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>
@endsection

@push('page-js')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const pegawaiSelect = document.getElementById('pegawai_id');
        const container = document.getElementById('sisaCutiContainer');
        const perYearList = document.getElementById('sisaCutiPerYear');
        const totalSpan = document.getElementById('sisaCutiTotal');
        const warning = document.getElementById('sisaCutiWarning');
        const urlTemplate = "{{ route('admin.cuti.sisa-cuti', ['pegawai' => '__pegawai__']) }}";

        const resetSisaCuti = () => {
            container.classList.add('d-none');
            warning.classList.add('d-none');
            perYearList.innerHTML = '';
            totalSpan.textContent = '0';
        };

        const renderSisaCuti = (data) => {
            const perYear = data.per_year || {};
            const total = data.total || 0;

            perYearList.innerHTML = '';
            Object.entries(perYear).forEach(([year, days]) => {
                const li = document.createElement('li');
                li.textContent = `${year}: ${days} hari`;
                perYearList.appendChild(li);
            });

            totalSpan.textContent = total;

            if (total > 0) {
                container.classList.remove('d-none');
                warning.classList.add('d-none');
            } else {
                container.classList.add('d-none');
                warning.classList.remove('d-none');
            }
        };

        const fetchSisaCuti = async (pegawaiId) => {
            if (!pegawaiId) {
                resetSisaCuti();
                return;
            }

            try {
                const response = await fetch(urlTemplate.replace('__pegawai__', pegawaiId));
                if (!response.ok) {
                    throw new Error('Gagal mengambil data sisa cuti');
                }

                const data = await response.json();
                renderSisaCuti(data);
            } catch (error) {
                resetSisaCuti();
                warning.classList.remove('d-none');
                warning.textContent = 'Tidak dapat memuat data sisa cuti. Silakan coba lagi.';
            }
        };

        pegawaiSelect.addEventListener('change', (event) => {
            warning.textContent = 'Data sisa cuti tahunan belum tersedia untuk pegawai ini.';
            fetchSisaCuti(event.target.value);
        });

        if (pegawaiSelect.value) {
            fetchSisaCuti(pegawaiSelect.value);
        }
    });
</script>
@endpush