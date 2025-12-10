# 📦 Panduan Dockerfile - Untuk Mahasiswa DevOps

## 🎯 Apa itu Dockerfile?

**Dockerfile** adalah file resep yang berisi instruksi untuk membuat **Docker Image**. Bayangkan seperti resep masakan - Anda tulis langkah demi langkah, lalu Docker akan "memasak" aplikasi Anda menjadi container yang siap jalan.

---

## 🏗️ Struktur Dockerfile CulturalTrip

### 1️⃣ **FROM** - Memilih Base Image
```dockerfile
FROM php:8.2-fpm-alpine
```
- **Fungsi**: Menentukan image dasar yang akan digunakan
- **php:8.2-fpm-alpine**: PHP versi 8.2 dengan FPM (FastCGI Process Manager) berbasis Alpine Linux
- **Alpine Linux**: Distribusi Linux yang sangat kecil (~5MB) - cocok untuk production
- **Analogi**: Seperti memilih jenis kompor (gas/listrik) sebelum masak

---

### 2️⃣ **WORKDIR** - Set Working Directory
```dockerfile
WORKDIR /var/www/html
```
- **Fungsi**: Menentukan direktori kerja di dalam container
- Semua perintah selanjutnya akan dijalankan di folder ini
- **Analogi**: Seperti menentukan ruangan mana yang akan dipakai untuk kerja

---

### 3️⃣ **RUN** - Menjalankan Perintah
```dockerfile
RUN apk add --no-cache git curl zip nodejs npm
```
- **Fungsi**: Menjalankan command di dalam container saat build
- **apk**: Package manager untuk Alpine Linux (seperti `apt` di Ubuntu)
- **--no-cache**: Tidak menyimpan cache untuk menghemat space
- Digunakan untuk install software, konfigurasi, dll.
- **Analogi**: Seperti menyiapkan bumbu dan alat masak sebelum masak

---

### 4️⃣ **COPY** - Copy File ke Container
```dockerfile
COPY composer.json composer.lock ./
COPY . .
```
- **Fungsi**: Menyalin file dari komputer Anda ke dalam container
- `COPY composer.json composer.lock ./` → Copy file dependency
- `COPY . .` → Copy semua file aplikasi
- **Kenapa bertahap?** → Docker Layer Caching (lebih efisien)
- **Analogi**: Seperti menyiapkan bahan makanan di atas meja

---

### 5️⃣ **EXPOSE** - Buka Port
```dockerfile
EXPOSE 9000
```
- **Fungsi**: Memberitahu Docker bahwa container akan listen di port 9000
- **Port 9000**: Default port untuk PHP-FPM
- Tidak benar-benar membuka port (dilakukan saat `docker run` dengan `-p`)
- **Analogi**: Seperti memasang nomor rumah, tapi pintu belum dibuka

---

### 6️⃣ **ENTRYPOINT & CMD** - Perintah Saat Container Start
```dockerfile
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["php-fpm"]
```
- **ENTRYPOINT**: Script yang **selalu** dijalankan saat container start
- **CMD**: Default command (bisa di-override)
- **entrypoint.sh**: Script untuk setup (migrate, cache, dll)
- **php-fpm**: Service yang menjalankan PHP
- **Analogi**: Seperti SOP pembukaan toko (entrypoint) dan jam kerja (cmd)

---

## 🔄 Docker Layer Caching

**Kenapa kita copy `composer.json` dulu sebelum copy semua file?**

```dockerfile
# Layer 1: Copy dependency files
COPY composer.json composer.lock ./
RUN composer install

# Layer 2: Copy application code
COPY . .
```

**Alasan:**
1. Docker menyimpan setiap step sebagai **layer** yang di-cache
2. Jika `composer.json` tidak berubah, Docker pakai cache layer 1
3. Hanya layer 2 yang rebuild saat kode berubah
4. **Hemat waktu build** dari ~10 menit jadi ~30 detik!

**Ilustrasi:**
```
Code berubah → Layer 1 (cached) ✅ → Layer 2 (rebuild) 🔄
Dependencies berubah → Layer 1 (rebuild) 🔄 → Layer 2 (rebuild) 🔄
```

---

## 🚀 Cara Build dan Run

