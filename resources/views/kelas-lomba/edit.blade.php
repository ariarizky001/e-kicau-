@extends('layouts.adminlte')

@section('title', 'Edit Kelas Lomba')

@section('page-title', 'Edit Kelas Lomba')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('kelas-lomba.index') }}">Kelas Lomba</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Form Edit Kelas Lomba</h3>
        </div>
        <form action="{{ route('kelas-lomba.update', $kelasLomba->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="card-body">
                <div class="form-group">
                    <label for="nomor_kelas">Nomor Kelas <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('nomor_kelas') is-invalid @enderror" 
                           id="nomor_kelas" name="nomor_kelas" 
                           placeholder="Contoh: 1, 2, 3A, 7, 8" 
                           value="{{ old('nomor_kelas', $kelasLomba->nomor_kelas) }}" required>
                    <small class="form-text text-muted">Format: 1, 2, 3A, 7, 8, dll</small>
                    @error('nomor_kelas')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="nama_kelas">Nama Kelas <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('nama_kelas') is-invalid @enderror" 
                           id="nama_kelas" name="nama_kelas" 
                           placeholder="Contoh: MURAI BATU REMAJA 75K" 
                           value="{{ old('nama_kelas', $kelasLomba->nama_kelas) }}" required>
                    @error('nama_kelas')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="status">Status <span class="text-danger">*</span></label>
                    <select class="form-control @error('status') is-invalid @enderror" id="status" name="status" required>
                        <option value="">Pilih Status</option>
                        <option value="aktif" {{ old('status', $kelasLomba->status) == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="nonaktif" {{ old('status', $kelasLomba->status) == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                    @error('status')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="batas_peserta">Batas Jumlah Peserta</label>
                    <input type="number" min="1" 
                           class="form-control @error('batas_peserta') is-invalid @enderror" 
                           id="batas_peserta" name="batas_peserta" 
                           placeholder="Kosongkan jika tidak ada batas" 
                           value="{{ old('batas_peserta', $kelasLomba->batas_peserta) }}">
                    <small class="form-text text-muted">
                        Masukkan jumlah maksimal peserta untuk kelas ini. Kosongkan jika tidak ada batas.
                        @if($kelasLomba->peserta->count() > 0)
                            <br><strong>Peserta saat ini: {{ $kelasLomba->peserta->count() }}</strong>
                        @endif
                    </small>
                    @error('batas_peserta')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Update</button>
                <a href="{{ route('kelas-lomba.index') }}" class="btn btn-default">Batal</a>
            </div>
        </form>
    </div>
@endsection

