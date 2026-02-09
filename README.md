# Pengaduan Layanan  Instruksi Singkat

Panduan cepat untuk menjalankan proyek ini setelah clone/download.

---

## Persyaratan
- PHP (7.4+)
- MySQL/MariaDB (atau MariaDB bawaan Laragon/XAMPP)
- Web server (Apache atau built-in dari Laragon/XAMPP)

---

## 1. Clone / Download

Clone repository atau download ZIP lalu ekstrak ke folder web server Anda (contoh Laragon: C:/laragon/www/, XAMPP: C:/xampp/htdocs/).

Contoh:
```
git clone https://github.com/achmadchamdan2412-ai/pengaduan-layanan.git
```

---

## 2. Update Database (Import SQL)

File SQL yang disertakan: pengaduan_layanan.sql (root repository).

1) Buat database baru (contoh nama: pengaduan_layanan).

Dengan mysql (terminal):
```bash
mysql -u root -p -e "CREATE DATABASE pengaduan_layanan CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;"
mysql -u root -p pengaduan_layanan < pengaduan_layanan.sql
```

Jika menggunakan Laragon/XAMPP dan user root tanpa password:
```bash
mysql -u root pengaduan_layanan < pengaduan_layanan.sql
```

2) Atau import melalui phpMyAdmin: pilih database baru  Import  pilih pengaduan_layanan.sql  Jalankan.

---

## 3. Update Konfigurasi Koneksi Database

Sesuaikan credential database pada file konfigurasi berikut (sesuaikan path jika perlu):
- config/config.php
- config/db.php (jika ada)
- atau file config.php di root (jika proyek menggunakan file tersebut)

Contoh pengaturan (MySQL):
```php
$db_host = '127.0.0.1';
$db_name = 'pengaduan_layanan';
$db_user = 'root';
$db_pass = '';
```

Catatan: periksa semua file config.php atau db.php di repository untuk memastikan semua koneksi diperbarui.

---

## 4. Menjalankan Aplikasi

- Mulai Apache (dan MySQL) melalui Laragon/XAMPP.
- Akses aplikasi di browser: http://localhost/<nama-folder-repo>

---

## 5. Rekap Commit Hari Ini (2026-02-09)

- 0fe1651 2026-02-09  Refactor SQL schema for improved structure and data integrity (Rendi Hendra Syahputra)
- cde5fea 2026-02-09  Refactor code structure for improved readability and maintainability (Rendi Hendra Syahputra)
- 8134a3d 2026-02-09  Refactor kepuasan.php for improved database connection handling and form submission logic (achmadchamdan2412-ai)
- 93fbd1a 2026-02-09  Refactor code structure for improved readability and maintainability (achmadchamdan2412-ai)

---

Jika Anda mau, saya bisa langsung commit perubahan README ini ke branch main. Mau saya commit sekarang?
