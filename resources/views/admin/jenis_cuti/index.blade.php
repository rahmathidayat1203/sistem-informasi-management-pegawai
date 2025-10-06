@extends('layouts.app')

@section('title', 'Manajemen Jenis Cuti')

@push('page-css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
@endpush

@section('content')
<div class="card">
    <h5 class="card-header">Data Jenis Cuti</h5>
    <div class="card-body">
        <a href="{{ route('admin.jenis_cuti.create') }}" class="btn btn-primary mb-3">Tambah Jenis Cuti</a>
        <div class="table-responsive">
            <table class="table table-bordered" id="jenis-cuti-table">
                <thead>
                    <tr>
                        <th>Nama Jenis Cuti</th>
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
            var table = $('#jenis-cuti-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route("admin.jenis_cuti.index") }}',
                columns: [
                    { data: 'nama', name: 'nama' },
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
                        $('#jenis-cuti-table').DataTable().ajax.reload();
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