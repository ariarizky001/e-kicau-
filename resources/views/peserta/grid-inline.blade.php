<div class="card card-primary card-outline mt-3">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0"><i class="fas fa-th"></i> Input Grid - {{ $kelasLomba->nama_kelas }}</h3>
            <div class="input-group input-group-sm" style="width: 250px;">
                <input type="text" id="gridSearch" class="form-control" placeholder="Cari nama pemilik...">
                <div class="input-group-append">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                </div>
            </div>
        </div>
    </div>

    <div class="card-body">
        <form id="gridForm" action="{{ route('peserta.store-grid', $kelasLomba) }}" method="POST" class="grid-form">
            @csrf

            {{-- Grid Display --}}
            <div class="grid-wrapper" style="overflow-x: auto; margin-bottom: 20px;">
                <div class="grid-container" style="display: grid; gap: 10px; padding: 10px;
                     grid-template-columns: repeat({{ $config->columns }}, 1fr);
                     min-width: max-content;">

                    @php $totalSlots = $config->rows * $config->columns; @endphp
                    @for($slot = 1; $slot <= $totalSlots; $slot++)
                        @php
                            // Cari peserta dengan nomor_urut sesuai slot
                            $pesertaAtSlot = null;
                            foreach($peserta as $key => $p) {
                                if(isset($p['nomor_urut']) && $p['nomor_urut'] == $slot) {
                                    $pesertaAtSlot = $p;
                                    break;
                                }
                            }

                            // Check apakah slot ini terisi data (nama_pemilik atau nama_burung tidak kosong)
                            $isSlotFilled = $pesertaAtSlot && (
                                !empty(trim($pesertaAtSlot['nama_pemilik'] ?? '')) ||
                                !empty(trim($pesertaAtSlot['nama_burung'] ?? ''))
                            );
                        @endphp

                        <div class="grid-card {{ $isSlotFilled ? 'filled' : 'empty' }}" data-slot="{{ $slot }}" data-nama-pemilik="{{ $pesertaAtSlot['nama_pemilik'] ?? '' }}" style="
                            border: 2px solid #ddd;
                            border-radius: 8px;
                            padding: 12px;
                            min-width: 240px;
                            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
                            transition: all 0.3s ease;
                        ">
                            <div style="text-align: center; margin-bottom: 8px;">
                                <span class="badge badge-primary" style="font-size: 11px;">SLOT {{ $slot }}</span>
                            </div>

                            {{-- Slot Number (auto-filled) --}}
                            <input type="hidden" name="slots[{{ $slot }}][nomor_urut]" value="{{ $slot }}">

                            {{-- Nomor Gantangan (auto-filled from slot number) --}}
                            <div class="form-group mb-2">
                                <small class="form-text text-muted">Nomor Gantangan</small>
                                <input type="text"
                                       class="form-control form-control-sm nomor-gantangan-field"
                                       name="slots[{{ $slot }}][nomor_gantangan]"
                                       value="{{ $pesertaAtSlot['nomor_gantangan'] ?? $slot }}"
                                       placeholder="Otomatis"
                                       style="font-weight: bold; text-align: center; font-size: 14px;"
                                       readonly>
                            </div>

                            {{-- Nama Pemilik --}}
                            <div class="form-group mb-2">
                                <small class="form-text text-muted">Pemilik</small>
                                <input type="text"
                                       class="form-control form-control-sm nama-pemilik-field"
                                       name="slots[{{ $slot }}][nama_pemilik]"
                                       value="{{ $pesertaAtSlot['nama_pemilik'] ?? '' }}"
                                       placeholder="Nama pemilik..."
                                       style="font-size: 12px;">
                            </div>

                            {{-- Nama Burung --}}
                            <div class="form-group mb-2">
                                <small class="form-text text-muted">Burung</small>
                                <input type="text"
                                       class="form-control form-control-sm nama-burung-field"
                                       name="slots[{{ $slot }}][nama_burung]"
                                       value="{{ $pesertaAtSlot['nama_burung'] ?? '' }}"
                                       placeholder="Nama burung..."
                                       style="font-size: 12px;">
                            </div>

                            {{-- Alamat --}}
                            <div class="form-group mb-2">
                                <small class="form-text text-muted">Alamat</small>
                                <input type="text"
                                       class="form-control form-control-sm alamat-field"
                                       name="slots[{{ $slot }}][alamat_team]"
                                       value="{{ $pesertaAtSlot['alamat_team'] ?? '' }}"
                                       placeholder="Alamat..."
                                       style="font-size: 12px;">
                            </div>

                            {{-- Action Buttons --}}
                            <div style="display: flex; gap: 5px; margin-top: 8px;">
                                <button type="button" class="btn btn-success btn-xs flex-grow-1 save-slot-btn" data-slot="{{ $slot }}" style="padding: 3px 6px; font-size: 11px;">
                                    <i class="fas fa-save"></i> Simpan
                                </button>
                                <button type="button" class="btn btn-danger btn-xs flex-grow-1 delete-slot-btn" data-slot="{{ $slot }}" style="padding: 3px 6px; font-size: 11px;">
                                    <i class="fas fa-trash"></i> Hapus
                                </button>
                            </div>

                            {{-- Status Badge --}}
                            <div style="margin-top: 6px; text-align: center;">
                                <small class="status-badge" style="padding: 3px 6px; border-radius: 3px; display: inline-block; font-size: 10px; font-weight: bold;">
                                    {{ $isSlotFilled ? 'TERISI' : 'KOSONG' }}
                                </small>
                            </div>
                        </div>
                    @endfor
                </div>
            </div>

            {{-- Buttons --}}
            <div class="form-group" style="margin-top: 20px;">
                <button type="submit" class="btn btn-success btn-sm" id="saveGridBtn">
                    <i class="fas fa-save"></i> Simpan Grid
                </button>
                <button type="button" class="btn btn-secondary btn-sm" onclick="clearGrid()">
                    <i class="fas fa-redo"></i> Reset Grid
                </button>
                <a href="{{ route('peserta.grid-settings', $kelasLomba) }}" class="btn btn-info btn-sm">
                    <i class="fas fa-cog"></i> Pengaturan
                </a>
            </div>
        </form>
    </div>
