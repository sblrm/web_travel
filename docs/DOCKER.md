# 🐳 Docker Deployment Guide for CulturalTrip

Complete guide for running CulturalTrip in Docker containers.

## 📋 Prerequisites

- Docker Desktop (Windows/Mac) or Docker Engine (Linux)
- Docker Compose v2+
- At least 4GB RAM allocated to Docker
- 10GB free disk space

## 🚀 Quick Start

### 1. Clone & Setup Environment

```bash
# Copy Docker environment file
cp .env.docker .env

# Generate application key
docker compose run --rm app php artisan key:generate
```

### 2. Build & Start Containers

```bash
# Build and start all services
docker compose up -d

# View logs
docker compose logs -f

# Check service status
docker compose ps
```

### 3. Access Application

- **Application**: http://localhost:8000
- **Admin Panel**: http://localhost:8000/admin
  - Email: admin@culturaltrip.com
  - Password: password
- **PHPMyAdmin**: http://localhost:8080 (dev profile only)
- **Vite Dev Server**: http://localhost:5173 (dev profile only)

## 🏗️ Architecture

### Services Overview

```
┌─────────────────────────────────────────────┐
│              Nginx (Port 8000)              │
│         Web Server & Reverse Proxy          │
└────────────────┬────────────────────────────┘
                 │
┌────────────────┴────────────────────────────┐
│          PHP-FPM Application                │
│     Laravel 12 + Filament v4 Admin          │
└─────┬──────────────────────┬────────────────┘
      │                      │
┌─────┴──────┐      ┌────────┴──────┐
│   MySQL    │      │     Redis     │
│ Database   │      │     Cache     │
└────────────┘      └───────────────┘
      │
┌─────┴──────┐
│   Queue    │
│   Worker   │
└────────────┘
```

### Container Details

| Service | Image | Ports | Purpose |
|---------|-------|-------|---------|
| **app** | php:8.2-fpm-alpine | 9000 | Main Laravel application |
| **nginx** | nginx:alpine | 8000 | Web server |
| **mysql** | mysql:8.0 | 3306 | Database |
| **redis** | redis:7-alpine | 6379 | Cache & sessions |
| **queue** | php:8.2-fpm-alpine | - | Queue worker |
| **phpmyadmin** | phpmyadmin:latest | 8080 | Database admin (dev) |
| **node** | node:20-alpine | 5173 | Vite dev server (dev) |

## 📝 Docker Commands

### Basic Operations

```bash
# Start all services
docker compose up -d

# Stop all services
docker compose down

# Restart services
docker compose restart

# View logs
docker compose logs -f [service_name]

# Check service status
docker compose ps

# Execute commands in container
docker compose exec app php artisan [command]
```

### Development Workflow

```bash
# Install Composer dependencies
docker compose exec app composer install

# Install NPM dependencies
docker compose exec app npm install

# Run migrations
docker compose exec app php artisan migrate

# Seed database
docker compose exec app php artisan db:seed

# Run tests
docker compose exec app php artisan test

# Format code
docker compose exec app vendor/bin/pint

# Clear cache
docker compose exec app php artisan optimize:clear

# Access container shell
docker compose exec app sh
```

### Development with Live Reload

```bash
# Start with dev profile (includes PHPMyAdmin & Vite)
docker compose --profile dev up -d

# Access Vite dev server
# Automatically reloads on file changes
# Visit: http://localhost:5173
```

### Database Management

```bash
# Access MySQL CLI
docker compose exec mysql mysql -u culturaltrip -psecret culturaltrip

# Backup database
docker compose exec mysql mysqldump -u root -proot culturaltrip > backup.sql

# Restore database
docker compose exec -T mysql mysql -u root -proot culturaltrip < backup.sql

# PHPMyAdmin (dev profile)
# Visit: http://localhost:8080
```

## 🔧 Configuration

### Environment Variables

Edit `.env` file for configuration:

```env
# Application
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database
DB_HOST=mysql
DB_DATABASE=culturaltrip
DB_USERNAME=culturaltrip
DB_PASSWORD=secret

# Redis
REDIS_HOST=redis
CACHE_DRIVER=redis
```

### PHP Configuration

Edit `docker/php/php.ini`:

