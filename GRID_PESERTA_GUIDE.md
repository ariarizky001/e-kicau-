# Panduan Penggunaan Grid Input Peserta

## Fitur Baru: Grid-Based Participant Input System

Sistem grid peserta memungkinkan input data peserta secara massal dalam format grid yang dapat dikustomisasi sesuai kebutuhan kelas.

### 📋 Daftar Fitur

1. **Grid Input dengan Layout Kustom**
   - Default: 4×4 (16 slot peserta)
   - Dapat dikonfigurasi hingga 10×10 (100 slot maksimum)
   - Responsive design untuk berbagai ukuran layar

2. **Konfigurasi Grid per Kelas**
   - Super Admin dapat mengatur jumlah baris dan kolom
   - Konfigurasi unik per kelas lomba
   - Live preview total slot saat mengatur

3. **Input Form Komprehensif**
   - Setiap slot berisi 4 field: Pemilik, Burung, Gantangan, Alamat
   - Label dan placeholder yang jelas
   - Responsive design untuk mobile

4. **Validasi dan Penyimpanan**
   - Hanya baris yang terisi yang disimpan
   - Nomor urut otomatis berdasarkan input order
   - Penghapusan data lama sebelum menyimpan baru

---

## 🚀 Cara Menggunakan

### 1. Akses Grid Input dari Dashboard

```
Dashboard → Tabel "Kelas Lomba & Input Peserta" → Klik tombol "Grid"
```

Atau langsung dari menu Peserta:

```
Menu Peserta → Pilih Kelas Lomba → Klik "Grid Input"
```

### 2. Atur Konfigurasi Grid (Super Admin)

**Langkah:**
1. Di halaman Grid Input, klik tombol **"Pengaturan Grid"** (ikon roda gigi)
2. Masukkan jumlah **Baris** (1-10)
3. Masukkan jumlah **Kolom** (1-10)
4. Total slot otomatis terhitung (Baris × Kolom)
5. Klik **"Simpan Pengaturan"**

**Contoh:**
- Untuk grid 4×4: Baris=4, Kolom=4 → Total 16 slot
- Untuk grid 6×9: Baris=6, Kolom=9 → Total 54 slot

### 3. Input Data Peserta

**Langkah:**
1. Akses halaman Grid Input untuk kelas yang diinginkan
2. Setiap kotak mewakili satu slot peserta dengan nomor urut
3. Isi form di setiap slot:
   - **Pemilik**: Nama pemilik burung
   - **Burung**: Nama burung
   - **Gantangan**: Nomor gantangan (opsional)
   - **Alamat**: Alamat tim (opsional)

4. Slot kosong boleh dibiarkan kosong (tidak wajib diisi semua)
5. Klik **"Simpan Grid Peserta"** setelah selesai input

**Tips:**
- Scroll horizontal jika grid terlalu lebar (mobile/tablet)
- Input field akan highlight saat di-focus (border biru)
- Hover pada kotak akan menunjukkan efek shadow

### 4. Verifikasi Data

**Dari halaman Grid:**
- Lihat data yang sudah diinput langsung di grid
- Edit baris individual sebelum menyimpan

**Dari halaman List Peserta:**
- Klik "List" untuk melihat daftar peserta dalam format tabel
- Tabel menampilkan semua peserta terurut berdasarkan nomor urut
- Edit atau hapus individual peserta jika perlu

---

## 🔧 Konfigurasi Grid untuk Super Admin

### Menu Pengaturan Grid

**Akses:**
```
Dashboard/Peserta → Grid Input → Tombol "Pengaturan Grid"
```

**Fitur:**
- Input Baris: Jumlah baris grid (min 1, max 10)
- Input Kolom: Jumlah kolom grid (min 1, max 10)
- Real-time update total slot
- Informasi kelas lomba (status, batas peserta)
- Tombol "Simpan Pengaturan" untuk menyimpan konfigurasi

### Validasi

- Jumlah baris harus angka, minimal 1, maksimal 10
- Jumlah kolom harus angka, minimal 1, maksimal 10
- Pesan error akan muncul jika input tidak valid

---

## 📊 Data Flow

