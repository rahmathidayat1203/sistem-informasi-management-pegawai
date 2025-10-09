@extends('layouts.app')

@section('title', 'Edit Perjalanan Dinas')

@section('content')
<div class="card">
    <h5 class="card-header">Edit Perjalanan Dinas #{{ $perjalananDinas->id }}</h5>
    <div class="card-body">
        <form action="{{ route('admin.perjalanan_dinas.update', $perjalananDinas->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="nomor_surat_tugas" class="form-label">Nomor Surat Tugas</label>
                        <input type="text" name="nomor_surat_tugas" class="form-control @error('nomor_surat_tugas') is-invalid @enderror" 
                               id="nomor_surat_tugas" value="{{ old('nomor_surat_tugas', $perjalananDinas->nomor_surat_tugas) }}" placeholder="Masukkan nomor surat tugas" required>
                        @error('nomor_surat_tugas')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="tempat_tujuan" class="form-label">Tempat Tujuan</label>
                        <input type="text" name="tempat_tujuan" class="form-control @error('tempat_tujuan') is-invalid @enderror" 
                               id="tempat_tujuan" value="{{ old('tempat_tujuan', $perjalananDinas->tempat_tujuan) }}" placeholder="Masukkan tempat tujuan" required>
                        @error('tempat_tujuan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="maksud_perjalanan" class="form-label">Maksud Perjalanan</label>
                        <textarea name="maksud_perjalanan" class="form-control @error('maksud_perjalanan') is-invalid @enderror" 
                                  id="maksud_perjalanan" rows="3" placeholder="Masukkan maksud perjalanan" required>{{ old('maksud_perjalanan', $perjalananDinas->maksud_perjalanan) }}</textarea>
                        @error('maksud_perjalanan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="pimpinan_pemberi_tugas_id" class="form-label">Pimpinan Pemberi Tugas</label>
                        <select name="pimpinan_pemberi_tugas_id" class="form-select @error('pimpinan_pemberi_tugas_id') is-invalid @enderror" id="pimpinan_pemberi_tugas_id" required>
                            <option value="">Pilih Pimpinan</option>
                            @foreach($pimpinans as $pimpinan)
                                <option value="{{ $pimpinan->id }}" {{ old('pimpinan_pemberi_tugas_id', $perjalananDinas->pimpinan_pemberi_tugas_id) == $pimpinan->id ? 'selected' : '' }}>
                                    {{ $pimpinan->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('pimpinan_pemberi_tugas_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="tgl_berangkat" class="form-label">Tanggal Berangkat</label>
                        <input type="date" name="tgl_berangkat" class="form-control @error('tgl_berangkat') is-invalid @enderror" 
                               id="tgl_berangkat" value="{{ old('tgl_berangkat', $perjalananDinas->tgl_berangkat) }}" required>
                        @error('tgl_berangkat')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="tgl_kembali" class="form-label">Tanggal Kembali</label>
                        <input type="date" name="tgl_kembali" class="form-control @error('tgl_kembali') is-invalid @enderror" 
                               id="tgl_kembali" value="{{ old('tgl_kembali', $perjalananDinas->tgl_kembali) }}" required>
                        @error('tgl_kembali')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-12">
                    <div class="mb-3">
                        <label for="pegawai_ids" class="form-label">Pegawai yang Ditugaskan</label>
                        <select class="form-select select2-ajax @error('pegawai_ids') is-invalid @enderror" id="pegawai_ids" name="pegawai_ids" multiple required>
                            <!-- Pre-selected existing pegawai -->
                            @foreach($perjalananDinas->pegawai as $pegawai)
                                <option value="{{ $pegawai->id }}" selected>
                                    {{ $pegawai->nama_lengkap }} - {{ $pegawai->NIP }}
                                </option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">Pilih satu atau lebih pegawai untuk ditugaskan. Gunakan fungsi pencarian untuk menemukan pegawai secara cepat.</small>
                        @error('pegawai_ids')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-12">
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.perjalanan_dinas.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i>
                            Kembali
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>
                            Update
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@section('scripts')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function() {
    // Initialize Select2 for pegawai selection
    $('.select2-ajax').select2({
        placeholder: 'Ketik nama atau NIP pegawai...',
        width: '100%',
        minimumInputLength: 2,
        allowClear: true,
        ajax: {
            url: '{{ route("admin.pegawai.search") }}',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term
                };
            },
            processResults: function (data) {
                return {
                    results: data.map(function(item) {
                        return {
                            id: item.id,
                            text: item.nama_lengkap + ' - ' + (item.NIP || item.nip || '')
                        };
                    })
                };
            },
            cache: true
        }
    });

    // Date validation
    $('#tgl_berangkat').change(function() {
        var minDate = $(this).val();
        $('#tgl_kembali').attr('min', minDate);
        if ($('#tgl_kembali').val() && $('#tgl_kembali').val() < minDate) {
            $('#tgl_kembali').val(minDate);
        }
    });
});
</script>
@endsection
