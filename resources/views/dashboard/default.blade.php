@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">Dashboard</h4>
    
    <div class="row g-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="text-center">
                        <h5 class="mb-3">Selamat Datang di Sistem Informasi Kepegawaian</h5>
                        <p class="mb-4">Dashboard Anda akan ditampilkan sesuai dengan role pengguna.</p>
                        
                        <div class="row justify-content-center">
                            <div class="col-md-8">
                                @if (auth()->user()->hasRole('Admin Kepegawaian'))
                                    <div class="alert alert-primary">
                                        <strong>Anda adalah Admin Kepegawaian</strong><br>
                                        Mengelola data master, user, dan laporan keseluruhan sistem.
                                    </div>
                                @elseif (auth()->user()->hasRole('Pimpinan'))
                                    <div class="alert alert-success">
                                        <strong>Anda adalah Pimpinan</strong><br>
                                        Menyetujui cuti, menugasi perjalanan dinas, dan memonitor aktivitas pegawai.
                                    </div>
                                @elseif (auth()->user()->hasRole('Admin Keuangan'))
                                    <div class="alert alert-warning">
                                        <strong>Anda adalah Admin Keuangan</strong><br>
                                        Memverifikasi laporan perjalanan dinas dan menangani keuangan.
                                    </div>
                                @elseif (auth()->user()->hasRole('Pegawai'))
                                    <div class="alert alert-info">
                                        <strong>Anda adalah Pegawai</strong><br>
                                        Mengelola data pribadi, mengajukan cuti, dan melihat tugas perjalanan dinas.
                                    </div>
                                @else
                                    <div class="alert alert-secondary">
                                        <strong>Role tidak diketahui</strong><br>
                                        Silakan hubungi administrator untuk konfigurasi role Anda.
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
