@extends('layouts.app')

@section('title', 'Tambah Perjalanan Dinas')

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <h5 class="card-header">Tambah Perjalanan Dinas {{Auth::user()->getRoleNames()}}</h5>
                    <div class="card-body">
                        <form action="{{ route('admin.perjalanan_dinas.store') }}" method="POST" id="formPerjalananDinas">
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
                                    <label>Pegawai yang Ditugaskan</label>
                                    <div class="border rounded p-3">
                                        <!-- Search box -->
                                        <input type="text" class="form-control mb-2" id="pegawai_search" placeholder="🔍 Cari pegawai...">
                                        
                                        <!-- Selected display -->
                                        <div id="selected_display" class="mb-2" style="min-height: 60px; border: 2px solid #e9ecef; padding: 12px; background: #f8f9fa; border-radius: 6px;">
                                            <div id="selected_empty" class="text-muted">
                                                <i class="fas fa-users"></i> Belum ada pegawai yang dipilih
                                            </div>
                                            <div id="selected_items" class="d-none"></div>
                                        </div>
                                        
                                        <!-- Pegawai list -->
                                        <div id="pegawai_list" style="max-height: 250px; overflow-y: auto; border: 1px solid #ddd; background: white;">
                                            @php
                                                try {
                                                    $pegawais = App\Models\Pegawai::select('id', 'nama_lengkap', 'NIP')->orderBy('nama_lengkap')->get();
                                                    if($pegawais->count() === 0) {
                                                        $pegawais = collect([
                                                            (object)['id' => 1, 'nama_lengkap' => 'Budi Santoso', 'NIP' => '198001012023123'],
                                                            (object)['id' => 2, 'nama_lengkap' => 'Siti Nurhaliza', 'NIP' => '199001012023456'],
                                                            (object)['id' => 3, 'nama_lengkap' => 'Ahmad Fauzi', 'NIP' => '200001012023789'],
                                                        ]);
                                                    }
                                                } catch(Exception $e) {
                                                    $pegawais = collect([
                                                        (object)['id' => 1, 'nama_lengkap' => 'Budi Santoso', 'NIP' => '198001012023123'],
                                                        (object)['id' => 2, 'nama_lengkap' => 'Siti Nurhaliza', 'NIP' => '199001012023456'],
                                                        (object)['id' => 3, 'nama_lengkap' => 'Ahmad Fauzi', 'NIP' => '200001012023789'],
                                                    ]);
                                                }
                                            @endphp
                                            
                                            <div class="p-2 bg-light">
                                                <small class="text-muted">📋 Daftar Pegawai ({{ $pegawais->count() }} total) - Klik untuk memilih</small>
                                            </div>
                                            
                                            @foreach($pegawais as $pegawai)
                                                <div class="pegawai-item p-3 border-bottom" 
                                                     style="cursor: pointer; transition: all 0.2s;" 
                                                     data-id="{{ $pegawai->id }}" 
                                                     data-name="{{ $pegawai->nama_lengkap }}"
                                                     data-nip="{{ $pegawai->NIP }}"
                                                     onmouseover="this.style.backgroundColor='#e3f2fd'" 
                                                     onmouseout="this.style.backgroundColor='transparent'">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <strong>{{ $pegawai->nama_lengkap }}</strong>
                                                            <br><small class="text-muted">NIP: {{ $pegawai->NIP }}</small>
                                                        </div>
                                                        <i class="fas fa-plus-circle text-muted"></i>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                        
                                        <!-- Hidden inputs for pegawai IDs -->
                                        <div id="pegawai_inputs_container"></div>
                                    </div>
                                    @error('pegawai_ids')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    <div id="pegawai_error" class="invalid-feedback" style="display: none;">Pilih minimal 1 pegawai!</div>
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

<script>
console.log('🔥 SIMPLE PEGAWAI SELECTOR');

document.addEventListener('DOMContentLoaded', function() {
    console.log('✅ DOM loaded');
    
    const form = document.getElementById('formPerjalananDinas');
    const searchInput = document.getElementById('pegawai_search');
    const items = document.querySelectorAll('.pegawai-item');
    const selectedItems = new Map();
    const pegawaiError = document.getElementById('pegawai_error');
    
    console.log('📊 Found', items.length, 'pegawai items');
    
    // Search functionality
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const search = this.value.toLowerCase();
            items.forEach(item => {
                const name = item.dataset.name || '';
                const nip = item.dataset.nip || '';
                if (name.toLowerCase().includes(search) || nip.toLowerCase().includes(search)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    }
    
    // Click handler for pegawai items
    items.forEach(item => {
        item.addEventListener('click', function() {
            const id = this.dataset.id;
            const name = this.dataset.name;
            
            if (selectedItems.has(id)) {
                selectedItems.delete(id);
                this.style.backgroundColor = 'transparent';
                this.querySelector('i').className = 'fas fa-plus-circle text-muted';
            } else {
                selectedItems.set(id, name);
                this.style.backgroundColor = '#e3f2fd';
                this.querySelector('i').className = 'fas fa-check-circle text-success';
            }
            
            updateDisplay();
        });
    });
    
    // Update display function
    function updateDisplay() {
        const selectedEmpty = document.getElementById('selected_empty');
        const selectedItemsDiv = document.getElementById('selected_items');
        const inputsContainer = document.getElementById('pegawai_inputs_container');
        
        if (selectedItems.size === 0) {
            selectedEmpty.classList.remove('d-none');
            selectedItemsDiv.classList.add('d-none');
            inputsContainer.innerHTML = '';
        } else {
            selectedEmpty.classList.add('d-none');
            selectedItemsDiv.classList.remove('d-none');
            
            let html = '<strong>Pegawai Dipilih (' + selectedItems.size + '):</strong><br>';
            selectedItems.forEach((name, id) => {
                html += `<span class="badge bg-primary me-1 mb-1" style="cursor: pointer;" onclick="removeSelected('${id}')" title="Hapus">${name} ×</span>`;
            });
            selectedItemsDiv.innerHTML = html;
            
            // Create hidden inputs for each selected pegawai
            inputsContainer.innerHTML = '';
            selectedItems.forEach((name, id) => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'pegawai_ids[]';
                input.value = id;
                inputsContainer.appendChild(input);
            });
        }
    }
    
    // Remove selected pegawai
    window.removeSelected = function(id) {
        const item = document.querySelector(`[data-id="${id}"]`);
        if (item && selectedItems.has(id)) {
            selectedItems.delete(id);
            item.style.backgroundColor = 'transparent';
            item.querySelector('i').className = 'fas fa-plus-circle text-muted';
            updateDisplay();
        }
    };
    
    // Form submit validation
    form.addEventListener('submit', function(e) {
        // Hide error message first
        pegawaiError.style.display = 'none';
        
        const count = selectedItems.size;
        
        // Validate pegawai selection
        if (count === 0) {
            e.preventDefault();
            pegawaiError.style.display = 'block';
            alert('❌ Pilih minimal 1 pegawai!');
            return false;
        }
        
        // Show confirmation
        const names = Array.from(selectedItems.values());
        const confirmed = confirm(`Apakah Anda yakin ingin submit dengan ${count} pegawai?\n\n${names.join('\n')}`);
        
        if (!confirmed) {
            e.preventDefault();
            return false;
        }
        
        // If validation passes, form will submit normally
        console.log('✅ Form validation passed, submitting...');
        return true;
    });
    
    window.selectedItems = selectedItems;
});
</script>
@endsection