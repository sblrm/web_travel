# 🚀 DevOps Flow - CulturalTrip

## 📋 Overview Singkat

**DevOps** adalah praktik yang menggabungkan **Development** (pengembangan) dan **Operations** (operasional) untuk mempercepat delivery aplikasi dengan otomasi dan monitoring.

---

## 🔄 DevOps Pipeline CulturalTrip

```
┌─────────────┐      ┌──────────────┐      ┌─────────────┐      ┌──────────────┐
│   Developer │─────▶│  Git Push    │─────▶│   GitHub    │─────▶│ CI/CD Trigger│
│  Write Code │      │  to GitHub   │      │  Repository │      │ (Actions)    │
└─────────────┘      └──────────────┘      └─────────────┘      └──────────────┘
                                                                         │
                                                                         ▼
┌─────────────┐      ┌──────────────┐      ┌─────────────┐      ┌──────────────┐
│   Monitor   │◀─────│  Production  │◀─────│  Kubernetes │◀─────│ Build & Test │
│   & Logs    │      │  Running App │      │   Deploy    │      │  (Automated) │
└─────────────┘      └──────────────┘      └─────────────┘      └──────────────┘
```

---

## 🎯 Flow Detail (Step by Step)

### **Step 1: Development** 👨‍💻
```bash
# Developer menulis kode di laptop
git add .
git commit -m "feat: add new feature"
git push origin main
```
**Output**: Kode terupload ke GitHub

---

### **Step 2: Continuous Integration (CI)** 🔧
File: `.github/workflows/tests.yml`

```yaml
# Otomatis jalan saat push ke GitHub
1. Install dependencies (composer, npm)
2. Run code quality check (Pint)
3. Run automated tests (Pest)
4. Check security vulnerabilities
```

**Jika GAGAL** ❌ → Developer dapat notifikasi, fix error, push lagi
**Jika SUKSES** ✅ → Lanjut ke tahap berikutnya

---

### **Step 3: Continuous Deployment (CD)** 🚢
File: `.github/workflows/deploy-kubernetes.yml`

```yaml
# Otomatis deploy ke production (bisa manual trigger)
1. Build Docker Image
2. Push to Docker Hub
3. Deploy to Kubernetes cluster
4. Update running containers (zero downtime)
```

**Output**: Aplikasi baru sudah jalan di production!

---

### **Step 4: Containerization** 🐳
File: `Dockerfile`

```dockerfile
# Membungkus aplikasi dalam Docker container
1. Install PHP, dependencies, extensions
2. Copy application code
3. Build frontend assets (npm run build)
4. Setup permissions
5. Ready to run!
```

**Benefit**: Jalan di laptop, server, cloud → **sama persis**!

---

### **Step 5: Orchestration** ☸️
Folder: `k8s/`

```yaml
# Kubernetes manage containers
- app-deployment.yaml     → Jalankan 3 replicas app
- mysql-statefulset.yaml  → Database persistent
- redis-deployment.yaml   → Caching layer
- ingress.yaml           → Entry point (domain)
- hpa.yaml               → Auto-scaling (traffic tinggi)
```

**Benefit**: Auto-restart jika crash, auto-scale jika ramai user

---

### **Step 6: Monitoring** 📊
```
- Docker logs  → Lihat error aplikasi
- Kubernetes dashboard → Monitor resource usage
- GitHub Actions logs → Trace build/deploy history
```

---

## 🎨 Visualisasi Flow (Untuk PPT)

### **Traditional Deployment** 😓
```
Developer → Manual upload via FTP → Server down saat update → User complain
   ↓            ↓                        ↓                         ↓
 1 jam       30 menit                  10 menit                Stress!
```

### **DevOps Deployment** 😎
```
Developer → Git push → Auto test → Auto deploy → Zero downtime
   ↓          ↓          ↓            ↓              ↓
 5 menit   Instant    2 menit      3 menit       Happy users!
```

---

