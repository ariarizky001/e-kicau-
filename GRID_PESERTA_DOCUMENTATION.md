# Dokumentasi Grid Input Peserta

## Overview
Fitur Grid Input Peserta memungkinkan input data peserta berdasarkan grid yang dapat dikustomisasi (default 4x4) sesuai template yang ditentukan oleh super admin.

## Fitur Utama

### 1. Input Grid Peserta
- **Halaman URL**: `/peserta/{kelas_lomba}/grid`
- **Akses**: Semua user yang terautentikasi
- **Deskripsi**: 
  - Tampilan grid dengan slot untuk input peserta
  - Setiap slot berisi 4 field: Pemilik, Burung, Nomor Gantangan, Alamat
  - Field terlihat dengan ikon yang jelas untuk kemudahan identifikasi
  - Slot yang terisi akan ditampilkan dengan badge "✓ Terisi" dan background hijau

### 2. Fitur Input Grid
- **Grid Dinamis**: Ukuran grid dapat diatur (1x1 hingga 10x10)
- **Visual Feedback**: 
  - Slot kosong dengan header gradient ungu
  - Slot terisi dengan header gradient hijau
  - Hover effect untuk interaksi yang lebih baik
- **Keyboard Navigation**:
  - `Tab`: Pindah ke field berikutnya
  - `Enter`: Pindah ke slot berikutnya
  - `Ctrl+S`: Simpan form
- **Shortcut Buttons**:
  - "Bantuan": Tampilkan tips penggunaan
  - "Hapus Semua": Reset semua field dalam grid
  - "Reset Semua": Kembali ke data sebelumnya

### 3. Validasi Data
- **Duplikasi Nomor Gantangan**: Tidak boleh ada duplikasi dalam satu kelas
- **Duplikasi Nama Burung**: Tidak boleh ada duplikasi dalam satu kelas
- **Nomor Urut Otomatis**: Di-generate berdasarkan urutan slot yang terisi
- **Empty Slot Skip**: Slot kosong secara otomatis dilewati

## Pengaturan Grid

### Akses Pengaturan
- **Halaman URL**: `/peserta/{kelas_lomba}/grid-settings`
- **Tombol**: "Pengaturan" di halaman grid

### Opsi Pengaturan

#### 1. Ubah Ukuran Grid
- Input jumlah baris (1-10)
- Input jumlah kolom (1-10)
- Real-time preview total slot
- Validasi dan error handling

#### 2. Salin dari Kelas Lain
- **Fitur**: Menyalin seluruh data peserta dari kelas lain
- **Kasus Penggunaan**:
  - Kelas dengan struktur peserta sama
  - Template yang sama untuk multiple kelas
  - Copy setup untuk akselerasi data entry
- **Warning**: Data peserta yang ada akan diganti
- **Prasyarat**: Kelas sumber harus memiliki peserta

#### 3. Reset Grid
- **Fitur**: Menghapus semua data peserta dalam satu kelas
- **Konfirmasi Berlapis**: 
  - Modal confirmation dialog
  - Checkbox acknowledge
- **Warning**: Tindakan tidak dapat dibatalkan
- **Info**: Menampilkan jumlah peserta yang akan dihapus

## Data Model

### GridPesertaConfig
```php
{
    'id' => ID,
    'kelas_lomba_id' => Foreign Key,
    'rows' => Integer (1-10),
    'columns' => Integer (1-10),
    'created_at' => Timestamp,
    'updated_at' => Timestamp
}
```

### Peserta
```php
{
    'id' => ID,
    'kelas_lomba_id' => Foreign Key,
    'nomor_urut' => Integer (auto-generated),
    'nama_pemilik' => String,
    'nama_burung' => String,
    'alamat_team' => String,
    'nomor_gantangan' => String,
    'created_at' => Timestamp,
    'updated_at' => Timestamp
}
```

## Controller Methods

### PesertaController

#### `showGrid(KelasLomba $kelasLomba)`
- **Route**: GET `/peserta/{kelas_lomba}/grid`
- **Response**: View `peserta.grid`
- **Data**: Grid config, existing peserta, grid slots

#### `storeGrid(Request $request, KelasLomba $kelasLomba)`
- **Route**: POST `/peserta/{kelas_lomba}/grid/store`
- **Validasi**: 
  - Semua field nullable
  - String max 255
  - Duplikasi check
- **Response**: Redirect dengan success/error message

#### `gridSettings(KelasLomba $kelasLomba)`
- **Route**: GET `/peserta/{kelas_lomba}/grid-settings`
- **Response**: View `peserta.grid-settings`
- **Data**: Grid config, other kelas for copy