</div>

<style>
.grid-form {
    font-size: 13px;
}

.grid-container {
    background: white;
    border-radius: 8px;
}

.grid-card {
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    cursor: pointer;
    position: relative;
}

.grid-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

.grid-card.filled {
    background: linear-gradient(135deg, #d4edda 0%, #84d672 100%);
    border-color: #28a745;
}

.grid-card.empty {
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
}

.form-control-sm {
    height: calc(1.5em + 0.5rem + 2px);
    padding: 0.25rem 0.5rem;
}

.status-badge {
    background-color: #e9ecef;
    color: #495057;
}

.grid-card.filled .status-badge {
    background-color: #d4edda;
    color: #155724;
}

.btn-xs {
    padding: 0.25rem 0.5rem;
    font-size: 11px;
    line-height: 1.2;
    border-radius: 0.2rem;
}

.btn-xs:disabled {
    cursor: not-allowed;
    opacity: 0.65;
}

.save-slot-btn {
    background-color: #28a745;
    border-color: #28a745;
    color: white;
}

.save-slot-btn:hover:not(:disabled) {
    background-color: #218838;
    border-color: #1e7e34;
}

.delete-slot-btn {
    background-color: #dc3545;
    border-color: #dc3545;
    color: white;
}

.delete-slot-btn:hover:not(:disabled) {
    background-color: #c82333;
    border-color: #bd2130;
}

#gridSearch {
    border-radius: 0.25rem;
}

#gridSearch.is-invalid {
    border-color: #dc3545;
    background-color: #fff5f5;
}

.flex-grow-1 {
    flex: 1 1 auto;
}
</style>

<script>
console.log('=== GRID-INLINE.BLADE.PHP LOADED ===');

// Global functions dan variables
let gridSearchInput = null;
let gridForm = null;

function showAllSlots() {
    const gridCards = document.querySelectorAll('.grid-card');
    gridCards.forEach(card => {
        card.style.display = 'block';
        card.style.opacity = '1';
        card.style.transform = 'scale(1)';
    });
    if (gridSearchInput) {
        gridSearchInput.classList.remove('is-invalid');
    }
}

