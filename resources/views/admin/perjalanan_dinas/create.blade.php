@extends('layouts.app')

@section('title', 'Tambah Perjalanan Dinas')

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <h5 class="card-header">Tambah Perjalanan Dinas</h5>
                    <div class="card-body">
                        <form action="{{ route('admin.perjalanan_dinas.store') }}" method="POST">
                            @csrf
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="nomor_surat_tugas" class="form-label">Nomor Surat Tugas</label>
                                    <input type="text" name="nomor_surat_tugas" class="form-control @error('nomor_surat_tugas') is-invalid @enderror" 
                                           id="nomor_surat_tugas" value="{{ old('nomor_surat_tugas') }}" placeholder="Masukkan nomor surat tugas" required>
                                    @error('nomor_surat_tugas')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="tempat_tujuan" class="form-label">Tempat Tujuan</label>
                                    <input type="text" name="tempat_tujuan" class="form-control @error('tempat_tujuan') is-invalid @enderror" 
                                           id="tempat_tujuan" value="{{ old('tempat_tujuan') }}" placeholder="Masukkan tempat tujuan" required>
                                    @error('tempat_tujuan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <label for="maksud_perjalanan" class="form-label">Maksud Perjalanan</label>
                                    <textarea name="maksud_perjalanan" class="form-control @error('maksud_perjalanan') is-invalid @enderror" 
                                              id="maksud_perjalanan" rows="3" placeholder="Masukkan maksud/tujuan perjalanan dinas" required>{{ old('maksud_perjalanan') }}</textarea>
                                    @error('maksud_perjalanan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="pimpinan_pemberi_tugas_id" class="form-label">Pimpinan Pemberi Tugas</label>
                                    <select name="pimpinan_pemberi_tugas_id" class="form-select @error('pimpinan_pemberi_tugas_id') is-invalid @enderror" id="pimpinan_pemberi_tugas_id" required>
                                        <option value="">Pilih Pimpinan</option>
                                        @if(isset($pimpinans))
                                            @foreach($pimpinans as $pimpinan)
                                                <option value="{{ $pimpinan->id }}" {{ old('pimpinan_pemberi_tugas_id') == $pimpinan->id ? 'selected' : '' }}>
                                                    {{ $pimpinan->name ?? $pimpinan->nama_lengkap }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @error('pimpinan_pemberi_tugas_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="tgl_berangkat" class="form-label">Tanggal Berangkat</label>
                                    <input type="date" name="tgl_berangkat" class="form-control @error('tgl_berangkat') is-invalid @enderror" 
                                           id="tgl_berangkat" value="{{ old('tgl_berangkat') }}" required>
                                    @error('tgl_berangkat')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="tgl_kembali" class="form-label">Tanggal Kembali</label>
                                    <input type="date" name="tgl_kembali" class="form-control @error('tgl_kembali') is-invalid @enderror" 
                                           id="tgl_kembali" value="{{ old('tgl_kembali') }}" required>
                                    @error('tgl_kembali')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <label class="form-label">Pegawai yang Ditugaskan</label>
                                    <div class="border rounded p-3">
                                        <input type="text" class="form-control mb-2" id="pegawai_search" placeholder="Cari pegawai...">
                                        <div id="selected_pegawai" class="mb-2" style="min-height: 40px; border: 1px solid #ddd; padding: 8px; background: #f9f9f9;">
                                            <small class="text-muted">Belum ada pegawai yang dipilih</small>
                                        </div>
                                        <div id="pegawai_options" style="max-height: 200px; overflow-y: auto; border: 1px solid #ddd; background: white;">
                                            @php
                                                $samplePegawais = App\Models\Pegawai::select('id', 'nama_lengkap', 'NIP')->limit(10)->get();
                                            @endphp
                                            @foreach($samplePegawais as $pegawai)
                                                <div class="pegawai-option p-2 border-bottom" style="cursor: pointer;" data-id="{{ $pegawai->id }}" data-name="{{ $pegawai->nama_lengkap }}">
                                                    {{ $pegawai->nama_lengkap }} - {{ $pegawai->NIP }}
                                                </div>
                                            @endforeach
                                        </div>
                                        <input type="hidden" id="pegawai_ids" name="pegawai_ids[]" value="">
                                    </div>
                                    @error('pegawai_ids')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-12">
                                    <div class="d-flex justify-content-between mt-4">
                                        <a href="{{ route('admin.perjalanan_dinas.index') }}" class="btn btn-secondary">
                                            <i class="fas fa-arrow-left me-1"></i> Kembali
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-1"></i> Simpan
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectedIds = new Set();
    const selectedNames = new Map();
    
    // Pegawai search
    document.getElementById('pegawai_search').addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const options = document.querySelectorAll('.pegawai-option');
        
        options.forEach(option => {
            const text = option.textContent.toLowerCase();
            if (text.includes(searchTerm)) {
                option.style.display = 'block';
            } else {
                option.style.display = 'none';
            }
        });
    });
    
    // Pegawai selection
    document.querySelectorAll('.pegawai-option').forEach(option => {
        option.addEventListener('click', function() {
            const id = this.dataset.id;
            const name = this.dataset.name;
            
            if (selectedIds.has(id)) {
                selectedIds.delete(id);
                selectedNames.delete(id);
                this.style.background = 'white';
            } else {
                selectedIds.add(id);
                selectedNames.set(id, name);
                this.style.background = '#e3f2fd';
            }
            
            updateSelectedDisplay();
        });
    });
    
    function updateSelectedDisplay() {
        const container = document.getElementById('selected_pegawai');
        const hiddenInput = document.getElementById('pegawai_ids');
        
        if (selectedIds.size === 0) {
            container.innerHTML = '<small class="text-muted">Belum ada pegawai yang dipilih</small>';
            hiddenInput.value = '';
        } else {
            let html = '<strong>Pegawai Dipilih:</strong><br>';
            selectedNames.forEach((name, id) => {
                html += `<span class="badge bg-primary me-1 mb-1" onclick="removePegawai('${id}')" style="cursor: pointer;">${name} x</span> `;
            });
            container.innerHTML = html;
            hiddenInput.value = Array.from(selectedIds).join(',');
        }
    }
    
    window.removePegawai = function(id) {
        selectedIds.delete(id);
        selectedNames.delete(id);
        
        const option = document.querySelector(`.pegawai-option[data-id="${id}"]`);
        if (option) {
            option.style.background = 'white';
        }
        
        updateSelectedDisplay();
    };
});
</script>
@endpush
