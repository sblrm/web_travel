# 🚀 Kubernetes Deployment Guide - CulturalTrip

Complete guide untuk deploy CulturalTrip ke Kubernetes cluster.

## 📋 Prerequisites

### 1. Tools Required
- `kubectl` - Kubernetes CLI
- `helm` (optional) - Package manager untuk K8s
- Docker registry access (Docker Hub, GCR, ECR, dll)
- Kubernetes cluster (GKE, EKS, AKS, DigitalOcean, atau local minikube)

### 2. Cluster Requirements
- Kubernetes 1.24+
- Ingress Controller (nginx-ingress recommended)
- Storage class dengan ReadWriteMany support
- Metrics Server (untuk HPA)
- Cert-Manager (untuk SSL/TLS otomatis)

## 🏗️ Architecture Overview

```
Internet → Ingress (HTTPS) → Service → Pods (App + Nginx)
                                      ↓
                                   Redis
                                      ↓
                                   MySQL (StatefulSet)
                                      ↓
                              Queue Workers (Background)
```

## 📦 Step 1: Build & Push Docker Image

### Build Production Image

```bash
# Build for production target
docker build --target production -t culturaltrip:latest .

# Tag untuk registry
docker tag culturaltrip:latest YOUR_REGISTRY/culturaltrip:latest
docker tag culturaltrip:latest YOUR_REGISTRY/culturaltrip:v1.0.0

# Push ke registry
docker push YOUR_REGISTRY/culturaltrip:latest
docker push YOUR_REGISTRY/culturaltrip:v1.0.0
```

**Registry Options:**
- Docker Hub: `docker.io/username/culturaltrip`
- Google GCR: `gcr.io/project-id/culturaltrip`
- AWS ECR: `123456789.dkr.ecr.region.amazonaws.com/culturaltrip`
- GitHub Container Registry: `ghcr.io/username/culturaltrip`

## ⚙️ Step 2: Configure Secrets

### Generate APP_KEY

```bash
# Generate Laravel APP_KEY
docker run --rm YOUR_REGISTRY/culturaltrip:latest php artisan key:generate --show
```

### Update secrets.yaml

Edit `k8s/secrets.yaml` dan replace semua `REPLACE_WITH_*`:

```bash
# JANGAN commit file ini ke Git!
# Gunakan Sealed Secrets atau Vault di production
nano k8s/secrets.yaml
```

**Security Best Practices:**
- Gunakan strong passwords (min 20 chars, random)
- Simpan secrets di 1Password/Bitwarden
- Di production, gunakan:
  - **Sealed Secrets**: Encrypt secrets di Git
  - **HashiCorp Vault**: External secret management
  - **Cloud provider secrets**: GCP Secret Manager, AWS Secrets Manager, Azure Key Vault

## 🚀 Step 3: Deploy to Kubernetes

### Deploy Infrastructure First

```bash
# 1. Create namespace
kubectl apply -f k8s/namespace.yaml

# 2. Create ConfigMap & Secrets
kubectl apply -f k8s/configmap.yaml
kubectl apply -f k8s/secrets.yaml

# 3. Create Storage
kubectl apply -f k8s/storage-pvc.yaml

# 4. Deploy MySQL
kubectl apply -f k8s/mysql-statefulset.yaml

# Wait for MySQL to be ready
kubectl wait --for=condition=ready pod -l app=mysql -n culturaltrip --timeout=300s

# 5. Deploy Redis
kubectl apply -f k8s/redis-deployment.yaml
```

### Update Image Registry

Edit semua deployment files dan ganti `YOUR_REGISTRY` dengan registry Anda:

```bash
# Quick replace (Linux/Mac)
sed -i 's|YOUR_REGISTRY|gcr.io/your-project|g' k8s/*.yaml

# Windows PowerShell
(Get-Content k8s/app-deployment.yaml) -replace 'YOUR_REGISTRY','gcr.io/your-project' | Set-Content k8s/app-deployment.yaml
(Get-Content k8s/queue-deployment.yaml) -replace 'YOUR_REGISTRY','gcr.io/your-project' | Set-Content k8s/queue-deployment.yaml
```

