@extends('layouts.app')

@section('title', 'Manajemen Riwayat Jabatan')

@push('page-css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
@endpush

@section('content')
<div class="card">
    <h5 class="card-header">Data Riwayat Jabatan</h5>
    <div class="card-body">
        <a href="{{ route('admin.riwayat_jabatan.create') }}" class="btn btn-primary mb-3">Tambah Riwayat Jabatan</a>
        <div class="table-responsive">
            <table class="table table-bordered" id="riwayat-jabatan-table">
                <thead>
                    <tr>
                        <th>Nama Pegawai</th>
                        <th>Jabatan</th>
                        <th>Unit Kerja</th>
                        <th>Jenis Jabatan</th>
                        <th>Nomor SK</th>
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
            var table = $('#riwayat-jabatan-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route("admin.riwayat_jabatan.index") }}',
                columns: [
                    { data: 'pegawai.nama_lengkap', name: 'pegawai.nama_lengkap' },
                    { data: 'jabatan.nama', name: 'jabatan.nama' },
                    { data: 'unitKerja.nama', name: 'unitKerja.nama' },
                    { data: 'jenis_jabatan', name: 'jenis_jabatan' },
                    { data: 'nomor_sk', name: 'nomor_sk' },
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
                        $('#riwayat-jabatan-table').DataTable().ajax.reload();
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