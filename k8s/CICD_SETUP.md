# CI/CD Pipeline Setup for CulturalTrip

Complete GitHub Actions CI/CD pipeline untuk automated testing, building, dan deployment ke Kubernetes.

## 🏗️ Pipeline Architecture

```
┌─────────────────┐
│  Push to Main   │
└────────┬────────┘
         │
         ├──> Run Tests (Pest)
         │    └──> Code Quality (Pint, PHPStan)
         │         └──> Security Scan
         │              │
         │              ✓ (Pass)
         │              │
         ├──────────────┴──> Build Docker Image
         │                    └──> Push to Registry (Docker Hub)
         │                         │
         │                         ✓
         │                         │
         └─────────────────────────┴──> Deploy to Kubernetes
                                         ├──> Apply Manifests
                                         ├──> Run Migrations
                                         └──> Verify Deployment
```

## 📋 Prerequisites

### 1. GitHub Repository Secrets

Configure these secrets di GitHub repository settings (`Settings > Secrets and variables > Actions`):

**Docker Registry:**
```
DOCKER_USERNAME=ragna13
DOCKER_PASSWORD=<your-docker-hub-token>
```

**Kubernetes Cluster:**
```
KUBECONFIG=<base64-encoded-kubeconfig>
```

**Application Secrets:**
```
APP_KEY=base64:Mvjxhwr5IwX84AFM7Q3Qlb9sn3qUO14p90DPqh4r0oI=
DB_PASSWORD=@Ragnarok1324
```

### 2. Generate KUBECONFIG Secret

**For Docker Desktop Kubernetes:**
```powershell
# Get kubeconfig
$kubeconfig = Get-Content $HOME/.kube/config -Raw

# Base64 encode
$bytes = [System.Text.Encoding]::UTF8.GetBytes($kubeconfig)
$encoded = [Convert]::ToBase64String($bytes)

# Copy to clipboard
Set-Clipboard -Value $encoded
```

**For Cloud Kubernetes (GKE/EKS/AKS):**
```bash
# GKE
gcloud container clusters get-credentials <cluster-name> --zone <zone>
cat ~/.kube/config | base64 -w 0

# EKS
aws eks update-kubeconfig --name <cluster-name>
cat ~/.kube/config | base64 -w 0

# AKS
az aks get-credentials --resource-group <rg> --name <cluster-name>
cat ~/.kube/config | base64 -w 0
```

### 3. Create Docker Hub Access Token

1. Login ke https://hub.docker.com
2. Account Settings → Security → New Access Token
3. Name: `github-actions-culturaltrip`
4. Permissions: Read, Write, Delete
5. Copy token → Add to GitHub Secrets as `DOCKER_PASSWORD`

## 🔄 Workflows

### 1. Test Workflow (`.github/workflows/test.yml`)

**Triggers:**
- Pull requests ke `main` atau `production`
- Push ke `develop` atau `feature/*` branches

**Jobs:**
- **Laravel Tests**: Run Pest tests dengan MySQL & Redis services
- **Code Quality**: Laravel Pint (formatting) + PHPStan (static analysis)
- **Security Scan**: Composer audit untuk vulnerable dependencies

**Example:**
```yaml
on:
  pull_request:
    branches: [main, production]
  push:
    branches: [develop, feature/*]
```

### 2. Deploy Workflow (`.github/workflows/deploy-kubernetes.yml`)

**Triggers:**
- Push ke `main` branch (auto-deploy to staging)
- Push ke `production` branch (auto-deploy to production)
- Manual trigger via `workflow_dispatch`

**Jobs:**
1. **Build & Push**: Build Docker image → Push to Docker Hub with tags
2. **Deploy to K8s**: Apply manifests → Run migrations → Verify
3. **Notify**: Send deployment status (success/failure)

**Tags Strategy:**
```
main-sha-abc1234     # Branch + commit SHA
main                 # Branch name
v1.2.3               # Semver (if tagged)
latest               # Latest main branch
```

## 🚀 Usage

### Automatic Deployment

**Staging (Main Branch):**
```bash
git checkout main
git merge develop
git push origin main  # Triggers auto-deploy
```

**Production:**
```bash
git checkout production
git merge main
git push origin production  # Triggers auto-deploy to prod
```

### Manual Deployment

Via GitHub UI:
1. Go to **Actions** tab
2. Select **Deploy to Kubernetes** workflow
3. Click **Run workflow**
4. Choose branch and click **Run**

Via GitHub CLI:
```bash
gh workflow run deploy-kubernetes.yml --ref main
```

### Rollback

```bash
# Via kubectl (manual)
kubectl rollout undo deployment/culturaltrip-app -n culturaltrip

# Or deploy previous image
kubectl set image deployment/culturaltrip-app \
  app=ragna13/culturaltrip:main-sha-abc1234 \
  -n culturaltrip
```

## 📊 Monitoring Deployments

### Check Workflow Status

```bash
# List recent workflow runs
gh run list --workflow=deploy-kubernetes.yml

# Watch specific run
gh run watch <run-id>

# View logs
gh run view <run-id> --log
```

### Check Kubernetes Deployment

```bash
# Watch rollout status
kubectl rollout status deployment/culturaltrip-app -n culturaltrip

# Check pod status
kubectl get pods -n culturaltrip -w

# View logs from deployed pods
kubectl logs -f deployment/culturaltrip-app -n culturaltrip -c app
```

