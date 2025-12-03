# 🏛️ CulturalTrip - Eksplorasi Budaya Indonesia

**CulturalTrip** adalah aplikasi web interaktif berbasis Laravel yang menampilkan destinasi wisata budaya Indonesia. Dengan fitur AI Trip Assistant, database budaya lengkap, dan peta interaktif.

## 🎯 Fitur Utama

- 🏛️ **Database Budaya Indonesia** - Koleksi 100+ destinasi wisata budaya
- 🤖 **AI Trip Assistant** - Asisten perjalanan cerdas (coming soon)
- 🗺️ **Peta Interaktif** - Visualisasi peta dengan OpenStreetMap dan Leaflet.js
- 💰 **Harga Transparan** - Informasi tiket, jam buka, dan durasi kunjungan
- 🛡️ **Admin Dashboard** - Panel admin berbasis Filament v4
- 🌐 **Desain Modern** - Responsif dengan Tailwind CSS dan warna budaya Indonesia

## 🛠️ Tech Stack

- **Backend**: Laravel 12
- **Frontend**: Blade Templates + TailwindCSS v4 + Alpine.js
- **Admin Panel**: Filament v4
- **Authentication**: Laravel Breeze v2
- **Testing**: Pest v3
- **Database**: MySQL/MariaDB
- **Maps**: Leaflet.js + OpenStreetMap

## 📋 Persyaratan Sistem

- PHP 8.2+
- Composer
- Node.js & NPM
- MySQL/MariaDB
- XAMPP/WAMP (untuk development lokal)

## 🚀 Instalasi

### 1. Clone Repository (atau extract zip)

```bash
cd S:\xampp\htdocs\cultural-trip
```

### 2. Install Dependencies

```bash
composer install
npm install
```

### 3. Setup Environment

File `.env` sudah dikonfigurasi. Pastikan konfigurasi database sesuai:

```env
DB_CONNECTION=mariadb
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=culturaltrip
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Buat Database

Buka **phpMyAdmin** di `http://localhost/phpmyadmin` dan buat database baru:

```sql
CREATE DATABASE culturaltrip CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Atau gunakan MySQL command line:

```bash
mysql -u root -e "CREATE DATABASE culturaltrip CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

### 5. Jalankan Migrasi

```bash
php artisan migrate
```

### 6. Seed Data

Import data 100 destinasi wisata budaya dari CSV:

```bash
php artisan db:seed
```

Ini akan membuat:
- Admin user (email: `admin@culturaltrip.com`, password: `password`)
- 27 provinsi
- 7 kategori budaya
- 100 destinasi wisata budaya dari `dataset wisata.csv`

### 7. Build Assets

```bash
npm run build
```

Atau untuk development dengan hot reload:

```bash
npm run dev
```

### 8. Jalankan Server

```bash
php artisan serve
```

Atau gunakan:

```bash
composer run dev
```

Aplikasi akan berjalan di: **http://localhost:8000**

## 🎨 Halaman Website

### Frontend (Public)
- **Beranda** (`/`) - Hero section, fitur highlights, galeri destinasi populer
- **Jelajahi** (`/destinasi`) - Grid destinasi dengan search & filter
- **Detail Destinasi** (`/destinasi/{slug}`) - Info lengkap, peta, destinasi terkait

### Admin Panel
- **Dashboard Admin** (`/admin`) - Login dengan:
  - Email: `admin@culturaltrip.com`
  - Password: `password`

Fitur admin:
- CRUD Destinasi
- CRUD Provinsi
- CRUD Kategori
- File upload untuk gambar
- Filter & Search

## 📁 Struktur Database

### Tabel `destinations`
- Nama, slug, deskripsi
- Provinsi & kategori (foreign keys)
- Koordinat (latitude, longitude)
- Jam buka & tutup
- Estimasi durasi kunjungan
- Harga tiket
- Rating
- Images (JSON array)

### Tabel `provinces`
- Nama provinsi
- Slug

### Tabel `categories`
- Nama kategori budaya
- Slug
- Deskripsi

## 🎯 Kategori Budaya

1. Warisan Kerajaan & Kolonial
2. Situs Sejarah & Arkeologi
3. Desa Adat & Kehidupan Tradisional
4. Seni, Kerajinan & Pasar
5. Museum & Monumen
6. Festival & Taman Budaya
7. Situs & Arsitektur Religi

## 🗺️ Fitur Peta

Halaman detail destinasi menggunakan **Leaflet.js** dengan:
- Marker lokasi destinasi
- Popup info (nama, lokasi, harga)
- Link "Dapatkan Arah" ke Google Maps
- Tiles dari OpenStreetMap

## 🎨 Desain & Warna

Tema warna budaya Indonesia:
- **Amber/Gold** - Warna emas batik Indonesia
- **Orange/Red** - Warna tradisional merah Indonesia
- **Gradient** - Kombinasi warm colors

Font: **Plus Jakarta Sans**

## 📝 Commands Penting

```bash
# Development server dengan Vite
composer run dev

# Setup lengkap
composer setup

# Run tests
php artisan test

# Code formatting
vendor/bin/pint

# Clear cache
php artisan optimize:clear

# Seed ulang data
php artisan migrate:fresh --seed
```

## 🔧 Troubleshooting

### Error: Unknown database 'culturaltrip'
- Buat database di phpMyAdmin atau MySQL CLI
- Pastikan nama database di `.env` sesuai

### Frontend tidak update
- Jalankan `npm run build` atau `npm run dev`
- Clear browser cache

### Error Filament
- Jalankan `php artisan filament:upgrade`
- Clear cache: `php artisan optimize:clear`

## 📚 Dokumentasi

- [Laravel 12](https://laravel.com/docs/12.x)
- [Filament v4](https://filamentphp.com/docs/4.x)
- [Tailwind CSS v4](https://tailwindcss.com/docs)
- [Leaflet.js](https://leafletjs.com/)

## 👥 Kontribusi

Project ini dibuat untuk eksplorasi budaya Indonesia. Contributions are welcome!

## 📄 License

MIT License

---

**Built with ❤️ for Indonesian Culture**

🏛️ CulturalTrip - Temukan Keindahan Budaya Indonesia di Ujung Jari Kamu
