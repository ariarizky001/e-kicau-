@extends('layouts.adminlte')

@section('title', 'Peserta & Burung')

@section('page-title', 'Peserta & Burung')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item active">Peserta & Burung</li>
@endsection

<style>
.dropdown-gantangan {
    position: relative;
    display: inline-block;
    width: 100%;
}

.dropdown-gantangan-btn {
    padding: 8px 12px;
    border: 1px solid #ced4da;
    background: white;
    cursor: pointer;
    width: 100%;
    text-align: left;
    border-radius: 4px;
    font-size: 14px;
}

.dropdown-gantangan-btn:hover {
    background: #f8f9fa;
}

.dropdown-gantangan-content {
    display: none;
    position: absolute;
    background-color: white;
    border: 1px solid #ced4da;
    padding: 12px;
    width: 280px;
    z-index: 1000;
    margin-top: 5px;
    border-radius: 4px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.dropdown-gantangan-content.show {
    display: block;
}

.grid-gantangan-box {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 8px;
}

.gantangan-item {
    background: #f1f1f1;
    padding: 12px;
    text-align: center;
    cursor: pointer;
    border-radius: 4px;
    transition: 0.2s;
    border: 1px solid #ddd;
    font-weight: 500;
}

.gantangan-item:hover {
    background: #dce6ff;
    border-color: #0066cc;
}

.gantangan-item.disabled {
    background: #e9ecef;
    color: #adb5bd;
    cursor: not-allowed;
    opacity: 0.6;
}

.gantangan-item.disabled:hover {
    background: #e9ecef;
    border-color: #ddd;
}

.gantangan-item.selected {
    background: #0066cc;
    color: white;
    border-color: #0066cc;
}
</style>

@section('content')
    {{-- Sticky Top Buttons --}}
    <div class="card card-primary card-outline sticky-top" style="top: 0; z-index: 100; margin-bottom: 20px;">
        <div class="card-body p-2">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="form-group mb-0">
                        <label class="mb-1"><strong>Pilih Kelas Lomba:</strong></label>
                        <select id="kelasLombaSelect" class="form-control form-control-sm">
                            <option value="">-- Semua Kelas --</option>
                            @foreach($kelasLomba as $kelas)
                                <option value="{{ $kelas->id }}" {{ request('kelas_lomba_id') == $kelas->id ? 'selected' : '' }}>
                                    {{ $kelas->nomor_kelas }} - {{ $kelas->nama_kelas }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex gap-2 justify-content-end">
                        <button type="button" id="gridInputBtn" class="btn btn-warning btn-sm" style="display: none;">
                            <i class="fas fa-times"></i> Tutup Grid
                        </button>
                        <button type="button" id="listViewBtn" class="btn btn-info btn-sm" style="display: none;">
                            <i class="fas fa-list"></i> Lihat List
                        </button>
                        <a href="{{ route('peserta.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Tambah Peserta
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Grid Input Modal/Container --}}
    <div id="gridContainer" style="display: none; margin-bottom: 20px;"></div>

    {{-- Daftar Peserta Card --}}
    <div class="card" id="pesertaCard" style="display: none;">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0">Daftar Peserta & Burung</h3>
            <button type="button" id="backToGridBtn" class="btn btn-sm btn-secondary" title="Kembali ke Grid">
                <i class="fas fa-arrow-left"></i> Kembali ke Grid
            </button>
        </div>
        <div class="card-body">
            <!-- Search -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Pencarian</label>
                        <input type="text" id="searchInput" class="form-control" placeholder="Cari nama pemilik, burung, atau alamat...">
                    </div>
                </div>
            </div>

            {{-- Status Alert --}}
            <div id="statusAlert" style="display: none;">
                <div class="alert alert-info d-flex justify-content-between align-items-center">
                    <div id="statusText"></div>
                </div>
            </div>

            {{-- Peserta Table --}}
            <div class="table-responsive" id="pesertaTableContainer">
                <p class="text-muted text-center">Pilih kelas untuk melihat peserta</p>
            </div>

            {{-- Pagination --}}
            <div class="mt-3" id="paginationContainer"></div>
        </div>
    </div>

