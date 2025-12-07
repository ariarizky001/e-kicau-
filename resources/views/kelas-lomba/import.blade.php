@extends('layouts.adminlte')

@section('title', 'Import Kelas Lomba')

@section('page-title', 'Import Kelas Lomba')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('kelas-lomba.index') }}">Kelas Lomba</a></li>
    <li class="breadcrumb-item active">Import</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Import Kelas Lomba dari Excel</h3>
                </div>
                <form action="{{ route('kelas-lomba.import.post') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="alert alert-info">
                            <h5><i class="icon fas fa-info"></i> Petunjuk Import</h5>
                            <ol>
                                <li>Download template Excel terlebih dahulu</li>
                                <li>Isi data kelas lomba sesuai format template</li>
                                <li>Upload file Excel yang sudah diisi</li>
                                <li>Format file yang didukung: .xlsx, .xls, .csv</li>
                            </ol>
                        </div>

                        @if(session('import_errors'))
                            <div class="alert alert-warning">
                                <h5><i class="icon fas fa-exclamation-triangle"></i> Peringatan</h5>
                                <ul class="mb-0">
                                    @foreach(session('import_errors') as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="form-group">
                            <label for="file">Pilih File Excel <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input @error('file') is-invalid @enderror" 
                                           id="file" name="file" accept=".xlsx,.xls,.csv" required>
                                    <label class="custom-file-label" for="file">Pilih file...</label>
                                </div>
                            </div>
                            <small class="form-text text-muted">Maksimal ukuran file: 10MB</small>
                            @error('file')
                                <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Format File Excel:</label>
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm">
                                    <thead>
                                        <tr>
                                            <th>nomor_kelas</th>
                                            <th>nama_kelas</th>
                                            <th>status</th>
                                            <th>batas_peserta</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>1</td>
                                            <td>MURAI BATU REMAJA 75K</td>
                                            <td>aktif</td>
                                            <td>24</td>
                                        </tr>
                                        <tr>
                                            <td>2</td>
                                            <td>MURAI BATU A RAHAYU 220K</td>
                                            <td>aktif</td>
                                            <td>24</td>
                                        </tr>
                                        <tr>
                                            <td>3A</td>
                                            <td>MURAI BURSA A SURAKARTAN 75K</td>
                                            <td>aktif</td>
                                            <td></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <small class="form-text text-muted">
                                <strong>Keterangan:</strong><br>
                                - <strong>nomor_kelas:</strong> Wajib (contoh: 1, 2, 3A, 7, 8)<br>
                                - <strong>nama_kelas:</strong> Wajib (contoh: MURAI BATU REMAJA 75K)<br>
                                - <strong>status:</strong> Opsional (aktif/nonaktif, default: aktif)<br>
                                - <strong>batas_peserta:</strong> Opsional (angka, kosongkan jika tidak ada batas)<br>
                                - Jika nomor kelas sudah ada, data akan diupdate
                            </small>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-upload"></i> Import Data
                        </button>
                        <a href="{{ route('kelas-lomba.index') }}" class="btn btn-default">Batal</a>
                    </div>
                </form>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Download Template</h3>
                </div>
                <div class="card-body">
                    <p>Download template Excel untuk memudahkan import data kelas lomba.</p>
                    <a href="{{ route('kelas-lomba.download-template') }}" class="btn btn-success btn-block">
                        <i class="fas fa-download"></i> Download Template Excel
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Update file input label
        $('.custom-file-input').on('change', function() {
            let fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').html(fileName || 'Pilih file...');
        });
    </script>
@endpush