#### `updateGridConfig(Request $request, KelasLomba $kelasLomba)`
- **Route**: PUT `/peserta/{kelas_lomba}/grid-config`
- **Validasi**: rows dan columns (1-10)
- **Response**: Redirect dengan success message

#### `copyFromKelas(Request $request, KelasLomba $kelasLomba)`
- **Route**: POST `/peserta/{kelas_lomba}/grid/copy`
- **Validasi**: source_kelas_id exists dan berbeda
- **Action**: Copy peserta dari kelas lain
- **Response**: Redirect dengan success message

#### `resetGrid(Request $request, KelasLomba $kelasLomba)`
- **Route**: POST `/peserta/{kelas_lomba}/grid/reset`
- **Validasi**: confirm checkbox accepted
- **Action**: Hapus semua peserta di kelas
- **Response**: Redirect dengan success message

## Routes

```php
Route::get('/peserta/{kelasLomba}/grid', [PesertaController::class, 'showGrid'])->name('peserta.grid');
Route::post('/peserta/{kelasLomba}/grid/store', [PesertaController::class, 'storeGrid'])->name('peserta.store-grid');
Route::get('/peserta/{kelasLomba}/grid-settings', [PesertaController::class, 'gridSettings'])->name('peserta.grid-settings');
Route::put('/peserta/{kelasLomba}/grid-config', [PesertaController::class, 'updateGridConfig'])->name('peserta.update-grid-config');
Route::post('/peserta/{kelasLomba}/grid/copy', [PesertaController::class, 'copyFromKelas'])->name('peserta.copy-grid');
Route::post('/peserta/{kelasLomba}/grid/reset', [PesertaController::class, 'resetGrid'])->name('peserta.reset-grid');
```

## UI/UX Features

### Responsive Design
- **Desktop (1400px+)**: Full 4x4 atau custom grid
- **Tablet (768-1399px)**: 3 kolom
- **Mobile (576-767px)**: 2 kolom
- **Small Mobile (<576px)**: 1 kolom

### Color Coding
- **Gradient Purple**: Slot kosong (unprocessed)
- **Gradient Green**: Slot terisi (has data)
- **Blue Badge**: Grid slot number, total slots, kelas info
- **Green Badge**: "✓ Terisi" indicator

### Icons
- `<i class="fas fa-th"></i>` - Grid menu
- `<i class="fas fa-user"></i>` - Pemilik field
- `<i class="fas fa-dove"></i>` - Burung field
- `<i class="fas fa-tag"></i>` - Gantangan field
- `<i class="fas fa-map-marker-alt"></i>` - Alamat field
- `<i class="fas fa-cog"></i>` - Settings
- `<i class="fas fa-copy"></i>` - Copy
- `<i class="fas fa-trash"></i>` - Delete/Reset

## Performance Considerations

1. **Grid Rendering**: 
   - Pre-render grid slots untuk load time lebih cepat
   - Hanya 16-100 items biasanya (4x4 hingga 10x10)

2. **Form Submission**:
   - Batch insert for better performance
   - Single DELETE + INSERT operation

3. **Validation**:
   - Client-side preview
   - Server-side validation untuk keamanan

## Future Enhancements

1. **Import/Export Excel**:
   - Export grid ke Excel
   - Import dari Excel template

2. **Drag & Drop**:
   - Reorder peserta via drag-drop
   - Swap antar slot

3. **Template Management**:
   - Save/Load grid templates
   - Multiple template presets

4. **Advanced Search**:
   - Find duplicate entries
   - Bulk edit operations

5. **API Support**:
   - REST API untuk grid CRUD
   - Real-time sync option

## Troubleshooting

### Grid tidak muncul
- Pastikan kelas_lomba_id valid
- Check GridPesertaConfig existence
- Verify user permissions

### Data tidak tersimpan
- Check validation errors
- Verify duplicate nomor_gantangan/nama_burung
- Check database connection

### Slot tidak terisi dengan benar
- Verify grid config rows × columns
- Check peserta count vs slot count
- Review submitted form data

## Admin Guidelines

### Best Practices
1. Set grid size sesuai kebutuhan di awal
2. Gunakan fitur copy untuk kelas dengan struktur sama
3. Validate peserta data sebelum submit
4. Backup data sebelum reset

### Monitoring
- Check activity log untuk grid operations
- Monitor duplicate data issues
- Review grid config history

---

**Last Updated**: December 2024
**Version**: 1.0