### Deploy Application

```bash
# 6. Deploy main application
kubectl apply -f k8s/app-deployment.yaml

# Wait for app to be ready
kubectl wait --for=condition=ready pod -l app=culturaltrip-app -n culturaltrip --timeout=300s

# 7. Deploy queue workers
kubectl apply -f k8s/queue-deployment.yaml

# 8. Setup autoscaling (optional)
kubectl apply -f k8s/hpa.yaml
```

### Setup Ingress (External Access)

```bash
# Install Nginx Ingress Controller (if not exists)
helm repo add ingress-nginx https://kubernetes.github.io/ingress-nginx
helm install ingress-nginx ingress-nginx/ingress-nginx --namespace ingress-nginx --create-namespace

# Install Cert-Manager for SSL (if not exists)
kubectl apply -f https://github.com/cert-manager/cert-manager/releases/download/v1.13.0/cert-manager.yaml

# Deploy ingress
kubectl apply -f k8s/ingress.yaml
```

## 🔍 Step 4: Verify Deployment

### Check All Pods

```bash
# List all pods
kubectl get pods -n culturaltrip

# Expected output:
# NAME                                 READY   STATUS    RESTARTS   AGE
# culturaltrip-app-xxx-xxx            2/2     Running   0          2m
# culturaltrip-app-xxx-yyy            2/2     Running   0          2m
# culturaltrip-app-xxx-zzz            2/2     Running   0          2m
# culturaltrip-queue-xxx-xxx          1/1     Running   0          2m
# culturaltrip-queue-xxx-yyy          1/1     Running   0          2m
# mysql-0                             1/1     Running   0          5m
# redis-xxx-xxx                       1/1     Running   0          4m
```

### Check Services

```bash
kubectl get svc -n culturaltrip

# Check ingress
kubectl get ingress -n culturaltrip
```

### View Logs

```bash
# App logs
kubectl logs -f deployment/culturaltrip-app -n culturaltrip -c app

# Queue worker logs
kubectl logs -f deployment/culturaltrip-queue -n culturaltrip

# MySQL logs
kubectl logs -f statefulset/mysql -n culturaltrip
```

### Test Application

```bash
# Port forward for testing
kubectl port-forward svc/culturaltrip-service 8080:80 -n culturaltrip

# Test in browser: http://localhost:8080
```

## 📊 Step 5: Post-Deployment Tasks

### Run Database Seeding (First Time Only)

```bash
# Get app pod name
POD=$(kubectl get pod -l app=culturaltrip-app -n culturaltrip -o jsonpath='{.items[0].metadata.name}')

# Run seeder
kubectl exec -it $POD -n culturaltrip -c app -- php artisan db:seed --force

# Verify admin user
kubectl exec -it $POD -n culturaltrip -c app -- php artisan tinker --execute="User::where('email', 'admin@culturaltrip.com')->first()"
```

### Setup Storage Link

```bash
kubectl exec -it $POD -n culturaltrip -c app -- php artisan storage:link
```

### Cache Optimization

```bash
kubectl exec -it $POD -n culturaltrip -c app -- php artisan config:cache
kubectl exec -it $POD -n culturaltrip -c app -- php artisan route:cache
kubectl exec -it $POD -n culturaltrip -c app -- php artisan view:cache
```

## 🔄 Updates & Rollbacks

### Deploy New Version

```bash
# Build & push new image
docker build --target production -t YOUR_REGISTRY/culturaltrip:v1.0.1 .
docker push YOUR_REGISTRY/culturaltrip:v1.0.1

# Update deployment
kubectl set image deployment/culturaltrip-app app=YOUR_REGISTRY/culturaltrip:v1.0.1 -n culturaltrip
kubectl set image deployment/culturaltrip-queue queue-worker=YOUR_REGISTRY/culturaltrip:v1.0.1 -n culturaltrip

# Watch rollout
kubectl rollout status deployment/culturaltrip-app -n culturaltrip
```

### Rollback to Previous Version