## 📁 File Penting untuk Presentasi

### 1. **CI/CD Workflows**
```
.github/workflows/
├── tests.yml              → Run tests otomatis
└── deploy-kubernetes.yml  → Deploy otomatis
```

### 2. **Docker Configuration**
```
Dockerfile                 → Build app image
docker-compose.yml         → Local development
```

### 3. **Kubernetes Manifests**
```
k8s/
├── app-deployment.yaml    → Deploy aplikasi
├── mysql-statefulset.yaml → Database
├── ingress.yaml          → Routing traffic
└── hpa.yaml              → Auto-scaling
```

### 4. **Documentation**
```
docs/
├── DOCKER.md             → Docker setup guide
├── KUBERNETES_SETUP.md   → K8s deployment guide
└── DOCKERFILE_GUIDE.md   → Penjelasan Dockerfile
```

---

## 🎯 Benefit DevOps (Untuk Slide)

| Aspek | Traditional | DevOps |
|-------|-------------|--------|
| **Deployment Time** | 2-4 jam | 5-10 menit |
| **Testing** | Manual | Automated |
| **Rollback** | 30+ menit | < 1 menit |
| **Downtime** | 10-30 menit | 0 menit |
| **Error Detection** | User report | Auto-detect |
| **Scaling** | Manual (hari) | Auto (menit) |

---

## 💡 Demo Singkat (Live Demo)

### 1. **Show Git Push**
```bash
# Terminal
git add .
git commit -m "demo: update feature"
git push
```

### 2. **Show GitHub Actions Running**
- Buka: `https://github.com/sblrm/web_travel/actions`
- Tunjukkan tests berjalan otomatis
- Hijau = sukses, Merah = gagal

### 3. **Show Docker Build**
```bash
docker build -t culturaltrip .
docker images  # Show image size
```

### 4. **Show Kubernetes**
```bash
kubectl get pods
kubectl get services
kubectl logs <pod-name>
```

---

## 🎓 Kesimpulan (Slide Terakhir)

### **Sebelum DevOps:**
- Manual deployment → Error prone
- No automated testing → Banyak bug production
- Slow delivery → Kompetitor lebih cepat

### **Setelah DevOps:**
- ✅ **Fast**: Deploy 10x lebih cepat
- ✅ **Reliable**: Automated testing catch bugs
- ✅ **Scalable**: Handle traffic spike otomatis
- ✅ **Secure**: Security check di CI/CD
- ✅ **Traceable**: Git history + logs lengkap

### **Tools yang Dipakai:**
- 🐙 **GitHub**: Version control + CI/CD
- 🐳 **Docker**: Containerization
- ☸️ **Kubernetes**: Orchestration
- 🧪 **Pest**: Automated testing
- 🎨 **Laravel Pint**: Code formatting

---

## 📊 Metrics yang Bisa Dipresentasikan

1. **Lead Time**: Dari commit ke production = ~10 menit
2. **Deployment Frequency**: Bisa deploy 10x per hari
3. **Change Failure Rate**: <5% (karena automated testing)
4. **Mean Time to Recovery**: <1 menit (rollback otomatis)

---

## 🎤 Tips Presentasi

1. **Start with Problem**: Tunjukkan masalah deployment manual
2. **Show Solution**: Explain DevOps pipeline
3. **Live Demo**: Git push → watch it deploy
4. **Show Results**: Metrics improvement
5. **Q&A**: Siapkan jawaban untuk pertanyaan umum

### **Pertanyaan Umum:**
- **Q**: Berapa lama setup ini?
  **A**: ~2 hari untuk initial setup, tapi save hundreds of hours

- **Q**: Biaya infrastruktur?
  **A**: GitHub Actions free untuk public repo, K8s bisa pakai free tier

- **Q**: Susah gak belajarnya?
  **A**: Learning curve memang ada, tapi benefit jangka panjang besar

---

**Good luck dengan presentasi! 🚀**