<script>
// ===== GLOBAL VARIABLES =====
let kelasSelect = null;
let gridInputBtn = null;
let listViewBtn = null;
let backToGridBtn = null;
let gridContainer = null;
let pesertaCard = null;
let searchInput = null;
let statusAlert = null;
let statusText = null;
let pesertaTableContainer = null;
let paginationContainer = null;

// ===== HELPER FUNCTIONS (defined first so they can be used by event listeners) =====

// Function untuk update nomor gantangan via AJAX
function updateGantanganAJAX(pesertaId, gantanganBaru, button, csrfToken, kelasId) {
    console.log('updateGantanganAJAX called - pesertaId:', pesertaId, 'gantangan baru:', gantanganBaru);

    button.disabled = true;
    button.style.opacity = '0.5';

    const dataToSend = {
        peserta_id: pesertaId,
        gantangan_baru: gantanganBaru,
        kelas_lomba_id: kelasId
    };

    console.log('Sending POST request to /api/update-gantangan');
    console.log('Data:', dataToSend);
    console.log('CSRF Token:', csrfToken);

    fetch('/api/update-gantangan', {
        method: 'POST',
        body: JSON.stringify(dataToSend),
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(response => {
        console.log('Response status:', response.status);
        console.log('Response headers:', response.headers);
        return response.json().then(data => ({
            status: response.status,
            data: data
        }));
    })
    .then(({status, data}) => {
        console.log('Response JSON:', data);

        if (data.success) {
            console.log('✓ Gantangan berhasil diubah');
            console.log('Updated peserta:', data.peserta);

            // Close dropdown
            const dropdown = button.nextElementSibling;
            dropdown.classList.remove('show');

            // Update button text dan attribute
            button.setAttribute('data-current-gantangan', gantanganBaru);
            button.textContent = gantanganBaru;

            // Reload both list view dan grid view
            console.log('Reloading peserta list and grid');
            loadPeserta(kelasId, '');
            loadGridData(kelasId);
        } else {
            throw new Error(data.message || 'Gagal mengubah gantangan');
        }
    })
    .catch(error => {
        console.error('✗ Error:', error);
        alert('Gagal: ' + error.message);
    })
    .finally(() => {
        button.disabled = false;
        button.style.opacity = '1';
    });
}

// ===== SEPARATE EVENT LISTENER FUNCTIONS =====

// Flag to prevent multiple event listener attachments
let gantanganListenersAttached = false;
let deleteListenersAttached = false;

// Attach event listeners untuk gantangan grid items dan button toggles
// Called once during DOMContentLoaded to prevent duplicate listeners
function attachGantanganEventListeners(csrfToken) {
    if (gantanganListenersAttached) {
        console.log('attachGantanganEventListeners already attached, skipping');
        return;
    }
    gantanganListenersAttached = true;
    console.log('attachGantanganEventListeners called');

    // Grid item click handler - grid item click
    document.addEventListener('click', function(e) {
        if (e.target.closest('.gantangan-item')) {
            const item = e.target.closest('.gantangan-item');
            if (item.classList.contains('disabled')) {
                console.log('Item disabled, tidak bisa klik');
                return;
            }

            const pesertaId = parseInt(item.dataset.pesertaId);
            const gantanganBaru = parseInt(item.dataset.gantangan);
            const currentBtn = document.querySelector(`.btn-toggle-gantangan[data-peserta-id="${pesertaId}"]`);
            const currentGantangan = parseInt(currentBtn.getAttribute('data-current-gantangan')) || null;
            const kelasSelect = document.getElementById('kelasLombaSelect');
            const kelasId = kelasSelect.value;

            console.log('Grid item clicked - pesertaId:', pesertaId, 'gantangan baru:', gantanganBaru, 'current:', currentGantangan);

            // Jika sama, tutup dropdown saja
            if (currentGantangan === gantanganBaru) {
                const dropdown = currentBtn.nextElementSibling;
                dropdown.classList.remove('show');
                return;
            }

            updateGantanganAJAX(pesertaId, gantanganBaru, currentBtn, csrfToken, kelasId);
        }
    });

    // Toggle button click handler
    document.addEventListener('click', function(e) {
        if (e.target.closest('.btn-toggle-gantangan')) {
            const btn = e.target.closest('.btn-toggle-gantangan');
            const pesertaId = btn.getAttribute('data-peserta-id');
            const dropdown = document.getElementById(`dropdown-${pesertaId}`);

            if (dropdown) {
                // Close other dropdowns
                document.querySelectorAll('.dropdown-gantangan-content').forEach(d => {
                    if (d.id !== `dropdown-${pesertaId}`) {
                        d.classList.remove('show');
                    }
                });

                // Toggle current
                dropdown.classList.toggle('show');
                console.log('Dropdown toggled for peserta', pesertaId, 'show:', dropdown.classList.contains('show'));
            }
        }
    });
}

// Attach delete event listeners untuk peserta delete buttons
// Called once during DOMContentLoaded to prevent duplicate listeners
function attachDeleteEventListeners(csrfToken) {
    if (deleteListenersAttached) {
        console.log('attachDeleteEventListeners already attached, skipping');
        return;
    }
    deleteListenersAttached = true;
    console.log('attachDeleteEventListeners called');

    document.addEventListener('click', function(e) {
        if (e.target.closest('.delete-peserta-btn')) {
            const btn = e.target.closest('.delete-peserta-btn');
            const pesertaId = btn.getAttribute('data-peserta-id');
            const pesertaName = btn.getAttribute('data-peserta-name');

            if (confirm(`Apakah Anda yakin ingin menghapus peserta "${pesertaName}"?`)) {
                deletePeserta(pesertaId, csrfToken);
            }
        }
    });
}

// ===== END OF EVENT LISTENER FUNCTIONS =====

// ===== GLOBAL HELPER FUNCTIONS (called from event listeners and AJAX) =====

// Load peserta via AJAX
function loadPeserta(kelasId, search) {
    fetch(`/api/peserta?kelas_lomba_id=${kelasId}&search=${search}`)
        .then(response => response.json())
        .then(data => {
            renderPesertaTable(data.peserta, data.kelas);
            renderPagination(data.pagination);
            updateStatus(data.kelas);
            // Populate grids setelah table di-render
            populateGantanganDropdowns(kelasId);
        })
        .catch(error => console.error('Error:', error));
}

// Load grid data
function loadGridData(kelasId) {
    fetch(`/api/grid-data/${kelasId}`)
        .then(response => response.json())
        .then(data => {
            if (data.html) {
                gridContainer.innerHTML = data.html;
                initializeGrid();
            }
        })
        .catch(error => console.error('Error loading grid:', error));
}

// Render peserta table
function renderPesertaTable(peserta, kelas) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
    const kelasId = kelasSelect.value;

    // Get grid config untuk total slots
    fetch(`/api/grid-config/${kelasId}`)
        .then(response => response.json())
        .then(data => {
            const totalSlots = data.config.rows * data.config.columns;

            let html = `<table class="table table-bordered table-striped table-hover">
                <thead class="thead-light">
                    <tr>
                        <th style="width: 50px; text-align: center;">No</th>
                        <th>NAMA PESERTA</th>
                        <th style="width: 100px; text-align: center;">G</th>
                        <th>NAMA BURUNG</th>
                        <th>Alamat</th>
                        <th style="width: 150px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>`;

            // Create map of existing peserta by nomor_urut for quick lookup
            const pesertaMap = {};
            peserta.forEach(p => {
                pesertaMap[p.nomor_urut] = p;
            });

            // Loop through all slots 1 to totalSlots
            for (let slot = 1; slot <= totalSlots; slot++) {
                const p = pesertaMap[slot];

                if (p) {
                    // Peserta ada, tampilkan data
                    html += `<tr style="background-color: #f8f9fa;">
                        <td style="text-align: center;"><strong>${p.nomor_urut}</strong></td>
                        <td>${p.nama_pemilik}</td>
                        <td style="text-align: center; vertical-align: middle;">
                            <div class="dropdown-gantangan">
                                <button type="button" class="dropdown-gantangan-btn btn-toggle-gantangan" data-peserta-id="${p.id}" data-current-gantangan="${p.nomor_gantangan || ''}">
                                    ${p.nomor_gantangan || '-'}
                                </button>
                                <div class="dropdown-gantangan-content" id="dropdown-${p.id}" style="left: -110px;">
                                    <div class="grid-gantangan-box" id="grid-${p.id}">
                                        <!-- Akan di-populate oleh JavaScript -->
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>${p.nama_burung}</td>
                        <td>${p.alamat_team || '-'}</td>
                        <td>
                            <a href="/peserta/${p.id}/edit" class="btn btn-sm btn-warning" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button type="button" class="btn btn-sm btn-danger delete-peserta-btn" data-peserta-id="${p.id}" data-peserta-name="${p.nama_pemilik}" title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>`;
                } else {
                    // Slot kosong, tampilkan form inline
                    html += `<tr style="background-color: #f0f7ff; border: 1px solid #b3d9ff;">
                        <td style="text-align: center; vertical-align: middle;"><strong>${slot}</strong></td>
                        <td colspan="5">
                            <div style="display: flex; gap: 8px; align-items: center;">
                                <form class="inlinePesertaForm" style="display: flex; gap: 8px; align-items: center; flex: 1;">
                                    <input type="hidden" name="kelas_lomba_id" value="${kelasId}">
                                    <input type="hidden" name="slot_number" value="${slot}">
                                    <input type="text" class="form-control form-control-sm nama-pemilik-inline" name="nama_pemilik" placeholder="Nama Pemilik" style="flex: 1;">
                                    <input type="text" class="form-control form-control-sm nomor-gantangan-inline" name="nomor_gantangan" placeholder="No Gantangan" style="width: 100px;" value="${slot}">
                                    <input type="text" class="form-control form-control-sm nama-burung-inline" name="nama_burung" placeholder="Nama Burung" style="flex: 1;">
                                    <input type="text" class="form-control form-control-sm" name="alamat_team" placeholder="Alamat" style="flex: 1;">
                                    <button type="button" class="btn btn-primary btn-sm submitInlineForm" data-slot="${slot}" style="white-space: nowrap;">
                                        <i class="fas fa-plus"></i> Simpan
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>`;
                }
            }

            html += `</tbody></table>`;
            pesertaTableContainer.innerHTML = html;

            // Grid items will be populated by populateGantanganDropdowns()
            // This is called after rendering to populate all grids with slot kosong data
        })
        .catch(error => console.error('Error loading grid config:', error));
}

// Populate gantangan dropdowns dengan slot kosong dari API
function populateGantanganDropdowns(kelasId) {
    console.log('populateGantanganDropdowns called for kelasId:', kelasId);

    // Fetch slot kosong dari API
    fetch(`/api/slot-kosong?kelas_lomba_id=${kelasId}`)
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                console.error('Error fetching slot kosong:', data);
                return;
            }

            const slotKosong = data.slot_kosong;
            const totalSlots = data.total_slots;
            const usedGantangan = data.used_gantangan;

            console.log('Slot kosong data:', {totalSlots, slotKosong, usedGantangan});

            // Populate grid dropdown untuk setiap peserta
            document.querySelectorAll('[id^="grid-"]').forEach(gridContainer => {
                const pesertaId = gridContainer.id.replace('grid-', '');
                const currentBtn = document.querySelector(`.btn-toggle-gantangan[data-peserta-id="${pesertaId}"]`);

                if (!currentBtn) {
                    console.warn('Button not found for peserta', pesertaId);
                    return;
                }

                const currentGantangan = parseInt(currentBtn.getAttribute('data-current-gantangan')) || null;

                // Clear existing items
                gridContainer.innerHTML = '';

                console.log('Populating grid for peserta', pesertaId, '- current gantangan:', currentGantangan);

                // Buat grid items untuk semua slot
                for (let i = 1; i <= totalSlots; i++) {
                    const item = document.createElement('div');
                    item.className = 'gantangan-item';
                    item.textContent = i;
                    item.dataset.gantangan = i;
                    item.dataset.pesertaId = pesertaId;

                    // Mark as selected jika ini adalah gantangan current
                    if (currentGantangan === i) {
                        item.classList.add('selected');
                    }

                    // Mark as disabled jika slot tidak kosong (dan bukan current)
                    if (!slotKosong.includes(i) && currentGantangan !== i) {
                        item.classList.add('disabled');
                        item.style.cursor = 'not-allowed';
                    } else {
                        item.style.cursor = 'pointer';
                    }

                    gridContainer.appendChild(item);
                }
                console.log('Grid populated for peserta', pesertaId);
            });
        })
        .catch(err => console.error('Error fetching slot kosong:', err));
}

