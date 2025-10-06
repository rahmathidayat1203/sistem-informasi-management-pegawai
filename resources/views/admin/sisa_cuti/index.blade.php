@extends('layouts.app')

@section('title', 'Manajemen Sisa Cuti')

@push('page-css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
@endpush

@section('content')
<div class="card">
    <h5 class="card-header">Data Sisa Cuti</h5>
    <div class="card-body">
        <a href="{{ route('admin.sisa_cuti.create') }}" class="btn btn-primary mb-3">Tambah Sisa Cuti</a>
        <div class="table-responsive">
            <table class="table table-bordered" id="sisa-cuti-table">
                <thead>
                    <tr>
                        <th>No</th> <!-- Kolom nomor urut yang ditambahkan oleh Yajra -->
                        <th>Nama Pegawai</th>
                        <th>Tahun</th>
                        <th>Jatah Cuti</th>
                        <th>Sisa Cuti</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Isi tabel akan diisi oleh DataTables -->
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('page-js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(function() {
            var table = $('#sisa-cuti-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route("admin.sisa_cuti.index") }}',
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false }, // Kolom nomor urut dari Yajra
                    { data: 'nama_pegawai', name: 'nama_pegawai' }, // Alias kolom dari query
                    { data: 'tahun', name: 'tahun' },
                    { data: 'jatah_cuti', name: 'jatah_cuti' },
                    { data: 'sisa_cuti', name: 'sisa_cuti' },
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
                        $('#sisa-cuti-table').DataTable().ajax.reload();
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