# 🚀 Kubernetes Deployment Script for CulturalTrip (PowerShell)
# Usage: .\k8s\deploy.ps1 -DockerUsername "yourname"

param(
    [Parameter(Mandatory=$true)]
    [string]$DockerUsername,
    
    [Parameter(Mandatory=$false)]
    [string]$Version = "latest",
    
    [Parameter(Mandatory=$false)]
    [switch]$SkipBuild,
    
    [Parameter(Mandatory=$false)]
    [switch]$SkipPush
)

$ErrorActionPreference = "Stop"

$ImageName = "culturaltrip"
$Registry = "docker.io/$DockerUsername"
$FullImage = "$Registry/${ImageName}:$Version"

Write-Host "🚀 CulturalTrip Kubernetes Deployment" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Green
Write-Host ""
Write-Host "Configuration:" -ForegroundColor Yellow
Write-Host "  Registry: $Registry"
Write-Host "  Image: $FullImage"
Write-Host ""

# Step 1: Docker login
if (-not $SkipBuild) {
    Write-Host "Step 1: Docker Login" -ForegroundColor Yellow
    docker login
    if ($LASTEXITCODE -ne 0) {
        Write-Host "Docker login failed" -ForegroundColor Red
        exit 1
    }
}

# Step 2: Build production image
if (-not $SkipBuild) {
    Write-Host "Step 2: Building production image..." -ForegroundColor Yellow
    docker build --target production -t $FullImage .
    if ($LASTEXITCODE -ne 0) {
        Write-Host "Build failed" -ForegroundColor Red
        exit 1
    }
}

# Step 3: Push to registry
if (-not $SkipPush) {
    Write-Host "Step 3: Pushing to Docker Hub..." -ForegroundColor Yellow
    docker push $FullImage
    if ($LASTEXITCODE -ne 0) {
        Write-Host "Push failed" -ForegroundColor Red
        exit 1
    }
}

# Step 4: Update K8s manifests
Write-Host "Step 4: Updating Kubernetes manifests..." -ForegroundColor Yellow
(Get-Content k8s/app-deployment.yaml) -replace 'YOUR_REGISTRY', $Registry | Set-Content k8s/app-deployment.yaml
(Get-Content k8s/queue-deployment.yaml) -replace 'YOUR_REGISTRY', $Registry | Set-Content k8s/queue-deployment.yaml
Write-Host "  ✓ Updated app-deployment.yaml" -ForegroundColor Green
Write-Host "  ✓ Updated queue-deployment.yaml" -ForegroundColor Green

# Step 5: Check secrets
$secretsContent = Get-Content k8s/secrets.yaml -Raw
if ($secretsContent -match "REPLACE_WITH") {
    Write-Host "⚠️  WARNING: Secrets not configured!" -ForegroundColor Red
    Write-Host "Please edit k8s/secrets.yaml and replace all REPLACE_WITH_* values"
    Write-Host ""
    Write-Host "Generate APP_KEY with:"
    Write-Host "  docker run --rm $FullImage php artisan key:generate --show" -ForegroundColor Cyan
    Write-Host ""
    $continue = Read-Host "Continue anyway? (y/N)"
    if ($continue -ne "y") {
        exit 1
    }
}

# Step 6: Deploy to Kubernetes
Write-Host "Step 6: Deploying to Kubernetes..." -ForegroundColor Yellow

# Create namespace
kubectl apply -f k8s/namespace.yaml
Write-Host "  ✓ Namespace created" -ForegroundColor Green

# Apply configs and secrets
kubectl apply -f k8s/configmap.yaml
kubectl apply -f k8s/secrets.yaml
Write-Host "  ✓ ConfigMap and Secrets applied" -ForegroundColor Green

# Create storage
kubectl apply -f k8s/storage-pvc.yaml
Write-Host "  ✓ Storage PVC created" -ForegroundColor Green

# Deploy MySQL
kubectl apply -f k8s/mysql-statefulset.yaml
Write-Host "  ✓ MySQL deployed" -ForegroundColor Green
Write-Host "  ⏳ Waiting for MySQL..." -ForegroundColor Yellow
kubectl wait --for=condition=ready pod -l app=mysql -n culturaltrip --timeout=300s

# Deploy Redis
kubectl apply -f k8s/redis-deployment.yaml
Write-Host "  ✓ Redis deployed" -ForegroundColor Green

# Deploy application
kubectl apply -f k8s/app-deployment.yaml
Write-Host "  ✓ Application deployed" -ForegroundColor Green
Write-Host "  ⏳ Waiting for app..." -ForegroundColor Yellow
kubectl wait --for=condition=ready pod -l app=culturaltrip-app -n culturaltrip --timeout=300s

# Deploy queue workers
kubectl apply -f k8s/queue-deployment.yaml
Write-Host "  ✓ Queue workers deployed" -ForegroundColor Green

# Deploy HPA
kubectl apply -f k8s/hpa.yaml
Write-Host "  ✓ Autoscaling configured" -ForegroundColor Green

# Step 7: Run database seeding (first time only)
Write-Host ""
Write-Host "Step 7: Database setup" -ForegroundColor Yellow
$runSeeder = Read-Host "Run database migrations and seeding? (y/N)"
if ($runSeeder -eq "y") {
    $POD = kubectl get pod -l app=culturaltrip-app -n culturaltrip -o jsonpath='{.items[0].metadata.name}'
    
    Write-Host "  ⏳ Running migrations..." -ForegroundColor Yellow
    kubectl exec -it $POD -n culturaltrip -c app -- php artisan migrate --force
    
    Write-Host "  ⏳ Seeding database..." -ForegroundColor Yellow
    kubectl exec -it $POD -n culturaltrip -c app -- php artisan db:seed --force
    
    Write-Host "  ⏳ Creating storage link..." -ForegroundColor Yellow
    kubectl exec -it $POD -n culturaltrip -c app -- php artisan storage:link
    
    Write-Host "  ⏳ Caching configuration..." -ForegroundColor Yellow
    kubectl exec -it $POD -n culturaltrip -c app -- php artisan config:cache
    kubectl exec -it $POD -n culturaltrip -c app -- php artisan route:cache
    kubectl exec -it $POD -n culturaltrip -c app -- php artisan view:cache
}

# Step 8: Summary
Write-Host ""
Write-Host "✅ Deployment Complete!" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Green
Write-Host ""
Write-Host "Check deployment status:"
Write-Host "  kubectl get pods -n culturaltrip" -ForegroundColor Cyan
Write-Host ""
Write-Host "View logs:"
Write-Host "  kubectl logs -f deployment/culturaltrip-app -n culturaltrip -c app" -ForegroundColor Cyan
Write-Host ""
Write-Host "Access application:"
Write-Host "  kubectl port-forward svc/culturaltrip-service 8080:80 -n culturaltrip" -ForegroundColor Cyan
Write-Host "  Then visit: http://localhost:8080"
Write-Host ""
Write-Host "Setup ingress for external access:"
Write-Host "  kubectl apply -f k8s/ingress.yaml" -ForegroundColor Cyan
Write-Host ""
