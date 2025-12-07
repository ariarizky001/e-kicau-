# QUICK REFERENCE: Grid Input Peserta

## 🎯 Akses Grid Input

```
Dashboard → "Kelas Lomba & Input Peserta" Table → Click [Grid]
atau
Menu Peserta → Pilih Kelas → Click [Grid Input]
```

## 📋 Form Fields per Slot

| Field | Type | Required | Max Length | Notes |
|-------|------|----------|------------|-------|
| Pemilik | Text | No | 255 | Nama pemilik burung |
| Burung | Text | No | 255 | Nama burung |
| Gantangan | Text | No | 50 | Nomor gantangan |
| Alamat | Text | No | 255 | Alamat team |

**Catatan:** Semua field opsional. Slot kosong tidak akan membuat record.

---

## ⚙️ Grid Configuration

### Default
- Baris: 4
- Kolom: 4
- Total Slot: 16

### Custom (Super Admin)
- Baris: 1-10
- Kolom: 1-10
- Total Slot: 1-100

### Cara Mengatur
1. Click "Pengaturan Grid" (ikon roda gigi)
2. Enter Baris & Kolom
3. Lihat Total Slot otomatis hitung
4. Click "Simpan Pengaturan"

---

## 💾 Data Flow

```
1. Input data di grid
2. Click "Simpan Grid Peserta"
3. System validate semua fields
4. Delete peserta lama untuk kelas ini
5. Insert peserta baru yang terisi
6. Auto-generate nomor_urut (1, 2, 3, ...)
7. Redirect ke Peserta List dengan success message
```

---

## 📱 Layout Responsif

| Device | Breakpoint | Layout |
|--------|-----------|--------|
| Desktop | ≥768px | Full grid display |
| Tablet | 576px-768px | 2 kolom |
| Mobile | <576px | 1 kolom |

---

## 🔑 Keyboard Shortcuts

- Tab: Pindah ke field berikutnya
- Shift+Tab: Pindah ke field sebelumnya
- Enter: Submit form (saat di last field)

---

## ⚠️ Important Rules

1. ✅ Hanya isi yang diinput → hanya itu yang disimpan
2. ✅ Nomor urut otomatis → jangan diisi manual
3. ✅ Data lama akan dihapus → saat save grid baru
4. ✅ Satu config per kelas → unique per kelas_lomba_id
5. ✅ Super Admin only → untuk mengubah grid size

---

## 🧪 Validation Rules

### Baris
- Required: ✅
- Type: Integer
- Min: 1
- Max: 10

### Kolom
- Required: ✅
- Type: Integer
- Min: 1
- Max: 10

### Peserta Fields
- Max length: 255 (nama) atau 50 (gantangan)
- Type: String
- Required: ❌ (Semua optional)

---

## 🎨 UI Components

### Grid Card
```
┌─────────────┐
│ No. 1       │ ← Nomor slot
├─────────────┤
│ [Input] ... │ ← 4 input fields
└─────────────┘
```

### Buttons
- **Grid**: Click untuk masuk grid input
- **List**: Click untuk lihat daftar peserta
- **Pengaturan Grid**: Click untuk ubah dimensi
- **Simpan Grid Peserta**: Click untuk simpan
- **Batal**: Click untuk kembali

### Alerts
- Green: Success message (auto-dismiss 4 sec)
- Red: Error message (auto-dismiss 4 sec)
- Blue: Information message
- Yellow: Warning message

---

## 🔍 Troubleshooting

### Grid tidak load
```
1. Refresh page (Ctrl+F5)
2. Check browser console (F12)
3. Clear cache & reload
4. Verify login status
5. Check kelas status = "aktif"
```

### Data tidak tersimpan
```
1. Check minimal 1 slot terisi
2. Look at validation error message
3. Verify field length tidak melebihi limit
4. Check internet connection
5. Try submit ulang
```

### Settings tidak muncul
```
1. Verify login sebagai Super Admin
2. Check user role = "super_admin"
3. Reload page
4. Clear browser cache
```

