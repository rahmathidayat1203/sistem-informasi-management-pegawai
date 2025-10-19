@extends('layouts.app')

@section('title', 'Notifikasi Sistem')

@push('page-css')
<!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<style>
    .notification-item {
        transition: all 0.3s ease;
        border-left: 4px solid transparent;
        cursor: pointer;
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
        color: white !important;
    }
    
    .bg-info-light {
        background-color: rgba(23, 162, 184, 0.1);
    }
    
    .bg-success-light {
        background-color: rgba(40, 167, 69, 0.1);
    }
    
    .bg-warning-light {
        background-color: rgba(255, 193, 7, 0.1);
    }
    
    .bg-danger-light {
        background-color: rgba(220, 53, 69, 0.1);
    }
    
    .bg-primary-light {
        background-color: rgba(0, 123, 255, 0.1);
    }
    
    .text-info {
        color: #17a2b8;
    }
    
    .text-success {
        color: #28a745;
    }
    
    .text-warning {
        color: #ffc107;
    }
    
    .text-danger {
        color: #dc3545;
    }
    
    .text-primary {
        color: #007bff;
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
                    <h5 class="card-title mb-0" id="total-count">{{ $notifications->total() }}</h5>
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
                    <h5 class="card-title mb-0" id="unread-count">{{ auth()->user()->unreadNotifications->count() }}</h5>
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
                        $data = is_array($notification->data) ? $notification->data : json_decode($notification->data, true);
                        $color = $data['color'] ?? 'info';
                        $icon = $data['icon'] ?? 'bell';
                        $title = $data['title'] ?? 'Notifikasi';
                        $message = $data['message'] ?? 'Tidak ada pesan';
                        $actionUrl = $data['action_url'] ?? '#';
                    @endphp
                    
                    <div class="notification-item p-3 border-bottom position-relative {{ $notification->read_at ? '' : 'unread' }} {{ $color }}"
                         onclick="handleNotificationClick('{{ $notification->id }}', '{{ $actionUrl }}')"
                         data-notification-id="{{ $notification->id }}"
                         data-color="{{ $color }}">
                        
                        <div class="d-flex align-items-start">
                            <div class="notification-icon me-3 bg-{{ $color }}-light text-{{ $color }}">
                                <i class="fas fa-{{ $icon }}"></i>
                            </div>
                            
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <h6 class="mb-0 {{ $notification->read_at ? 'fw-normal' : 'fw-bold' }}">
                                        {{ $title }}
                                    </h6>
                                    @if(!$notification->read_at)
                                        <span class="notification-badge bg-{{ $color }} text-white">Baru</span>
                                    @endif
                                </div>
                                
                                <p class="notification-content mb-2 text-muted">{{ $message }}</p>
                                
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="notification-time">
                                        <i class="far fa-clock me-1"></i>
                                        {{ $notification->created_at->diffForHumans() }}
                                    </small>
                                    
                                    <div class="d-flex gap-1">
                                        @if(!$notification->read_at)
                                            <button class="btn btn-sm btn-outline-primary" 
                                                    onclick="event.stopPropagation(); markAsRead('{{ $notification->id }}')"
                                                    title="Tandai Dibaca">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        @endif
                                        
                                        <button class="btn btn-sm btn-outline-danger" 
                                                onclick="event.stopPropagation(); deleteNotification('{{ $notification->id }}')"
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

@push('page-css')
<!-- Tambahan SweetAlert2 CSS jika belum ada di layout -->
@if(!View::hasSection('has-sweetalert'))
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
@endif
@endpush

@push('page-js')
<!-- Tambahan SweetAlert2 JS jika belum ada di layout -->
@if(!View::hasSection('has-sweetalert'))
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
@endif

<script>
// Tunggu sampai semua library siap
document.addEventListener('DOMContentLoaded', function() {
    // Cek jika Swal belum tersedia
    if (typeof Swal === 'undefined') {
        console.warn('SweetAlert2 belum di-load, gunakan alert biasa');
    }
});

// CSRF Token setup for AJAX
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

$(function() {
    loadStatistics();
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
            $(this).toggle($(this).hasClass('unread'));
        });
    } else {
        notifications.each(function() {
            $(this).toggle($(this).hasClass(type));
        });
    }
    
    updateEmptyState();
}

