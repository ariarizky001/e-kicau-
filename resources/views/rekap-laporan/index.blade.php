@extends('layouts.adminlte')

@section('title', 'Rekap & Laporan')

@section('page-title', 'Rekap & Laporan')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item active">Rekap & Laporan</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Rekap & Laporan</h3>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Tanggal Mulai</label>
                                <input type="date" class="form-control" id="tanggal_mulai">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Tanggal Akhir</label>
                                <input type="date" class="form-control" id="tanggal_akhir">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Kelas Lomba</label>
                                <select class="form-control" id="kelas_lomba">
                                    <option value="">Semua Kelas</option>
                                    <!-- Data akan diisi dari database -->
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <button type="button" class="btn btn-primary" onclick="filterLaporan()">
                                <i class="fas fa-filter"></i> Filter
                            </button>
                            <form action="{{ route('rekap-laporan.export') }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-file-excel"></i> Export Excel
                                </button>
                            </form>
                            <button type="button" class="btn btn-danger" onclick="exportPDF()">
                                <i class="fas fa-file-pdf"></i> Export PDF
                            </button>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Peserta</th>
                                    <th>Kelas Lomba</th>
                                    <th>Total Nilai</th>
                                    <th>Rata-rata</th>
                                    <th>Ranking</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="6" class="text-center">Data akan ditampilkan di sini</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function filterLaporan() {
            // Logic untuk filter laporan
            alert('Fitur filter akan diimplementasikan');
        }

        function exportPDF() {
            // Logic untuk export PDF
            alert('Fitur export PDF akan diimplementasikan');
        }
    </script>
@endpush

