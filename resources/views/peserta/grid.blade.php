@extends('layouts.adminlte')

@section('title', 'Grid Input Peserta')
@section('page-title', 'Grid Input Peserta')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('peserta.index') }}">Peserta & Burung</a></li>
    <li class="breadcrumb-item active">Grid Input</li>
@endsection

@section('content')
<div class="container-fluid mt-3">
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-0">
                        <i class="fas fa-th"></i> Grid Input Peserta
                    </h4>
                    <small class="text-muted">{{ $kelasLomba->nama_kelas }} (Kelas {{ $kelasLomba->nomor_kelas }})</small>
                </div>
                <div class="btn-group" role="group">
                    <a href="{{ route('peserta.grid-settings', $kelasLomba) }}" class="btn btn-outline-primary" title="Ubah ukuran grid">
                        <i class="fas fa-cog"></i> Pengaturan
                    </a>
                    <a href="{{ route('peserta.index', ['kelas_lomba_id' => $kelasLomba->id]) }}" class="btn btn-outline-secondary" title="Lihat daftar peserta">
                        <i class="fas fa-list"></i> Daftar
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header bg-light border-bottom">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h6 class="mb-0">
                                <i class="fas fa-info-circle text-info"></i>
                                <strong>Konfigurasi:</strong> {{ $gridConfig->rows }} baris × {{ $gridConfig->columns }} kolom = <span class="badge badge-primary">{{ $gridConfig->rows * $gridConfig->columns }} slot</span>
                            </h6>
                        </div>
                        <div class="col-md-6 text-right">
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="clearAllBtn" data-toggle="tooltip" data-placement="top" title="Hapus semua data grid">
                                <i class="fas fa-trash"></i> Hapus Semua
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-info" id="toggleHelpBtn" data-toggle="tooltip" data-placement="top" title="Tampilkan bantuan">
                                <i class="fas fa-question-circle"></i> Bantuan
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    {{-- Alert Section --}}
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <div class="d-flex align-items-start">
                                <div>
                                    <strong><i class="fas fa-exclamation-circle"></i> Ada Kesalahan!</strong>
                                    <ul class="mb-0 mt-2">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <div class="d-flex align-items-start">
                                <div>
                                    <i class="fas fa-check-circle"></i>
                                    <strong>Berhasil!</strong> {{ session('success') }}
                                </div>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">&times;</button>
                            </div>
                        </div>
                    @endif

                    {{-- Help Section --}}
                    <div id="helpAlert" class="alert alert-info alert-dismissible fade show d-none" role="alert">
                        <div class="row">
                            <div class="col-md-6">
                                <h6><strong><i class="fas fa-lightbulb"></i> Tips Penggunaan:</strong></h6>
                                <ul class="small mb-0">
                                    <li>Isi data pemilik, nama burung, dan nomor gantangan pada setiap slot</li>
                                    <li>Untuk slot kosong yang ingin dilewati, biarkan semua field kosong</li>
                                    <li>Nomor urut otomatis disusun ulang berdasarkan slot yang terisi</li>
                                    <li>Data akan disimpan berdasarkan urutan grid dari kiri ke kanan, atas ke bawah</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6><strong><i class="fas fa-keyboard"></i> Shortcut:</strong></h6>
                                <ul class="small mb-0">
                                    <li><kbd>Tab</kbd> - Pindah ke field berikutnya</li>
                                    <li><kbd>Enter</kbd> - Pindah ke slot berikutnya</li>
                                    <li><kbd>Ctrl+S</kbd> - Simpan form</li>
                                    <li>Click pada kartu untuk fokus ke field pertama</li>
                                </ul>
                            </div>
                        </div>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">&times;</button>
                    </div>

                    {{-- Main Form --}}
                    <form id="gridForm" action="{{ route('peserta.store-grid', $kelasLomba) }}" method="POST" class="needs-validation">
                        @csrf
                        @method('POST')

                        {{-- Grid Container --}}
                        <div class="grid-container mb-4" id="gridContainer" style="
                            display: grid;
                            grid-template-columns: repeat({{ $gridConfig->columns }}, 1fr);
                            gap: 12px;
                            max-width: 100%;
                        ">
                            @foreach ($gridData as $index => $peserta)
                                <div class="grid-item" data-index="{{ $index }}" data-slot="{{ $peserta->nomor_urut }}">
                                    <div class="card grid-card h-100 position-relative" style="
                                        border: 2px solid #dee2e6;
                                        min-height: 220px;
                                        cursor: pointer;
                                        transition: all 0.3s ease;
                                    " onclick="focusCard(this)">
                                        {{-- Header with Slot Number --}}
                                        <div class="card-header bg-gradient p-2" style="
                                            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                                            color: white;
                                            border-bottom: none;
                                            border-radius: 4px 4px 0 0;
                                        ">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <strong class="fs-5">Slot {{ $peserta->nomor_urut }}</strong>
                                                <span class="badge badge-light text-dark filled-badge" style="display: none;">✓ Terisi</span>
                                            </div>
                                        </div>

                                        {{-- Form Fields --}}
                                        <div class="card-body p-2">
                                            {{-- Nama Pemilik --}}
                                            <div class="mb-2">
                                                <label class="form-label small mb-1" style="font-size: 0.75rem; font-weight: 600;">
                                                    <i class="fas fa-user"></i> Pemilik
                                                </label>
                                                <input type="text"
                                                    class="form-control form-control-sm peserta-input"
                                                    name="peserta[{{ $index }}][nama_pemilik]"
                                                    value="{{ $peserta->nama_pemilik }}"
                                                    placeholder="Nama pemilik"
                                                    style="font-size: 0.85rem;"
                                                    data-field="nama_pemilik"
                                                    @input="updateCardStatus(this)">
                                            </div>

                                            {{-- Nama Burung --}}
                                            <div class="mb-2">
                                                <label class="form-label small mb-1" style="font-size: 0.75rem; font-weight: 600;">
                                                    <i class="fas fa-dove"></i> Burung
                                                </label>
                                                <input type="text"
                                                    class="form-control form-control-sm peserta-input"
                                                    name="peserta[{{ $index }}][nama_burung]"
                                                    value="{{ $peserta->nama_burung }}"
                                                    placeholder="Nama burung"
                                                    style="font-size: 0.85rem;"
                                                    data-field="nama_burung"
                                                    @input="updateCardStatus(this)">
                                            </div>

                                            {{-- Nomor Gantangan --}}
                                            <div class="mb-2">
                                                <label class="form-label small mb-1" style="font-size: 0.75rem; font-weight: 600;">
                                                    <i class="fas fa-tag"></i> Gantangan
                                                </label>
                                                <input type="text"
                                                    class="form-control form-control-sm peserta-input"
                                                    name="peserta[{{ $index }}][nomor_gantangan]"
                                                    value="{{ $peserta->nomor_gantangan }}"
                                                    placeholder="No. Gantangan"
                                                    style="font-size: 0.85rem;"
                                                    data-field="nomor_gantangan"
                                                    @input="updateCardStatus(this)">
                                            </div>

                                            {{-- Alamat Team --}}
                                            <div>
                                                <label class="form-label small mb-1" style="font-size: 0.75rem; font-weight: 600;">
                                                    <i class="fas fa-map-marker-alt"></i> Alamat
                                                </label>
                                                <input type="text"
                                                    class="form-control form-control-sm peserta-input"
                                                    name="peserta[{{ $index }}][alamat_team]"
                                                    value="{{ $peserta->alamat_team }}"
                                                    placeholder="Alamat/Team"
                                                    style="font-size: 0.85rem;"
                                                    data-field="alamat_team"
                                                    @input="updateCardStatus(this)">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Action Buttons --}}
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-grid gap-2 d-md-flex justify-content-md-between">
                                    <div>
                                        <button type="submit" class="btn btn-success btn-lg" id="submitBtn" style="padding: 0.75rem 2rem;">
                                            <i class="fas fa-save"></i> Simpan Grid Peserta
                                        </button>
                                        <a href="{{ route('peserta.index', ['kelas_lomba_id' => $kelasLomba->id]) }}" class="btn btn-secondary btn-lg" style="padding: 0.75rem 2rem;">
                                            <i class="fas fa-times"></i> Batal
                                        </a>
                                    </div>
                                    <button type="button" class="btn btn-outline-warning btn-lg" id="resetBtn" style="padding: 0.75rem 2rem;">
                                        <i class="fas fa-undo"></i> Reset Semua
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .grid-container {
        animation: fadeIn 0.3s ease-in-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .grid-card {
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        background: #fff;
        border-radius: 6px;
    }

    .grid-card:hover {
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
        transform: translateY(-2px);
        border-color: #667eea !important;
    }

    .grid-card.filled {
        border-color: #28a745 !important;
        background: #f8fff9;
    }

    .grid-card.filled .card-header {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%) !important;
    }

    .grid-card:focus-within {
        outline: 3px solid #0d6efd;
        outline-offset: 2px;
    }

    .peserta-input {
        border-radius: 4px;
        border: 1px solid #e0e0e0;
        transition: all 0.2s ease;
    }

    .peserta-input:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.15);
        background-color: #f8f9ff;
    }

    .peserta-input::-webkit-autofill {
        -webkit-box-shadow: 0 0 0 1000px white inset;
    }

    .filled-badge {
        animation: slideIn 0.3s ease-out;
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateX(10px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    .card-header.bg-gradient {
        background-color: #667eea !important;
    }

    {{-- Responsive Grid --}}
    @media (max-width: 1400px) {
        .grid-container {
            gap: 10px;
        }
    }

    @media (max-width: 992px) {
        .grid-container {
            grid-template-columns: repeat(min({{ $gridConfig->columns }}, 3), 1fr) !important;
            gap: 8px;
        }

        .grid-card {
            min-height: 200px;
        }

        .card-body {
            padding: 0.75rem !important;
        }
    }

    @media (max-width: 768px) {
        .grid-container {
            grid-template-columns: repeat(2, 1fr) !important;
            gap: 8px;
        }

        .peserta-input {
            font-size: 0.8rem;
        }

        .form-label {
            font-size: 0.7rem !important;
        }
    }

    @media (max-width: 576px) {
        .grid-container {
            grid-template-columns: 1fr !important;
        }

        .btn-lg {
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
        }

        .card-body {
            padding: 0.5rem !important;
        }
    }

    {{-- Loading State --}}
    .btn:disabled {
        opacity: 0.7;
        cursor: not-allowed;
    }

    .spinner-border {
        width: 1rem;
        height: 1rem;
        border-width: 0.2em;
        margin-right: 0.5rem;
    }

    {{-- Alert Styles --}}
    .alert {
        border-radius: 6px;
        border-left: 4px solid;
    }

    .alert-success {
        border-left-color: #28a745;
        background-color: #f8fff9;
    }

    .alert-danger {
        border-left-color: #dc3545;
        background-color: #fff8f8;
    }

    .alert-info {
        border-left-color: #0dcaf0;
        background-color: #f0f8ff;
    }

    .alert-warning {
        border-left-color: #ffc107;
        background-color: #fffef5;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const gridForm = document.getElementById('gridForm');
    const submitBtn = document.getElementById('submitBtn');
    const resetBtn = document.getElementById('resetBtn');
    const clearAllBtn = document.getElementById('clearAllBtn');
    const toggleHelpBtn = document.getElementById('toggleHelpBtn');
    const helpAlert = document.getElementById('helpAlert');
    const gridContainer = document.getElementById('gridContainer');

    // Initialize tooltips (Bootstrap 4)
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-toggle="tooltip"]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return $(tooltipTriggerEl).tooltip();
    });

    {{-- Keyboard Shortcuts --}}
    document.addEventListener('keydown', function(e) {
        if (e.ctrlKey && e.key === 's') {
            e.preventDefault();
            gridForm.submit();
        }
    });

    {{-- Toggle Help --}}
    if (toggleHelpBtn) {
        toggleHelpBtn.addEventListener('click', function() {
            helpAlert.classList.toggle('d-none');
            this.classList.toggle('active');
        });
    }

    {{-- Clear All Data --}}
    if (clearAllBtn) {
        clearAllBtn.addEventListener('click', function() {
            if (confirm('Yakin ingin menghapus SEMUA data? Tindakan ini tidak dapat dibatalkan.')) {
                const inputs = document.querySelectorAll('.peserta-input');
                inputs.forEach(input => {
                    input.value = '';
                    updateCardStatus(input);
                });
            }
        });
    }

    {{-- Reset to Original --}}
    if (resetBtn) {
        resetBtn.addEventListener('click', function() {
            if (confirm('Yakin ingin kembali ke data sebelumnya? Semua perubahan akan hilang.')) {
                location.reload();
            }
        });
    }

    {{-- Submit Handler --}}
    gridForm.addEventListener('submit', function() {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
    });

    {{-- Auto dismiss alerts --}}
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            $(alert).fadeOut('slow', function() {
                $(this).alert('close');
            });
        }, 5000);
    });
});

