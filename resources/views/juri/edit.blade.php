@extends('layouts.adminlte')

@section('title', 'Edit Juri')

@section('page-title', 'Edit Juri')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('juri.index') }}">Juri</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Form Edit Juri</h3>
        </div>
        <form action="{{ route('juri.update', $juri->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong><i class="fas fa-exclamation-circle"></i> Error Validasi:</strong>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                <div class="form-group">
                    <label for="nama_juri">Nama Juri <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('nama_juri') is-invalid @enderror"
                           id="nama_juri" name="nama_juri" placeholder="Masukkan nama juri"
                           value="{{ old('nama_juri', $juri->nama_juri) }}" required>
                    @error('nama_juri')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update
                </button>
                <a href="{{ route('juri.index') }}" class="btn btn-default">
                    <i class="fas fa-times"></i> Batal
                </a>
            </div>
        </form>
    </div>
@endsection
