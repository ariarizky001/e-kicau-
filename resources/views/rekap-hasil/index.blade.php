@extends('layouts.adminlte')

@section('title', 'Rekap Hasil')

@section('page-title', 'Rekap Hasil')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item active">Rekap Hasil</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Rekap Hasil Penilaian</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Kelas Lomba</label>
                                <select class="form-control" id="kelas_lomba">
                                    <option value="">Semua Kelas</option>
                                    <!-- Data akan diisi dari database -->
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Tanggal</label>
                                <input type="date" class="form-control" id="tanggal">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <button type="button" class="btn btn-primary btn-block" onclick="filterHasil()">
                                    <i class="fas fa-filter"></i> Filter
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Peserta</th>
                                    <th>Nama Burung</th>
                                    <th>Kelas Lomba</th>
                                    <th>Total Nilai</th>
                                    <th>Rata-rata</th>
                                    <th>Ranking</th>
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
            </div>
        </div>
    </div>

    <!-- Chart Section -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Grafik Hasil Penilaian</h3>
                </div>
                <div class="card-body">
                    <canvas id="hasilChart" style="min-height: 300px; height: 300px; max-height: 300px; max-width: 100%;"></canvas>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <script>
        function filterHasil() {
            // Logic untuk filter hasil
            alert('Fitur filter akan diimplementasikan');
        }

        // Chart
        $(function () {
            var ctx = document.getElementById('hasilChart').getContext('2d');
            var hasilChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Peserta 1', 'Peserta 2', 'Peserta 3', 'Peserta 4', 'Peserta 5'],
                    datasets: [{
                        label: 'Total Nilai',
                        data: [95.5, 92.3, 90.1, 88.7, 87.2],
                        backgroundColor: 'rgba(60,141,188,0.9)',
                        borderColor: 'rgba(60,141,188,0.8)',
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100
                        }
                    }
                }
            });
        });
    </script>
@endpush