function updateEmptyState() {
    const visibleNotifications = $('.notification-item:visible');
    const emptyState = visibleNotifications.length === 0;
    
    if (emptyState && !$('#empty-state').length) {
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
        
        $('.notification-item').hide();
        $('#notifications-container').append(`
            <div id="empty-state" class="text-center py-5">
                <div class="text-muted mb-3">
                    <i class="fas fa-bell-slash fa-3x"></i>
                </div>
                <h5>${message}</h5>
                <p class="text-muted">Coba ubah filter atau periksa kembali nanti.</p>
            </div>
        `);
    } else if (!emptyState) {
        $('#empty-state').remove();
    }
}

function handleNotificationClick(id, actionUrl) {
    markAsRead(id, function() {
        if (actionUrl && actionUrl !== '#') {
            window.location.href = actionUrl;
        }
    });
}

function markAsRead(notificationId, callback) {
    showLoading();
    
    // FIXED: Menggunakan route helper dengan parameter yang benar
    let url = '{{ route("notifications.mark-as-read", ":id") }}'.replace(':id', notificationId);
    
    $.ajax({
        url: url,
        type: 'POST',
        data: { 
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            if (response.success) {
                const $item = $(`.notification-item[data-notification-id="${notificationId}"]`);
                $item.removeClass('unread');
                $item.find('.notification-badge').remove();
                $item.find('h6').removeClass('fw-bold').addClass('fw-normal');
                $item.find('.btn-outline-primary').remove();
                
                loadStatistics();
                hideLoading();
                
                if (typeof callback === 'function') {
                    callback();
                }
            }
        },
        error: function(xhr) {
            hideLoading();
            console.error('Error marking notification as read:', xhr);
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: 'Terjadi kesalahan saat menandai notifikasi.',
                confirmButtonColor: '#d33'
            });
        }
    });
}