```ini
memory_limit = 512M
upload_max_filesize = 256M
post_max_size = 256M
max_execution_time = 300
```

### MySQL Configuration

Edit `docker/mysql/my.cnf`:

```ini
max_connections = 200
innodb_buffer_pool_size = 256M
```

### Nginx Configuration

Edit `docker/nginx/nginx.conf` for web server settings.

## 🏭 Production Deployment

### Build Production Image

```bash
# Build production image
docker build --target production -t culturaltrip:latest .

# Run production container
docker run -d \
  --name culturaltrip_prod \
  -p 80:9000 \
  -e APP_ENV=production \
  -e APP_DEBUG=false \
  culturaltrip:latest
```

### Docker Compose Production

Create `docker-compose.prod.yml`:

```yaml
version: '3.8'

services:
  app:
    build:
      context: .
      target: production
    environment:
      - APP_ENV=production
      - APP_DEBUG=false
    # ... other production configs
```

Run production stack:

```bash
docker compose -f docker-compose.prod.yml up -d
```

## 🐛 Troubleshooting

### Container Won't Start

```bash
# Check logs
docker compose logs app

# Rebuild containers
docker compose down
docker compose build --no-cache
docker compose up -d
```

### Permission Issues

```bash
# Fix storage permissions
docker compose exec app chown -R www-data:www-data storage bootstrap/cache
docker compose exec app chmod -R 775 storage bootstrap/cache
```

### Database Connection Errors

```bash
# Check MySQL is ready
docker compose exec mysql mysqladmin ping -h localhost -u root -proot

# Wait for database
docker compose exec app php artisan db:show
```

### Port Already in Use

```bash
# Check what's using port
# Windows
netstat -ano | findstr :8000

# Linux/Mac
lsof -i :8000

# Change port in docker-compose.yml
ports:
  - "8001:80"  # Use 8001 instead
```

### Clear Everything & Start Fresh

```bash
# Stop and remove everything
docker compose down -v

# Remove images
docker compose down --rmi all

# Start fresh
docker compose up -d --build
```

## 📊 Monitoring

### View Container Stats

```bash
# Real-time stats
docker stats

# Specific containers
docker stats culturaltrip_app culturaltrip_mysql
```

### View Logs

```bash
# All services
docker compose logs -f

# Specific service
docker compose logs -f app

# Last 100 lines
docker compose logs --tail=100 app
```

## 🔒 Security Best Practices

### Production Checklist

- [ ] Change default passwords in `.env`
- [ ] Set `APP_DEBUG=false`
- [ ] Use strong `DB_PASSWORD`
- [ ] Don't expose MySQL port (remove `ports:` section)
- [ ] Use secrets for sensitive data
- [ ] Enable HTTPS with SSL certificates
- [ ] Regular security updates: `docker compose pull`
- [ ] Backup database regularly
- [ ] Monitor container logs

### Environment Variables Security

```bash
# Use Docker secrets (Swarm mode)
docker secret create db_password db_password.txt
```

## 🚀 Performance Optimization

### Production Optimizations

```dockerfile
# Already included in Dockerfile:
- OPcache enabled
- Composer optimized autoloader
- Config/route/view caching
- Minimal Alpine base image
- Multi-stage build
```

### Database Performance

```sql
-- Optimize tables
OPTIMIZE TABLE destinations, reviews, users;

-- Analyze tables
ANALYZE TABLE destinations, reviews, users;
```

## 📚 Additional Resources

- [Docker Documentation](https://docs.docker.com/)
- [Docker Compose Documentation](https://docs.docker.com/compose/)
- [Laravel Docker Best Practices](https://laravel.com/docs/deployment#server-requirements)
- [Nginx Configuration](https://nginx.org/en/docs/)

## 🆘 Support

### Common Issues

1. **Memory Issues**: Increase Docker memory limit in settings
2. **Slow Build**: Use BuildKit: `DOCKER_BUILDKIT=1 docker compose build`
3. **Network Issues**: Check Docker network: `docker network ls`

### Logs Location

- Application: `docker compose logs app`
- Web Server: `docker compose logs nginx`
- Database: `docker compose logs mysql`
- Queue: `docker compose logs queue`

---

**Built with ❤️ for Indonesian Culture**

Last Updated: December 2, 2025