function updateGridCardStatus() {
    console.log('=== updateGridCardStatus CALLED ===');
    const gridCards = document.querySelectorAll('.grid-card');
    console.log('Found', gridCards.length, 'grid cards');

    gridCards.forEach(card => {
        const namaPemilik = card.querySelector('.nama-pemilik-field');
        const namaBurung = card.querySelector('.nama-burung-field');
        const statusBadge = card.querySelector('.status-badge');

        if (!namaPemilik || !namaBurung || !statusBadge) {
            console.error('Missing elements in card');
            return;
        }

        const slot = card.getAttribute('data-slot');
        const namaPemilikVal = namaPemilik.value.trim();
        const namaBurungVal = namaBurung.value.trim();
        // Slot filled jika salah satu (nama_pemilik ATAU nama_burung) ada
        const isFilled = namaPemilikVal !== '' || namaBurungVal !== '';

        console.log(`Slot ${slot}: "${namaPemilikVal}" / "${namaBurungVal}" -> ${isFilled ? 'TERISI' : 'KOSONG'}`);

        // Update data attribute untuk search
        card.setAttribute('data-nama-pemilik', namaPemilikVal);

        if (isFilled) {
            card.classList.add('filled');
            card.classList.remove('empty');
            statusBadge.textContent = 'TERISI';
            statusBadge.style.backgroundColor = '#d4edda';
            statusBadge.style.color = '#155724';
            statusBadge.style.fontWeight = 'bold';
            // Warna berbeda untuk slot terisi
            card.style.background = 'linear-gradient(135deg, #d4edda 0%, #84d672 100%)';
            card.style.borderColor = '#28a745';
            card.style.borderWidth = '2px';
        } else {
            card.classList.remove('filled');
            card.classList.add('empty');
            statusBadge.textContent = 'KOSONG';
            statusBadge.style.backgroundColor = '#e9ecef';
            statusBadge.style.color = '#495057';
            statusBadge.style.fontWeight = 'bold';
            // Warna default untuk slot kosong
            card.style.background = 'linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%)';
            card.style.borderColor = '#ddd';
            card.style.borderWidth = '2px';
        }
    });
    console.log('=== updateGridCardStatus FINISHED ===');
}

// Export to window object so it's accessible from index.blade.php
window.updateGridCardStatus = updateGridCardStatus;

// NOTE: Event delegation untuk Save dan Delete buttons dipindahkan ke index.blade.php
// untuk menghindari duplicate listeners. Event listeners di sana akan menangani
// button clicks karena menggunakan document-level event delegation.