function markAllAsRead() {
    const unreadCount = $('.notification-item.unread').length;
    
    if (unreadCount === 0) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'info',
                title: 'Tidak ada notifikasi',
                text: 'Semua notifikasi sudah ditandai dibaca.',
                confirmButtonColor: '#3085d6'
            });
        } else {
            alert('Semua notifikasi sudah ditandai dibaca.');
        }
        return;
    }
    
    const showConfirm = () => {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Konfirmasi',
                text: `Tandai ${unreadCount} notifikasi sebagai dibaca?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Tandai Semua',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    performMarkAllAsRead();
                }
            });
        } else {
            if (confirm(`Tandai ${unreadCount} notifikasi sebagai dibaca?`)) {
                performMarkAllAsRead();
            }
        }
    };
    
    showConfirm();
}

function performMarkAllAsRead() {
    showLoading();
    
    $.ajax({
        url: '{{ route("notifications.mark-all-as-read") }}',
        type: 'POST',
        data: { _token: '{{ csrf_token() }}' },
        success: function(response) {
            if (response.success) {
                $('.notification-item').removeClass('unread');
                $('.notification-item .notification-badge').remove();
                $('.notification-item h6').removeClass('fw-bold').addClass('fw-normal');
                $('.notification-item .btn-outline-primary').remove();
                
                loadStatistics();
                hideLoading();
                
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Semua notifikasi telah ditandai dibaca.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else {
                    alert('Semua notifikasi telah ditandai dibaca.');
                }
            }
        },
        error: function(xhr) {
            hideLoading();
            console.error('Error:', xhr);
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: 'Terjadi kesalahan, silakan coba lagi.',
                    confirmButtonColor: '#d33'
                });
            } else {
                alert('Terjadi kesalahan, silakan coba lagi.');
            }
        }
    });
}

function deleteNotification(notificationId) {
    console.log('Deleting notification ID:', notificationId); // DEBUG
    
    // Gunakan confirmation modern dengan fallback
    const confirmDelete = () => {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: 'Notifikasi ini akan dihapus secara permanen.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    performDelete(notificationId);
                }
            });
        } else {
            if (confirm('Apakah Anda yakin ingin menghapus notifikasi ini?')) {
                performDelete(notificationId);
            }
        }
    };
    
    confirmDelete();
}

function performDelete(notificationId) {
    showLoading();
    
    // FIXED: Menggunakan route helper dengan parameter yang benar
    let deleteUrl = '{{ route("notifications.destroy", ":id") }}'.replace(':id', notificationId);
    console.log('Delete URL:', deleteUrl); // DEBUG
    
    $.ajax({
        url: deleteUrl,
        type: 'DELETE',
        data: { _token: '{{ csrf_token() }}' },
        success: function(response) {
            console.log('Success response:', response); // DEBUG
            
            if (response.success) {
                $(`.notification-item[data-notification-id="${notificationId}"]`).fadeOut(300, function() {
                    $(this).remove();
                    loadStatistics();
                    updateEmptyState();
                });
                
                hideLoading();
                
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Dihapus!',
                        text: response.message || 'Notifikasi berhasil dihapus.',
                        timer: 1500,
                        showConfirmButton: false
                    });
                } else {
                    alert('Notifikasi berhasil dihapus');
                }
            }
        },
        error: function(xhr) {
            hideLoading();
            console.error('Error response:', xhr); // DEBUG
            console.error('Response text:', xhr.responseText); // DEBUG
            
            let errorMessage = 'Terjadi kesalahan, silakan coba lagi.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            }
            
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: errorMessage,
                    confirmButtonColor: '#d33'
                });
            } else {
                alert('Gagal: ' + errorMessage);
            }
        }
    });
}

function clearReadNotifications() {
    const readNotifications = $('.notification-item:not(.unread)');
    
    if (readNotifications.length === 0) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'info',
                title: 'Tidak ada notifikasi',
                text: 'Tidak ada notifikasi yang sudah dibaca.',
                confirmButtonColor: '#3085d6'
            });
        } else {
            alert('Tidak ada notifikasi yang sudah dibaca.');
        }
        return;
    }
    
    const showConfirm = () => {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: `${readNotifications.length} notifikasi yang sudah dibaca akan dihapus.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Hapus Semua',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    performClearRead();
                }
            });
        } else {
            if (confirm(`${readNotifications.length} notifikasi yang sudah dibaca akan dihapus. Lanjutkan?`)) {
                performClearRead();
            }
        }
    };
    
    showConfirm();
}

function performClearRead() {
    showLoading();
    const readNotifications = $('.notification-item:not(.unread)');
    
    $.ajax({
        url: '{{ route("notifications.clear-read") }}',
        type: 'DELETE',
        data: { _token: '{{ csrf_token() }}' },
        success: function(response) {
            if (response.success) {
                readNotifications.fadeOut(300, function() {
                    $(this).remove();
                    loadStatistics();
                    updateEmptyState();
                });
                
                hideLoading();
                
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else {
                    alert(response.message || 'Notifikasi berhasil dihapus.');
                }
            }
        },
        error: function(xhr) {
            hideLoading();
            console.error('Error:', xhr);
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: 'Terjadi kesalahan, silakan coba lagi.',
                    confirmButtonColor: '#d33'
                });
            } else {
                alert('Terjadi kesalahan, silakan coba lagi.');
            }
        }
    });
}

function showLoading() {
    $('#loading-overlay').removeClass('d-none').addClass('d-flex');
}

function hideLoading() {
    $('#loading-overlay').removeClass('d-flex').addClass('d-none');
}
</script>
@endpush