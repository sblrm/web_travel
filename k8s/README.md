# 📦 Kubernetes Manifests - CulturalTrip

## 🎯 Apa itu Kubernetes (K8s)?

**Kubernetes** adalah platform untuk **orchestration** container. Seperti "conductor" orkestra yang mengatur banyak container (musisi) agar main dengan harmonis.

**Analogi sederhana:**
- **Docker** = Bikin container (1 aplikasi dalam 1 box)
- **Kubernetes** = Manage banyak container (ratusan box) secara otomatis

---

## 📁 File-File di Folder `k8s/`

### **1. namespace.yaml** - Isolasi Resource
```
Fungsi: Pisahkan resource CulturalTrip dari aplikasi lain
Seperti: Folder terpisah untuk setiap project
```

### **2. configmap.yaml** - Environment Variables (Non-Rahasia)
```
Fungsi: Simpan konfigurasi public (APP_NAME, DB_HOST, dll)
Seperti: .env tapi versi Kubernetes
```

### **3. secrets.yaml** - Data Rahasia (Password, API Key)
```
Fungsi: Simpan data sensitif secara encrypted
Seperti: Vault untuk password
Note: secrets.yaml di .gitignore (jangan di-commit!)
```

### **4. mysql-statefulset.yaml** - Database
```
Fungsi: Deploy MySQL dengan persistent storage
StatefulSet: Untuk aplikasi yang butuh data persistent
```

### **5. redis-deployment.yaml** - Cache & Queue
```
Fungsi: Deploy Redis untuk caching & background jobs
Deployment: Untuk stateless application
```

### **6. app-deployment.yaml** - Aplikasi Laravel
```
Fungsi: Deploy Laravel app dengan 3 replicas (high availability)
Include: Nginx sidecar untuk serve static files
```

### **7. queue-deployment.yaml** - Background Job Processor
```
Fungsi: Worker untuk process email, notifications, dll
Jalan: php artisan queue:work secara terus menerus
```

### **8. storage-pvc.yaml** - Persistent Storage
```
Fungsi: Request storage untuk file uploads
PVC: Persistent Volume Claim (minta storage ke cluster)
```

### **9. ingress.yaml** - Entry Point (Routing)
```
Fungsi: Route traffic dari internet ke aplikasi
Seperti: Nginx reverse proxy + SSL certificate
```

### **10. hpa.yaml** - Auto-Scaling
```
Fungsi: Auto tambah/kurangi pod based on CPU/Memory
Benefit: Hemat cost + maintain performance
```

---

## 🔄 Alur Deploy ke Kubernetes

```
Step 1: Build Docker Image
docker build -t culturaltrip:v1.0 .
docker push docker.io/ragna13/culturaltrip:v1.0

Step 2: Apply K8s Manifests (urutan penting!)
kubectl apply -f namespace.yaml
kubectl apply -f configmap.yaml
kubectl apply -f secrets.yaml
kubectl apply -f storage-pvc.yaml
kubectl apply -f mysql-statefulset.yaml
kubectl apply -f redis-deployment.yaml
kubectl apply -f app-deployment.yaml
kubectl apply -f queue-deployment.yaml
kubectl apply -f ingress.yaml
kubectl apply -f hpa.yaml

Step 3: Verify Deployment
kubectl get pods -n culturaltrip
kubectl get services -n culturaltrip
kubectl logs <pod-name> -n culturaltrip
```

---

## 🏗️ Arsitektur Kubernetes CulturalTrip

```
┌─────────────────────────────────────────────────────────────────┐
│                         INTERNET                                │
└────────────────────────┬────────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────────┐
│                     INGRESS (Entry Point)                       │
│             SSL Certificate + Routing Rules                     │
└────────────────────────┬────────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────────┐
│                 SERVICE (Load Balancer)                         │
│           Distribute traffic ke multiple pods                   │
└────────────────────────┬────────────────────────────────────────┘
                         │
         ┌───────────────┼───────────────┐
         │               │               │
         ▼               ▼               ▼
    ┌────────┐      ┌────────┐      ┌────────┐
    │ Pod 1  │      │ Pod 2  │      │ Pod 3  │  ← App Pods (Auto-scaled by HPA)
    │ App    │      │ App    │      │ App    │
    │ Nginx  │      │ Nginx  │      │ Nginx  │
    └────────┘      └────────┘      └────────┘
         │               │               │
         └───────────────┼───────────────┘
                         │
         ┌───────────────┼───────────────┐
         │               │               │
         ▼               ▼               ▼
    ┌─────────┐    ┌─────────┐    ┌──────────┐
    │ MySQL   │    │ Redis   │    │ Queue    │
    │ (DB)    │    │ (Cache) │    │ Worker   │
    └─────────┘    └─────────┘    └──────────┘
         │
         ▼
    ┌─────────┐
    │ Storage │  ← Persistent Volume (File uploads)
    │ (PVC)   │
    └─────────┘
```