```bash
# Rollback app
kubectl rollout undo deployment/culturaltrip-app -n culturaltrip

# Rollback to specific revision
kubectl rollout history deployment/culturaltrip-app -n culturaltrip
kubectl rollout undo deployment/culturaltrip-app --to-revision=2 -n culturaltrip
```

## 📈 Monitoring & Scaling

### Manual Scaling

```bash
# Scale app pods
kubectl scale deployment/culturaltrip-app --replicas=5 -n culturaltrip

# Scale queue workers
kubectl scale deployment/culturaltrip-queue --replicas=3 -n culturaltrip
```

### View HPA Status

```bash
kubectl get hpa -n culturaltrip

# Watch autoscaling in action
watch kubectl get hpa -n culturaltrip
```

### Resource Usage

```bash
# Pod resource usage
kubectl top pods -n culturaltrip

# Node resource usage
kubectl top nodes
```

## 🐛 Troubleshooting

### Pod Not Starting

```bash
# Check pod events
kubectl describe pod POD_NAME -n culturaltrip

# Check logs
kubectl logs POD_NAME -n culturaltrip -c app --previous
```

### Database Connection Issues

```bash
# Test MySQL connectivity
kubectl exec -it $POD -n culturaltrip -c app -- mysql -h mysql-service -u culturaltrip -p

# Check MySQL logs
kubectl logs statefulset/mysql -n culturaltrip
```

### Storage Issues

```bash
# Check PVC status
kubectl get pvc -n culturaltrip

# Describe PVC
kubectl describe pvc culturaltrip-storage -n culturaltrip
```

### Clear Cache

```bash
kubectl exec -it $POD -n culturaltrip -c app -- php artisan optimize:clear
```

## 🔒 Security Best Practices

### 1. Use Secrets Management
- **Production**: Use Sealed Secrets, Vault, or cloud provider secrets
- Never commit `secrets.yaml` to Git

### 2. Network Policies
```bash
# Restrict traffic between pods
kubectl apply -f k8s/network-policy.yaml  # Create this file
```

### 3. RBAC
```bash
# Create service account with minimal permissions
kubectl apply -f k8s/rbac.yaml  # Create this file
```

### 4. Pod Security
- Run containers as non-root
- Use read-only file systems where possible
- Set resource limits

### 5. SSL/TLS
- Always use HTTPS in production
- Configure cert-manager for automatic certificate renewal

## 💰 Cost Optimization

### Right-Sizing Resources

Monitor actual usage and adjust:

```yaml
resources:
  requests:
    memory: "256Mi"  # Actual usage
    cpu: "200m"
  limits:
    memory: "512Mi"  # Safety buffer
    cpu: "500m"
```

### Use Spot/Preemptible Instances

For queue workers that can handle interruptions:

```yaml
nodeSelector:
  cloud.google.com/gke-preemptible: "true"  # GKE
  eks.amazonaws.com/capacityType: SPOT       # EKS
```

### Enable Cluster Autoscaler

Automatically scale cluster nodes based on demand.

## 🌍 Production Checklist

- [ ] Strong passwords in secrets
- [ ] Image registry configured
- [ ] Storage class supports ReadWriteMany
- [ ] Ingress controller installed
- [ ] Cert-manager for SSL configured
- [ ] Monitoring setup (Prometheus/Grafana)
- [ ] Backup strategy for MySQL
- [ ] Log aggregation (ELK, Loki)
- [ ] HPA configured
- [ ] Resource limits set
- [ ] Health checks configured
- [ ] CI/CD pipeline setup
- [ ] Disaster recovery plan

## 📚 Additional Resources

- [Kubernetes Documentation](https://kubernetes.io/docs/)
- [Laravel on Kubernetes Best Practices](https://laravel.com/docs/deployment)
- [Nginx Ingress Controller](https://kubernetes.github.io/ingress-nginx/)
- [Cert-Manager Docs](https://cert-manager.io/docs/)
- [Sealed Secrets](https://github.com/bitnami-labs/sealed-secrets)

---

**Built for Scale** 🚀  
CulturalTrip on Kubernetes - Production Ready
