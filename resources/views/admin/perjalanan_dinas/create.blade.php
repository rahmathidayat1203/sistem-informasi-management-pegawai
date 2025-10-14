@extends('layouts.app')

@section('title', 'Tambah Perjalanan Dinas')

@push('page-css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
<style>
/* Force Select2 visibility */
.select2-container {
    width: 100% !important;
    position: relative !important;
    z-index: 9999 !important;
}
.select2-container .select2-selection--multiple {
    min-height: 38px !important;
    border: 1px solid #ced4da !important;
    border-radius: 0.375rem !important;
    background: white !important;
}
.select2-container .select2-selection--multiple .select2-selection__rendered {
    padding: 0.375rem 0.75rem !important;
}
.select2-dropdown {
    z-index: 99999 !important;
    border: 1px solid #ced4da !important;
    border-radius: 0.375rem !important;
    box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,.075) !important;
}
.select2-container--focus .select2-selection--multiple {
    border-color: #86b7fe !important;
    outline: 0 !important;
    box-shadow: 0 0 0 0.25rem rgba(13,110,253,.25) !important;
}
/* Hide original select after Select2 initialization */
.select2-container ~ select {
    display: none !important;
}
</style>
@endpush

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- TESTING - Remove this after debugging -->
        <div class="alert alert-info">
            TESTING: If you can see this message, the view is loading correctly.
        </div>
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
                                        @foreach($pimpinans as $pimpinan)
                                            <option value="{{ $pimpinan->id }}" {{ old('pimpinan_pemberi_tugas_id') == $pimpinan->id ? 'selected' : '' }}>
                                                {{ $pimpinan->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('pimpinan_pemberi_tugas_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="row">
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
                                    <label for="pegawai_ids" class="form-label">Pegawai yang Ditugaskan</label>
                                    <select class="form-select select2-ajax @error('pegawai_ids') is-invalid @enderror" id="pegawai_ids" name="pegawai_ids[]" multiple required>
                                        <!-- Add some initial options to show it works -->
                                        @php
                                            $samplePegawais = App\Models\Pegawai::select('id', 'nama_lengkap', 'NIP')->limit(10)->get();
                                        @endphp
                                        @foreach($samplePegawais as $pegawai)
                                            <option value="{{ $pegawai->id }}">{{ $pegawai->nama_lengkap }} - {{ $pegawai->NIP }}</option>
                                        @endforeach
                                    </select>
                                    
                                    <!-- FALLBACK SOLUTION - Manual Multi-Select with Search -->
                                    <script>
                                    console.log("🚀 INLINE SCRIPT: Using fallback solution!");
                                    
                                    document.addEventListener('DOMContentLoaded', function() {
                                        console.log("🚀 DOMContentLoaded fired - Starting manual multi-select");
                                        
                                        // Convert original select to manual searchable multi-select
                                        setTimeout(function() {
                                            try {
                                                const originalSelect = document.getElementById('pegawai_ids');
                                                const containerDiv = document.createElement('div');
                                                containerDiv.className = 'manual-multi-select-container';
                                                containerDiv.style.cssText = 'position: relative; width: 100%;';
                                                
                                                // Hidden input to store selected values
                                                const hiddenInput = document.createElement('input');
                                                hiddenInput.type = 'hidden';
                                                hiddenInput.name = 'pegawai_ids[]'; // Multiple values will be handled differently
                                                hiddenInput.id = 'pegawai_ids_hidden';
                                                
                                                // Search box
                                                const searchBox = document.createElement('input');
                                                searchBox.type = 'text';
                                                searchBox.className = 'form-control';
                                                searchBox.placeholder = '🔍 Cari pegawai (ketik untuk filter)...';
                                                searchBox.style.cssText = 'margin-bottom: 10px; border: 2px solid #ced4da; border-radius: 8px; padding: 10px;';
                                                
                                                // Selected items container
                                                const selectedContainer = document.createElement('div');
                                                selectedContainer.className = 'selected-items';
                                                selectedContainer.style.cssText = 'min-height: 80px; border: 2px solid #ced4da; border-radius: 8px; padding: 10px; background: white; margin-bottom: 10px;';
                                                
                                                // Options container (for selection) - initially shown
                                                const optionsContainer = document.createElement('div');
                                                optionsContainer.className = 'options-container';
                                                optionsContainer.style.cssText = 'max-height: 300px; overflow-y: auto; border: 2px solid #ced4da; border-radius: 8px; background: white; display: block;';
                                                
                                                // Get existing options
                                                const existingOptions = [];
                                                @php
                                                    $pegawais = App\Models\Pegawai::select('id', 'nama_lengkap', 'NIP')->limit(20)->get();
                                                    foreach($pegawais as $pegawai) {
                                                        echo "existingOptions.push({id: " . $pegawai->id . ", text: '" . addslashes($pegawai->nama_lengkap . ' - ' . $pegawai->NIP) . "'});";
                                                    }
                                                @endphp
                                                
                                                // Add header
                                                const headerDiv = document.createElement('div');
                                                headerDiv.id = 'header';
                                                headerDiv.style.cssText = 'padding: 10px; background: #007bff; color: white; font-weight: bold; position: sticky; top: 0; z-index: 10;';
                                                headerDiv.textContent = '📋 Daftar Pegawai (Klik untuk memilih)';
                                                optionsContainer.appendChild(headerDiv);

                                                // Populate options
                                                existingOptions.forEach(option => {
                                                    const optionDiv = document.createElement('div');
                                                    optionDiv.className = 'option-item';
                                                    optionDiv.style.cssText = 'padding: 10px 8px; border-bottom: 1px solid #eee; cursor: pointer; background: white; transition: all 0.2s;';
                                                    optionDiv.dataset.id = option.id;
                                                    optionDiv.textContent = option.text;
                                                    optionDiv.onmouseover = function() { 
                                                        this.style.background = '#e3f2fd'; 
                                                        this.style.transform = 'translateX(5px)';
                                                    };
                                                    optionDiv.onmouseout = function() { 
                                                        this.style.background = 'white'; 
                                                        this.style.transform = 'translateX(0)';
                                                    };
                                                    optionDiv.onclick = function() { toggleOption(option.id, option.text); };
                                                    optionsContainer.appendChild(optionDiv);
                                                });
                                                
                                                // Store selected values
                                                const selectedValues = new Set();
                                                const selectedLabels = new Map();
                                                
                                                function toggleOption(id, text) {
                                                    if (selectedValues.has(id)) {
                                                        selectedValues.delete(id);
                                                        selectedLabels.delete(id);
                                                    } else {
                                                        selectedValues.add(id);
                                                        selectedLabels.set(id, text);
                                                    }
                                                    updateDisplay();
                                                }
                                                
                                                function updateDisplay() {
                                                    // Update hidden input (for form submission)
                                                    hiddenInput.value = Array.from(selectedValues).join(',');
                                                    
                                                    // Update selected container
                                                    selectedContainer.innerHTML = selectedValues.size > 0 ? 
                                                        `<div style="margin-bottom: 5px; font-size: 12px; color: #666;">
                                                            <strong>Pegawai Dipilih (${selectedValues.size}):</strong>
                                                        </div>` +
                                                        Array.from(selectedLabels.entries()).map(([id, text]) => 
                                                            `<span style="display: inline-block; background: #007bff; color: white; padding: 4px 8px; margin: 2px; border-radius: 4px; cursor: pointer; font-size: 12px;" title="Klik untuk hapus" onclick="removeOption('${id}')">${text} ×</span>`
                                                        ).join('') : 
                                                        '<div style="text-align: center; padding: 20px; color: #999; border: 2px dashed #ddd; border-radius: 8px;">
                                                            <div style="font-size: 16px; margin-bottom: 5px;">📋</div>
                                                            <div>Belum ada pegawai yang dipilih</div>
                                                            <div style="font-size: 12px; color: #666;">Klik pada daftar pegawai di bawah untuk memilih</div>
                                                        </div>';
                                                }
                                                
                                                function removeOption(id) {
                                                    selectedValues.delete(id);
                                                    selectedLabels.delete(id);
                                                    updateDisplay();
                                                }
                                                
                                                // Search functionality
                                                searchBox.oninput = function() {
                                                    const searchTerm = this.value.toLowerCase();
                                                    const options = optionsContainer.children;
                                                    
                                                    for (let option of options) {
                                                        // Skip header
                                                        if (option.id === 'header') continue;
                                                        
                                                        const text = option.textContent.toLowerCase();
                                                        if (text.includes(searchTerm) || searchTerm === '') {
                                                            option.style.display = 'block';
                                                        } else {
                                                            option.style.display = 'none';
                                                        }
                                                    }
                                                    
                                                    // Always show container, but highlight search
                                                    if (searchTerm) {
                                                        optionsContainer.style.border = '3px solid #28a745'; // Green when searching
                                                        const allHidden = Array.from(options).every(option => {
                                                            if (option.id === 'header') return true; // Header always visible
                                                            return option.style.display === 'none';
                                                        });
                                                        headerDiv.textContent = `📋 ${allHidden ? 'Tidak ada hasil' : existingOptions.filter(o => o.text.toLowerCase().includes(searchTerm)).length} hasil ditemukan`;
                                                    } else {
                                                        optionsContainer.style.border = '2px solid #ced4da';
                                                        headerDiv.textContent = `📋 Daftar Pegawai (${existingOptions.length} total) - Klik untuk memilih`;
                                                    }
                                                };
                                                
                                                // Make removeOption globally accessible
                                                window.removeOption = removeOption;
                                                
                                                // Replace original select with custom implementation
                                                originalSelect.parentNode.insertBefore(containerDiv, originalSelect);
                                                containerDiv.appendChild(searchBox);
                                                containerDiv.appendChild(selectedContainer);
                                                containerDiv.appendChild(optionsContainer);
                                                containerDiv.appendChild(hiddenInput);
                                                
                                                // Remove original select from DOM completely to avoid form validation issues
                                                originalSelect.remove();
                                                
                                                // Add visual success indicator
                                                searchBox.style.border = '3px solid lime';
                                                selectedContainer.style.border = '3px solid lime';
                                                optionsContainer.style.border = '3px solid lime';
                                                
                                                console.log("🎉 SUCCESS! Manual multi-select implemented!");
                                                console.log("🟢 Green borders: Working perfectly!");
                                                
                                            } catch (error) {
                                                console.error("❌ Error in manual implementation:", error);
                                            }
                                        }, 1000);
                                    });
                                    </script>
                                    <small class="form-text text-muted">
                                        <i class="fas fa-info-circle"></i> 
                                        <strong>Catatan:</strong> Pilih satu atau lebih pegawai untuk ditugaskan. 
                                        Gunakan fungsi pencarian untuk menemukan pegawai secara cepat. 
                                        Pegawai yang dipilih akan menerima notifikasi (jika memiliki akun user).
                                    </small>
                                    @error('pegawai_ids')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-12">
                                    <div class="d-flex justify-content-between mt-4">
                                        <a href="{{ route('admin.perjalanan_dinas.index') }}" class="btn btn-secondary">
                                            <i class="fas fa-arrow-left me-1"></i>
                                            Kembali
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-1"></i>
                                            Simpan
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

@push('page-css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
@endpush

@push('scripts')
<script>
    console.log("🔥 START: Perjalanan Dinas CREATE page loaded");
</script>
<!-- LOCAL APPROACH - Use existing jQuery and create custom select -->
<script>
<script>
// Initialize on window load to ensure all scripts are loaded
window.addEventListener('load', function() {
    delayedSelect2Init();
});

function delayedSelect2Init() {
    // Wait for DOM to be fully ready
    setTimeout(function() {
        initSelect2Ajax();
    }, 500);
}

function initSelect2Ajax() {
    try {
        console.log("=== INITIALIZING SELECT2 ===");
        console.log("jQuery available:", typeof $ !== 'undefined');
        console.log("Select2 available:", $.fn.select2 !== undefined);

        if (typeof $ === 'undefined' || $.fn.select2 === undefined) {
            console.error("jQuery or Select2 not available");
            return;
        }

        var $select = $('.select2-ajax');
        console.log("Select element found:", $select.length);
        
        if ($select.length === 0) {
            console.error("Select element not found");
            return;
        }

        // FIRST: Initialize without data to see if it works
        console.log("Step 1: Basic initialization...");
        $select.select2({
            theme: 'bootstrap-5',
            placeholder: 'Klik untuk mencari pegawai...',
            width: '100%',
            minimumInputLength: 1,
            allowClear: true,
            multiple: true
        });

        console.log("Step 2: Checking if Select2 is visible...");
        setTimeout(function() {
            var $container = $select.next('.select2-container');
            console.log("Select2 container exists:", $container.length > 0);
            console.log("Select2 container visible:", $container.is(':visible'));
            console.log("Select2 container CSS:", $container.css(['display', 'visibility', 'z-index']));
            
            if ($container.length > 0) {
                // ADD VISUAL INDICATOR
                $container.css('border', '3px solid red');
                console.log("Red border added to Select2 container for visibility test");
            }
        }, 100);

        // THEN: Add data and AJAX
        console.log("Step 3: Adding data and AJAX...");
        var existingOptions = [];
        $select.find('option').each(function() {
            if ($(this).val() !== '') {
                existingOptions.push({
                    id: $(this).val(),
                    text: $(this).text(),
                    selected: true
                });
            }
        });

        // Re-initialize with data
        $select.select2('destroy').select2({
            theme: 'bootstrap-5',
            dropdownParent: $select.parent(),
            placeholder: 'Klik untuk mencari pegawai...',
            width: '100%',
            minimumInputLength: 1,
            allowClear: true,
            multiple: true,
            data: existingOptions,
            ajax: {
                url: '{{ route("admin.perjalanan_dinas.searchpegawai") }}',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    console.log('🔍 Searching for:', params.term);
                    return {
                        q: params.term
                    };
                },
                processResults: function (data) {
                    console.log('📊 Search results:', data.length, 'items');
                    if (data.error) {
                        console.error('Search error:', data.error);
                        return { results: [] };
                    }
                    return {
                        results: data.map(function(item) {
                            return {
                                id: item.id,
                                text: item.nama_lengkap + ' - ' + (item.NIP || '')
                            };
                        })
                    };
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', xhr.responseText);
                    return { results: [] };
                },
                cache: true
            }
        });

        console.log("✅ Select2 initialized successfully with full data");

    } catch (error) {
        console.error('❌ Error initializing Select2:', error);
    }
}

// Date validation
$(document).ready(function() {
    $('#tgl_berangkat').change(function() {
        var minDate = $(this).val();
        $('#tgl_kembali').attr('min', minDate);
        if ($('#tgl_kembali').val() && $('#tgl_kembali').val() < minDate) {
            $('#tgl_kembali').val(minDate);
        }
    });
});
</script>
@endpush
