@extends('layouts.app')

@section('title', 'Manajemen Keluarga')

@push('page-css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
@endpush

@section('content')
<div class="card">
    <h5 class="card-header">Data Keluarga</h5>
    <div class="card-body">
        <a href="{{ route('admin.keluarga.create') }}" class="btn btn-primary mb-3">Tambah Keluarga</a>
        <div class="table-responsive">
            <table class="table table-bordered" id="keluarga-table">
                <thead>
                    <tr>
                        <th>Nama Pegawai</th>
                        <th>Nama Keluarga</th>
                        <th>Hubungan</th>
                        <th>Jenis Kelamin</th>
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
            var table = $('#keluarga-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route("admin.keluarga.index") }}',
                columns: [
                    { data: 'pegawai.nama_lengkap', name: 'pegawai.nama_lengkap' },
                    { data: 'nama_lengkap', name: 'nama_lengkap' },
                    { data: 'hubungan', name: 'hubungan' },
                    { data: 'jenis_kelamin', name: 'jenis_kelamin' },
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
                        $('#keluarga-table').DataTable().ajax.reload();
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