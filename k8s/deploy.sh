#!/bin/bash

# 🚀 Kubernetes Deployment Script for CulturalTrip
# Usage: ./k8s/deploy.sh [docker-username]

set -e  # Exit on error

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Configuration
DOCKER_USERNAME="${1:-}"
IMAGE_NAME="culturaltrip"
VERSION="${2:-latest}"

echo -e "${GREEN}🚀 CulturalTrip Kubernetes Deployment${NC}"
echo "========================================"

# Check if Docker username provided
if [ -z "$DOCKER_USERNAME" ]; then
    echo -e "${RED}Error: Docker username required${NC}"
    echo "Usage: ./k8s/deploy.sh [docker-username] [version]"
    echo ""
    echo "Example: ./k8s/deploy.sh johndo latest"
    echo ""
    echo "Don't have Docker Hub account? Sign up at: https://hub.docker.com"
    exit 1
fi

REGISTRY="docker.io/$DOCKER_USERNAME"
FULL_IMAGE="$REGISTRY/$IMAGE_NAME:$VERSION"

echo -e "${YELLOW}Configuration:${NC}"
echo "  Registry: $REGISTRY"
echo "  Image: $FULL_IMAGE"
echo ""

# Step 1: Docker login
echo -e "${YELLOW}Step 1: Docker Login${NC}"
docker login || {
    echo -e "${RED}Docker login failed${NC}"
    exit 1
}

# Step 2: Build production image
echo -e "${YELLOW}Step 2: Building production image...${NC}"
docker build --target production -t $FULL_IMAGE . || {
    echo -e "${RED}Build failed${NC}"
    exit 1
}

# Step 3: Push to registry
echo -e "${YELLOW}Step 3: Pushing to Docker Hub...${NC}"
docker push $FULL_IMAGE || {
    echo -e "${RED}Push failed${NC}"
    exit 1
}

# Step 4: Update K8s manifests
echo -e "${YELLOW}Step 4: Updating Kubernetes manifests...${NC}"
sed -i.bak "s|YOUR_REGISTRY|$REGISTRY|g" k8s/app-deployment.yaml
sed -i.bak "s|YOUR_REGISTRY|$REGISTRY|g" k8s/queue-deployment.yaml
echo "  ✓ Updated app-deployment.yaml"
echo "  ✓ Updated queue-deployment.yaml"

# Step 5: Check secrets
if grep -q "REPLACE_WITH" k8s/secrets.yaml; then
    echo -e "${RED}⚠️  WARNING: Secrets not configured!${NC}"
    echo "Please edit k8s/secrets.yaml and replace all REPLACE_WITH_* values"
    echo ""
    echo "Generate APP_KEY with:"
    echo "  docker run --rm $FULL_IMAGE php artisan key:generate --show"
    echo ""
    read -p "Press Enter to continue or Ctrl+C to exit..."
fi

# Step 6: Deploy to Kubernetes
echo -e "${YELLOW}Step 6: Deploying to Kubernetes...${NC}"

# Create namespace
kubectl apply -f k8s/namespace.yaml
echo "  ✓ Namespace created"

# Apply configs and secrets
kubectl apply -f k8s/configmap.yaml
kubectl apply -f k8s/secrets.yaml
echo "  ✓ ConfigMap and Secrets applied"

# Create storage
kubectl apply -f k8s/storage-pvc.yaml
echo "  ✓ Storage PVC created"

# Deploy MySQL
kubectl apply -f k8s/mysql-statefulset.yaml
echo "  ✓ MySQL deployed"
echo "  ⏳ Waiting for MySQL..."
kubectl wait --for=condition=ready pod -l app=mysql -n culturaltrip --timeout=300s

# Deploy Redis
kubectl apply -f k8s/redis-deployment.yaml
echo "  ✓ Redis deployed"

# Deploy application
kubectl apply -f k8s/app-deployment.yaml
echo "  ✓ Application deployed"
echo "  ⏳ Waiting for app..."
kubectl wait --for=condition=ready pod -l app=culturaltrip-app -n culturaltrip --timeout=300s

# Deploy queue workers
kubectl apply -f k8s/queue-deployment.yaml
echo "  ✓ Queue workers deployed"

# Deploy HPA
kubectl apply -f k8s/hpa.yaml
echo "  ✓ Autoscaling configured"

# Step 7: Run database seeding (first time only)
echo -e "${YELLOW}Step 7: Database setup${NC}"
read -p "Run database migrations and seeding? (y/N) " -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]; then
    POD=$(kubectl get pod -l app=culturaltrip-app -n culturaltrip -o jsonpath='{.items[0].metadata.name}')
    echo "  ⏳ Running migrations..."
    kubectl exec -it $POD -n culturaltrip -c app -- php artisan migrate --force
    echo "  ⏳ Seeding database..."
    kubectl exec -it $POD -n culturaltrip -c app -- php artisan db:seed --force
    echo "  ⏳ Creating storage link..."
    kubectl exec -it $POD -n culturaltrip -c app -- php artisan storage:link
    echo "  ⏳ Caching configuration..."
    kubectl exec -it $POD -n culturaltrip -c app -- php artisan config:cache
    kubectl exec -it $POD -n culturaltrip -c app -- php artisan route:cache
    kubectl exec -it $POD -n culturaltrip -c app -- php artisan view:cache
fi

# Step 8: Summary
echo ""
echo -e "${GREEN}✅ Deployment Complete!${NC}"
echo "========================================"
echo ""
echo "Check deployment status:"
echo "  kubectl get pods -n culturaltrip"
echo ""
echo "View logs:"
echo "  kubectl logs -f deployment/culturaltrip-app -n culturaltrip -c app"
echo ""
echo "Access application:"
echo "  kubectl port-forward svc/culturaltrip-service 8080:80 -n culturaltrip"
echo "  Then visit: http://localhost:8080"
echo ""
echo "Setup ingress for external access:"
echo "  kubectl apply -f k8s/ingress.yaml"
echo ""
