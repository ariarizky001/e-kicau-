@extends('layouts.adminlte')

@section('title', 'Pengaturan Grid Peserta')
@section('page-title', 'Pengaturan Grid Peserta')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('peserta.index') }}">Peserta & Burung</a></li>
    <li class="breadcrumb-item active">Pengaturan Grid</li>
@endsection

@section('content')
<div class="container-fluid mt-3">
    <div class="row">
        <div class="col-lg-8">
            {{-- Alert Section --}}
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong><i class="fas fa-exclamation-circle"></i> Ada Kesalahan!</strong>
                    <ul class="mb-0 mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            @endif

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle"></i>
                    <strong>Berhasil!</strong> {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <i class="fas fa-info-circle"></i>
                    <strong>Perhatian!</strong> {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            @endif

            {{-- Grid Configuration Card --}}
            <div class="card mb-3">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-cog"></i> Pengaturan Ukuran Grid - {{ $kelasLomba->nama_kelas }}
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('peserta.update-grid-config', $kelasLomba) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="rows" class="form-label">
                                        <strong><i class="fas fa-arrow-down"></i> Jumlah Baris</strong>
                                    </label>
                                    <input type="number"
                                        class="form-control @error('rows') is-invalid @enderror"
                                        id="rows"
                                        name="rows"
                                        value="{{ old('rows', $gridConfig->rows) }}"
                                        min="1"
                                        max="10"
                                        required>
                                    <small class="form-text text-muted">Minimal 1, maksimal 10 baris</small>
                                    @error('rows')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="columns" class="form-label">
                                        <strong><i class="fas fa-arrow-right"></i> Jumlah Kolom</strong>
                                    </label>
                                    <input type="number"
                                        class="form-control @error('columns') is-invalid @enderror"
                                        id="columns"
                                        name="columns"
                                        value="{{ old('columns', $gridConfig->columns) }}"
                                        min="1"
                                        max="10"
                                        required>
                                    <small class="form-text text-muted">Minimal 1, maksimal 10 kolom</small>
                                    @error('columns')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info mb-3">
                            <strong>Total Slot:</strong> <span id="totalSlots" class="badge badge-info">{{ $gridConfig->rows * $gridConfig->columns }}</span>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan Pengaturan
                            </button>
                            <a href="{{ route('peserta.index', ['kelas_lomba_id' => $kelasLomba->id]) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Kembali ke Grid
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Copy from Another Kelas --}}
            @if ($otherKelasLomba->isNotEmpty())
                <div class="card mb-3">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-copy"></i> Salin Data dari Kelas Lain
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-3">Salin peserta dari kelas lain untuk mempercepat input data.</p>
                        <form action="{{ route('peserta.copy-grid', $kelasLomba) }}" method="POST" class="mb-0">
                            @csrf

                            <div class="mb-3">
                                <label for="source_kelas" class="form-label">
                                    <strong>Pilih Kelas Sumber</strong>
                                </label>
                                <div class="input-group">
                                    <select class="form-select" id="source_kelas" name="source_kelas_id" required onchange="updateKelasInfo()">
                                        <option value="">-- Pilih Kelas --</option>
                                        @foreach ($otherKelasLomba as $kelas)
                                            <option value="{{ $kelas->id }}" data-count="{{ $kelas->peserta_count }}">
                                                {{ $kelas->nama_kelas }} (Kelas {{ $kelas->nomor_kelas }}) - {{ $kelas->peserta_count }} peserta
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <small id="kelasInfo" class="form-text text-muted"></small>
                            </div>

                            <div class="alert alert-warning d-none" id="confirmAlert" role="alert">
                                <i class="fas fa-exclamation-triangle"></i>
                                <strong>Perhatian!</strong> Data peserta yang ada akan diganti dengan data dari kelas yang dipilih. Lanjutkan?
                            </div>

                            <button type="submit" class="btn btn-info" id="copyBtn" disabled>
                                <i class="fas fa-copy"></i> Salin Data
                            </button>
                        </form>
                    </div>
                </div>
            @endif

            {{-- Reset Grid --}}
            <div class="card border-danger">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-trash"></i> Reset Grid (Hapus Semua Data)
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">
                        <strong>Peringatan:</strong> Tindakan ini akan menghapus SEMUA data peserta di grid ini dan tidak dapat dibatalkan.
                    </p>
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#resetModal">
                        <i class="fas fa-trash"></i> Hapus Semua Data
                    </button>
                </div>
            </div>
        </div>

        {{-- Info Sidebar --}}
        <div class="col-lg-4">
            <div class="card sticky-top" style="top: 20px;">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-info-circle"></i> Informasi Kelas</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm mb-0">
                        <tr>
                            <td><strong>Nomor Kelas:</strong></td>
                            <td>{{ $kelasLomba->nomor_kelas }}</td>
                        </tr>
                        <tr>
                            <td><strong>Nama Kelas:</strong></td>
                            <td>{{ $kelasLomba->nama_kelas }}</td>
                        </tr>
                        <tr>
                            <td><strong>Status:</strong></td>
                            <td>
                                @if ($kelasLomba->status == 'aktif')
                                    <span class="badge badge-success">Aktif</span>
                                @else
                                    <span class="badge badge-danger">Non-Aktif</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Batas Peserta:</strong></td>
                            <td>{{ $kelasLomba->batas_peserta ?? 'Unlimited' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Jumlah Peserta:</strong></td>
                            <td><span class="badge badge-primary">{{ $kelasLomba->peserta()->count() }}</span></td>
                        </tr>
                        <tr class="border-top">
                            <td><strong>Konfigurasi Grid:</strong></td>
                            <td><span class="badge badge-secondary">{{ $gridConfig->rows }} × {{ $gridConfig->columns }}</span></td>
                        </tr>
                        <tr>
                            <td><strong>Total Slot:</strong></td>
                            <td><span class="badge badge-info">{{ $gridConfig->rows * $gridConfig->columns }}</span></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Reset Modal --}}
