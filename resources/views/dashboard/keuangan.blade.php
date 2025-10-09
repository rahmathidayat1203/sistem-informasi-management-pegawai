@extends('layouts.app')

@section('title', 'Dashboard Admin Keuangan')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">Dashboard Admin Keuangan</h4>
    
    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h3 class="mb-2">{{ $stats['laporan_pending'] }}</h3>
                                    <p class="mb-0">Laporan Menunggu Verifikasi</p>
                                </div>
                                <div class="avatar">
                                    <div class="avatar-initial bg-label-warning rounded-2">
                                        <i class="ti ti-file-text ti-md text-warning"></i>
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
                                    <h3 class="mb-2">{{ $stats['laporan verified_bulan_ini'] }}</h3>
                                    <p class="mb-0">Laporan Verified Bulan Ini</p>
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
                                    <h3 class="mb-2">{{ $stats['perjalanan_dinas_selesai'] }}</h3>
                                    <p class="mb-0">Perjalanan Selesai</p>
                                </div>
                                <div class="avatar">
                                    <div class="avatar-initial bg-label-secondary rounded-2">
                                        <i class="ti ti-checklist ti-md text-secondary"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Action -->
    <div class="row g-4 mb-4">
        <div class="col-12">
            <a href="{{ route('keuangan.laporan_pd.verification') }}" class="btn btn-warning btn-lg w-100">
                <i class="ti ti-file-text me-2"></i>
                Verifikasi Laporan Perjalanan Dinas ({{ $stats['laporan_pending'] }})
            </a>
        </div>
    </div>

    <!-- Pending Verifications -->
    <div class="row g-6">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Laporan Perjalanan Dinas Menunggu Verifikasi</h5>
                    <a href="{{ route('keuangan.laporan_pd.verification') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive text-nowrap">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Pegawai</th>
                                    <th>Tujuan</th>
                                    <th>Tanggal</th>
                                    <th>Biaya</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($pendingLaporan as $laporan)
                                    <tr>
                                        <td>{{ $laporan->perjalananDinas->pegawai->pluck('nama_lengkap')->join(', ') }}</td>
                                        <td>{{ $laporan->perjalananDinas->tujuan }}</td>
                                        <td>{{ \Carbon\Carbon::parse($laporan->perjalananDinas->tgl_berangkat)->format('d M Y') }}</td>
                                        <td>{{ format_currency($laporan->biaya) }}</td>
                                        <td>
                                            @switch($laporan->status_verifikasi)
                                                @case('Diajukan')
                                                    <span class="badge bg-label-warning">Menunggu Verifikasi</span>
                                                @break
                                                @case('Disetujui')
                                                    <span class="badge bg-label-success">Disetujui</span>
                                                @break
                                                @case('Ditolak')
                                                    <span class="badge bg-label-danger">Ditolak</span>
                                                @break
                                            @endswitch
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="#" onclick="verifyLaporan({{ $laporan->id }})" class="btn btn-sm btn-success">
                                                    <i class="ti ti-check"></i> Verify
                                                </a>
                                                <a href="#" onclick="rejectLaporan({{ $laporan->id }})" class="btn btn-sm btn-danger">
                                                    <i class="ti ti-x"></i> Reject
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">Tidak ada laporan menunggu verifikasi</td>
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

<script>
function verifyLaporan(laporanId) {
    if (confirm('Verifikasi laporan ini?')) {
        fetch(`/keuangan/laporan-pd/${laporanId}/verify`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.message) {
                alert(data.message);
                location.reload();
            }
        })
        .catch(error => {
            alert('Terjadi kesalahan: ' + error.message);
        });
    }
}

function rejectLaporan(laporanId) {
    const alasan = prompt('Alasan penolakan:');
    if (alasan) {
        fetch(`/keuangan/laporan-pd/${laporanId}/reject`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ alasan_penolakan: alasan })
        })
        .then(response => response.json())
        .then(data => {
            if (data.message) {
                alert(data.message);
                location.reload();
            }
        })
        .catch(error => {
            alert('Terjadi kesalahan: ' + error.message);
        });
    }
}
</script>
@endsection
