@extends('layouts.app')

@section('title', 'Detail Riwayat Pangkat')

@section('content')
<div class="card">
    <h5 class="card-header">Detail Riwayat Pangkat</h5>
    <div class="card-body">
        <table class="table table-borderless">
            <tr>
                <td><strong>Nama Pegawai</strong></td>
                <td>{{ $riwayatPangkat->pegawai->nama_lengkap ?? '-' }}</td>
            </tr>
            <tr>
                <td><strong>Golongan</strong></td>
                <td>{{ $riwayatPangkat->golongan->nama ?? '-' }}</td>
            </tr>
            <tr>
                <td><strong>Nomor SK</strong></td>
                <td>{{ $riwayatPangkat->nomor_sk }}</td>
            </tr>
            <tr>
                <td><strong>Tanggal SK</strong></td>
                <td>{{ \Carbon\Carbon::parse($riwayatPangkat->tanggal_sk)->format('d-m-Y') }}</td>
            </tr>
            <tr>
                <td><strong>TMT Pangkat</strong></td>
                <td>{{ \Carbon\Carbon::parse($riwayatPangkat->tmt_pangkat)->format('d-m-Y') }}</td>
            </tr>
            @if($riwayatPangkat->file_sk)
            <tr>
                <td><strong>File SK</strong></td>
                <td>
                    @if(pathinfo($riwayatPangkat->file_sk, PATHINFO_EXTENSION) === 'pdf')
                        <a href="{{ asset('storage/' . $riwayatPangkat->file_sk) }}" target="_blank" class="btn btn-sm btn-info">
                            Lihat File PDF
                        </a>
                    @else
                        <img src="{{ asset('storage/' . $riwayatPangkat->file_sk) }}" alt="File SK" width="200" class="img-thumbnail">
                    @endif
                </td>
            </tr>
            @endif
        </table>
        <div class="mt-3">
            <a href="{{ route('admin.riwayat_pangkat.index') }}" class="btn btn-secondary">Kembali</a>
            <a href="{{ route('admin.riwayat_pangkat.edit', $riwayatPangkat->id) }}" class="btn btn-primary">Edit</a>
        </div>
    </div>
</div>
@endsection