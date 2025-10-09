@extends('layouts.app')

@section('title', 'Verifikasi Laporan Perjalanan Dinas')

@push('page-css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        .status-badge {
            padding: 0.4rem 0.8rem;
            border-radius: 0.25rem;
            font-size: 0.875rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.025em;
        }
        .action-buttons {
            white-space: nowrap;
        }
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
        }
        .stats-card {
            border-left: 4px solid #667eea;
            background: #f8f9fa;
        }
    </style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card stats-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Menunggu Verifikasi</h6>
                            <h4 class="mb-0" id="pending-count">0</h4>
                        </div>
                        <div class="text-warning">
                            <i class="fas fa-clock fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stats-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Disetujui Hari Ini</h6>
                            <h4 class="mb-0" id="approved-count">0</h4>
                        </div>
                        <div class="text-success">
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stats-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Ditolak Hari Ini</h6>
                            <h4 class="mb-0" id="rejected-count">0</h4>
                        </div>
                        <div class="text-danger">
                            <i class="fas fa-times-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Verification Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-clipboard-check me-2"></i>
                Verifikasi Laporan Perjalanan Dinas
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="verification-table">
                    <thead class="table-light">
                        <tr>
                            <th>Surat Tugas</th>
                            <th>Nama Pegawai</th>
                            <th>Tempat Tujuan</th>
                            <th>Tanggal Upload</th>
                            <th>Tanggal Perjalanan</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Verification Modal -->