### Build Image
```bash
# Build image dengan tag "culturaltrip:latest"
docker build -t culturaltrip:latest .

# Build dengan name yang spesifik
docker build -t culturaltrip:v1.0 .

# Build tanpa cache (force rebuild semua)
docker build --no-cache -t culturaltrip:latest .
```

### Run Container
```bash
# Run container dengan port mapping
docker run -d -p 8000:9000 --name culturaltrip-app culturaltrip:latest

# Run dengan environment variables
docker run -d \
  -p 8000:9000 \
  -e DB_HOST=mysql \
  -e DB_PASSWORD=secret \
  --name culturaltrip-app \
  culturaltrip:latest

# Lihat logs
docker logs culturaltrip-app

# Masuk ke dalam container
docker exec -it culturaltrip-app sh
```

---

## 📊 Best Practices yang Diterapkan

### ✅ 1. **Use Alpine Linux**
- Size kecil (~5MB vs Ubuntu ~77MB)
- Lebih aman (attack surface lebih kecil)
- Build dan deploy lebih cepat

### ✅ 2. **Minimize Layers**
- Gabungkan beberapa `RUN` command dengan `&&`
- Hemat storage dan mempercepat build

### ✅ 3. **Leverage Build Cache**
- Copy dependency files dulu sebelum copy kode
- Dependencies jarang berubah, kode sering berubah

### ✅ 4. **Use .dockerignore**
- Exclude file yang tidak perlu (node_modules, .git, vendor)
- Mempercepat build dan mengecilkan image size

### ✅ 5. **Security Practices**
- Use official images (`php:8.2-fpm-alpine`)
- Set proper permissions (`chown`, `chmod`)
- Don't run as root (use `www-data` user)

---

## 🎓 Konsep DevOps yang Dipelajari

### 1. **Containerization**
- Aplikasi dan dependencies dikemas dalam satu unit
- "Works on my machine" → "Works everywhere!"

### 2. **Immutability**
- Container tidak diubah setelah dibuat
- Update = build image baru, bukan edit container lama

### 3. **Reproducibility**
- Dockerfile = resep yang bisa dijalankan siapa saja
- Hasil build selalu sama (kecuali dependency update)

### 4. **Portability**
- Image bisa jalan di laptop, server, cloud, Kubernetes
- Tidak peduli OS host (Windows, Linux, Mac)

---

## 🛠️ Troubleshooting

### Build Error: "composer: not found"
**Solusi**: Pastikan line `COPY --from=composer:2 ...` ada

### Build Error: "npm: not found"
**Solusi**: Install nodejs dan npm di `RUN apk add` section

### Container Exit Immediately
**Solusi**: Check logs dengan `docker logs <container-name>`

### Permission Denied di Storage
**Solusi**: Run `chmod -R 777 storage` di host atau dalam container

---

## 📚 Resources untuk Belajar Lebih Lanjut

1. **Docker Official Docs**: https://docs.docker.com/
2. **Dockerfile Best Practices**: https://docs.docker.com/develop/dev-best-practices/
3. **Laravel Deployment**: https://laravel.com/docs/deployment
4. **Alpine Linux**: https://alpinelinux.org/

---

## ❓ Latihan untuk Mahasiswa

1. **Modifikasi Dockerfile untuk development:**
   - Install Xdebug
   - Tambahkan `--dev` di `composer install`
   - Mount volume untuk live reload

2. **Optimize build time:**
   - Analyze layer sizes dengan `docker history`
   - Identifikasi layer yang paling besar
   - Coba minimize dengan multi-stage build

3. **Security hardening:**
   - Scan vulnerability dengan `docker scan`
   - Update base image ke versi terbaru
   - Remove unnecessary packages

4. **CI/CD Integration:**
   - Build image di GitHub Actions
   - Push ke Docker Hub
   - Auto-deploy ke Kubernetes

---

**Tips untuk Tugas Kuliah:**
- ✅ Screenshot setiap step (build, run, logs)
- ✅ Dokumentasikan error yang Anda temui dan solusinya
- ✅ Buat diagram arsitektur (Dockerfile → Image → Container)
- ✅ Bandingkan performance dengan cara deployment tradisional
- ✅ Jelaskan benefit containerization untuk project Anda

**Selamat Belajar DevOps! 🚀**
