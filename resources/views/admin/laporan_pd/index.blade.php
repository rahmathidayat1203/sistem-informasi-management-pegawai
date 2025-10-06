@extends('layouts.app')

@section('title', 'Manajemen Laporan Perjalanan Dinas')

@push('page-css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
@endpush

@section('content')
<div class="card">
    <h5 class="card-header">Data Laporan Perjalanan Dinas</h5>
    <div class="card-body">
        <a href="{{ route('admin.laporan_pd.create') }}" class="btn btn-primary mb-3">Tambah Laporan PD</a>
        <div class="table-responsive">
            <table class="table table-bordered" id="laporan-pd-table">
                <thead>
                    <tr>
                        <th>Nomor Surat Tugas</th>
                        <th>Tanggal Upload</th>
                        <th>Status Verifikasi</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
@endsection

@push('page-js')
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(function() {
            var table = $('#laporan-pd-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route("admin.laporan_pd.index") }}',
                columns: [
                    { data: 'perjalananDinas.nomor_surat_tugas', name: 'perjalananDinas.nomor_surat_tugas' },
                    { data: 'tgl_unggah', name: 'tgl_unggah' },
                    { data: 'status_verifikasi', name: 'status_verifikasi' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ]
            });
        });

        function deleteData(url) {
            if (confirm('Anda yakin ingin menghapus data ini?')) {
                $.ajax({
                    url: url,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        $('#laporan-pd-table').DataTable().ajax.reload();
                        alert('Data berhasil dihapus.');
                    },
                    error: function(xhr) {
                        alert('Gagal menghapus data.');
                    }
                });
            }
        }
    </script>
@endpush