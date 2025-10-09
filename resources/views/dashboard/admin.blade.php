@extends('layouts.app')

@section('title', 'Dashboard Admin Kepegawaian')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">Dashboard Admin Kepegawaian</h4>
    
    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h3 class="mb-2">{{ $stats['total_pegawai'] }}</h3>
                                    <p class="mb-0">Total Pegawai</p>
                                </div>
                                <div class="avatar">
                                    <div class="avatar-initial bg-label-primary rounded-2">
                                        <i class="ti ti-users ti-md text-primary"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h3 class="mb-2">{{ $stats['cuti_pending'] }}</h3>
                                    <p class="mb-0">Cuti Menunggu Persetujuan</p>
                                </div>
                                <div class="avatar">
                                    <div class="avatar-initial bg-label-warning rounded-2">
                                        <i class="ti ti-calendar ti-md text-warning"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h3 class="mb-2">{{ $stats['total_perjalanan_dinas'] }}</h3>
                                    <p class="mb-0">Total Perjalanan Dinas</p>
                                </div>
                                <div class="avatar">
                                    <div class="avatar-initial bg-label-info rounded-2">
                                        <i class="ti ti-plane ti-md text-info"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h3 class="mb-2">{{ $stats['perjalanan_dinas_aktif'] }}</h3>
                                    <p class="mb-0">Perjalanan Dinas Aktif</p>
                                </div>
                                <div class="avatar">
                                    <div class="avatar-initial bg-label-success rounded-2">
                                        <i class="ti ti-map-pin ti-md text-success"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="row g-6">
        <!-- Recent Cuti -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header border-bottom">
                    <h5 class="card-title mb-0">Pengajuan Cuti Terbaru</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive text-nowrap">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Pegawai</th>
                                    <th>Jenis Cuti</th>
                                    <th>Status</th>
                                    <th>Tanggal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($recentCuti as $cuti)
                                    <tr>
                                        <td>{{ $cuti->pegawai->nama_lengkap ?? '-' }}</td>
                                        <td>{{ $cuti->jenisCuti->nama ?? '-' }}</td>
                                        <td>
                                            @switch($cuti->status_persetujuan)
                                                @case('Diajukan')
                                                    <span class="badge bg-label-warning">Diajukan</span>
                                                @break
                                                @case('Disetujui')
                                                    <span class="badge bg-label-success">Disetujui</span>
                                                @break
                                                @case('Ditolak')
                                                    <span class="badge bg-label-danger">Ditolak</span>
                                                @break
                                            @endswitch
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($cuti->created_at)->format('d M Y') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">Belum ada data</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Recent Perjalanan Dinas -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header border-bottom">
                    <h5 class="card-title mb-0">Perjalanan Dinas Terbaru</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive text-nowrap">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Pegawai</th>
                                    <th>Tujuan</th>
                                    <th>Tanggal</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($recentPerjalanan as $perjalanan)
                                    <tr>
                                        <td>{{ $perjalanan->pegawai->pluck('nama_lengkap')->join(', ') ?? '-' }}</td>
                                        <td>{{ $perjalanan->tujuan ?? '-' }}</td>
                                        <td>{{ \Carbon\Carbon::parse($perjalanan->tgl_berangkat)->format('d M Y') }}</td>
                                        <td>
                                            @if (now()->between($perjalanan->tgl_berangkat, $perjalanan->tgl_kembali))
                                                <span class="badge bg-label-success">Aktif</span>
                                            @else
                                                <span class="badge bg-label-secondary">Selesai</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">Belum ada data</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