### Grid size tidak berubah
```
1. Go back ke grid view
2. Reload page (Ctrl+F5)
3. Clear browser cache
4. Check database konfigurasi (GridPesertaConfig)
```

---

## 📊 Grid Size Reference

| Rows | Cols | Total | Use Case |
|------|------|-------|----------|
| 2 | 2 | 4 | Kecil |
| 3 | 3 | 9 | Kecil-Sedang |
| 4 | 4 | 16 | **DEFAULT** |
| 5 | 5 | 25 | Sedang |
| 6 | 9 | 54 | Besar (seperti image reference) |
| 8 | 8 | 64 | Besar |
| 10 | 10 | 100 | Maksimal |

---

## 🔐 Access Levels

| Role | Grid Input | View Settings | Change Config |
|------|-----------|----------------|----------------|
| Super Admin | ✅ | ✅ | ✅ |
| Admin | ✅ | ✅ | ❌ |
| Operator | ✅ | ✅ | ❌ |
| Guest | ❌ | ❌ | ❌ |

---

## 📌 Tips & Tricks

1. **Bulk Entry**: Gunakan Tab untuk navigasi cepat antar field
2. **Copy-Paste**: Jika data sama untuk beberapa baris, copy-paste di Excel dulu, lalu paste ke form
3. **Mobile**: Gunakan landscape mode untuk input lebih nyaman
4. **Verification**: Selalu lihat List view setelah save untuk verify data
5. **Backup**: Download/screenshot sebelum bulk import untuk backup

---

## 🔗 Related Links

- Dashboard: `/dashboard`
- Peserta List: `/peserta?kelas_lomba_id={id}`
- Grid Input: `/peserta/{id}/grid`
- Grid Settings: `/peserta/{id}/grid-settings`
- Kelas Lomba: `/kelas-lomba`
- Juri: `/juri`

---

## 📞 Support

### Error Messages

```
"Jumlah baris harus diisi"
→ Masukkan angka untuk baris

"Jumlah baris harus berupa angka"
→ Jangan gunakan karakter khusus

"Jumlah baris minimal 1"
→ Minimal input: 1

"Jumlah baris maksimal 10"
→ Maksimal input: 10

"Konfigurasi grid berhasil diupdate!"
→ Success! Grid sudah berubah
```

### FAQ

**Q: Bisakah saya ubah grid size tanpa menghapus data peserta?**
A: Ya! Ubah size di pengaturan, data peserta existing akan tetap ada.

**Q: Berapa maksimal peserta per kelas?**
A: Unlimited (Grid 10×10 = 100 slots, bisa di-add lebih banyak di input individual).

**Q: Apakah nomor urut otomatis?**
A: Ya, nomor urut di-generate otomatis sesuai urutan penyimpanan.

**Q: Bisa ganti grid size kapan saja?**
A: Ya, kapan saja melalui "Pengaturan Grid" button.

**Q: Jika saya save grid kosong, apa terjadi?**
A: Tidak ada peserta yang dibuat, semua peserta lama dihapus.

---

## 📋 Checklist Sebelum Submit

- [ ] Minimal 1 slot terisi dengan data lengkap
- [ ] Semua field isian sudah benar
- [ ] Tidak ada typo di nama peserta/burung
- [ ] Grid size sudah sesuai kebutuhan
- [ ] Internet connection stabil
- [ ] Sudah cek preview di List view
- [ ] Ready untuk click "Simpan Grid Peserta"

---

## ✨ Best Practices

1. **Plan First**: Tentukan grid size dulu sebelum input
2. **Test**: Coba dengan 1-2 slot dulu sebelum bulk
3. **Verify**: Selalu lihat List view setelah save
4. **Backup**: Screenshot atau export sebelum mass update
5. **Communication**: Inform team sebelum perubahan grid config
6. **Documentation**: Catat grid config setiap kelas untuk reference

---

**Last Updated:** 2024-12-04  
**Version:** 1.0  
**Language:** Indonesian  
**For:** Aplikasi Penilaian - Bird Competition Management System