// DOMContentLoaded - initialize event listeners
document.addEventListener('DOMContentLoaded', function() {
    gridForm = document.getElementById('gridForm');
    gridSearchInput = document.getElementById('gridSearch');

    // Listen to input changes
    const inputs = document.querySelectorAll('.nama-pemilik-field, .nama-burung-field, .alamat-field');
    inputs.forEach(input => {
        input.addEventListener('input', updateGridCardStatus);
        input.addEventListener('blur', function() {
            // Validate when user leaves a field
            const card = this.closest('.grid-card');
            const namaPemilik = card.querySelector('.nama-pemilik-field');
            const namaBurung = card.querySelector('.nama-burung-field');

            // Clear warning color
            if ((namaPemilik.value.trim() && namaBurung.value.trim()) ||
                (!namaPemilik.value.trim() && !namaBurung.value.trim())) {
                card.style.borderColor = namaPemilik.value.trim() && namaBurung.value.trim() ? '#28a745' : '#ddd';
            }
        });
    });

    // Search functionality - highlight dan arahkan cursor ke slot
    if (gridSearchInput) {
        gridSearchInput.addEventListener('input', function() {
            const searchTerm = this.value.trim().toLowerCase();

            if (!searchTerm) {
                showAllSlots();
                return;
            }

            let foundCount = 0;
            let firstFoundSlot = null;
            const gridCards = document.querySelectorAll('.grid-card');

            gridCards.forEach(card => {
                const namaPemilik = card.getAttribute('data-nama-pemilik').toLowerCase();
                const slot = card.getAttribute('data-slot');

                if (namaPemilik.includes(searchTerm)) {
                    card.style.display = 'block';
                    card.style.opacity = '1';
                    card.style.transform = 'scale(1)';
                    if (!firstFoundSlot) firstFoundSlot = slot;
                    foundCount++;
                } else {
                    card.style.display = 'block';
                    card.style.opacity = '0.3';
                    card.style.transform = 'scale(0.95)';
                }
            });

            // Scroll ke slot pertama yang ditemukan dan focus ke input nama pemilik
            if (firstFoundSlot) {
                const foundCard = document.querySelector(`[data-slot="${firstFoundSlot}"]`);
                foundCard.scrollIntoView({ behavior: 'smooth', block: 'nearest' });

                // Focus ke input nama pemilik
                const namaPemilikInput = foundCard.querySelector('.nama-pemilik-field');
                setTimeout(() => {
                    namaPemilikInput.focus();
                    namaPemilikInput.select();
                }, 300);
            }

            // Update counter di search input
            if (foundCount === 0) {
                this.classList.add('is-invalid');
            } else {
                this.classList.remove('is-invalid');
            }
        });
    }

    // Handle form submission with validation
    if (gridForm) {
        gridForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const gridCards = document.querySelectorAll('.grid-card');

            // Validate that we have data to save
            let hasPeserta = false;
            gridCards.forEach(card => {
                const namaPemilik = card.querySelector('.nama-pemilik-field');
                const namaBurung = card.querySelector('.nama-burung-field');
                if (namaPemilik.value.trim() && namaBurung.value.trim()) {
                    hasPeserta = true;
                }
            });

            if (!hasPeserta) {
                alert('Silakan isi minimal satu slot dengan nama pemilik dan nama burung!');
                return false;
            }

            // Check for duplicate nama_burung
            const namaBurungValues = [];
            gridCards.forEach(card => {
                const namaBurung = card.querySelector('.nama-burung-field');
                if (namaBurung.value.trim()) {
                    namaBurungValues.push(namaBurung.value.trim());
                }
            });

            const duplicateBurung = namaBurungValues.filter((v, i) => namaBurungValues.indexOf(v) !== i);
            if (duplicateBurung.length > 0) {
                alert('Nama burung tidak boleh ada yang duplikat: ' + duplicateBurung.join(', '));
                return false;
            }

            // Check for duplicate nomor_gantangan
            const nomorGantanganValues = [];
            gridCards.forEach(card => {
                const nomorGantangan = card.querySelector('.nomor-gantangan-field');
                if (nomorGantangan.value.trim()) {
                    nomorGantanganValues.push(nomorGantangan.value.trim());
                }
            });

            const duplicateGantangan = nomorGantanganValues.filter((v, i) => nomorGantanganValues.indexOf(v) !== i);
            if (duplicateGantangan.length > 0) {
                alert('Nomor gantangan tidak boleh ada yang duplikat: ' + duplicateGantangan.join(', '));
                return false;
            }

            // Show loading state
            const submitBtn = this.querySelector('#saveGridBtn');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';

            // Submit the form
            setTimeout(() => {
                this.submit();
            }, 300);
        });
    }

    // Handle keyboard navigation
    const inputs = document.querySelectorAll('.nama-pemilik-field, .nama-burung-field, .alamat-field');
    let currentCardIndex = 0;
    inputs.forEach((input, index) => {
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && e.ctrlKey) {
                if (gridForm) gridForm.submit();
            } else if (e.key === 'Tab') {
                const allInputs = document.querySelectorAll('.grid-card input[type="text"]:not([readonly])');
                currentCardIndex = Array.from(allInputs).indexOf(e.target);
            }
        });
    });

    // Initial status update
    updateGridCardStatus();
});

function clearGrid() {
    if (confirm('Apakah Anda yakin ingin mengosongkan semua slot?')) {
        document.querySelectorAll('.grid-card input[type="text"]:not([readonly])').forEach(input => {
            input.value = '';
        });
        document.querySelectorAll('.grid-card').forEach(card => {
            card.classList.remove('filled');
            card.classList.add('empty');
            card.querySelector('.status-badge').textContent = 'KOSONG';
            card.style.borderColor = '#ddd';
            card.style.background = 'linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%)';
        });
        if (gridSearchInput) {
            gridSearchInput.value = '';
        }
    }
}
</script>