<div class="modal fade" id="verificationModal" tabindex="-1" aria-labelledby="verificationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="verificationModalLabel">
                    <span id="modal-action-icon"></span>
                    <span id="modal-action-text"></span> Laporan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="verificationForm">
                <div class="modal-body">
                    <input type="hidden" id="laporan-id" name="laporan_id">
                    <input type="hidden" id="verification-action" name="action">
                    
                    <!-- Laporan Details -->
                    <div class="alert alert-info">
                        <h6 class="alert-heading">Detail Laporan:</h6>
                        <p class="mb-1"><strong>No. Surat Tugas:</strong> <span id="detail-surat-tugas"></span></p>
                        <p class="mb-1"><strong>Nama Pegawai:</strong> <span id="detail-pegawai"></span></p>
                        <p class="mb-0"><strong>Tempat Tujuan:</strong> <span id="detail-tujuan"></span></p>
                    </div>

                    <!-- Catatan/Alasan -->
                    <div id="catatan-section">
                        <label for="catatan-text" class="form-label">
                            <span id="catatan-label"></span>
                        </label>
                        <textarea class="form-control" id="catatan-text" name="catatan" rows="3" 
                                  placeholder="Masukkan catatan verifikasi..."></textarea>
                        <div class="form-text">
                            <span id="catatan-hint"></span>
                        </div>
                    </div>

                    <!-- Confirmation Warning -->
                    <div class="alert alert-warning mt-3">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <span id="warning-message"></span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn" id="submit-btn">
                        <i class="fas fa-check me-1"></i>
                        <span id="submit-text"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('page-js')
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(function() {
            // Initialize DataTable
            var verificationTable = $('#verification-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route("keuangan.laporan_pd.verification") }}',
                language: {
                    "processing": '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>',
                    "emptyTable": "Tidak ada laporan yang menunggu verifikasi."
                },
                columns: [
                    { data: 'perjalananDinas.nomor_surat_tugas', name: 'perjalananDinas.nomor_surat_tugas' },
                    { data: 'pegawai.nama_lengkap', name: 'pegawai.nama_lengkap' },
                    { data: 'perjalananDinas.tempat_tujuan', name: 'perjalananDinas.tempat_tujuan' },
                    { data: 'tgl_unggah', name: 'tgl_unggah' },
                    { 
                        data: null,
                        name: 'perjalanan',
                        render: function(data, type, row) {
                            var berangkat = row.perjalananDinas.tgl_berangkat ? 
                                new Date(row.perjalananDinas.tgl_berangkat).toLocaleDateString('id-ID') : '-';
                            var kembali = row.perjalananDinas.tgl_kembali ? 
                                new Date(row.perjalananDinas.tgl_kembali).toLocaleDateString('id-ID') : '-';
                            return berangkat + ' - ' + kembali;
                        }
                    },
                    { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' }
                ],
                initComplete: function() {
                    loadStatistics();
                }
            });

            // Load statistics
            function loadStatistics() {
                $.ajax({
                    url: '{{ route("keuangan.laporan_pd.verificationStats") }}',
                    method: 'GET',
                    success: function(data) {
                        $('#pending-count').text(data.pending || 0);
                        $('#approved-count').text(data.approved_today || 0);
                        $('#rejected-count').text(data.rejected_today || 0);
                    }
                });
            }
        });

        // Verification functions
        function verifyLaporan(url, id) {
            showVerificationModal(id, 'verify', url);
        }

        function rejectLaporan(url, id) {
            showVerificationModal(id, 'reject', url);
        }

        function showVerificationModal(id, action, url) {
            // Reset form
            $('#verificationForm')[0].reset();
            $('#laporan-id').val(id);
            $('#verification-action').val(action);

            if (action === 'verify') {
                $('#modal-action-icon').html('✅');
                $('#modal-action-text').text('Setujui');
                $('#catatan-label').text('Catatan Verifikasi (Opsional):');
                $('#catatan-hint').text('Tambahkan catatan jika diperlukan.');
                $('#warning-message').text('Apakah Anda yakin ingin menyetujui laporan ini? Proses ini akan mengirim notifikasi ke Pimpinan.');
                $('#submit-btn').removeClass('btn-danger').addClass('btn-success').html('<i class="fas fa-check me-1"></i>Setujui');
                $('#submit-text').text('Setujui');
                $('#catatan-section').find('textarea').prop('required', false);
            } else {
                $('#modal-action-icon').html('❌');
                $('#modal-action-text').text('Tolak');
                $('#catatan-label').text('Alasan Penolakan:');  
                $('#catatan-hint').text('Harap berikan alasan mengapa laporan ini ditolak.');
                $('#warning-message').text('Apakah Anda yakin ingin menolak laporan ini? Proses ini akan mengirim notifikasi ke Pimpinan.');
                $('#submit-btn').removeClass('btn-success').addClass('btn-danger').html('<i class="fas fa-times me-1"></i>Tolak');
                $('#submit-text').text('Tolak');
                $('#catatan-section').find('textarea').prop('required', true);
            }

            // Load laporan details (this would need an API endpoint)
            loadLaporanDetails(id);

            // Show modal
            var modal = new bootstrap.Modal(document.getElementById('verificationModal'));
            modal.show();
        }

        function loadLaporanDetails(id) {
            // This should be replaced with actual API call
            // For now, loading from table data
            var table = $('#verification-table').DataTable();
            var data = table.row(function(idx, d, node) {
                return d.id == id; // Assuming there's an id column
            }).data();
            
            if (data) {
                $('#detail-surat-tugas').text(data.perjalananDinas?.nomor_surat_tugas || 'N/A');
                $('#detail-pegawai').text(data.pegawai?.nama_lengkap || 'N/A');
                $('#detail-tujuan').text(data.perjalananDinas?.tempat_tujuan || 'N/A');
            }
        }

        // Handle form submission
        $('#verificationForm').on('submit', function(e) {
            e.preventDefault();
            
            var formData = new FormData(this);
            var action = $('#verification-action').val();
            var id = $('#laporan-id').val();
            
            var url = action === 'verify' ? 
                '{{ route("keuangan.laporan_pd.verify", ":id") }}'.replace(':id', id) :
                '{{ route("keuangan.laporan_pd.reject", ":id") }}'.replace(':id', id);
            
            // Append additional fields
            if (action === 'verify') {
                formData.append('catatan_verifikasi', $('#catatan-text').val());
            } else {
                formData.append('alasan_penolakan', $('#catatan-text').val());
            }
            formData.append('_token', '{{ csrf_token() }}');

            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    // Close modal
                    bootstrap.Modal.getInstance(document.getElementById('verificationModal')).hide();
                    
                    // Show success message
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response.message,
                        timer: 3000,
                        showConfirmButton: false
                    });
                    
                    // Reload table
                    $('#verification-table').DataTable().ajax.reload();
                    
                    // Reload statistics
                    loadStatistics();
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: xhr.responseJSON?.message || 'Terjadi kesalahan.',
                        confirmButtonColor: '#d33'
                    });
                }
            });
        });
    </script>
@endpush
