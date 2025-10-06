@extends('layouts.app')

@section('title', 'Detail Jenis Cuti')

@section('content')
<div class="card">
    <h5 class="card-header">Detail Jenis Cuti</h5>
    <div class="card-body">
        <table class="table table-borderless">
            <tr>
                <td><strong>Nama Jenis Cuti</strong></td>
                <td>{{ $jenisCuti->nama }}</td>
            </tr>
        </table>
        <div class="mt-3">
            <a href="{{ route('admin.jenis_cuti.index') }}" class="btn btn-secondary">Kembali</a>
            <a href="{{ route('admin.jenis_cuti.edit', $jenisCuti->id) }}" class="btn btn-primary">Edit</a>
        </div>
    </div>
</div>
@endsection