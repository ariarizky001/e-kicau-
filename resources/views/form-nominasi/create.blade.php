@extends('layouts.adminlte')

@section('title', 'Input Nominasi')

@section('page-title', 'Input Nominasi')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('form-nominasi.index') }}">Form Input Nominasi</a></li>
    <li class="breadcrumb-item active">Input Baru</li>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Form Input Nominasi</h3>
        </div>
        <form action="{{ route('form-nominasi.store') }}" method="POST">
            @csrf
            <div class="card-body">
                <div class="form-group">
                    <label for="peserta_id">Peserta & Burung</label>
                    <select class="form-control" id="peserta_id" name="peserta_id" required>
                        <option value="">Pilih Peserta</option>
                        <!-- Data akan diisi dari database -->
                    </select>
                </div>
                <div class="form-group">
                    <label for="juri_id">Juri</label>
                    <select class="form-control" id="juri_id" name="juri_id" required>
                        <option value="">Pilih Juri</option>
                        <!-- Data akan diisi dari database -->
                    </select>
                </div>
                <div class="form-group">
                    <label for="nilai">Nilai</label>
                    <input type="number" class="form-control" id="nilai" name="nilai" step="0.01" min="0" max="100" placeholder="Masukkan nilai" required>
                </div>
                <div class="form-group">
                    <label for="keterangan">Keterangan</label>
                    <textarea class="form-control" id="keterangan" name="keterangan" rows="3" placeholder="Masukkan keterangan"></textarea>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="{{ route('form-nominasi.index') }}" class="btn btn-default">Batal</a>
            </div>
        </form>
    </div>
@endsection

