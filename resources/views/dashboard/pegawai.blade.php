@extends('layouts.app')

@section('title', 'Dashboard Pegawai')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">Dashboard Pegawai</h4>
    
    @if (isset($error))
        <div class="alert alert-danger">
            {{ $error }}
        </div>
        @else
        <!-- User Info -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="avatar me-4">
                        @if ($pegawai->foto)
                            <img src="{{ Storage::url($pegawai->foto) }}" alt="Foto" class="rounded-circle" width="100" height="100">
                        @else
                            <div class="avatar-initial rounded-circle bg-primary text-white">
                                {{ strtoupper(substr($pegawai->nama_lengkap, 0, 1)) }}
                            </div>
                        @endif
                    </div>
                    <div>
                        <h5 class="mb-1">{{ $pegawai->nama_lengkap }}</h5>
                        <p class="mb-0">{{ $pegawai->nip ?? '-' }}</p>
                        <p class="mb-0 text-muted">{{ $pegawai->jabatan->nama ?? '-' }} | {{ $pegawai->golongan->nama ?? '-' }}</p>
                    </div>
                    <div class="ms-auto">
                        <a href="{{ route('pegawai.profile.edit') }}" class="btn btn-primary">
                            <i class="ti ti-edit me-1"></i> Edit Profile
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Stats Cards -->
        <div class="row g-4 mb-4">
            <div class="col-xl-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between">
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h3 class="mb-2">{{ $stats['total_sisa_cuti'] }}</h3>
                                        <p class="mb-0">Total Sisa Cuti</p>
                                    </div>
                                    <div class="avatar">
                                        <div class="avatar-initial bg-label-primary rounded-2">
                                            <i class="ti ti-calendar ti-md text-primary"></i>
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
                                            <i class="ti ti-clock ti-md text-warning"></i>
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
                                        <h3 class="mb-2">{{ $stats['cuti_disetujui_tahun_ini'] }}</h3>
                                        <p class="mb-0">Cuti Disetujui Tahun Ini</p>
                                    </div>
                                    <div class="avatar">
                                        <div class="avatar-initial bg-label-success rounded-2">
                                            <i class="ti ti-check ti-md text-success"></i>
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
                                        <p class="mb-0">Tugas Aktif</p>
                                    </div>
                                    <div class="avatar">
                                        <div class="avatar-initial bg-label-info rounded-2">
                                            <i class="ti ti-map-pin ti-md text-info"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <a href="{{ route('pegawai.cuti.my') }}" class="btn btn-primary btn-lg w-100">
                    <i class="ti ti-calendar me-2"></i>
                    Riwayat Cuti
                </a>
            </div>
            <div class="col-md-4">
                <a href="{{ route('pegawai.perjalanan_dinas.my') }}" class="btn btn-info btn-lg w-100">
                    <i class="ti ti-plane me-2"></i>
                    Tugas Saya
                </a>
            </div>
            <div class="col-md-4">
                <a href="{{ route('pegawai.statistics') }}" class="btn btn-secondary btn-lg w-100">
                    <i class="ti ti-chart-pie me-2"></i>
                    Statistik
                </a>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="row g-6">
            <!-- Recent Cuti -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Cuti Terbaru</h5>
                        <a href="{{ route('pegawai.cuti.my') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive text-nowrap">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Jenis Cuti</th>
                                        <th>Tanggal</th>
                                        <th>Lama</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($myCuti as $cuti)
                                        <tr>
                                            <td>{{ $cuti->jenisCuti->nama }}</td>
                                            <td>{{ \Carbon\Carbon::parse($cuti->tgl_mulai)->format('d M Y') }}</td>
                                            <td>{{ \Carbon\Carbon::parse($cuti->tgl_mulai)->diffInDays($cuti->tgl_selesai) + 1 }} hari</td>
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
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center">Belum ada data cuti</td>
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
                    <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Perjalanan Dinas Terbaru</h5>
                        <a href="{{ route('pegawai.perjalanan_dinas.my') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive text-nowrap">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Tujuan</th>
                                        <th>Tanggal Berangkat</th>
                                        <th>Tanggal Kembali</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($myPerjalanan as $perjalanan)
                                        <tr>
                                            <td>{{ $perjalanan->tujuan }}</td>
                                            <td>{{ \Carbon\Carbon::parse($perjalanan->tgl_berangkat)->format('d M Y') }}</td>
                                            <td>{{ \Carbon\Carbon::parse($perjalanan->tgl_kembali)->format('d M Y') }}</td>
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
                                            <td colspan="4" class="text-center">Belum ada data perjalanan dinas</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
