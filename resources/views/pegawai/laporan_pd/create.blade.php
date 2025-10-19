@extends('layouts.app')

@section('title', 'Buat Laporan Perjalanan Dinas')

@push('page-css')
<style>
    .form-card {
        border-left: 4px solid #667eea;
    }
    
    .card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    
    .upload-area {
        border: 2px dashed #ddd;
        border-radius: 0.5rem;
        padding: 2rem;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .upload-area:hover {
        border-color: #667eea;
        background-color: rgba(102, 126, 234, 0.05);
    }
    
    .upload-area.dragover {
        border-color: #667eea;
        background-color: rgba(102, 126, 234, 0.1);
    }
    
    .file-info {
        margin-top: 1rem;
        padding: 1rem;
        background-color: #f8f9fa;
        border-radius: 0.5rem;
    }
    
    .preview-image {
        max-width: 100%;
        max-height: 300px;
        margin: 1rem auto;
        display: none;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">
                <i class="bx bx-plus-circle me-2 text-primary"></i>
                Buat Laporan Perjalanan Dinas
            </h4>
            <p class="text-muted mb-0">Formulir pembuatan laporan perjalanan dinas</p>
        </div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('pegawai.laporan_pd.my') }}">Laporan PD</a></li>
                <li class="breadcrumb-item active">Buat Laporan</li>
            </ol>
        </nav>
    </div>

    <!-- Form Card -->
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card form-card border-0 shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bx bx-file me-2"></i>
                        Form Laporan
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('pegawai.laporan_pd.store') }}" method="POST" enctype="multipart/form-data" id="laporan-form">
                        @csrf
                        
                        <!-- Perjalanan Dinas Selection -->
                        <div class="mb-4">
                            <label for="perjalanan_dinas_id" class="form-label fw-bold">
                                <i class="bx bx-briefcase me-1"></i>
                                Pilih Perjalanan Dinas
                            </label>
                            <select name="perjalanan_dinas_id" id="perjalanan_dinas_id" class="form-select @error('perjalanan_dinas_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Perjalanan Dinas --</option>
                                @foreach($perjalananDinas as $pd)
                                    <option value="{{ $pd->id }}" 
                                            data-maksud="{{ $pd->maksud_perjalanan }}"
                                            data-tempat="{{ $pd->tempat_tujuan }}"
                                            data-berangkat="{{ \Carbon\Carbon::parse($pd->tgl_berangkat)->format('d-m-Y') }}"
                                            data-kembali="{{ \Carbon\Carbon::parse($pd->tgl_kembali)->format('d-m-Y') }}">
                                        {{ $pd->nomor_surat_tugas }} - {{ $pd->tempat_tujuan }}
                                    </option>
                                @endforeach
                            </select>
                            @error('perjalanan_dinas_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            
                            <!-- Perjalanan Dinas Info -->
                            <div class="mt-3 p-3 bg-light rounded" id="pd-info" style="display: none;">
                                <h6 class="mb-3">Informasi Perjalanan Dinas:</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>Maksud:</strong> <span id="info-maksud">-</span></p>
                                        <p class="mb-1"><strong>Tempat Tujuan:</strong> <span id="info-tempat">-</span></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>Tanggal Berangkat:</strong> <span id="info-berangkat">-</span></p>
                                        <p class="mb-1"><strong>Tanggal Kembali:</strong> <span id="info-kembali">-</span></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- File Upload Section -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">
                                <i class="bx bx-upload me-1"></i>
                                Unggah Laporan
                            </label>
                            <div class="upload-area" id="upload-area">
                                <i class="bx bx-cloud-upload fs-1 mb-3 text-primary"></i>
                                <p class="mb-1">Seret & lepas file laporan Anda di sini</p>
                                <p class="text-muted small mb-2">atau</p>
                                <button type="button" class="btn btn-outline-primary btn-sm" id="browse-btn">
                                    Telusuri File
                                </button>
                                <input type="file" name="file_laporan" id="file_laporan" class="d-none @error('file_laporan') is-invalid @enderror" accept=".pdf,.jpg,.jpeg,.png">
                            </div>
                            @error('file_laporan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            
                            <!-- File Info -->
                            <div class="file-info" id="file-info" style="display: none;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="mb-0 fw-bold" id="file-name">-</p>
                                        <p class="mb-0 text-muted small" id="file-size">-</p>
                                    </div>
                                    <button type="button" class="btn btn-outline-danger btn-sm" id="remove-file">
                                        <i class="bx bx-trash"></i>
                                    </button>
                                </div>
                                <img src="" alt="Preview" class="preview-image mt-2" id="preview-image">
                            </div>
                        </div>

                        <!-- Additional Notes -->
                        <div class="mb-4">
                            <label for="catatan_verifikasi" class="form-label fw-bold">
                                <i class="bx bx-comment-detail me-1"></i>
                                Catatan Tambahan (Opsional)
                            </label>
                            <textarea name="catatan_verifikasi" id="catatan_verifikasi" class="form-control @error('catatan_verifikasi') is-invalid @enderror" rows="3" placeholder="Tambahkan catatan tambahan jika diperlukan...">{{ old('catatan_verifikasi') }}</textarea>
                            @error('catatan_verifikasi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Hidden Fields -->
                        <input type="hidden" name="status_verifikasi" value="Belum Diverifikasi">
                        
                        <!-- Form Actions -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('pegawai.laporan_pd.my') }}" class="btn btn-outline-secondary">
                                <i class="bx bx-arrow-back me-1"></i>
                                Kembali
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-save me-1"></i>
                                Simpan Laporan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('page-js')
<script>
$(function() {
    // Perjalanan Dinas selection handler
    $('#perjalanan_dinas_id').change(function() {
        const selectedOption = $(this).find('option:selected');
        if (selectedOption.val()) {
            $('#info-maksud').text(selectedOption.data('maksud'));
            $('#info-tempat').text(selectedOption.data('tempat'));
            $('#info-berangkat').text(selectedOption.data('berangkat'));
            $('#info-kembali').text(selectedOption.data('kembali'));
            $('#pd-info').show();
        } else {
            $('#pd-info').hide();
        }
    });

    // File upload handlers
    const uploadArea = $('#upload-area');
    const fileInput = $('#file_laporan');
    const fileInfo = $('#file-info');
    const fileName = $('#file-name');
    const fileSize = $('#file-size');
    const previewImage = $('#preview-image');
    const browseBtn = $('#browse-btn');
    const removeFileBtn = $('#remove-file');

    // Browse button click
    browseBtn.click(function(e) {
        e.preventDefault();
        fileInput.click();
    });

    // File input change
    fileInput.change(function() {
        handleFileSelection(this.files[0]);
    });

    // Drag and drop events
    uploadArea.on('dragover', function(e) {
        e.preventDefault();
        $(this).addClass('dragover');
    });

    uploadArea.on('dragleave', function(e) {
        e.preventDefault();
        $(this).removeClass('dragover');
    });

    uploadArea.on('drop', function(e) {
        e.preventDefault();
        $(this).removeClass('dragover');
        
        if (e.originalEvent.dataTransfer.files.length) {
            handleFileSelection(e.originalEvent.dataTransfer.files[0]);
        }
    });

    // Click upload area to trigger file input
    uploadArea.click(function() {
        fileInput.click();
    });

    // Remove file button
    removeFileBtn.click(function() {
        fileInput.val('');
        fileInfo.hide();
        previewImage.hide().attr('src', '');
    });

    // Form submission handler
    $('#laporan-form').submit(function(e) {
        // Validate required fields
        const perjalananDinasId = $('#perjalanan_dinas_id').val();
        const fileLaporan = fileInput.val();
        
        if (!perjalananDinasId) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Silakan pilih perjalanan dinas terlebih dahulu.',
            });
            return false;
        }
        
        if (!fileLaporan) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Silakan unggah file laporan terlebih dahulu.',
            });
            return false;
        }
        
        // Show loading indicator
        Swal.fire({
            title: 'Menyimpan...',
            text: 'Mohon tunggu, sedang menyimpan laporan Anda.',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
    });
});

function handleFileSelection(file) {
    if (!file) return;
    
    // Validate file type
    const allowedTypes = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'];
    if (!allowedTypes.includes(file.type)) {
        Swal.fire({
            icon: 'error',
            title: 'File Tidak Valid!',
            text: 'Hanya file PDF, JPG, JPEG, dan PNG yang diperbolehkan.',
        });
        return;
    }
    
    // Validate file size (10MB max)
    const maxSize = 10 * 1024 * 1024; // 10MB in bytes
    if (file.size > maxSize) {
        Swal.fire({
            icon: 'error',
            title: 'File Terlalu Besar!',
            text: 'Ukuran file maksimal 10MB.',
        });
        return;
    }
    
    // Display file info
    fileName.text(file.name);
    fileSize.text(formatFileSize(file.size));
    fileInfo.show();
    
    // Preview image if it's an image
    if (file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImage.attr('src', e.target.result).show();
        };
        reader.readAsDataURL(file);
    } else {
        previewImage.hide();
    }
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}
</script>
@endpush