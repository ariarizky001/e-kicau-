@extends('layouts.adminlte')

@section('title', 'Edit Peserta & Burung')

@section('page-title', 'Edit Peserta & Burung')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('peserta.index') }}">Peserta & Burung</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Form Edit Peserta & Burung</h3>
        </div>
        <form action="{{ route('peserta.update', $peserta->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="card-body">
                <div class="form-group">
                    <label for="kelas_lomba_id">Kelas Lomba <span class="text-danger">*</span></label>
                    <select class="form-control @error('kelas_lomba_id') is-invalid @enderror" 
                            id="kelas_lomba_id" name="kelas_lomba_id" required>
                        <option value="">Pilih Kelas Lomba</option>
                        @foreach($kelasLomba as $kelas)
                            <option value="{{ $kelas->id }}" {{ old('kelas_lomba_id', $peserta->kelas_lomba_id) == $kelas->id ? 'selected' : '' }}>
                                {{ $kelas->nomor_kelas }} - {{ $kelas->nama_kelas }}
                            </option>
                        @endforeach
                    </select>
                    @error('kelas_lomba_id')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="nomor_urut">Nomor Urut <span class="text-danger">*</span></label>
                    <input type="number" min="1" 
                           class="form-control @error('nomor_urut') is-invalid @enderror" 
                           id="nomor_urut" name="nomor_urut" 
                           placeholder="Nomor urut" 
                           value="{{ old('nomor_urut', $peserta->nomor_urut) }}" required>
                    <small class="form-text text-muted">Pastikan nomor urut unik dalam kelas yang sama</small>
                    @error('nomor_urut')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="nama_pemilik">Nama Pemilik (NAMA PESERTA) <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('nama_pemilik') is-invalid @enderror" 
                           id="nama_pemilik" name="nama_pemilik" 
                           placeholder="Masukkan nama pemilik" 
                           value="{{ old('nama_pemilik', $peserta->nama_pemilik) }}" required>
                    @error('nama_pemilik')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="nama_burung">Nama Burung (NAMA BURUNG) <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('nama_burung') is-invalid @enderror" 
                           id="nama_burung" name="nama_burung" 
                           placeholder="Masukkan nama burung" 
                           value="{{ old('nama_burung', $peserta->nama_burung) }}" required>
                    @error('nama_burung')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="nomor_gantangan">Nomor Gantangan (G)</label>
                    <input type="text" class="form-control @error('nomor_gantangan') is-invalid @enderror" 
                           id="nomor_gantangan" name="nomor_gantangan" 
                           placeholder="Masukkan nomor gantangan burung" 
                           value="{{ old('nomor_gantangan', $peserta->nomor_gantangan) }}">
                    <small class="form-text text-muted">Nomor gantangan tempat burung digantung</small>
                    @error('nomor_gantangan')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="alamat_team">Alamat/Team</label>
                    <input type="text" class="form-control @error('alamat_team') is-invalid @enderror" 
                           id="alamat_team" name="alamat_team" 
                           placeholder="Masukkan alamat atau nama team" 
                           value="{{ old('alamat_team', $peserta->alamat_team) }}">
                    @error('alamat_team')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Update</button>
                <a href="{{ route('peserta.index', ['kelas_lomba_id' => $peserta->kelas_lomba_id]) }}" class="btn btn-default">Batal</a>
            </div>
        </form>
    </div>
@endsection