// Delete peserta via AJAX
function deletePeserta(pesertaId, csrfToken) {
    fetch(`/peserta/${pesertaId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(response => {
        if (response.ok) {
            return response.json();
        }
        throw new Error('Delete failed');
    })
    .then(data => {
        console.log('Peserta deleted successfully:', data);
        // Reload peserta list and grid
        const kelasId = kelasSelect.value;
        if (kelasId) {
            loadPeserta(kelasId, searchInput.value);
            loadGridData(kelasId);
        }
        // Show success message
        alert(data.message || 'Peserta berhasil dihapus');
    })
    .catch(error => {
        console.error('Error deleting peserta:', error);
        alert('Gagal menghapus peserta. Silakan coba lagi.');
    });
}

// Update status
function updateStatus(kelas) {
    if (kelas) {
        const pesertaCount = kelas.peserta_count || 0;
        const batas = kelas.batas_peserta;
        const sisa = batas ? (batas - pesertaCount) : null;

        let statusHtml = `<strong>Kelas: ${kelas.nomor_kelas} - ${kelas.nama_kelas}</strong><br>
            Total Peserta: <strong>${pesertaCount}</strong>`;

        if (batas) {
            statusHtml += ` / ${batas}`;
            if (sisa !== null) {
                if (sisa > 0) {
                    statusHtml += ` | Sisa Slot: <strong class="text-success">${sisa}</strong>`;
                } else {
                    statusHtml += ` | <strong class="text-danger">PENUH</strong>`;
                }
            }
        }

        statusText.innerHTML = statusHtml;
        statusAlert.style.display = 'block';
    }
}

// Render pagination
function renderPagination(pagination) {
    paginationContainer.innerHTML = pagination.html;
}

// ===== END OF HELPER FUNCTIONS =====

document.addEventListener('DOMContentLoaded', function() {
    kelasSelect = document.getElementById('kelasLombaSelect');
    gridInputBtn = document.getElementById('gridInputBtn');
    listViewBtn = document.getElementById('listViewBtn');
    backToGridBtn = document.getElementById('backToGridBtn');
    gridContainer = document.getElementById('gridContainer');
    pesertaCard = document.getElementById('pesertaCard');
    searchInput = document.getElementById('searchInput');
    statusAlert = document.getElementById('statusAlert');
    statusText = document.getElementById('statusText');
    pesertaTableContainer = document.getElementById('pesertaTableContainer');
    paginationContainer = document.getElementById('paginationContainer');

    // Get CSRF token for all AJAX requests
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

    // ===== INITIALIZE EVENT LISTENERS (CALLED ONCE) =====
    attachGantanganEventListeners(csrfToken);
    attachDeleteEventListeners(csrfToken);
    // ===== END OF INITIALIZATION =====

    // ===== EVENT DELEGATION untuk GRID BUTTONS (SIMPAN & HAPUS) =====
    document.addEventListener('click', function(e) {
        // Handle SAVE SLOT button
        if (e.target.closest('.save-slot-btn')) {
            e.preventDefault();
            e.stopPropagation();
            console.log('✓✓✓ GRID SAVE BUTTON CLICKED');

            const button = e.target.closest('.save-slot-btn');
            const slot = button.getAttribute('data-slot');
            const card = document.querySelector(`.grid-card[data-slot="${slot}"]`);

            if (!card) {
                console.error('Card not found');
                return;
            }

            const namaPemilik = card.querySelector('.nama-pemilik-field');
            const namaBurung = card.querySelector('.nama-burung-field');

            if (!namaPemilik.value.trim() || !namaBurung.value.trim()) {
                alert('Silakan isi nama pemilik dan nama burung!');
                return;
            }

            // Get form and submit
            const gridForm = document.getElementById('gridForm');
            if (!gridForm) {
                console.error('Grid form not found');
                return;
            }

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
            const formData = new FormData(gridForm);

            // Show loading
            const originalHtml = button.innerHTML;
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
            button.disabled = true;

            fetch(gridForm.getAttribute('action'), {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (response.ok) {
                    button.innerHTML = '<i class="fas fa-check"></i> Disimpan';
                    setTimeout(() => {
                        button.innerHTML = originalHtml;
                        button.disabled = false;

                        // Always reload both grid and list view
                        const kelasId = kelasSelect.value;
                        if (kelasId) {
                            console.log('Reloading grid after save...');
                            loadGridData(kelasId);
                            console.log('Refreshing peserta list after save...');
                            loadPeserta(kelasId, searchInput.value);
                        }
                    }, 800);
                    console.log('✓ Save successful');
                } else {
                    throw new Error('Save failed');
                }
            })
            .catch(error => {
                console.error('Error saving:', error);
                button.innerHTML = '<i class="fas fa-exclamation-circle"></i> Gagal';
                setTimeout(() => {
                    button.innerHTML = originalHtml;
                    button.disabled = false;
                }, 2000);
            });
        }

        // Handle DELETE SLOT button
        if (e.target.closest('.delete-slot-btn')) {
            e.preventDefault();
            e.stopPropagation();
            console.log('✓✓✓ GRID DELETE BUTTON CLICKED');

            const button = e.target.closest('.delete-slot-btn');
            const slot = button.getAttribute('data-slot');

            if (!confirm('Apakah Anda yakin ingin menghapus data di slot ini?')) {
                return;
            }

            const card = document.querySelector(`.grid-card[data-slot="${slot}"]`);
            if (!card) {
                console.error('Card not found');
                return;
            }

            // Clear fields IMMEDIATELY for instant UI update
            console.log('Clearing fields for slot', slot);
            const namaPemilikField = card.querySelector('.nama-pemilik-field');
            const namaBurungField = card.querySelector('.nama-burung-field');
            const alamatField = card.querySelector('.alamat-field');
            const nomorGantanganField = card.querySelector('.nomor-gantangan-field');

            if (namaPemilikField) namaPemilikField.value = '';
            if (namaBurungField) namaBurungField.value = '';
            if (alamatField) alamatField.value = '';
            if (nomorGantanganField) nomorGantanganField.value = slot;

            // Update status IMMEDIATELY after clearing fields
            if (window.updateGridCardStatus) {
                console.log('Calling updateGridCardStatus immediately after clear...');
                window.updateGridCardStatus();
            }

            // Get form and submit
            const gridForm = document.getElementById('gridForm');
            if (!gridForm) {
                console.error('Grid form not found');
                return;
            }

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
            const formData = new FormData(gridForm);

            // Show loading
            const originalHtml = button.innerHTML;
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menghapus...';
            button.disabled = true;

            fetch(gridForm.getAttribute('action'), {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (response.ok) {
                    button.innerHTML = '<i class="fas fa-check"></i> Dihapus';
                    setTimeout(() => {
                        button.innerHTML = originalHtml;
                        button.disabled = false;

                        // Always reload both grid and list view
                        const kelasId = kelasSelect.value;
                        if (kelasId) {
                            console.log('Reloading grid after delete...');
                            loadGridData(kelasId);
                            console.log('Refreshing peserta list after delete...');
                            loadPeserta(kelasId, searchInput.value);
                        }
                    }, 800);
                    console.log('✓ Delete successful');
                } else {
                    throw new Error('Delete failed');
                }
            })
            .catch(error => {
                console.error('Error deleting:', error);
                button.innerHTML = '<i class="fas fa-exclamation-circle"></i> Gagal';
                setTimeout(() => {
                    button.innerHTML = originalHtml;
                    button.disabled = false;
                }, 2000);
            });
        }
    });

    // Auto-load peserta saat kelas dipilih
    kelasSelect.addEventListener('change', function() {
        const kelasId = this.value;

        if (kelasId) {
            gridInputBtn.style.display = 'inline-block';
            listViewBtn.style.display = 'inline-block';
            loadPeserta(kelasId, '');
            loadGridData(kelasId);
            // Show grid by default when kelas is selected
            gridContainer.style.display = 'block';
            pesertaCard.style.display = 'none';
            gridInputBtn.innerHTML = '<i class="fas fa-times"></i> Tutup Grid';
            gridInputBtn.classList.add('btn-danger');
            gridInputBtn.classList.remove('btn-warning');
        } else {
            gridInputBtn.style.display = 'none';
            listViewBtn.style.display = 'none';
            gridContainer.style.display = 'none';
            pesertaCard.style.display = 'none';
            statusAlert.style.display = 'none';
            pesertaTableContainer.innerHTML = '<p class="text-muted text-center">Pilih kelas untuk melihat peserta</p>';
        }
    });

    // Auto-search saat mengetik
    searchInput.addEventListener('input', function() {
        const kelasId = kelasSelect.value;
        if (kelasId) {
            loadPeserta(kelasId, this.value);
        }
    });

    // Grid Input Button - Toggle grid visibility
    gridInputBtn.addEventListener('click', function() {
        gridContainer.style.display = gridContainer.style.display === 'none' ? 'block' : 'none';
        pesertaCard.style.display = 'none';
        listViewBtn.innerHTML = '<i class="fas fa-list"></i> Lihat List';
        listViewBtn.classList.remove('btn-secondary');
        listViewBtn.classList.add('btn-info');

        if (gridContainer.style.display === 'block') {
            this.innerHTML = '<i class="fas fa-times"></i> Tutup Grid';
            this.classList.add('btn-danger');
            this.classList.remove('btn-warning');
        } else {
            this.innerHTML = '<i class="fas fa-th"></i> Grid Input';
            this.classList.remove('btn-danger');
            this.classList.add('btn-warning');
        }
    });

    // List View Button - Toggle list visibility
    listViewBtn.addEventListener('click', function() {
        if (pesertaCard.style.display === 'none') {
            pesertaCard.style.display = 'block';
            gridContainer.style.display = 'none';
            this.innerHTML = '<i class="fas fa-th"></i> Lihat Grid';
            this.classList.remove('btn-info');
            this.classList.add('btn-secondary');
            gridInputBtn.innerHTML = '<i class="fas fa-th"></i> Grid Input';
            gridInputBtn.classList.remove('btn-danger');
            gridInputBtn.classList.add('btn-warning');
        } else {
            pesertaCard.style.display = 'none';
            this.innerHTML = '<i class="fas fa-list"></i> Lihat List';
            this.classList.add('btn-info');
            this.classList.remove('btn-secondary');
        }
    });

    // Back to Grid Button
    backToGridBtn.addEventListener('click', function() {
        pesertaCard.style.display = 'none';
        gridContainer.style.display = 'block';
        listViewBtn.innerHTML = '<i class="fas fa-list"></i> Lihat List';
        listViewBtn.classList.remove('btn-secondary');
        listViewBtn.classList.add('btn-info');
        gridInputBtn.innerHTML = '<i class="fas fa-times"></i> Tutup Grid';
        gridInputBtn.classList.add('btn-danger');
        gridInputBtn.classList.remove('btn-warning');
    });

    // ===== EVENT DELEGATION untuk LIST INLINE FORM BUTTONS =====
    document.addEventListener('click', function(e) {
        // Handle inline form submit button
        if (e.target.closest('.submitInlineForm')) {
            const submitBtn = e.target.closest('.submitInlineForm');
            submitInlineForm(submitBtn);
        }
    });

    // Handle Enter key on inline form inputs
    document.addEventListener('keypress', function(e) {
        // Jika enter dipencet di input form inline
        if (e.key === 'Enter' && e.target.closest('.inlinePesertaForm')) {
            e.preventDefault();
            e.stopPropagation();

            const form = e.target.closest('.inlinePesertaForm');
            const submitBtn = form.querySelector('.submitInlineForm');

            if (submitBtn) {
                submitInlineForm(submitBtn);
            }
        }
    });

    // Submit inline form function (dapat dipanggil dari click atau Enter key)
    function submitInlineForm(submitBtn) {
        const form = submitBtn.closest('.inlinePesertaForm');

        if (!form) {
            console.error('Form tidak ditemukan');
            return;
        }

        const namaPemilik = form.querySelector('.nama-pemilik-inline')?.value.trim() || '';
        const namaBurung = form.querySelector('.nama-burung-inline')?.value.trim() || '';
        const nomorGantangan = form.querySelector('[name="nomor_gantangan"]')?.value.trim() || '';
        const alamat = form.querySelector('[name="alamat_team"]')?.value.trim() || '';
        const kelasId = form.querySelector('[name="kelas_lomba_id"]')?.value || '';
        const slotNumber = form.querySelector('[name="slot_number"]')?.value || '';

        console.log('=== FORM SUBMIT ===');
        console.log('Slot:', slotNumber);
        console.log('Nama Pemilik:', namaPemilik);
        console.log('Nama Burung:', namaBurung);
        console.log('No Gantangan:', nomorGantangan);
        console.log('Alamat:', alamat);
        console.log('Kelas ID:', kelasId);

        if (!namaPemilik || !namaBurung) {
            alert('Slot ' + slotNumber + ': Nama pemilik dan nama burung harus diisi!');
            return;
        }

        // Prepare FormData
        const formData = new FormData();
        formData.append('kelas_lomba_id', kelasId);
        formData.append('nomor_urut', slotNumber);
        formData.append('nama_pemilik', namaPemilik);
        formData.append('nama_burung', namaBurung);
        formData.append('nomor_gantangan', nomorGantangan);
        formData.append('alamat_team', alamat);

        // Get CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

        console.log('CSRF Token:', csrfToken);
        console.log('FormData entries:', Array.from(formData.entries()));

        // Show loading
        const originalHtml = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
        submitBtn.disabled = true;

        console.log('Sending POST to /peserta...');

        fetch('/peserta', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(async response => {
            console.log('Response received - Status:', response.status);
            console.log('Response OK:', response.ok);
            console.log('Response headers:', response.headers);

            const contentType = response.headers.get('content-type');
            console.log('Content-Type:', contentType);

                let data;
                if (contentType && contentType.includes('application/json')) {
                    data = await response.json();
                    console.log('Parsed JSON:', data);
                } else {
                    const text = await response.text();
                    console.log('Response text:', text);
                    data = { error: text };
                }

                if (!response.ok) {
                    throw new Error(data.message || data.error || `HTTP ${response.status}`);
                }

                return data;
            })
            .then(data => {
                console.log('SUCCESS - Response data:', data);
                if (data.success) {
                    form.reset();
                    submitBtn.innerHTML = '<i class="fas fa-check"></i> Tersimpan';

                    setTimeout(() => {
                        submitBtn.innerHTML = originalHtml;
                        submitBtn.disabled = false;
                        console.log('Reloading peserta data and grid...');
                        // Reload peserta list and grid
                        const searchTerm = document.getElementById('searchInput')?.value || '';
                        loadPeserta(kelasId, searchTerm);
                        loadGridData(kelasId);
                    }, 1500);
                } else {
                    throw new Error(data.message || 'Gagal menambahkan peserta');
                }
            })
            .catch(error => {
                console.error('=== ERROR ===', error);
                alert('Gagal: ' + error.message);
                submitBtn.innerHTML = '<i class="fas fa-exclamation-circle"></i> Gagal';
                setTimeout(() => {
                    submitBtn.innerHTML = originalHtml;
                    submitBtn.disabled = false;
                }, 2000);
            });
    }

    // Initialize grid if kelas already selected
    if (kelasSelect.value) {
        gridInputBtn.style.display = 'inline-block';
        listViewBtn.style.display = 'inline-block';
        loadPeserta(kelasSelect.value, '');
        loadGridData(kelasSelect.value);
        gridContainer.style.display = 'block';
        pesertaCard.style.display = 'none';
        gridInputBtn.innerHTML = '<i class="fas fa-times"></i> Tutup Grid';
        gridInputBtn.classList.add('btn-danger');
        gridInputBtn.classList.remove('btn-warning');
    }
});

function initializeGrid() {
    // Grid functionality akan di-handle oleh grid-inline view
}
</script>

@endsection

