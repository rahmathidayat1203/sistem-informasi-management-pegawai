@extends('layouts.app')

@section('title', 'Perjalanan Dinas Saya')

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
                <i class="bx bx-briefcase me-2 text-primary"></i>
                Perjalanan Dinas Saya
            </h4>
            <p class="text-muted mb-0">Daftar penugasan perjalanan dinas yang Anda terima</p>
        </div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Perjalanan Dinas</li>
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
                        <i class="bx bx-briefcase" style="font-size: 2rem;"></i>
                    </div>
                    <h4 class="mb-0" id="total-assignments">0</h4>
                    <p class="text-muted mb-0 small">Total Penugasan</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-warning mb-2">
                        <i class="bx bx-time-five" style="font-size: 2rem;"></i>
                    </div>
                    <h4 class="mb-0" id="pending-assignments">0</h4>
                    <p class="text-muted mb-0 small">Belum Dimulai</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-info mb-2">
                        <i class="bx bx-run" style="font-size: 2rem;"></i>
                    </div>
                    <h4 class="mb-0" id="ongoing-assignments">0</h4>
                    <p class="text-muted mb-0 small">Sedang Berlangsung</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-success mb-2">
                        <i class="bx bx-check-circle" style="font-size: 2rem;"></i>
                    </div>
                    <h4 class="mb-0" id="completed-assignments">0</h4>
                    <p class="text-muted mb-0 small">Selesai</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="card border-0 shadow-sm">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="bx bx-list-ul me-2"></i>
                Daftar Penugasan
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="perjalanan-dinas-table" style="width:100%">
                    <thead>
                        <tr>
                            <th>No. Surat Tugas</th>
                            <th>Tujuan</th>
                            <th>Maksud</th>
                            <th>Tanggal Berangkat</th>
                            <th>Tanggal Kembali</th>
                            <th>Pemberi Tugas</th>
                            <th>Status</th>
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
    var table = $('#perjalanan-dinas-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route('pegawai.perjalanan_dinas.my') }}',
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
                data: 'nomor_surat_tugas',
                name: 'nomor_surat_tugas'
            },
            { 
                data: 'tempat_tujuan',
                name: 'tempat_tujuan'
            },
            { 
                data: 'maksud_perjalanan',
                name: 'maksud_perjalanan',
                render: function(data) {
                    return data.length > 50 ? data.substring(0, 50) + '...' : data;
                }
            },
            { 
                data: 'tgl_berangkat',
                name: 'tgl_berangkat'
            },
            { 
                data: 'tgl_kembali',
                name: 'tgl_kembali'
            },
            { 
                data: 'pimpinan',
                name: 'pimpinanPemberiTugas.name'
            },
            { 
                data: 'status',
                name: 'status',
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
        order: [[3, 'desc']], // Order by tanggal berangkat
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
    let ongoing = 0;
    let completed = 0;
    
    $('#perjalanan-dinas-table tbody tr').each(function() {
        total++;
        const statusHtml = $(this).find('td:eq(6)').html();
        
        if (statusHtml) {
            if (statusHtml.includes('Belum Dimulai')) {
                pending++;
            } else if (statusHtml.includes('Sedang Berlangsung')) {
                ongoing++;
            } else if (statusHtml.includes('Selesai')) {
                completed++;
            }
        }
    });
    
    // Update the statistics cards
    $('#total-assignments').text(total);
    $('#pending-assignments').text(pending);
    $('#ongoing-assignments').text(ongoing);
    $('#completed-assignments').text(completed);
}

// Handle action buttons with event delegation
$(document).on('click', '.btn-detail', function() {
    const url = $(this).data('url');
    if (url) {
        window.location.href = url;
    }
});

$(document).on('click', '.btn-create-report', function() {
    const url = $(this).data('url');
    if (url) {
        window.location.href = url;
    }
});

$(document).on('click', '.btn-view-report', function() {
    const url = $(this).data('url');
    if (url) {
        window.location.href = url;
    }
});
</script>
@endpush