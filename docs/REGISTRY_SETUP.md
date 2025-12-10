# 🐳 Docker Registry Setup Guide

Panduan lengkap setup Docker Registry untuk Kubernetes deployment.

## 🎯 Pilihan Registry (Diurutkan dari Termudah)

### 1. Docker Hub (GRATIS - REKOMENDASI)

**✅ Keuntungan:**
- Gratis untuk public images
- Paling mudah digunakan
- Tidak perlu setup khusus
- Langsung bisa diakses dari K8s

**📝 Langkah Setup:**

```bash
# 1. Daftar di https://hub.docker.com
# Misal username: johndo

# 2. Login dari terminal
docker login
# Masukkan username & password

# 3. Build image
docker build --target production -t johndo/culturaltrip:latest .

# 4. Push ke Docker Hub
docker push johndo/culturaltrip:latest

# 5. Test pull
docker pull johndo/culturaltrip:latest
```

**🔧 Gunakan di Kubernetes:**
```yaml
image: docker.io/johndo/culturaltrip:latest
# atau cukup:
image: johndo/culturaltrip:latest
```

**💰 Pricing:**
- Public repos: **GRATIS unlimited**
- Private repos: **GRATIS 1 repo**, $7/bulan untuk unlimited

---

### 2. GitHub Container Registry (GRATIS)

**✅ Keuntungan:**
- Gratis untuk public & private
- Terintegrasi dengan GitHub Actions
- Automatic versioning dari Git tags

**📝 Langkah Setup:**

```bash
# 1. Buat Personal Access Token di GitHub
# Settings → Developer settings → Personal access tokens → Tokens (classic)
# Scope: write:packages, read:packages, delete:packages

# 2. Login
echo YOUR_GITHUB_TOKEN | docker login ghcr.io -u YOUR_USERNAME --password-stdin

# 3. Build image
docker build --target production -t ghcr.io/YOUR_USERNAME/culturaltrip:latest .

# 4. Push
docker push ghcr.io/YOUR_USERNAME/culturaltrip:latest
```

**🔧 Gunakan di Kubernetes:**
```yaml
image: ghcr.io/YOUR_USERNAME/culturaltrip:latest
```

**🔒 Private Image Setup:**
```bash
# Buat secret di K8s
kubectl create secret docker-registry ghcr-secret \
  --docker-server=ghcr.io \
  --docker-username=YOUR_USERNAME \
  --docker-password=YOUR_TOKEN \
  -n culturaltrip

# Tambahkan di deployment:
spec:
  imagePullSecrets:
    - name: ghcr-secret
```

---

### 3. Google Container Registry (GCR)

**📝 Setup untuk Google Kubernetes Engine (GKE):**

```bash
# 1. Install gcloud CLI
# https://cloud.google.com/sdk/docs/install

# 2. Login & setup project
gcloud auth login
gcloud config set project YOUR_PROJECT_ID

# 3. Configure Docker
gcloud auth configure-docker

# 4. Build & Push
docker build --target production -t gcr.io/YOUR_PROJECT_ID/culturaltrip:latest .
docker push gcr.io/YOUR_PROJECT_ID/culturaltrip:latest
```

**🔧 Gunakan di Kubernetes:**
```yaml
image: gcr.io/YOUR_PROJECT_ID/culturaltrip:latest
```

**💰 Pricing:**
- Storage: $0.026/GB per bulan
- Network egress: Varies by region

---

### 4. AWS Elastic Container Registry (ECR)

**📝 Setup untuk Amazon EKS:**

```bash
# 1. Install AWS CLI
# https://aws.amazon.com/cli/

# 2. Create repository
aws ecr create-repository --repository-name culturaltrip --region us-east-1

# 3. Get login password
aws ecr get-login-password --region us-east-1 | docker login --username AWS --password-stdin 123456789012.dkr.ecr.us-east-1.amazonaws.com

# 4. Build & Push
docker build --target production -t 123456789012.dkr.ecr.us-east-1.amazonaws.com/culturaltrip:latest .
docker push 123456789012.dkr.ecr.us-east-1.amazonaws.com/culturaltrip:latest
```

**🔧 Gunakan di Kubernetes:**
```yaml
image: 123456789012.dkr.ecr.us-east-1.amazonaws.com/culturaltrip:latest
```

---

### 5. Azure Container Registry (ACR)

**📝 Setup untuk Azure AKS:**

```bash
# 1. Create registry
az acr create --resource-group myResourceGroup --name myregistry --sku Basic

# 2. Login
az acr login --name myregistry

# 3. Build & Push
docker build --target production -t myregistry.azurecr.io/culturaltrip:latest .
docker push myregistry.azurecr.io/culturaltrip:latest
```

---

## 🚀 Quick Deploy dengan Script

### Menggunakan Script Otomatis

**PowerShell (Windows):**
```powershell
# Deploy dengan Docker Hub username
.\k8s\deploy.ps1 -DockerUsername "johndo"

# Deploy dengan version tertentu
.\k8s\deploy.ps1 -DockerUsername "johndo" -Version "v1.0.0"

# Skip build (jika sudah build sebelumnya)
.\k8s\deploy.ps1 -DockerUsername "johndo" -SkipBuild
```

**Bash (Linux/Mac):**
```bash
# Deploy dengan Docker Hub username
./k8s/deploy.sh johndo

# Deploy dengan version tertentu
./k8s/deploy.sh johndo v1.0.0
```

