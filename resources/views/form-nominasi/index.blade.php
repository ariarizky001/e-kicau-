@extends('layouts.adminlte')

@section('title', 'Form Input Nominasi')

@section('page-title', 'Form Input Nominasi')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item active">Form Input Nominasi</li>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Form Input Nominasi</h3>
            <div class="card-tools">
                <a href="{{ route('form-nominasi.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Input Nominasi Baru
                </a>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Peserta</th>
                        <th>Kelas Lomba</th>
                        <th>Juri</th>
                        <th>Nilai</th>
                        <th>Tanggal Input</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="7" class="text-center">Data akan ditampilkan di sini</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection

