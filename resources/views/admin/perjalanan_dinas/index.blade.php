@extends('layouts.app')

@section('title', 'Manajemen Perjalanan Dinas')

@push('page-css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
@endpush

@section('content')
<div class="card">
    <h5 class="card-header">Data Perjalanan Dinas</h5>
    <div class="card-body">
        <div class="d-flex justify-content-between mb-3">
            <a href="{{ route('admin.perjalanan_dinas.create') }}" class="btn btn-primary">Tambah Perjalanan Dinas</a>
            <a href="{{ route('admin.perjalanan_dinas.export.pdf') }}" class="btn btn-success">
                <i class="fas fa-file-pdf"></i> Export PDF
            </a>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered" id="perjalanan-dinas-table">
                <thead>
                    <tr>
                        <th>Nomor Surat Tugas</th>
                        <th>Maksud Perjalanan</th>
                        <th>Tujuan</th>
                        <th>Tanggal Berangkat</th>
                        <th>Tanggal Kembali</th>
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
            var table = $('#perjalanan-dinas-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route("admin.perjalanan_dinas.index") }}',
                columns: [
                    { data: 'nomor_surat_tugas', name: 'nomor_surat_tugas' },
                    { data: 'maksud_perjalanan', name: 'maksud_perjalanan' },
                    { data: 'tempat_tujuan', name: 'tempat_tujuan' },
                    { data: 'tgl_berangkat', name: 'tgl_berangkat' },
                    { data: 'tgl_kembali', name: 'tgl_kembali' },
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
                        $('#perjalanan-dinas-table').DataTable().ajax.reload();
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