Script akan otomatis:
1. ✅ Login ke Docker
2. ✅ Build production image
3. ✅ Push ke registry
4. ✅ Update K8s manifests
5. ✅ Deploy ke Kubernetes
6. ✅ Run migrations & seeding

---

## 📋 Manual Deployment Steps

Jika tidak ingin pakai script:

### 1. Pilih Registry

Pilih salah satu (rekomendasi Docker Hub):
```bash
# Docker Hub
REGISTRY="docker.io/johndo"

# GitHub
REGISTRY="ghcr.io/johndo"

# GCR
REGISTRY="gcr.io/your-project"
```

### 2. Build & Push

```bash
# Build
docker build --target production -t $REGISTRY/culturaltrip:latest .

# Push
docker push $REGISTRY/culturaltrip:latest
```

### 3. Update Manifests

Edit `k8s/app-deployment.yaml` dan `k8s/queue-deployment.yaml`:
```yaml
# Ganti:
image: YOUR_REGISTRY/culturaltrip:latest

# Dengan:
image: docker.io/johndo/culturaltrip:latest
```

### 4. Deploy

```bash
kubectl apply -f k8s/namespace.yaml
kubectl apply -f k8s/configmap.yaml
kubectl apply -f k8s/secrets.yaml
kubectl apply -f k8s/storage-pvc.yaml
kubectl apply -f k8s/mysql-statefulset.yaml
kubectl apply -f k8s/redis-deployment.yaml
kubectl apply -f k8s/app-deployment.yaml
kubectl apply -f k8s/queue-deployment.yaml
kubectl apply -f k8s/hpa.yaml
```

---

## 🔐 Private Registry Authentication

Jika menggunakan private registry:

### Create Secret

```bash
kubectl create secret docker-registry regcred \
  --docker-server=YOUR_REGISTRY \
  --docker-username=YOUR_USERNAME \
  --docker-password=YOUR_PASSWORD \
  --docker-email=YOUR_EMAIL \
  -n culturaltrip
```

### Update Deployment

Tambahkan di `k8s/app-deployment.yaml` dan `k8s/queue-deployment.yaml`:

```yaml
spec:
  template:
    spec:
      imagePullSecrets:
        - name: regcred
      containers:
        - name: app
          image: YOUR_PRIVATE_REGISTRY/culturaltrip:latest
```

---

## 🎯 Best Practices

### 1. Gunakan Version Tags

**❌ Jangan:**
```yaml
image: johndo/culturaltrip:latest
```

**✅ Lebih Baik:**
```yaml
image: johndo/culturaltrip:v1.0.0
```

### 2. Multi-Tag Strategy

```bash
# Tag dengan version number
docker tag culturaltrip:prod johndo/culturaltrip:v1.0.0
docker push johndo/culturaltrip:v1.0.0

# Tag dengan latest
docker tag culturaltrip:prod johndo/culturaltrip:latest
docker push johndo/culturaltrip:latest

# Tag dengan git commit SHA
GIT_SHA=$(git rev-parse --short HEAD)
docker tag culturaltrip:prod johndo/culturaltrip:$GIT_SHA
docker push johndo/culturaltrip:$GIT_SHA
```

### 3. Automated Builds (CI/CD)

**GitHub Actions Example:**

```yaml
# .github/workflows/build-and-push.yml
name: Build and Push

on:
  push:
    tags:
      - 'v*'

jobs:
  build:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v3
      
      - name: Login to Docker Hub
        uses: docker/login-action@v2
        with:
          username: ${{ secrets.DOCKER_USERNAME }}
          password: ${{ secrets.DOCKER_PASSWORD }}
      
      - name: Build and push
        uses: docker/build-push-action@v4
        with:
          context: .
          target: production
          push: true
          tags: |
            ${{ secrets.DOCKER_USERNAME }}/culturaltrip:latest
            ${{ secrets.DOCKER_USERNAME }}/culturaltrip:${{ github.ref_name }}
```

---

## 🆘 Troubleshooting

### Error: "denied: requested access to the resource is denied"

**Solusi:**
```bash
# Cek login
docker logout
docker login

# Pastikan username benar
docker images  # Cek nama image
```

### Error: "unauthorized: authentication required"

**Solusi:**
```bash
# K8s tidak bisa pull private image
kubectl create secret docker-registry regcred \
  --docker-server=docker.io \
  --docker-username=YOUR_USERNAME \
  --docker-password=YOUR_PASSWORD \
  -n culturaltrip
```

### Error: "manifest unknown"

**Solusi:**
```bash
# Image tidak ada di registry
docker push YOUR_REGISTRY/culturaltrip:latest

# Atau typo di nama image
kubectl describe pod POD_NAME -n culturaltrip
```

---

## 📚 Resources

- [Docker Hub Documentation](https://docs.docker.com/docker-hub/)
- [GitHub Container Registry](https://docs.github.com/en/packages/working-with-a-github-packages-registry/working-with-the-container-registry)
- [Google Container Registry](https://cloud.google.com/container-registry/docs)
- [AWS ECR Documentation](https://docs.aws.amazon.com/ecr/)
- [Azure ACR Documentation](https://docs.microsoft.com/en-us/azure/container-registry/)

---

**Rekomendasi: Mulai dengan Docker Hub untuk development, pindah ke cloud provider registry untuk production** 🚀
