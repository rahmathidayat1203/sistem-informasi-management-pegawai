@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-lg-12">
                <h4 class="fw-bold py-3 mb-4">
                    <span class="text-muted fw-light">Admin /</span> Pengaturan
                </h4>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-md-12">
                <div class="card mb-4">
                    <h5 class="card-header">Pengaturan Sistem</h5>
                    <div class="card-body">
                        <form action="{{ route('admin.pengaturan.update') }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <!-- Informasi Kepala OPD -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h6 class="mb-3 fw-bold text-primary">
                                        <i class="fas fa-user-tie me-2"></i>Informasi Kepala OPD
                                    </h6>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nama Kepala OPD</label>
                                    <input type="text" class="form-control" name="nama_kepala_opd" 
                                           value="{{ $kepala_opd['nama_kepala_opd'] }}" required>
                                    <div class="form-text">Nama lengkap kepala OPD yang akan muncul di tanda tangan laporan</div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">NIP Kepala OPD</label>
                                    <input type="text" class="form-control" name="nip_kepala_opd" 
                                           value="{{ $kepala_opd['nip_kepala_opd'] }}" required>
                                    <div class="form-text">NIP kepala OPD untuk tanda tangan laporan</div>
                                </div>
                            </div>

                            <!-- Informasi OPD -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h6 class="mb-3 fw-bold text-info">
                                        <i class="fas fa-building me-2"></i>Informasi OPD
                                    </h6>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nama Daerah</label>
                                    <input type="text" class="form-control" name="nama_daerah" 
                                           value="{{ $informasi_opd['nama_daerah'] }}" required>
                                    <div class="form-text">Nama daerah/kota (contoh: Kota Palembang)</div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nama OPD</label>
                                    <input type="text" class="form-control" name="nama_opd" 
                                           value="{{ $informasi_opd['nama_opd'] }}" required>
                                    <div class="form-text">Nama lengkap organisasi perangkat daerah</div>
                                </div>
                                
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Alamat OPD</label>
                                    <textarea class="form-control" name="alamat_opd" rows="2" required>{{ $informasi_opd['alamat_opd'] }}</textarea>
                                    <div class="form-text">Alamat lengkap kantor OPD</div>
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Telepon</label>
                                    <input type="text" class="form-control" name="telepon_opd" 
                                           value="{{ $informasi_opd['telepon_opd'] }}" required>
                                    <div class="form-text">Nomor telepon kantor</div>
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Website</label>
                                    <input type="text" class="form-control" name="website_opd" 
                                           value="{{ $informasi_opd['website_opd'] }}" required>
                                    <div class="form-text">Website resmi OPD</div>
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control" name="email_opd" 
                                           value="{{ $informasi_opd['email_opd'] }}" required>
                                    <div class="form-text">Email resmi OPD</div>
                                </div>
                            </div>

                            <!-- Preview Kop Surat -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h6 class="mb-3 fw-bold text-success">
                                        <i class="fas fa-eye me-2"></i>Preview Kop Surat
                                    </h6>
                                </div>
                                <div class="col-12">
                                    <div class="border p-3 bg-light" style="font-family: Arial, sans-serif;">
                                        <div style="text-align: center; margin-bottom: 15px;">
                                            <h5 style="font-size: 16px; font-weight: bold; margin: 0; text-transform: uppercase;">
                                                Pemerintah Daerah {{ $informasi_opd['nama_daerah'] }}
                                            </h5>
                                            <h6 style="font-size: 12px; font-weight: normal; margin: 3px 0;">
                                                {{ $informasi_opd['nama_opd'] }}<br>
                                                Alamat: {{ $informasi_opd['alamat_opd'] }} Telp: {{ $informasi_opd['telepon_opd'] }}<br>
                                                Website: {{ $informasi_opd['website_opd'] }}, Email: {{ $informasi_opd['email_opd'] }}
                                            </h6>
                                            <hr style="border: 1px solid black; margin: 8px 0;">
                                        </div>
                                        <div style="text-align: center; margin-top: 20px;">
                                            <small class="text-muted">
                                                <strong>Preview Tanda Tangan:</strong><br>
                                                MENGETAHUI<br>
                                                <strong>{{ $kepala_opd['nama_kepala_opd'] }}</strong><br>
                                                <strong>Kepala {{ $informasi_opd['nama_opd'] }}</strong><br>
                                                <strong>NIP. {{ $kepala_opd['nip_kepala_opd'] }}</strong>
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-save me-2"></i>Simpan Pengaturan
                                    </button>
                                    <a href="{{ route('dashboard') }}" class="btn btn-secondary btn-lg ms-2">
                                        <i class="fas fa-arrow-left me-2"></i>Kembali
                                    </a>
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