{{-- Update card status when input changes --}}
function updateCardStatus(input) {
    const card = input.closest('.grid-card');
    const badge = card.querySelector('.filled-badge');
    const inputs = card.querySelectorAll('.peserta-input');

    let hasData = false;
    inputs.forEach(inp => {
        if (inp.value.trim()) {
            hasData = true;
        }
    });

    if (hasData) {
        card.classList.add('filled');
        if (badge) badge.style.display = 'inline-block';
    } else {
        card.classList.remove('filled');
        if (badge) badge.style.display = 'none';
    }
}

{{-- Focus card functionality --}}
function focusCard(cardElement) {
    const firstInput = cardElement.querySelector('.peserta-input');
    if (firstInput) {
        firstInput.focus();
    }
}

{{-- Initialize card status on load --}}
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.grid-card').forEach(card => {
        const inputs = card.querySelectorAll('.peserta-input');
        let hasData = false;
        inputs.forEach(inp => {
            if (inp.value.trim()) {
                hasData = true;
            }
        });
        if (hasData) {
            card.classList.add('filled');
            const badge = card.querySelector('.filled-badge');
            if (badge) badge.style.display = 'inline-block';
        }
    });

    {{-- Add enter key navigation between cards --}}
    document.querySelectorAll('.peserta-input').forEach((input, index) => {
        input.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const allInputs = Array.from(document.querySelectorAll('.peserta-input'));
                const nextIndex = allInputs.indexOf(input) + 4; {{-- 4 fields per card --}}
                if (nextIndex < allInputs.length) {
                    allInputs[nextIndex].focus();
                } else {
                    document.getElementById('submitBtn').focus();
                }
            }
        });
    });
});
</script>
@endsection