---

## 🔑 Konsep Penting Kubernetes

### **1. Pod**
- Unit terkecil di K8s
- 1 pod = 1 atau lebih container yang jalan bareng
- Seperti: 1 rumah bisa isi 1 atau lebih orang

### **2. Deployment**
- Blueprint untuk create pods
- Handle: scaling, rolling update, rollback
- Untuk: Stateless application (app, queue worker)

### **3. StatefulSet**
- Seperti Deployment tapi untuk stateful app
- Pod punya identitas tetap (nama & storage)
- Untuk: Database, queue broker

### **4. Service**
- Network endpoint untuk akses pod
- Load balancer built-in
- Hostname tetap meski pod restart

### **5. ConfigMap & Secret**
- ConfigMap: Environment variables (public)
- Secret: Environment variables (encrypted)
- Inject ke pod as env vars

### **6. PVC (Persistent Volume Claim)**
- Request untuk storage
- Persistent = data tidak hilang saat pod restart
- Untuk: File uploads, database data

### **7. Ingress**
- HTTP/HTTPS routing dari internet
- SSL/TLS termination
- Path-based routing

### **8. HPA (Horizontal Pod Autoscaler)**
- Auto scale pods based on metrics
- CPU > 70% → Add more pods
- CPU < 30% → Remove pods

---

## 📊 Kubectl Commands (Cheat Sheet)

### **Lihat Resource**
```bash
# Lihat semua pods
kubectl get pods -n culturaltrip

# Lihat semua services
kubectl get services -n culturaltrip

# Lihat semua deployments
kubectl get deployments -n culturaltrip

# Lihat semua resources sekaligus
kubectl get all -n culturaltrip
```

### **Debug & Logs**
```bash
# Lihat logs pod
kubectl logs <pod-name> -n culturaltrip

# Lihat logs secara real-time (follow)
kubectl logs -f <pod-name> -n culturaltrip

# Masuk ke dalam pod (interactive shell)
kubectl exec -it <pod-name> -n culturaltrip -- sh

# Describe pod (detail info + events)
kubectl describe pod <pod-name> -n culturaltrip
```

### **Scale & Update**
```bash
# Scale manual (tambah/kurangi pod)
kubectl scale deployment culturaltrip-app --replicas=5 -n culturaltrip

# Rolling update (update image)
kubectl set image deployment/culturaltrip-app app=culturaltrip:v2.0 -n culturaltrip

# Rollback ke versi sebelumnya
kubectl rollout undo deployment/culturaltrip-app -n culturaltrip
```

### **Delete Resource**
```bash
# Delete pod (akan auto-recreate by deployment)
kubectl delete pod <pod-name> -n culturaltrip

# Delete deployment
kubectl delete deployment culturaltrip-app -n culturaltrip

# Delete semua resources di namespace
kubectl delete namespace culturaltrip
```

---

## 🎓 Tips untuk Presentasi

### **Demo Flow:**
1. **Show manifests** dengan comment yang sudah ada
2. **Jelaskan setiap file** fungsinya apa (5-10 detik per file)
3. **Tunjukkan kubectl get all** - lihat semua resource running
4. **Scale demo**: `kubectl scale` dan lihat pod bertambah
5. **Logs demo**: `kubectl logs` untuk troubleshooting
6. **Rolling update**: Update image dan lihat zero-downtime deployment

### **Key Points:**
- ✅ **Orchestration**: K8s manage ratusan container otomatis
- ✅ **Self-Healing**: Pod crash? Auto-restart
- ✅ **Auto-Scaling**: Traffic naik? Add pods
- ✅ **Zero-Downtime**: Update tanpa downtime
- ✅ **Declarative**: Define "what you want", K8s handle "how"

### **Bandingkan dengan Manual:**
| Aspek | Manual Server | Kubernetes |
|-------|---------------|------------|
| Deploy | SSH + manual command | `kubectl apply` |
| Scale | Setup new server (hours) | `kubectl scale` (seconds) |
| Crash | Manual restart | Auto-restart |
| Load Balance | Setup nginx manually | Built-in Service |
| SSL | Manual cert setup | Auto with cert-manager |
| Monitoring | Install tools manually | Built-in metrics |

---

**Good luck untuk presentasi Kubernetes! ☸️**
