@extends('layouts.adminlte')

@section('title', 'Kelas Lomba')

@section('page-title', 'Kelas Lomba')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item active">Kelas Lomba</li>
@endsection

@section('content')
    <style>
        #inlineFormRow input, #inlineFormRow select {
            height: 32px;
            border-radius: 4px;
            border: 1px solid #ddd;
        }

        #inlineFormRow input:focus, #inlineFormRow select:focus {
            border-color: #80bdff;
            outline: 0;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        #inlineFormRow td {
            vertical-align: middle;
            padding: 10px 8px !important;
        }

        #inlineFormRow .btn {
            height: 32px;
            line-height: 1;
        }

        #inlineFormRow small {
            display: block;
            margin-top: 4px;
            height: 16px;
        }
    </style>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Daftar Kelas Lomba</h3>
            <div class="card-tools">
                <a href="{{ route('kelas-lomba.import') }}" class="btn btn-success btn-sm mr-2">
                    <i class="fas fa-file-upload"></i> Import Excel
                </a>
                <a href="{{ route('kelas-lomba.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Tambah Kelas Lomba
                </a>
            </div>
        </div>
        <div class="card-body">
            <!-- Search and Filter -->
            <form method="GET" action="{{ route('kelas-lomba.index') }}" class="mb-3">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <input type="text" name="search" class="form-control" placeholder="Cari nomor kelas atau nama kelas..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <select name="status" class="form-control">
                                <option value="">Semua Status</option>
                                <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="nonaktif" {{ request('status') == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-info">
                            <i class="fas fa-search"></i> Cari
                        </button>
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('kelas-lomba.index') }}" class="btn btn-secondary">
                            <i class="fas fa-redo"></i> Reset
                        </a>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th style="width: 100px;">Nomor Kelas</th>
                            <th>Nama Kelas</th>
                            <th style="width: 100px;">Status</th>
                            <th style="width: 150px;">Jumlah Peserta</th>
                            <th style="width: 150px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="kelasLombaBody">
                        @forelse($kelasLomba as $index => $kelas)
                            @php
                                $jumlahPeserta = $kelas->peserta_count ?? $kelas->peserta->count();
                                $batasPeserta = $kelas->batas_peserta;
                                $sisaSlot = $batasPeserta ? ($batasPeserta - $jumlahPeserta) : null;
                            @endphp
                            <tr>
                                <td>{{ $kelasLomba->firstItem() + $index }}</td>
                                <td><strong>{{ $kelas->nomor_kelas }}</strong></td>
                                <td>{{ $kelas->nama_kelas }}</td>
                                <td>
                                    @if($kelas->status == 'aktif')
                                        <span class="badge badge-success">Aktif</span>
                                    @else
                                        <span class="badge badge-secondary">Nonaktif</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-info">{{ $jumlahPeserta }}</span>
                                    @if($batasPeserta)
                                        <span class="text-muted">/ {{ $batasPeserta }}</span>
                                        @if($sisaSlot !== null)
                                            @if($sisaSlot > 0)
                                                <br><small class="text-success">Sisa: {{ $sisaSlot }}</small>
                                            @else
                                                <br><small class="text-danger"><strong>PENUH</strong></small>
                                            @endif
                                        @endif
                                    @else
                                        <br><small class="text-muted">Tidak ada batas</small>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('peserta.index', ['kelas_lomba_id' => $kelas->id]) }}" class="btn btn-sm btn-info" title="Lihat Peserta">
                                        <i class="fas fa-users"></i>
                                    </a>
                                    <a href="{{ route('kelas-lomba.edit', $kelas->id) }}" class="btn btn-sm btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('kelas-lomba.destroy', $kelas->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kelas lomba ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Tidak ada data kelas lomba</td>
                            </tr>
                        @endforelse

                        <!-- Inline Add Form Row -->
                        <tr id="inlineFormRow" class="table-light">
                            <td></td>
                            <td>
                                <input type="text" id="inlineNomorKelas" class="form-control form-control-sm"
                                       placeholder="Nomor kelas" required>
                                <small class="text-danger" id="errorNomorKelas"></small>
                            </td>
                            <td>
                                <input type="text" id="inlineNamaKelas" class="form-control form-control-sm"
                                       placeholder="Nama kelas" required>
                                <small class="text-danger" id="errorNamaKelas"></small>
                            </td>
                            <td>
                                <select id="inlineStatus" class="form-control form-control-sm" required>
                                    <option value="">-- Pilih --</option>
                                    <option value="aktif">Aktif</option>
                                    <option value="nonaktif">Nonaktif</option>
                                </select>
                                <small class="text-danger" id="errorStatus"></small>
                            </td>
                            <td>
                                <input type="number" id="inlineBatasPeserta" class="form-control form-control-sm"
                                       placeholder="Batas" min="1">
                                <small class="text-danger" id="errorBatasPeserta"></small>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <button type="button" id="btnSaveInline" class="btn btn-success"
                                            onclick="saveInlineKelas()" title="Simpan">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button type="button" id="btnCancelInline" class="btn btn-secondary"
                                            onclick="cancelInlineKelas()" title="Batal">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-3">
                {{ $kelasLomba->links() }}
            </div>
        </div>
    </div>

    <script>
        function clearInlineForm() {
            document.getElementById('inlineNomorKelas').value = '';
            document.getElementById('inlineNamaKelas').value = '';
            document.getElementById('inlineStatus').value = '';
            document.getElementById('inlineBatasPeserta').value = '';
            clearAllErrors();
        }

        function clearAllErrors() {
            document.getElementById('errorNomorKelas').textContent = '';
            document.getElementById('errorNamaKelas').textContent = '';
            document.getElementById('errorStatus').textContent = '';
            document.getElementById('errorBatasPeserta').textContent = '';
        }

        function saveInlineKelas() {
            clearAllErrors();

            const nomorKelas = document.getElementById('inlineNomorKelas').value.trim();
            const namaKelas = document.getElementById('inlineNamaKelas').value.trim();
            const status = document.getElementById('inlineStatus').value;
            const batasPeserta = document.getElementById('inlineBatasPeserta').value;

            const btnSave = document.getElementById('btnSaveInline');
            const btnCancel = document.getElementById('btnCancelInline');
            btnSave.disabled = true;
            btnCancel.disabled = true;
            btnSave.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';

            const data = {
                nomor_kelas: nomorKelas,
                nama_kelas: namaKelas,
                status: status,
                batas_peserta: batasPeserta || null,
                _token: document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'
            };

            fetch('{{ route("kelas-lomba.store-inline") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': data._token,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => Promise.reject(err));
                }
                return response.json();
            })
            .then(result => {
                if (result.success) {
                    // Tambah row baru ke table
                    addNewRowToTable(result.data);

                    // Clear form
                    clearInlineForm();

                    // Show success message
                    showSuccessAlert(result.message);

                    // Enable buttons
                    btnSave.disabled = false;
                    btnCancel.disabled = false;
                    btnSave.innerHTML = '<i class="fas fa-check"></i> Simpan';
                }
            })
            .catch(error => {
                console.error('Error:', error);

                if (error.message && !error.errors) {
                    showErrorAlert(error.message);
                }

                if (error.errors) {
                    if (error.errors.nomor_kelas) {
                        document.getElementById('errorNomorKelas').textContent = error.errors.nomor_kelas[0];
                    }
                    if (error.errors.nama_kelas) {
                        document.getElementById('errorNamaKelas').textContent = error.errors.nama_kelas[0];
                    }
                    if (error.errors.status) {
                        document.getElementById('errorStatus').textContent = error.errors.status[0];
                    }
                    if (error.errors.batas_peserta) {
                        document.getElementById('errorBatasPeserta').textContent = error.errors.batas_peserta[0];
                    }
                }

                btnSave.disabled = false;
                btnCancel.disabled = false;
                btnSave.innerHTML = '<i class="fas fa-check"></i> Simpan';
            });
        }

        function addNewRowToTable(kelas) {
            const tbody = document.getElementById('kelasLombaBody');

            // Hitung nomor urut: jumlah rows existing + 1
            const existingRows = tbody.querySelectorAll('tr:not(#inlineFormRow)').length;
            const noUrut = existingRows + 1;

            // Status badge
            const statusBadge = kelas.status === 'aktif'
                ? '<span class="badge badge-success">Aktif</span>'
                : '<span class="badge badge-secondary">Nonaktif</span>';

            // Batas peserta display
            const batasPesertaDisplay = kelas.batas_peserta
                ? `<span class="text-muted">/ ${kelas.batas_peserta}</span><br><small class="text-success">Sisa: ${kelas.batas_peserta}</small>`
                : '<br><small class="text-muted">Tidak ada batas</small>';

            const pesertaLink = `{{ route('peserta.index', ['kelas_lomba_id' => '__ID__']) }}`.replace('__ID__', kelas.id);
            const editLink = `{{ route('kelas-lomba.edit', ['kelasLomba' => '__ID__']) }}`.replace('__ID__', kelas.id);
            const deleteLink = `{{ route('kelas-lomba.destroy', ['kelasLomba' => '__ID__']) }}`.replace('__ID__', kelas.id);

            const newRow = document.createElement('tr');
            newRow.innerHTML = `
                <td>${noUrut}</td>
                <td><strong>${escapeHtml(kelas.nomor_kelas)}</strong></td>
                <td>${escapeHtml(kelas.nama_kelas)}</td>
                <td>${statusBadge}</td>
                <td class="text-center">
                    <span class="badge badge-info">0</span>
                    ${batasPesertaDisplay}
                </td>
                <td>
                    <a href="${pesertaLink}" class="btn btn-sm btn-info" title="Lihat Peserta">
                        <i class="fas fa-users"></i>
                    </a>
                    <a href="${editLink}" class="btn btn-sm btn-warning" title="Edit">
                        <i class="fas fa-edit"></i>
                    </a>
                    <form action="${deleteLink}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kelas lomba ini?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </td>
            `;

            // Insert sebelum form row
            const formRow = document.getElementById('inlineFormRow');
            formRow.parentNode.insertBefore(newRow, formRow);
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function cancelInlineKelas() {
            clearInlineForm();
        }

        function showSuccessAlert(message) {
            const alertHtml = `
                <div class="alert alert-success alert-dismissible fade show" role="alert" style="margin-bottom: 1rem;">
                    <i class="fas fa-check-circle"></i> <strong>${message}</strong>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            `;

            const cardBody = document.querySelector('.card-body');
            const alertElement = document.createElement('div');
            alertElement.innerHTML = alertHtml;
            cardBody.insertBefore(alertElement.firstElementChild, cardBody.firstChild);

            // Auto dismiss after 4 seconds
            setTimeout(() => {
                const alert = cardBody.querySelector('.alert-success');
                if (alert) {
                    alert.remove();
                }
            }, 4000);
        }

        function showErrorAlert(message) {
            const alertHtml = `
                <div class="alert alert-danger alert-dismissible fade show" role="alert" style="margin-bottom: 1rem;">
                    <i class="fas fa-exclamation-circle"></i> <strong>Error:</strong> ${message}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            `;

            const cardBody = document.querySelector('.card-body');
            const alertElement = document.createElement('div');
            alertElement.innerHTML = alertHtml;
            cardBody.insertBefore(alertElement.firstElementChild, cardBody.firstChild);
        }

        // Add CSRF token meta tag if not exists
        if (!document.querySelector('meta[name="csrf-token"]')) {
            const meta = document.createElement('meta');
            meta.name = 'csrf-token';
            meta.content = '{{ csrf_token() }}';
            document.head.appendChild(meta);
        }
    </script>
@endsection

