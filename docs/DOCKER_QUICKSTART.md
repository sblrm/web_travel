# 🐳 Docker Quick Start Guide

Get CulturalTrip running in Docker containers in 5 minutes!

## ⚡ Quick Start (TL;DR)

```bash
# 1. Copy environment file
cp .env.docker .env

# 2. Start containers
docker compose up -d

# 3. Generate app key
docker compose exec app php artisan key:generate

# 4. Run migrations & seed
docker compose exec app php artisan migrate --seed

# 5. Access application
# Web: http://localhost:8000
# Admin: http://localhost:8000/admin (admin@culturaltrip.com / password)
```

## 📋 Prerequisites

- [Docker Desktop](https://www.docker.com/products/docker-desktop/) installed
- At least 4GB RAM allocated to Docker
- Ports available: 8000 (web), 3306 (mysql), 6379 (redis)

## 🏗️ What You Get

When you run `docker compose up -d`, you get:

- ✅ **Laravel Application** - PHP 8.2 with all extensions
- ✅ **Nginx Web Server** - Optimized for Laravel
- ✅ **MySQL 8.0 Database** - Pre-configured with health checks
- ✅ **Redis Cache** - For sessions and cache
- ✅ **Queue Worker** - Processes background jobs
- ✅ **PHPMyAdmin** - Database management (dev profile)
- ✅ **Vite Dev Server** - Hot module reload (dev profile)

## 📝 Common Commands

### Start/Stop Services

```bash
# Start all services
docker compose up -d

# Start with development tools (PHPMyAdmin + Vite)
docker compose --profile dev up -d

# Stop all services
docker compose down

# Stop and remove volumes (fresh start)
docker compose down -v

# Restart services
docker compose restart
```

### Laravel Commands

```bash
# Run migrations
docker compose exec app php artisan migrate

# Seed database
docker compose exec app php artisan db:seed

# Run tests
docker compose exec app php artisan test

# Clear cache
docker compose exec app php artisan optimize:clear

# Format code
docker compose exec app vendor/bin/pint

# Install composer packages
docker compose exec app composer install

# Install npm packages
docker compose exec app npm install
```

### View Logs

```bash
# All services
docker compose logs -f

# Specific service
docker compose logs -f app
docker compose logs -f nginx
docker compose logs -f mysql

# Last 100 lines
docker compose logs --tail=100 app
```

### Access Containers

```bash
# Access app container
docker compose exec app sh

# Access MySQL
docker compose exec mysql mysql -u culturaltrip -psecret culturaltrip

# Access Redis CLI
docker compose exec redis redis-cli
```

## 🌐 URLs

| Service | URL | Credentials |
|---------|-----|-------------|
| **Application** | http://localhost:8000 | - |
| **Admin Panel** | http://localhost:8000/admin | admin@culturaltrip.com / password |
| **PHPMyAdmin** | http://localhost:8080 | root / root (dev only) |
| **Vite Dev** | http://localhost:5173 | - (dev only) |

## 🔧 Development Workflow

### First Time Setup

```bash
# 1. Copy environment
cp .env.docker .env

# 2. Start services
docker compose --profile dev up -d

# 3. Generate key
docker compose exec app php artisan key:generate

# 4. Setup database
docker compose exec app php artisan migrate --seed

# 5. Verify admin email
docker compose exec app php artisan tinker --execute="User::where('email', 'admin@culturaltrip.com')->update(['email_verified_at' => now()]);"
```

### Daily Development

```bash
# Start containers
docker compose --profile dev up -d

# Watch logs
docker compose logs -f app

# Make changes to code (auto-reloads with Vite)

# Run tests
docker compose exec app php artisan test

# Format code before commit
docker compose exec app vendor/bin/pint

# Stop when done
docker compose down
```

## 🐛 Troubleshooting

### Port Already in Use

**Error**: `Bind for 0.0.0.0:8000 failed: port is already allocated`

**Solution**: Stop XAMPP or change port in `docker-compose.yml`:

```yaml
nginx:
  ports:
    - "8001:80"  # Use 8001 instead
```

### Database Connection Failed

**Error**: `SQLSTATE[HY000] [2002] Connection refused`

**Solution**: Wait for MySQL to be ready:

```bash
# Check MySQL health
docker compose ps

# Wait and retry
docker compose exec app php artisan db:show
```

### Permission Denied

**Error**: `Permission denied` when accessing storage

**Solution**: Fix permissions:

```bash
docker compose exec app chown -R www-data:www-data storage bootstrap/cache
docker compose exec app chmod -R 775 storage bootstrap/cache
```

### Assets Not Loading

**Solution**: Rebuild assets:

```bash
# Inside container
docker compose exec app npm run build

# Or use dev server (--profile dev)
docker compose --profile dev up -d
```

### Fresh Start

If something is broken, reset everything:

```bash
# Stop and remove everything
docker compose down -v

# Rebuild from scratch
docker compose build --no-cache

# Start fresh
docker compose up -d

# Re-run setup
docker compose exec app php artisan migrate --seed
```

## 📊 Monitoring

### Check Container Status

```bash
# List running containers
docker compose ps

# View resource usage
docker stats

# Check specific service
docker compose logs app --tail=50
```

### Database Management

```bash
# Via PHPMyAdmin (dev profile)
# Visit: http://localhost:8080

# Via MySQL CLI
docker compose exec mysql mysql -u culturaltrip -psecret culturaltrip

# Backup database
docker compose exec mysql mysqldump -u root -proot culturaltrip > backup.sql

# Restore database
docker compose exec -T mysql mysql -u root -proot culturaltrip < backup.sql
```

## 🚀 Production Deployment

For production deployment with Docker:

```bash
# Build production image
docker build --target production -t culturaltrip:prod .

# Use production compose file
docker compose -f docker-compose.prod.yml up -d
```

See [DOCKER.md](DOCKER.md) for complete production setup.

## 📚 More Information

- **Full Documentation**: [DOCKER.md](DOCKER.md)
- **Deployment Guide**: [DEPLOYMENT.md](DEPLOYMENT.md)
- **Laravel Docs**: [https://laravel.com/docs](https://laravel.com/docs)
- **Docker Docs**: [https://docs.docker.com](https://docs.docker.com)

## 🆘 Need Help?

1. Check [DOCKER.md](DOCKER.md) for detailed troubleshooting
2. View container logs: `docker compose logs -f`
3. Access container: `docker compose exec app sh`
4. Check service health: `docker compose ps`

---

**Happy Dockerizing! 🐳**
