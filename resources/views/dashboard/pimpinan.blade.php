@extends('layouts.app')

@section('title', 'Dashboard Pimpinan')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">Dashboard Pimpinan</h4>
    
    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
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
                                    <h3 class="mb-2">{{ $stats['cuti_disetujui_bulan_ini'] }}</h3>
                                    <p class="mb-0">Cuti Disetujui Bulan Ini</p>
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
    </div>

    <!-- Quick Actions -->
    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <a href="{{ route('pimpinan.cuti.approval') }}" class="btn btn-warning btn-lg w-100">
                <i class="ti ti-calendar me-2"></i>
                Persetujuan Cuti ({{ $stats['cuti_pending'] }})
            </a>
        </div>
        <div class="col-md-6">
            <a href="{{ route('pimpinan.perjalanan_dinas.assignment') }}" class="btn btn-info btn-lg w-100">
                <i class="ti ti-plane me-2"></i>
                Penugasan Perjalanan Dinas
            </a>
        </div>
    </div>

    <!-- Pending Approvals -->
    <div class="row g-6">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Pengajuan Cuti Menunggu Persetujuan</h5>
                    <a href="{{ route('pimpinan.cuti.approval') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive text-nowrap">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Pegawai</th>
                                    <th>Jenis Cuti</th>
                                    <th>Tanggal Mulai</th>
                                    <th>Tanggal Selesai</th>
                                    <th>Lama</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($pendingCuti as $cuti)
                                    <tr>
                                        <td>{{ $cuti->pegawai->nama_lengkap }}</td>
                                        <td>{{ $cuti->jenisCuti->nama }}</td>
                                        <td>{{ \Carbon\Carbon::parse($cuti->tgl_mulai)->format('d M Y') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($cuti->tgl_selesai)->format('d M Y') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($cuti->tgl_mulai)->diffInDays($cuti->tgl_selesai) + 1 }} hari</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="#" onclick="approveCuti({{ $cuti->id }})" class="btn btn-sm btn-success">
                                                    <i class="ti ti-check"></i> Approve
                                                </a>
                                                <a href="#" onclick="rejectCuti({{ $cuti->id }})" class="btn btn-sm btn-danger">
                                                    <i class="ti ti-x"></i> Reject
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">Tidak ada pengajuan cuti menunggu persetujuan</td>
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
function approveCuti(cutiId) {
    if (confirm('Setujui pengajuan cuti ini?')) {
        fetch(`/pimpinan/cuti/${cutiId}/approve`, {
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

function rejectCuti(cutiId) {
    const alasan = prompt('Alasan penolakan:');
    if (alasan) {
        fetch(`/pimpinan/cuti/${cutiId}/reject`, {
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
