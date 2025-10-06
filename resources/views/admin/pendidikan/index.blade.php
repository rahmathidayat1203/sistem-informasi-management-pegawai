@extends('layouts.app')

@section('title', 'Manajemen Pendidikan')

@push('page-css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
@endpush

@section('content')
<div class="card">
    <h5 class="card-header">Data Pendidikan</h5>
    <div class="card-body">
        <a href="{{ route('admin.pendidikan.create') }}" class="btn btn-primary mb-3">Tambah Pendidikan</a>
        <div class="table-responsive">
            <table class="table table-bordered" id="pendidikan-table">
                <thead>
                    <tr>
                        <th>Nama Pegawai</th>
                        <th>Jenjang</th>
                        <th>Nama Institusi</th>
                        <th>Tahun Lulus</th>
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
            var table = $('#pendidikan-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route("admin.pendidikan.index") }}',
                columns: [
                    { data: 'pegawai.nama_lengkap', name: 'pegawai.nama_lengkap' },
                    { data: 'jenjang', name: 'jenjang' },
                    { data: 'nama_institusi', name: 'nama_institusi' },
                    { data: 'tahun_lulus', name: 'tahun_lulus' },
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
                        $('#pendidikan-table').DataTable().ajax.reload();
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