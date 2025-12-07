# GRID PESERTA - TESTING CHECKLIST

## 🧪 Functional Testing

### Grid Display Tests
- [ ] Grid dengan ukuran 4x4 ditampilkan dengan benar
- [ ] Setiap slot menampilkan 4 input fields (Pemilik, Burung, Gantangan, Alamat)
- [ ] Slot kosong berwarna ungu dengan gradient
- [ ] Slot terisi berwarna hijau dengan gradient
- [ ] Badge "✓ Terisi" muncul pada slot dengan data
- [ ] Total slot counter menampilkan jumlah benar (rows × columns)
- [ ] Grid responsive di desktop, tablet, dan mobile

### Input & Validation Tests
- [ ] Keyboard Tab navigation bekerja dengan baik
- [ ] Keyboard Enter berpindah ke slot berikutnya
- [ ] Ctrl+S shortcut menyimpan form
- [ ] Click pada card auto-focus ke field pertama
- [ ] Duplikasi nomor_gantangan ditolak dengan error message
- [ ] Duplikasi nama_burung ditolak dengan error message
- [ ] Empty slots di-skip saat penyimpanan
- [ ] Nomor urut auto-increment dari 1 ke N

### Button Tests
- [ ] "Simpan Grid Peserta" menyimpan data dengan benar
- [ ] "Batal" kembali ke daftar peserta
- [ ] "Hapus Semua" clear semua field
- [ ] "Reset Semua" reload halaman
- [ ] "Pengaturan" membuka halaman settings
- [ ] "Daftar" membuka halaman list
- [ ] "Bantuan" toggle help section

### Settings Page Tests
- [ ] Input rows dapat diubah (1-10)
- [ ] Input columns dapat diubah (1-10)
- [ ] Total slot counter update real-time
- [ ] Save button menyimpan perubahan
- [ ] Info sidebar menampilkan data kelas dengan benar
- [ ] Jumlah peserta menampilkan angka benar

### Copy Feature Tests
- [ ] Dropdown kelas sumber menampilkan kelas aktif lain
- [ ] Select kelas sumber enable tombol copy
- [ ] Copy button copy semua peserta dari kelas sumber
- [ ] Data di kelas tujuan replace dengan benar
- [ ] Warning alert muncul sebelum copy
- [ ] Success message menampilkan jumlah peserta yg disalin
- [ ] Copy dari kelas kosong show error message

### Reset Feature Tests
- [ ] Reset button membuka modal confirmation
- [ ] Modal menampilkan jumlah peserta akan dihapus
- [ ] Checkbox confirmation harus dicentang
- [ ] Tombol "Ya, Hapus Semua" disabled sampai checkbox dicentang
- [ ] Click tombol hapus menghapus semua peserta
- [ ] Success message muncul setelah reset
- [ ] Grid menjadi kosong setelah reset

## 🎨 UI/UX Testing

### Visual Tests
- [ ] Gradient colors tampil dengan benar
- [ ] Icons tampil dengan jelas
- [ ] Layout tidak overlapping pada berbagai ukuran
- [ ] Alert styling konsisten
- [ ] Modal dialog styling rapi
- [ ] Responsive design bekerja pada semua breakpoints

### Interactive Tests
- [ ] Hover effects pada cards smooth
- [ ] Focus states jelas terlihat
- [ ] Transitions/animations halus
- [ ] Loading state (spinner) tampil saat submit
- [ ] Tooltips appear on hover
- [ ] Sticky sidebar tetap fixed saat scroll

### Alert & Notification Tests
- [ ] Success alert muncul & auto-dismiss
- [ ] Error alert muncul & menampilkan pesan jelas
- [ ] Warning alert muncul untuk operasi berbahaya
- [ ] Info alert muncul untuk informasi
- [ ] Alerts auto-dismiss setelah 5 detik
- [ ] Manual dismiss via close button bekerja

## 🔒 Security & Data Tests

### Validation Tests
- [ ] Duplikasi nomor_gantangan per kelas dicegah
- [ ] Duplikasi nama_burung per kelas dicegah
- [ ] Max length validation (255 chars) bekerja
- [ ] Max length gantangan (50 chars) bekerja
- [ ] XSS prevention (special chars) bekerja
- [ ] CSRF token disertakan dalam form

### Authorization Tests
- [ ] Hanya authenticated user dapat akses
- [ ] Non-eksisten kelas_id show 404
- [ ] Copy/Reset hanya bekerja untuk user's kelas
- [ ] Activity logged untuk operasi kritis

### Data Integrity Tests
- [ ] Peserta dengan nomor_urut 1 dimulai dari pertama
- [ ] Nomor urut tidak ada gap setelah penyimpanan
- [ ] Tidak ada duplicate peserta di DB
- [ ] Relasi kelas_lomba_id valid
- [ ] Timestamps (created_at, updated_at) update dengan benar

## 📱 Responsive Design Tests

