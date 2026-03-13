# Vehicle Management & Booking System

Aplikasi ini dibuat untuk membantu perusahaan dalam mengelola kendaraan, melakukan pemesanan kendaraan, serta melakukan proses persetujuan (approval) pemakaian kendaraan.

---

# 📌 Teknologi yang Digunakan

| Komponen         | Versi                          |
| ---------------- | ------------------------------ |
| PHP              | 8.2                            |
| Database         | MySQL 8                        |
| Framework        | Laravel 11                     |
| Admin Panel      | Filament 3                     |
| Library Tambahan | Filament Shield, Laravel Excel |

---

# 👤 Akun Login

Gunakan akun berikut untuk mengakses sistem.

| Role     | Email                                               | Password |
| -------- | --------------------------------------------------- | -------- |
| Admin    | [admin@admin.com]    | admin |
| Approver  | [budi-santoso@gmail.com]   | password |
| Approver | [andi-wijaya@gmail.com] | password |

---

# 🗄️ Database

Database yang digunakan adalah **MySQL**.

Contoh konfigurasi `.env`:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=vehicle_management
DB_USERNAME=root
DB_PASSWORD=
```

---

# ⚙️ Instalasi Aplikasi

1. Clone repository

```
git clone https://github.com/username/repository-name.git
```

2. Masuk ke folder project

```
cd repository-name
```

3. Install dependency

```
composer install
```

4. Copy file environment

```
cp .env.example .env
```

5. Generate key

```
php artisan key:generate
```

6. Atur konfigurasi database di file `.env`

7. import db vehicle_management.sql di phpmyadmin

8. Jalankan aplikasi

```
php artisan serve
```

Aplikasi dapat diakses di:

```
http://127.0.0.1:8000
```

---

# 🚗 Panduan Penggunaan Aplikasi

### 1. Instal dan Jalankan Aplikasi Terlebih dahulu

### 2. Login ke Aplikasi

* Login ke aplikasi dapat menggunakan username yang disediakan

### 3. Jika Anda Login Sebagai Admin

* Anda Dapat melihat dashboard, laporan periodik pemesanan (bisa export excel) dan laporan penggunaan kendaraan
* Anda dapat mencatat pengeluaran bahan bakar di Fuel Logs
* Anda dapat mencatat service di Service Logs
* Anda dapat menambah, mengedit, ataupun menghapus kendaraan
* Anda dapat menambah, mengedit, ataupun menghapus pengemudi / driver
* Anda dapat menambah, mengedit, ataupun menghapus region
* Anda dapat mengajukan pemesanan melalui menu bookings
* Anda dapat mencatat pengembalian mobil melalui  menu return vehicle
* Anda dapat  melihat semua logs aplikasi melalui menu activity logs (saat ini belum berfungsi)

### 4. Jika Anda Login Sebagai Approver

* Anda Dapat melihat dashboard
* Anda dapat menyetujui pemesanan di menu approvals
* Anda dapat  melihat semua logs aplikasi melalui menu activity logs (saat ini belum berfungsi)

---

# 📊 Alur Penggunaan Sistem

### 1. Login

User login menggunakan akun yang tersedia.

### 2. Membuat Booking Kendaraan

Employee dapat membuat permintaan pemakaian kendaraan.

### 3. Proses Approval

Manager akan melakukan approval terhadap booking yang diajukan.

### 4. Penggunaan Kendaraan

Setelah disetujui, kendaraan dapat digunakan sesuai jadwal.

### 5. Monitoring & Report

Admin dapat melihat laporan pemakaian kendaraan dan mengekspor data ke Excel.

### 6. Pengembalian Kendaraan

Setelah selesai admin bisa menginput pengembalian

---

# 📄 Catatan

Aplikasi ini dibuat sebagai bagian dari **Technical Test Fullstack Developer dari PT Sekawan Media Informatika**.
