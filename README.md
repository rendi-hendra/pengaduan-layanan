# Sistem Kuesioner Kepuasan Pasien RS Ekahusada
Aplikasi web berbasis **PHP Native** dan **PostgreSQL** untuk mengelola dan menyimpan data survei kepuasan pasien rumah sakit.

Project ini dirancang untuk keperluan pembelajaran dan implementasi sistem informasi sederhana pada lingkungan lokal (**localhost**).

---

## ✨ Fitur Utama
- Form kuesioner kepuasan pasien
- Validasi input wajib
- Penyimpanan data ke database PostgreSQL
- Halaman konfirmasi (Thank You Page)
- Struktur database terpisah dari konfigurasi (aman untuk GitHub)

---

## 🛠 Teknologi yang Digunakan
- PHP Native  
- PostgreSQL  
- HTML & CSS  
- XAMPP (Apache + PHP)  
- Git & GitHub  

---

## 📂 Struktur Folder
pengaduan-layanan-rs/
├── index.php
├── thank-you.php
├── style.css
├── pengaduanlayanan.sql
├── .gitignore
├── README.md
└── images/
└── logo.png

## buka php.ini cari 
- ;extension=pgsql
- ;extension=pdo_pgsql
dan hilangkan ;
lalu, ctrl+save

---

## ⚙️ Cara Menjalankan Project (Localhost)
### 1. Clone Repository
git clone https://github.com/USERNAME/pengaduan-layanan-rs.git


### 2. Pindahkan ke Folder XAMPP
Letakkan folder project ke:
C:\xampp\htdocs\pengaduanlayanan


### 3. Jalankan Server
- Buka **XAMPP Control Panel**
- Start **Apache**
- Pastikan **PostgreSQL Service** aktif

---

### 4. Setup Database PostgreSQL
1. Buka **pgAdmin**
2. Buat database dengan nama:
3. Import file:
pengaduanlayanan.sql
---

### 5. Buat File Konfigurasi Database
Buat file `config.php` di root project:
```php
<?php
$db_host = "localhost";
$db_port = "5432";
$db_name = "pengaduanlayanan";
$db_user = "postgres";
$db_pass = "PASSWORD_DATABASE_KAMU";
```

### 6. Akses Aplikasi
Buka browser dan jalankan:
http://localhost/pengaduan-layanan-rs/index.php