### Desktop (1400px+)
- [ ] Full 4×4 grid ditampilkan comfortable
- [ ] All fields visible tanpa scroll horizontal
- [ ] Buttons arranged dengan baik

### Tablet (992-1399px)
- [ ] Grid max 3 kolom
- [ ] Layout tetap readable
- [ ] Buttons tetap accessible

### Small Devices (576-991px)
- [ ] Grid 2 kolom
- [ ] Font sizes readable
- [ ] Buttons appropriate size

### Mobile (<576px)
- [ ] Grid 1 kolom
- [ ] All inputs full width
- [ ] Buttons stacked vertically

## 🔄 Database Tests

### Create Tests
- [ ] Peserta baru terbuat dengan nomor_urut benar
- [ ] GridPesertaConfig otomatis dibuat jika belum ada
- [ ] Timestamps terisi otomatis

### Read Tests
- [ ] Query peserta per kelas cepat (<200ms)
- [ ] GridPesertaConfig loaded correctly
- [ ] Relationship loading bekerja (eager loading)

### Update Tests
- [ ] Grid config update applied correctly
- [ ] Peserta data update tidak affect kelas lain
- [ ] Copy operation atomic & consistent

### Delete Tests
- [ ] Reset delete semua peserta di kelas
- [ ] Peserta lain di kelas berbeda tetap aman
- [ ] GridPesertaConfig tidak terhapus saat reset

## 🚀 Performance Tests

### Load Tests
- [ ] Grid dengan 16 slots load < 500ms
- [ ] Grid dengan 100 slots load < 1s
- [ ] Form submit dengan 100 slots < 2s
- [ ] Page transitions smooth

### Resource Tests
- [ ] No console errors
- [ ] No memory leaks saat long session
- [ ] Network requests optimized

## 📝 Data Entry Workflow Tests

### Scenario 1: Full Grid Entry
1. [ ] Buka grid untuk kelas
2. [ ] Isi semua 16 slots (4×4)
3. [ ] Simpan
4. [ ] Verify di daftar peserta (16 entries)
5. [ ] Verify nomor urut 1-16

### Scenario 2: Partial Grid Entry
1. [ ] Isi hanya 5 slots dari 16
2. [ ] Leave 11 slots kosong
3. [ ] Simpan
4. [ ] Verify di daftar (5 entries)
5. [ ] Verify nomor urut 1-5

### Scenario 3: Duplikasi Prevention
1. [ ] Isi slot 1: Burung = "Lovebird1", Gantangan = "100"
2. [ ] Isi slot 2: Burung = "Lovebird1", Gantangan = "101"
3. [ ] Simpan
4. [ ] Verify error: duplikasi nama_burung
5. [ ] Tidak disimpan

### Scenario 4: Resize Grid
1. [ ] Konfigurasi grid 3×3 (9 slots)
2. [ ] Isi 5 slots
3. [ ] Ubah ke 4×4 (16 slots)
4. [ ] Verify data peserta masih ada di 5 slots pertama
5. [ ] Grid expanded untuk 11 slots kosong

### Scenario 5: Copy Kelas
1. [ ] Kelas A memiliki 10 peserta
2. [ ] Kelas B kosong
3. [ ] Dari Kelas B, copy dari Kelas A
4. [ ] Verify Kelas B sekarang punya 10 peserta (sama dengan A)
5. [ ] Verify nomor urut 1-10

### Scenario 6: Reset Kelas
1. [ ] Kelas memiliki 16 peserta
2. [ ] Buka grid settings
3. [ ] Klik "Hapus Semua Data"
4. [ ] Confirm di modal
5. [ ] Verify success message
6. [ ] Verify daftar peserta kosong

## 🔗 Integration Tests

### Navigation Tests
- [ ] Grid link dari index page bekerja
- [ ] Settings link dari grid page bekerja
- [ ] Kembali link ke list page bekerja
- [ ] Breadcrumb navigation bekerja

### Data Consistency Tests
- [ ] Data di grid match dengan list peserta
- [ ] Nomor urut di grid = di list
- [ ] Filter kelas_lomba_id bekerja di list

## 📊 Browser Compatibility Tests

- [ ] Chrome (latest)
- [ ] Firefox (latest)
- [ ] Safari (latest)
- [ ] Edge (latest)
- [ ] Mobile Safari
- [ ] Chrome Mobile

## ✅ Sign-Off Checklist

- [ ] Semua functional tests passed
- [ ] Semua UI/UX tests passed
- [ ] Semua security tests passed
- [ ] Semua responsive tests passed
- [ ] Database integrity verified
- [ ] Performance acceptable
- [ ] No console errors
- [ ] Documentation complete
- [ ] Code reviewed
- [ ] Ready for production

---

## 📋 Test Results

### Date: ___________
### Tester: ___________
### Overall Status: [ ] Pass [ ] Fail

### Notes:
```
[Add any notes or issues found]
```

### Sign-off:
- QA Lead: ___________
- Date: ___________

---

**Test Checklist Version**: 1.0
**Last Updated**: December 2024
