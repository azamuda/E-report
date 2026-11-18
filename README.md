# E-Report

E-Report adalah prototipe aplikasi layanan pengaduan dan aspirasi warga. Antarmukanya menyediakan alur untuk warga dan admin, mulai dari melihat serta mengelola laporan hingga meninjau status penanganan.

## Fitur antarmuka

- Halaman publik, simulasi masuk dan pendaftaran warga.
- Dashboard warga, pembuatan dan pengelolaan laporan, detail laporan, serta profil.
- Dashboard admin untuk meninjau laporan dan memperbarui status.
- Interaksi prototipe seperti notifikasi, unggah gambar, cetak detail, dan ekspor CSV.

> **Catatan:** antarmuka utama saat ini merupakan prototipe sisi browser. Data dan perubahan antarmuka disimpan sementara di browser dan tidak terhubung ke autentikasi atau penyimpanan laporan di server. Rute Laravel yang tersedia hanya `/`, yang menampilkan `resources/views/welcome.blade.php`. Migrasi serta model laporan sudah mulai disiapkan, tetapi belum menjadi alur backend yang berfungsi penuh.

## Teknologi

- PHP 8.3 atau lebih baru
- Laravel 13
- Livewire 4 (tersedia sebagai dependensi proyek)
- Vite dan Tailwind CSS 4 untuk proses aset proyek
- SQLite sebagai konfigurasi database bawaan
- Alpine.js dan Tailwind CSS CDN pada prototipe halaman utama

## Persyaratan

Pastikan perangkat telah memiliki:

- PHP 8.3+ beserta ekstensi yang dibutuhkan Laravel
- Composer
- Node.js dan npm

## Menjalankan aplikasi

Dari direktori proyek, jalankan skrip setup:

```bash
composer run setup
```

Skrip tersebut memasang dependensi, menyiapkan `.env`, membuat application key, menjalankan migrasi, memasang dependensi frontend, dan membangun aset.

Kemudian jalankan server pengembangan:

```bash
composer run dev
```

Buka alamat yang ditampilkan oleh Laravel (biasanya `http://localhost:8000`). Skrip pengembangan menjalankan Laravel, worker antrean, dan Vite secara bersamaan.

Konfigurasi awal menggunakan SQLite. Nilai koneksi database dan pengaturan aplikasi dapat diubah di `.env`.

## Prototipe halaman statis

Folder `E-Report-View/` berisi contoh halaman HTML statis dan aset pendukungnya. Halaman-halaman tersebut dapat dibuka langsung di browser; `E-Report-View/index.html` merupakan indeksnya. Isinya adalah data ilustrasi dan tombolnya tidak terhubung ke backend.

## Pengujian

Jalankan pengujian Laravel dengan:

```bash
composer test
```

## Struktur penting

```text
resources/views/welcome.blade.php  Antarmuka prototipe utama
routes/web.php                    Rute web Laravel
app/Models/Laporan.php            Model laporan
database/migrations/              Skema awal database
E-Report-View/                    Contoh halaman HTML statis
```
