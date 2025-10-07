@extends('layouts.app')

@section('title', 'Atur Permissions untuk Role: ' . $role->name)

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Atur Permissions untuk Role: {{ $role->name }}</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.roles.assignPermissions', $role->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="mb-3">
                    <label class="form-label">Pilih Permissions</label>
                    <div class="row">
                        @forelse($permissions as $permission)
                            <div class="col-md-4 col-lg-3 mb-2">
                                <div class="form-check">
                                    <input 
                                        class="form-check-input" 
                                        type="checkbox" 
                                        name="permissions[]" 
                                        value="{{ $permission->id }}" 
                                        id="permission_{{ $permission->id }}"
                                        {{ in_array($permission->id, $rolePermissions) ? 'checked' : '' }}
                                    >
                                    <label class="form-check-label" for="permission_{{ $permission->id }}">
                                        {{ $permission->name }}
                                    </label>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <p>Belum ada permissions tersedia. Silakan tambah permissions terlebih dahulu.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary me-md-2">Kembali</a>
                    <button type="submit" class="btn btn-primary">Simpan Permissions</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection