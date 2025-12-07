# Grid Peserta - Quick Start Guide

## 🎯 Apa itu Grid Peserta?

Grid Peserta adalah fitur input data peserta & burung menggunakan layout grid yang dapat dikustomisasi. Default 4x4 (16 slot), tapi bisa diubah hingga 10x10 (100 slot) sesuai kebutuhan.

## 📍 Akses Halaman

### Via Main Menu
1. Login ke aplikasi
2. Buka menu **Peserta & Burung**
3. Pilih kelas dari dropdown "Input Peserta dengan Grid"
4. Klik tombol **"Buka Grid Input"** (warna orange)

### Direct URL
```
/peserta/{kelas_lomba_id}/grid
```

## 🎮 Cara Menggunakan

### Step 1: Buka Grid
```
Dashboard → Peserta & Burung → Pilih Kelas → Buka Grid Input
```

### Step 2: Isi Data
Setiap slot berisi 4 field:

| Field | Deskripsi | Required |
|-------|-----------|----------|
| Pemilik | Nama pemilik/peserta | Yes (minimal) |
| Burung | Nama burung | Yes (minimal) |
| Gantangan | Nomor gantangan/leg band | No |
| Alamat | Alamat/team | No |

**Tips:** Minimal isi Pemilik ATAU Burung untuk satu slot dianggap terisi.

### Step 3: Simpan
Klik tombol **"Simpan Grid Peserta"** untuk menyimpan semua data.

### Step 4: Verifikasi
Klik "Daftar" atau kembali ke index untuk lihat hasil.

## ⌨️ Keyboard Shortcuts

| Shortcut | Fungsi |
|----------|--------|
| `Tab` | Pindah ke field berikutnya |
| `Enter` | Pindah ke slot berikutnya |
| `Ctrl+S` | Simpan form |
| Click Card | Focus ke field pertama di slot |

## 🎨 Visual Indicators

- **Ungu Gradient**: Slot kosong (belum diisi)
- **Hijau Gradient**: Slot terisi dengan data
- **Badge ✓**: Menunjukkan slot sudah ada data
- **Red Border**: Jika ada error/duplikasi

## ⚙️ Mengatur Grid

### Buka Settings
Dari halaman grid, klik tombol **"Pengaturan"** (top right).

### Ubah Ukuran
1. Input **Jumlah Baris** (1-10)
2. Input **Jumlah Kolom** (1-10)
3. Lihat preview total slot
4. Klik **"Simpan Pengaturan"**

### Copy dari Kelas Lain
1. Scroll ke bagian "Salin Data dari Kelas Lain"
2. Pilih **Kelas Sumber** dari dropdown
3. Klik **"Salin Data"**
4. Confirm di modal dialog
5. Data akan disalin otomatis

⚠️ **Warning**: Data existing akan diganti!

### Reset Grid (Hapus Semua)
1. Scroll ke bagian "Reset Grid"
2. Klik **"Hapus Semua Data"**
3. Modal dialog akan muncul
4. **Centang** checkbox konfirmasi
5. Klik **"Ya, Hapus Semua"**

⚠️ **Warning**: Tindakan tidak bisa dibatalkan!

## ✅ Validasi Data

### Sistem akan menolak jika:
- ❌ Nomor gantangan duplikat dalam satu kelas
- ❌ Nama burung duplikat dalam satu kelas
- ❌ Karakter lebih dari 255 (pemilik, burung, alamat)
- ❌ Karakter lebih dari 50 (nomor gantangan)

### Sistem akan skip jika:
- ✓ Slot kosong (semua field kosong) = tidak disimpan

## 🔢 Nomor Urut

- Auto-generated dari 1, 2, 3, ... N
- Urutan berdasarkan slot dari kiri → kanan, atas → bawah
- Slot kosong tidak menghasilkan nomor urut
- Jika ubah/delete, nomor urut otomatis di-reorder

## 📱 Responsive Design

| Device | Layout |
|--------|--------|
| Desktop (1400px+) | Full grid (4×4 atau custom) |
| Tablet (768px+) | 3 kolom |
| Small Mobile (576px+) | 2 kolom |
| Mobile (<576px) | 1 kolom |

## 🆘 Troubleshooting

### Q: Grid tidak muncul
**A:** Refresh halaman, pastikan kelas sudah dipilih

### Q: Data tidak tersimpan
**A:** Cek error message, pastikan tidak ada duplikasi nomor gantangan/nama burung

### Q: Duplikasi ditolak padahal beda
**A:** Mungkin ada spasi/karakter khusus. Cek spelling dengan teliti

### Q: Keyboard navigation tidak jalan
**A:** Pastikan cursor ada di input field, tidak di luar

### Q: Grid ukuran berubah tapi data hilang
**A:** Data tetap ada di slot awal, slot baru kosong. Tidak akan hilang

### Q: Tombol copy disabled
**A:** Pilih kelas sumber yang berbeda & punya peserta

## 📊 Contoh Workflow

### Scenario 1: Input Peserta Baru

```
1. Dashboard → Peserta & Burung
2. Pilih "Kelas M" dari dropdown
3. Klik "Buka Grid Input"
4. Isi slot 1-10 dengan data peserta
5. Tab/Enter untuk navigasi
6. Klik "Simpan Grid Peserta"
7. Lihat hasil di "Daftar Peserta"
```

### Scenario 2: Copy Template

```
1. Dari Settings Grid Kelas N
2. Pilih "Salin dari Kelas M"
3. Pilih Kelas M dari dropdown
4. Klik "Salin Data"
5. Confirm di modal
6. Selesai! Semua peserta Kelas M tercopy ke Kelas N
```

### Scenario 3: Customize Ukuran

```
1. Kelas Khusus butuh 6×5 = 30 slot
2. Buka Settings Grid
3. Input Rows = 6, Columns = 5
4. Klik "Simpan Pengaturan"
5. Grid berubah jadi 6×5
6. Isi data peserta
7. Simpan
```

## 📞 FAQ

**Q: Bisa edit setelah disimpan?**
A: Ya, buka grid lagi, edit, simpan ulang.

**Q: Bisa export ke Excel?**
A: Fitur ini akan datang di versi berikutnya.

**Q: Bagaimana jika salah simpan?**
A: Edit ulang atau reset & input ulang.

**Q: Ada limit berapa peserta?**
A: Grid max 10×10 = 100 peserta, batas kelas bisa di-setting terpisah.

**Q: Bisa drag-drop reorder?**
A: Fitur ini akan datang di versi berikutnya.

## 🎓 Tips & Tricks

1. **Tab faster**: Tab untuk cepat pindah field
2. **Enter untuk next slot**: Lebih cepat dari mouse
3. **Bantuan button**: Klik untuk lihat tips detail
4. **Hapus semua**: Gunakan untuk mulai fresh
5. **Copy template**: Hemat waktu untuk kelas sama struktur
6. **Check duplikasi**: Review daftar sebelum submit

## 📚 Dokumentasi Lengkap

Untuk dokumentasi lebih detail, lihat file:
- `GRID_PESERTA_DOCUMENTATION.md` - Full documentation
- `GRID_PESERTA_IMPLEMENTATION_SUMMARY.md` - Implementation details
- `GRID_PESERTA_TESTING_CHECKLIST.md` - Testing guide

## 🔄 Update & Changelog

### v1.0 (December 2024)
- ✅ Initial release
- ✅ Grid input dengan 4x4 default
- ✅ Keyboard navigation
- ✅ Validasi duplikasi
- ✅ Copy kelas feature
- ✅ Reset grid feature
- ✅ Responsive design

### Planned v1.1
- 🔜 Export/Import Excel
- 🔜 Drag & drop reordering
- 🔜 Template management
- 🔜 Bulk operations

---

**Last Updated**: December 2024
**Version**: 1.0
**Status**: Production Ready ✅

Untuk bantuan lebih lanjut, hubungi admin atau lihat dokumentasi lengkap.