<div class="modal fade" id="resetModal" tabindex="-1" role="dialog" aria-labelledby="resetModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle"></i> Konfirmasi Reset Grid
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <p>Anda akan menghapus <strong>SEMUA {{ $kelasLomba->peserta()->count() }} peserta</strong> di kelas <strong>{{ $kelasLomba->nama_kelas }}</strong>.</p>
                <p class="text-danger mb-0"><strong>Tindakan ini tidak dapat dibatalkan!</strong></p>
            </div>
            <div class="modal-footer">
                <form action="{{ route('peserta.reset-grid', $kelasLomba) }}" method="POST" class="w-100">
                    @csrf

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="confirmReset" name="confirm" value="on" required>
                        <label class="form-check-label" for="confirmReset">
                            Saya yakin ingin menghapus semua data peserta
                        </label>
                    </div>

                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        Batal
                    </button>
                    <button type="submit" class="btn btn-danger" id="confirmResetBtn" disabled>
                        <i class="fas fa-trash"></i> Ya, Hapus Semua
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const rowsInput = document.getElementById('rows');
    const columnsInput = document.getElementById('columns');
    const totalSlotsSpan = document.getElementById('totalSlots');
    const confirmReset = document.getElementById('confirmReset');
    const confirmResetBtn = document.getElementById('confirmResetBtn');
    const sourceKelas = document.getElementById('source_kelas');
    const copyBtn = document.getElementById('copyBtn');
    const confirmAlert = document.getElementById('confirmAlert');

    {{-- Update total slots --}}
    function updateTotalSlots() {
        const rows = parseInt(rowsInput.value) || 1;
        const columns = parseInt(columnsInput.value) || 1;
        totalSlotsSpan.textContent = rows * columns;
    }

    rowsInput.addEventListener('change', updateTotalSlots);
    columnsInput.addEventListener('change', updateTotalSlots);
    rowsInput.addEventListener('input', updateTotalSlots);
    columnsInput.addEventListener('input', updateTotalSlots);

    {{-- Confirm reset --}}
    confirmReset.addEventListener('change', function() {
        confirmResetBtn.disabled = !this.checked;
    });

    {{-- Update kelas info --}}
    window.updateKelasInfo = function() {
        const selected = sourceKelas.options[sourceKelas.selectedIndex];
        if (selected.value) {
            const count = selected.getAttribute('data-count');
            document.getElementById('kelasInfo').innerHTML = `
                <span class="badge badge-info mr-2">${count} peserta</span>
                <small class="text-muted">akan disalin ke kelas ini</small>
            `;
            copyBtn.disabled = false;
            confirmAlert.classList.remove('d-none');
        } else {
            document.getElementById('kelasInfo').innerHTML = '';
            copyBtn.disabled = true;
            confirmAlert.classList.add('d-none');
        }
    };

    {{-- Auto dismiss alert --}}
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            $(alert).fadeOut('slow', function() {
                $(this).alert('close');
            });
        }, 5000);
    });
});
</script>
@endsection
