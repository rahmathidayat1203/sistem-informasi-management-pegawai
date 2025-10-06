@extends('layouts.app')

@section('title', 'Manajemen Cuti')

@push('page-css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
@endpush

@section('content')
<div class="card">
    <h5 class="card-header">Data Cuti</h5>
    <div class="card-body">
        <a href="{{ route('admin.cuti.create') }}" class="btn btn-primary mb-3">Tambah Cuti</a>
        <div class="table-responsive">
            <table class="table table-bordered" id="cuti-table">
                <thead>
                    <tr>
                        <th>Nama Pegawai</th>
                        <th>Jenis Cuti</th>
                        <th>Tanggal Mulai</th>
                        <th>Tanggal Selesai</th>
                        <th>Status</th>
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
            var table = $('#cuti-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route("admin.cuti.index") }}',
                columns: [
                    { data: 'pegawai.nama_lengkap', name: 'pegawai.nama_lengkap' },
                    { data: 'jenisCuti.nama', name: 'jenisCuti.nama' },
                    { data: 'tgl_mulai', name: 'tgl_mulai' },
                    { data: 'tgl_selesai', name: 'tgl_selesai' },
                    { data: 'status_persetujuan', name: 'status_persetujuan' },
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
                        $('#cuti-table').DataTable().ajax.reload();
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