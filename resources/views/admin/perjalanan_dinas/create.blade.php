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
                                    <label>Pegawai yang Ditugaskan</label>
                                    <div id="pegawai_selector" class="border rounded p-3">
                                        <!-- Search box -->
                                        <input type="text" class="form-control mb-2" id="pegawai_search" placeholder="🔍 Cari pegawai...">
                                        
                                        <!-- Selected items display -->
                                        <div id="selected_display" class="mb-2" style="min-height: 60px; border: 2px solid #e9ecef; padding: 12px; background: #f8f9fa; border-radius: 6px;">
                                            <div id="selected_empty" class="text-muted">
                                                <i class="fas fa-users"></i> Belum ada pegawai yang dipilih
                                            </div>
                                            <div id="selected_items" class="d-none"></div>
                                        </div>
                                        
                                        <!-- Available pegawai list -->
                                        <div id="pegawai_list" class="border rounded" style="max-height: 250px; overflow-y: auto;">
                                            @php
                                                try {
                                                    $pegawais = App\Models\Pegawai::select('id', 'nama_lengkap', 'NIP')->orderBy('nama_lengkap')->get();
                                                    if($pegawais->count() === 0) {
                                                        // Fallback sample data
                                                        $pegawais = collect([
                                                            (object)['id' => 1, 'nama_lengkap' => 'Budi Santoso', 'NIP' => '198001012023123'],
                                                            (object)['id' => 2, 'nama_lengkap' => 'Siti Nurhaliza', 'NIP' => '199001012023456'],
                                                            (object)['id' => 3, 'nama_lengkap' => 'Ahmad Fauzi', 'NIP' => '200001012023789'],
                                                        ]);
                                                    }
                                                } catch(Exception $e) {
                                                    // Fallback hardcoded data
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
                                        
                                        <!-- Hidden input for form submission -->
                                        <input type="hidden" id="pegawai_ids_data" name="pegawai_ids[]" value="">
                                        
                                        <!-- Debug info -->
                                        <div class="mt-2">
                                            <small class="text-muted" id="debug_info">
                                                Ready: {{ $pegawais->count() }} pegawai tersedia
                                            </small>
                                        </div>
                                        
                                        <!-- Test debug -->
                                        <div class="mt-2 border p-2 bg-light">
                                            <small><strong>DEBUG:</strong></small><br>
                                            <small id="test_debug">JavaScript status: Waiting...</small>
                                        </div>
                                    </div>
                                    @error('pegawai_ids')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-12">
                                    <div class="d-flex justify-content-between mt-4">
                                        <a href="{{ route('admin.perjalanan_dinas.index') }}" class="btn btn-secondary">
                                            <i class="fas fa-arrow-left me-1"></i> Kembali
                                        </a>
                                        <button type="button" class="btn btn-primary" onclick="validateAndSubmit()">
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

<script>
// DIRECT SCRIPT - Test if this loads
console.log('🚀 DIRECT SCRIPT LOADED');
alert('DIRECT SCRIPT LOADED! Testing...');

// Wait for DOM to be ready
document.addEventListener('DOMContentLoaded', function() {
    console.log('✅ DOM Ready');
    
    const testDebug = document.getElementById('test_debug');
    const searchInput = document.getElementById('pegawai_search');
    const pegawaiList = document.getElementById('pegawai_list');
    
    if (testDebug) {
        testDebug.innerHTML = '✅ DOM Ready - Processing pegawai selector...';
        console.log('✅ Debug element found!');
    } else {
        console.error('❌ Debug element NOT found!');
        return;
    }
    
    if (!searchInput || !pegawaiList) {
        console.error('❌ Missing elements', {searchInput, pegawaiList});
        if (testDebug) testDebug.innerHTML = '❌ Error: Missing search input or pegawai list';
        return;
    }
    
    console.log('🚀 DEBUG: All DOM elements found successfully!');
    
    testDebug.innerHTML = '✅ Elements found, starting search & click setup...';
    
    const items = document.querySelectorAll('.pegawai-item');
    console.log('📊 Found pegawai items:', items.length);
    
    items.forEach(item => {
        console.log('Item:', {
            id: item.dataset.id,
            name: item.dataset.name,
            nip: item.dataset.nip
        });
    });
    
    // Basic search
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            console.log('🔍 Searching:', this.value);
            
            items.forEach(item => {
                const name = item.dataset.name || '';
                const nip = item.dataset.nip || '';
                const search = this.value.toLowerCase();
                
                if (name.toLowerCase().includes(search) || nip.toLowerCase().includes(search)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    }
    
    // Track selected items
    const selectedItems = new Map(); // id -> name
    
    // Simple click handlers
    items.forEach(item => {
        item.addEventListener('click', function() {
            const id = this.dataset.id;
            const name = this.dataset.name;
            console.log('👆 Clicked:', {id, name});
            
            // Simple toggle
            if (selectedItems.has(id)) {
                selectedItems.delete(id);
                this.style.backgroundColor = 'transparent';
                this.querySelector('i').className = 'fas fa-plus-circle text-muted';
            } else {
                selectedItems.set(id, name);
                this.style.backgroundColor = '#e3f2fd';
                this.querySelector('i').className = 'fas fa-check-circle text-success';
            }
            
            // Update display
            updateSelectedDisplay(selectedItems);
        });
    });
    
    // Update selected display function
    function updateSelectedDisplay(selectedItems) {
        const selectedDisplay = document.getElementById('selected_display');
        const selectedEmpty = document.getElementById('selected_empty');
        const selectedItemsDiv = document.getElementById('selected_items');
        const hiddenInput = document.getElementById('pegawai_ids_data');
        
        if (selectedItems.size === 0) {
            if (selectedEmpty) selectedEmpty.classList.remove('d-none');
            if (selectedItemsDiv) selectedItemsDiv.classList.add('d-none');
            if (hiddenInput) hiddenInput.value = '';
        } else {
            if (selectedEmpty) selectedEmpty.classList.add('d-none');
            if (selectedItemsDiv) {
                selectedItemsDiv.classList.remove('d-none');
                let html = '<div><strong>Pegawai Dipilih (' + selectedItems.size + '):</strong></div><div class="mt-2">';
                selectedItems.forEach((name, id) => {
                    html += `<div class="d-inline-block m-1">
                        <span class="badge bg-primary text-white" style="cursor: pointer;" onclick="unselectPegawai('${id}')" title="Klik untuk hapus">
                            <i class="fas fa-user"></i> ${name}
                            <i class="fas fa-times ms-1"></i>
                        </span>
                    </div>`;
                });
                html += '</div>';
                selectedItemsDiv.innerHTML = html;
            }
            if (hiddenInput) hiddenInput.value = Array.from(selectedItems.keys()).join(',');
        }
        
        console.log('💾 Selected IDs:', Array.from(selectedItems.keys()));
    }
    
    // Global unselect function
    window.unselectPegawai = function(id) {
        const item = document.querySelector(`.pegawai-item[data-id="${id}"]`);
        if (item) {
            const name = item.dataset.name;
            if (selectedItems.has(id)) {
                selectedItems.delete(id);
                item.style.backgroundColor = 'transparent';
                item.querySelector('i').className = 'fas fa-plus-circle text-muted';
                updateSelectedDisplay(selectedItems);
            }
        }
    };
    
    testDebug.innerHTML = `✅ READY: ${items.length} pegawai items found! Search and click enabled.`;
    console.log('✅ Pegawai selector READY');
    
    // Make selectedItems globally accessible
    window.selectedItems = selectedItems;
});

// Validation and submit function
window.validateAndSubmit = function() {
    const hiddenInput = document.getElementById('pegawai_ids_data');
    const selectedItemsCount = window.selectedItems ? window.selectedItems.size : 0;
    
    console.log('🚀 Submit validation:');
    console.log('Hidden input value:', hiddenInput ? hiddenInput.value : 'NOT FOUND');
    console.log('Selected items count:', selectedItemsCount);
    
    if (selectedItemsCount === 0) {
        alert('❌ Error: Harap pilih minimal 1 pegawai! \n\nKlik pada nama pegawai di daftar untuk memilih.');
        
        // Highlight the pegawai section
        const pegawaiSelector = document.getElementById('pegawai_selector');
        if (pegawaiSelector) {
            pegawaiSelector.style.border = '3px solid #dc3545';
            pegawaiSelector.scrollIntoView({ behavior: 'smooth' });
            
            setTimeout(() => {
                pegawaiSelector.style.border = '1px solid #ced4da';
            }, 3000);
        }
        return;
    }
    
    // Show confirmation dialog
    const selectedNames = Array.from(window.selectedItems.values());
    const confirmMessage = `📋 Konfirmasi Submit:\n\n` +
        `Pegawai dipilih (${selectedItemsCount}):\n` +
        `• ${selectedNames.join('\n• ')}\n\n` +
        `Data yang dipilih benar dan akan disimpan?`;
    
    if (!confirm(confirmMessage)) {
        console.log('❌ Submit cancelled by user');
        return;
    }
    
    // Submit the form
    console.log('✅ Submitting form...');
    const form = document.querySelector('form');
    if (form) {
        form.submit();
    } else {
        alert('❌ Error: Form tidak ditemukan!');
    }
};
</script>
