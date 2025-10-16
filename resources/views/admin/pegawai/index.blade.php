@extends('layouts.app')

@section('title', 'Manajemen Pegawai')

@push('page-css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
@endpush

@section('content')
<div class="card">
    <h5 class="card-header">Data Pegawai</h5>
    <div class="card-body">
        <div class="d-flex justify-content-between mb-3">
        <a href="{{ route('admin.pegawai.create') }}" class="btn btn-primary">Tambah Pegawai</a>
        <a href="{{ route('admin.pegawai.export.pdf') }}" class="btn btn-success">
            <i class="fas fa-file-pdf"></i> Export PDF
        </a>
    </div>
        <div class="table-responsive">
            <table class="table table-bordered" id="pegawai-table">
                <thead>
                    <tr>
                        <th>NIP</th>
                        <th>Nama Lengkap</th>
                        <th>Jabatan</th>
                        <th>Golongan</th>
                        <th>Unit Kerja</th>
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
            var table = $('#pegawai-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route("admin.pegawai.index") }}',
                columns: [
                    { data: 'NIP', name: 'NIP' },
                    { data: 'nama_lengkap', name: 'nama_lengkap' },
                    { data: 'jabatan.nama', name: 'jabatan.nama' },
                    { data: 'golongan.nama', name: 'golongan.nama' },
                    { data: 'unit_kerja.nama', name: 'unitKerja.nama' },
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
                        $('#pegawai-table').DataTable().ajax.reload();
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
