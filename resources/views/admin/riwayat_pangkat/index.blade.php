@extends('layouts.app')

@section('title', 'Manajemen Riwayat Pangkat')

@push('page-css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
@endpush

@section('content')
<div class="card">
    <h5 class="card-header">Data Riwayat Pangkat</h5>
    <div class="card-body">
        <a href="{{ route('admin.riwayat_pangkat.create') }}" class="btn btn-primary mb-3">Tambah Riwayat Pangkat</a>
        <div class="table-responsive">
            <table class="table table-bordered" id="riwayat-pangkat-table">
                <thead>
                    <tr>
                        <th>Nama Pegawai</th>
                        <th>Golongan</th>
                        <th>Nomor SK</th>
                        <th>Tanggal SK</th>
                        <th>TMT Pangkat</th>
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
            var table = $('#riwayat-pangkat-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route("admin.riwayat_pangkat.index") }}',
                columns: [
                    { data: 'pegawai.nama_lengkap', name: 'pegawai.nama_lengkap' },
                    { data: 'golongan.nama', name: 'golongan.nama' },
                    { data: 'nomor_sk', name: 'nomor_sk' },
                    { data: 'tanggal_sk', name: 'tanggal_sk' },
                    { data: 'tmt_pangkat', name: 'tmt_pangkat' },
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
                        $('#riwayat-pangkat-table').DataTable().ajax.reload();
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