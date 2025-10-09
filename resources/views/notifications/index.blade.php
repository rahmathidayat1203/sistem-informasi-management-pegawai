@extends('layouts.app')

@section('title', 'Notifikasi Sistem')

@push('page-css')
<style>
    .notification-item {
        transition: all 0.3s ease;
        border-left: 4px solid transparent;
    }
    
    .notification-item:hover {
        transform: translateX(2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    
    .notification-item.unread {
        border-left-color: #667eea;
        background-color: #f8f9ff;
    }
    
    .notification-item.success {
        border-left-color: #28a745;
    }
    
    .notification-item.danger {
        border-left-color: #dc3545;
    }
    
    .notification-item.warning {
        border-left-color: #ffc107;
    }
    
    .notification-item.info {
        border-left-color: #17a2b8;
    }
    
    .notification-badge {
        position: absolute;
        top: -5px;
        right: -5px;
        min-width: 20px;
        height: 20px;
        border-radius: 10px;
        font-size: 0.75rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0 6px;
    }
    
    .notification-time {
        font-size: 0.875rem;
        color: #6c757d;
    }
    
    .notification-content {
        line-height: 1.4;
    }
    
    .notification-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
    }
    
    .filter-btn {
        transition: all 0.2s ease;
    }
    
    .filter-btn:hover {
        transform: translateY(-2px);
    }
    
    .filter-btn.active {
        background-color: #667eea !important;
        border-color: #667eea !important;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">
                <i class="fas fa-bell me-2 text-primary"></i>
                Pusat Notifikasi
            </h4>
            <p class="text-muted mb-0">Kelola semua notifikasi sistem Anda</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-primary" onclick="markAllAsRead()">
                <i class="fas fa-check-double me-1"></i>
                Tandai semua dibaca
            </button>
            <button class="btn btn-outline-danger" onclick="clearReadNotifications()">
                <i class="fas fa-trash me-1"></i>
                Hapus yang dibaca
            </button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-primary mb-2">
                        <i class="fas fa-envelope fa-2x"></i>
                    </div>
                    <h5 class="card-title mb-0" id="total-count">0</h5>
                    <p class="card-text text-muted">Total Notifikasi</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-warning mb-2">
                        <i class="fas fa-envelope-open-text fa-2x"></i>
                    </div>
                    <h5 class="card-title mb-0" id="unread-count">0</h5>
                    <p class="card-text text-muted">Belum Dibaca</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-success mb-2">
                        <i class="fas fa-check-circle fa-2x"></i>
                    </div>
                    <h5 class="card-title mb-0" id="success-count">0</h5>
                    <p class="card-text text-muted">Disetujui</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-danger mb-2">
                        <i class="fas fa-times-circle fa-2x"></i>
                    </div>
                    <h5 class="card-title mb-0" id="danger-count">0</h5>
                    <p class="card-text text-muted">Ditolak</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Buttons -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex flex-wrap gap-2">
                <button class="btn filter-btn btn-outline-secondary active" onclick="filterNotifications('all')">
                    <i class="fas fa-list me-1"></i>Semua
                </button>
                <button class="btn filter-btn btn-outline-warning" onclick="filterNotifications('unread')">
                    <i class="fas fa-envelope me-1"></i>Belum Dibaca
                </button>
                <button class="btn filter-btn btn-outline-success" onclick="filterNotifications('success')">
                    <i class="fas fa-check-circle me-1"></i>Disetujui
                </button>
                <button class="btn filter-btn btn-outline-danger" onclick="filterNotifications('danger')">
                    <i class="fas fa-times-circle me-1"></i>Ditolak
                </button>
                <button class="btn filter-btn btn-outline-info" onclick="filterNotifications('info')">
                    <i class="fas fa-info-circle me-1"></i>Informasi
                </button>
            </div>
        </div>
    </div>

    <!-- Notifications List -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div id="notifications-container">
                @forelse($notifications as $notification)
                    @php
                        $data = json_decode($notification->data, true);
                        $color = $data['color'] ?? 'info';
                    @endphp
                    
                    <div class="notification-item p-3 border-bottom position-relative {{ $notification->read_at ? '' : 'unread' }} {{ $color }}"
                         onclick="handleNotificationClick({{ $notification->id }}, '{{ $data['action_url'] ?? '#' }}')"
                         data-notification-id="{{ $notification->id }}"
                         data-color="{{ $color }}">
                        
                        <div class="d-flex align-items-start">
                            <div class="notification-icon me-3 bg-{{ $color }}-light text-{{ $color }}">
                                <i class="fas fa-{{ $data['icon'] ?? 'bell' }}"></i>
                            </div>
                            
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <h6 class="mb-0 fw-bold">{{ $data['title'] ?? 'Notifikasi' }}</h6>
                                    @if(!$notification->read_at)
                                        <span class="notification-badge bg-{{ $color }} text-white">Baru</span>
                                    @endif
                                </div>
                                
                                <p class="notification-content mb-2">{{ $data['message'] ?? 'Tidak ada pesan' }}</p>
                                
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="notification-time">
                                        <i class="far fa-clock me-1"></i>
                                        {{ $notification->created_at->diffForHumans() }}
                                    </small>
                                    
                                    <div class="d-flex gap-1">
                                        @if(!$notification->read_at)
                                            <button class="btn btn-sm btn-outline-primary" 
                                                    onclick="event.stopPropagation(); markAsRead({{ $notification->id }})"
                                                    title="Tandai Dibaca">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        @endif
                                        
                                        <button class="btn btn-sm btn-outline-danger" 
                                                onclick="event.stopPropagation(); deleteNotification({{ $notification->id }})"
                                                title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5">
                        <div class="text-muted mb-3">
                            <i class="fas fa-bell-slash fa-3x"></i>
                        </div>
                        <h5>Belum ada notifikasi</h5>
                        <p class="text-muted">Notifikasi akan muncul di sini ketika ada aktivitas terkait akun Anda.</p>
                    </div>
                @endforelse
            </div>
            
            <!-- Pagination -->
            @if($notifications->hasPages())
                <div class="p-3 border-top">
                    {{ $notifications->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Loading Overlay -->
<div id="loading-overlay" class="d-none position-fixed top-0 start-0 w-100 h-100 bg-dark bg-opacity-50 d-flex justify-content-center align-items-center" style="z-index: 9999;">
    <div class="spinner-border text-light" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
</div>
@endsection

@push('page-js')
<script>
$(function() {
    loadStatistics();
    
    // Set initial filter
    window.currentFilter = 'all';
});

function loadStatistics() {
    const container = $('#notifications-container');
    const notifications = container.find('.notification-item');
    
    let total = 0, unread = 0, success = 0, danger = 0;
    
    notifications.each(function() {
        total++;
        if ($(this).hasClass('unread')) unread++;
        if ($(this).hasClass('success')) success++;
        if ($(this).hasClass('danger')) danger++;
    });
    
    $('#total-count').text(total);
    $('#unread-count').text(unread);
    $('#success-count').text(success);
    $('#danger-count').text(danger);
}

function filterNotifications(type) {
    window.currentFilter = type;
    
    // Update active button
    $('.filter-btn').removeClass('active');
    $(`.filter-btn[onclick="filterNotifications('${type}')"]`).addClass('active');
    
    // Filter notifications
    const notifications = $('.notification-item');
    
    if (type === 'all') {
        notifications.show();
    } else if (type === 'unread') {
        notifications.each(function() {
            $(this).toggleClass('d-none', !$(this).hasClass('unread'));
        });
    } else {
        notifications.each(function() {
            $(this).toggleClass('d-none', !$(this).hasClass(type));
        });
    }
    
    updateEmptyState();
}

function updateEmptyState() {
    const visibleNotifications = $('.notification-item:not(.d-none)');
    const emptyState = visibleNotifications.length === 0;
    
    if (emptyState) {
        let message = '';
        switch(window.currentFilter) {
            case 'unread':
                message = 'Tidak ada notifikasi yang belum dibaca.';
                break;
            case 'success':
                message = 'Tidak ada notifikasi yang disetujui.';
                break;
            case 'danger':
                message = 'Tidak ada notifikasi yang ditolak.';
                break;
            case 'info':
                message = 'Tidak ada notifikasi informasi.';
                break;
            default:
                message = 'Belum ada notifikasi.';
        }
        
        if (!$('#empty-state').length) {
            $('#notifications-container').html(`
                <div id="empty-state" class="text-center py-5">
                    <div class="text-muted mb-3">
                        <i class="fas fa-bell-slash fa-3x"></i>
                    </div>
                    <h5>${message}</h5>
                    <p class="text-muted">Coba ubah filter atau periksa kembali nanti.</p>
                </div>
            `);
        }
    } else {
        $('#empty-state').remove();
    }
}

function handleNotificationClick(id, actionUrl) {
    // Mark as read
    markAsRead(id);
    
    // Navigate to action URL if provided
    if (actionUrl && actionUrl !== '#') {
        setTimeout(() => {
            window.location.href = actionUrl;
        }, 300);
    }
}

function markAsRead(notificationId) {
    showLoading();
    
    let url = notificationId ? 
        `{{ route('notifications.read', ':id') }}`.replace(':id', notificationId) :
        '{{ route('notifications.read-all') }}';
    
    $.ajax({
        url: url,
        type: 'POST',
        data: { _token: '{{ csrf_token() }}' },
        success: function(response) {
            if (notificationId) {
                $(`.notification-item[data-notification-id="${notificationId}"]`).removeClass('unread')
                    .find('.notification-badge').remove();
            } else {
                $('.notification-item').removeClass('unread')
                    .find('.notification-badge').remove();
            }
            
            loadStatistics();
            hideLoading();
            
            Swal.fire({
                icon: 'success',
                title: notificationId ? 'Notifikasi ditandai dibaca' : 'Semua notifikasi ditandai dibaca',
                timer: 1500,
                showConfirmButton: false
            });
        },
        error: function() {
            hideLoading();
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: 'Terjadi kesalahan, silakan coba lagi.',
                confirmButtonColor: '#d33'
            });
        }
    });
}

function deleteNotification(notificationId) {
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: 'Notifikasi ini akan dihapus secara permanen.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Hapus',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            showLoading();
            
            $.ajax({
                url: `{{ route('notifications.destroy', ':id') }}`.replace(':id', notificationId),
                type: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: function(response) {
                    $(`.notification-item[data-notification-id="${notificationId}"]`).fadeOut(300, function() {
                        $(this).remove();
                        loadStatistics();
                        updateEmptyState();
                    });
                    
                    hideLoading();
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Dihapus!',
                        text: 'Notifikasi berhasil dihapus.',
                        timer: 1500,
                        showConfirmButton: false
                    });
                },
                error: function() {
                    hideLoading();
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Terjadi kesalahan, silakan coba lagi.',
                        confirmButtonColor: '#d33'
                    });
                }
            });
        }
    });
}

function clearReadNotifications() {
    const readNotifications = $('.notification-item:not(.unread)');
    
    if (readNotifications.length === 0) {
        Swal.fire({
            icon: 'info',
            title: 'Tidak ada notifikasi yang dibaca',
            text: 'Tidak ada notifikasi yang bisa dihapus.',
            confirmButtonColor: '#3085d6'
        });
        return;
    }
    
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: `Semua notifikasi yang sudah dibaca (${readNotifications.length}) akan dihapus.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Hapus Semua',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            showLoading();
            
            readNotifications.each(function() {
                const id = $(this).data('notification-id');
                deleteNotification(id);
            });
            
            hideLoading();
        }
    });
}

function showLoading() {
    $('#loading-overlay').removeClass('d-none');
}

function hideLoading() {
    $('#loading-overlay').addClass('d-none');
}
</script>
@endpush