```
Dashboard
    ↓
Klik "Grid" pada Kelas Lomba
    ↓
Halaman Grid Input
    ├─ Tampilkan grid sesuai konfigurasi
    ├─ Load data peserta existing (jika ada)
    ├─ Biarkan slot kosong untuk input baru
    └─ Tombol: Simpan, Pengaturan, Kembali
    
Klik "Pengaturan Grid"
    ↓
Halaman Grid Settings
    ├─ Input baris/kolom
    ├─ Preview total slot
    └─ Simpan konfigurasi (hanya Super Admin)
    
Klik "Simpan Grid Peserta"
    ↓
Controller: storeGrid()
    ├─ Validasi data
    ├─ Delete peserta lama untuk kelas ini
    ├─ Insert peserta baru yang terisi
    ├─ Generate nomor_urut otomatis
    └─ Redirect ke list peserta dengan success message
```

---

## 💾 Database Schema

### Tabel: grid_peserta_configs

```sql
CREATE TABLE grid_peserta_configs (
  id BIGINT PRIMARY KEY,
  kelas_lomba_id BIGINT NOT NULL UNIQUE,
  rows INTEGER DEFAULT 4,
  columns INTEGER DEFAULT 4,
  created_at TIMESTAMP,
  updated_at TIMESTAMP,
  FOREIGN KEY (kelas_lomba_id) REFERENCES kelas_lomba(id) ON DELETE CASCADE
);
```

**Fields:**
- `id`: Primary key
- `kelas_lomba_id`: Foreign key ke tabel kelas_lomba (UNIQUE - satu config per kelas)
- `rows`: Jumlah baris (default 4)
- `columns`: Jumlah kolom (default 4)
- `created_at`, `updated_at`: Timestamp

---

## 🔐 Keamanan & Akses

### Authorization

- **Super Admin**: Dapat mengakses semua fitur (grid input + pengaturan)
- **Admin**: Dapat mengakses grid input (tidak dapat mengubah konfigurasi)
- **Operator**: Dapat mengakses grid input (tidak dapat mengubah konfigurasi)

### Routes

```php
// Protected by 'auth' middleware
GET  /peserta/{kelasLomba}/grid              → showGrid()
POST /peserta/{kelasLomba}/grid/store        → storeGrid()
GET  /peserta/{kelasLomba}/grid-settings     → gridSettings()
PUT  /peserta/{kelasLomba}/grid-config       → updateGridConfig()
```

---

## 📱 Responsive Design

### Desktop (≥768px)
- Grid ditampilkan penuh sesuai konfigurasi
- Semua 4 field form terlihat dalam setiap kotak

### Tablet (576px - 768px)
- Grid menjadi 2 kolom
- Dapat scroll horizontal jika perlu

### Mobile (<576px)
- Grid menjadi 1 kolom
- Optimal untuk satu demi satu input

---

## ⚠️ Catatan Penting

1. **Data Lama Akan Dihapus**: Saat menyimpan grid baru, semua peserta lama untuk kelas tersebut akan dihapus
2. **Hanya Isi Terisi yang Disimpan**: Slot kosong tidak akan membuat record peserta
3. **Nomor Urut Otomatis**: Nomor urut (nomor_urut) dibuat secara otomatis berdasarkan urutan penyimpanan
4. **Unik per Kelas**: Setiap kelas memiliki konfigurasi grid sendiri
5. **Default**: Jika belum ada konfigurasi, sistem akan otomatis membuat default 4×4

---

## 🐛 Troubleshooting

### Grid tidak muncul
- Pastikan sudah login
- Periksa apakah kelas lomba status "aktif"
- Clear cache browser (Ctrl+F5)

### Tombol "Pengaturan Grid" tidak muncul
- Hanya Super Admin yang bisa melihat/mengakses pengaturan
- Login sebagai Super Admin

### Data tidak tersimpan
- Periksa console browser untuk error message
- Pastikan minimal satu slot terisi
- Cek koneksi internet

### Validasi error saat input
- Perhatikan pesan error di atas form
- Pastikan data sesuai dengan format (text max 255 karakter)
- Nomor gantangan dan alamat opsional (boleh kosong)

---

## 📝 Update & Maintenance

### Migrasi Database
```bash
php artisan migrate
```

### Syntax Check
```bash
php -l app/Http/Controllers/PesertaController.php
```

### Route List
```bash
php artisan route:list | grep peserta
```

---

## 📞 Support

Untuk pertanyaan atau masalah, hubungi administrator sistem.

---

**Last Updated:** 2024-12-04  
**Version:** 1.0  
**Framework:** Laravel 12.0
