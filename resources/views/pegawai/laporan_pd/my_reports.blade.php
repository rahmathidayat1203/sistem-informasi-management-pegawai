@extends('layouts.app')

@section('title', 'Laporan Perjalanan Dinas Saya')

@push('page-css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<style>
    .status-badge {
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        font-weight: 600;
        font-size: 0.875rem;
    }
    
    .card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    
    .info-card {
        border-left: 4px solid #667eea;
        transition: transform 0.2s;
    }
    
    .info-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    
    .table-actions .btn {
        margin: 0 2px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">
                <i class="bx bx-file me-2 text-primary"></i>
                Laporan Perjalanan Dinas Saya
            </h4>
            <p class="text-muted mb-0">Daftar laporan perjalanan dinas yang Anda buat</p>
        </div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Laporan PD</li>
            </ol>
        </nav>
    </div>

    <!-- Pegawai Info Card -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card info-card border-0 shadow-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-2 text-center">
                            <div class="avatar avatar-xl bg-primary-light rounded-circle d-inline-flex align-items-center justify-content-center">
                                <i class="bx bx-user text-primary" style="font-size: 2.5rem;"></i>
                            </div>
                        </div>
                        <div class="col-md-10">
                            <h5 class="mb-1">{{ $pegawai->nama_lengkap }}</h5>
                            <div class="row">
                                <div class="col-md-4">
                                    <p class="mb-1 text-muted"><small>NIP</small></p>
                                    <p class="mb-0 fw-bold">{{ $pegawai->NIP }}</p>
                                </div>
                                <div class="col-md-4">
                                    <p class="mb-1 text-muted"><small>Jabatan</small></p>
                                    <p class="mb-0 fw-bold">{{ $pegawai->jabatan->nama_jabatan ?? '-' }}</p>
                                </div>
                                <div class="col-md-4">
                                    <p class="mb-1 text-muted"><small>Unit Kerja</small></p>
                                    <p class="mb-0 fw-bold">{{ $pegawai->unitKerja->nama_unit_kerja ?? '-' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-primary mb-2">
                        <i class="bx bx-file" style="font-size: 2rem;"></i>
                    </div>
                    <h4 class="mb-0" id="total-reports">0</h4>
                    <p class="text-muted mb-0 small">Total Laporan</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-warning mb-2">
                        <i class="bx bx-time-five" style="font-size: 2rem;"></i>
                    </div>
                    <h4 class="mb-0" id="pending-reports">0</h4>
                    <p class="text-muted mb-0 small">Belum Diverifikasi</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-success mb-2">
                        <i class="bx bx-check-circle" style="font-size: 2rem;"></i>
                    </div>
                    <h4 class="mb-0" id="approved-reports">0</h4>
                    <p class="text-muted mb-0 small">Disetujui</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-danger mb-2">
                        <i class="bx bx-x-circle" style="font-size: 2rem;"></i>
                    </div>
                    <h4 class="mb-0" id="rejected-reports">0</h4>
                    <p class="text-muted mb-0 small">Perbaikan</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Button -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-end">
                <a href="{{ route('pegawai.laporan_pd.create') }}" class="btn btn-primary">
                    <i class="bx bx-plus me-1"></i>
                    Buat Laporan Baru
                </a>
            </div>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="card border-0 shadow-sm">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="bx bx-list-ul me-2"></i>
                Daftar Laporan
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="laporan-pd-table" style="width:100%">
                    <thead>
                        <tr>
                            <th>No. Surat Tugas</th>
                            <th>Maksud</th>
                            <th>Tanggal Unggah</th>
                            <th>Status Verifikasi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('page-js')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
$(function() {
    // Initialize DataTable
    var table = $('#laporan-pd-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route('pegawai.laporan_pd.my') }}',
            error: function(xhr, error, code) {
                console.error('DataTables error:', error, code);
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Gagal memuat data. Silakan refresh halaman.',
                });
            }
        },
        columns: [
            { 
                data: 'perjalanan_dinas',
                name: 'perjalananDinas.nomor_surat_tugas'
            },
            { 
                data: 'perjalanan_dinas.maksud_perjalanan',
                name: 'perjalananDinas.maksud_perjalanan',
                render: function(data) {
                    return data.length > 50 ? data.substring(0, 50) + '...' : data;
                }
            },
            { 
                data: 'tgl_unggah',
                name: 'tgl_unggah'
            },
            { 
                data: 'status_verifikasi',
                name: 'status_verifikasi',
                orderable: false,
                searchable: false
            },
            { 
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false,
                className: 'table-actions'
            }
        ],
        order: [[2, 'desc']], // Order by tanggal unggah
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json'
        },
        drawCallback: function() {
            updateStatistics();
        }
    });
    
    // Initial statistics update
    updateStatistics();
});

function updateStatistics() {
    // Count visible rows by status
    let total = 0;
    let pending = 0;
    let approved = 0;
    let rejected = 0;
    
    $('#laporan-pd-table tbody tr').each(function() {
        total++;
        const statusHtml = $(this).find('td:eq(3)').html();
        
        if (statusHtml) {
            if (statusHtml.includes('Belum Diverifikasi')) {
                pending++;
            } else if (statusHtml.includes('Disetujui')) {
                approved++;
            } else if (statusHtml.includes('Perbaikan')) {
                rejected++;
            }
        }
    });
    
    // Update the statistics cards
    $('#total-reports').text(total);
    $('#pending-reports').text(pending);
    $('#approved-reports').text(approved);
    $('#rejected-reports').text(rejected);
}

$(document).on('click', '.btn-detail', function() {
    const url = $(this).data('url');
    if (url) {
        window.location.href = url;
    }
});
</script>
@endpush