## 🔧 Troubleshooting

### Issue: Docker Build Fails

**Symptoms:** `ERROR: failed to solve: process ... did not complete successfully`

**Solutions:**
1. Check Dockerfile syntax
2. Verify `npm run build` works locally
3. Check Docker Hub rate limits

```bash
# Test build locally
docker build -t test:local --target production .
```

### Issue: kubectl Connection Failed

**Symptoms:** `The connection to the server was refused`

**Solutions:**
1. Verify KUBECONFIG secret is valid (base64 encoded)
2. Check cluster is accessible from GitHub Actions runners
3. For cloud clusters, ensure firewall rules allow GitHub IPs

```bash
# Test kubeconfig locally
export KUBECONFIG=/tmp/test-config
echo $KUBECONFIG_BASE64 | base64 -d > $KUBECONFIG
kubectl cluster-info
```

### Issue: Migrations Fail in CI/CD

**Symptoms:** `SQLSTATE[HY000] [2002] Connection refused`

**Solutions:**
1. Ensure MySQL StatefulSet is ready before running migrations
2. Check `kubectl wait` timeout is sufficient
3. Verify database credentials in secrets

```yaml
# Add longer wait time
- name: Wait for MySQL ready
  run: kubectl wait --for=condition=ready pod -l app=mysql -n culturaltrip --timeout=600s
```

### Issue: Image Pull Errors

**Symptoms:** `ErrImagePull` or `ImagePullBackOff`

**Solutions:**
1. Verify image exists: `docker pull ragna13/culturaltrip:latest`
2. Check imagePullPolicy is `Always`
3. For private registries, create imagePullSecrets

```bash
# Create image pull secret
kubectl create secret docker-registry regcred \
  --docker-server=docker.io \
  --docker-username=ragna13 \
  --docker-password=<token> \
  -n culturaltrip
```

## 🔐 Security Best Practices

### 1. Secret Management

✅ **DO:**
- Store all sensitive data in GitHub Secrets
- Rotate Docker Hub tokens every 90 days
- Use separate kubeconfig for CI/CD (not admin)
- Enable 2FA on Docker Hub account

❌ **DON'T:**
- Commit secrets to Git (check with `git secrets` tool)
- Use hardcoded passwords in manifests
- Share kubeconfig files via Slack/email

### 2. RBAC for CI/CD

Create dedicated ServiceAccount dengan minimal permissions:

```yaml
apiVersion: v1
kind: ServiceAccount
metadata:
  name: github-actions
  namespace: culturaltrip
---
apiVersion: rbac.authorization.k8s.io/v1
kind: Role
metadata:
  name: github-actions-deployer
  namespace: culturaltrip
rules:
  - apiGroups: ["apps"]
    resources: ["deployments", "statefulsets"]
    verbs: ["get", "list", "update", "patch"]
  - apiGroups: [""]
    resources: ["pods", "pods/log"]
    verbs: ["get", "list"]
  - apiGroups: [""]
    resources: ["pods/exec"]
    verbs: ["create"]
---
apiVersion: rbac.authorization.k8s.io/v1
kind: RoleBinding
metadata:
  name: github-actions-deployer
  namespace: culturaltrip
roleRef:
  apiGroup: rbac.authorization.k8s.io
  kind: Role
  name: github-actions-deployer
subjects:
  - kind: ServiceAccount
    name: github-actions
    namespace: culturaltrip
```

### 3. Image Security

Enable vulnerability scanning:

```yaml
# Add to deploy workflow
- name: Scan image for vulnerabilities
  uses: aquasecurity/trivy-action@master
  with:
    image-ref: 'ragna13/culturaltrip:latest'
    format: 'sarif'
    output: 'trivy-results.sarif'

- name: Upload Trivy results to GitHub Security
  uses: github/codeql-action/upload-sarif@v2
  with:
    sarif_file: 'trivy-results.sarif'
```

## 📈 Advanced: Multi-Environment Setup

### Environment-Specific Deployments

**Structure:**
```
k8s/
├── base/              # Common resources
│   ├── deployment.yaml
│   └── service.yaml
├── overlays/
│   ├── staging/       # Staging-specific
│   │   ├── kustomization.yaml
│   │   └── patches/
│   └── production/    # Production-specific
│       ├── kustomization.yaml
│       └── patches/
```

**Deploy with Kustomize:**
```yaml
# In workflow
- name: Deploy to Staging
  run: kubectl apply -k k8s/overlays/staging -n culturaltrip-staging

- name: Deploy to Production
  run: kubectl apply -k k8s/overlays/production -n culturaltrip-prod
```

## 📚 Additional Resources

- [GitHub Actions Documentation](https://docs.github.com/actions)
- [Kubernetes CI/CD Best Practices](https://kubernetes.io/docs/concepts/cluster-administration/manage-deployment/)
- [Docker Build Push Action](https://github.com/docker/build-push-action)
- [kubectl Setup Action](https://github.com/azure/setup-kubectl)

---

**Setup Time:** ~30 minutes untuk initial configuration  
**Build Time:** ~5-8 minutes per deployment  
**Rollback Time:** <2 minutes dengan kubectl rollout undo
