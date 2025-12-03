# Laravel Cloud Deployment Guide

## Prerequisites
1. GitHub account with repository access
2. Laravel Cloud account (https://cloud.laravel.com)
3. GitHub Secrets configured in repository settings

## Step 1: Setup Laravel Cloud Project

1. **Create New Project**
   - Go to Laravel Cloud dashboard
   - Click "New Project"
   - Connect your GitHub repository: `your-username/cultural-trip`
   - Select branch strategy: `main` for staging, `production` for production

2. **Configure Environment**
   - Navigate to your project → Environments
   - Add environment variables from `.env.production.example`
   - Laravel Cloud will auto-provide: `DB_*`, `REDIS_*` credentials
   - Generate new `APP_KEY`: `php artisan key:generate --show`

3. **Setup Database**
   - Laravel Cloud provides MySQL database automatically
   - Note the database credentials from environment settings
   - Configure database connection in environment variables

4. **Configure Queue Workers**
   - Go to Workers section
   - Add queue worker: `php artisan queue:work --tries=3 --timeout=90`
   - Set number of workers: 2-3 for production

## Step 2: Configure GitHub Secrets

Add these secrets in your GitHub repository:
**Settings → Secrets and variables → Actions → New repository secret**

```bash
LARAVEL_CLOUD_API_TOKEN
# Get from: Laravel Cloud → Account Settings → API Tokens → Create New Token

LARAVEL_CLOUD_PROJECT_ID
# Get from: Laravel Cloud → Your Project → Settings → Project ID
```

## Step 3: Push Code to GitHub

```bash
# Add remote if not already added
git remote add origin https://github.com/your-username/cultural-trip.git

# Push to main branch (triggers staging deployment)
git add .
git commit -m "Setup CI/CD pipeline for Laravel Cloud"
git push origin main

# For production deployment
git checkout -b production
git push origin production
```

## Step 4: Monitor Deployment

1. **GitHub Actions**
   - Go to your repository → Actions tab
   - Watch the deployment workflow running
   - Check for any errors in test or deployment steps

2. **Laravel Cloud Dashboard**
   - Go to your project → Deployments
   - Monitor deployment progress
   - Check deployment logs for any issues

## Step 5: Post-Deployment Tasks

Run these commands via **Laravel Cloud Console**:

```bash
# Run migrations
php artisan migrate --force

# Seed admin user and initial data
php artisan db:seed --class=DatabaseSeeder --force

# Link storage for images
php artisan storage:link

# Cache configuration for better performance
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Verify admin email for Filament access
php artisan tinker --execute="App\Models\User::where('email', 'admin@culturaltrip.com')->update(['email_verified_at' => now()]);"
```

## Step 6: Verify Deployment

1. **Check Application**
   - Visit your Laravel Cloud URL: `https://your-project.laravel.cloud`
   - Test homepage, destinations listing, and destination detail pages

2. **Test Admin Panel**
   - Go to: `https://your-project.laravel.cloud/admin`
   - Login with: `admin@culturaltrip.com` / `password`
   - Verify all Filament resources are accessible

3. **Test Queue Workers**
   - Create a test review via admin panel
   - Verify notification is sent (check logs)

## Deployment Workflow

### Main Branch (Staging)
```bash
git checkout main
git add .
git commit -m "Your feature"
git push origin main
# Automatically deploys to staging environment
```

### Production Branch (Production)
```bash
git checkout production
git merge main
git push origin production
# Automatically deploys to production environment
```

### Pull Request (Tests Only)
```bash
git checkout -b feature/new-feature
git add .
git commit -m "Add new feature"
git push origin feature/new-feature
# Create PR → Runs tests only, no deployment
```

## Troubleshooting

### Deployment Failed
1. Check GitHub Actions logs
2. Verify all secrets are correctly configured
3. Check Laravel Cloud deployment logs
4. Ensure database migrations are compatible

### 403 Forbidden on Admin Panel
1. Verify admin user email ends with `@culturaltrip.com`
2. Check email verification: `php artisan tinker --execute="User::where('email', 'admin@culturaltrip.com')->first()->email_verified_at"`
3. Verify `FilamentUser` contract implementation in `User` model

### Assets Not Loading
1. Run `npm run build` locally to verify build works
2. Check Vite manifest file is generated
3. Run `php artisan storage:link` on Laravel Cloud
4. Verify `APP_URL` is correct in environment variables

### Queue Not Processing
1. Check queue worker is running in Laravel Cloud
2. Verify `QUEUE_CONNECTION=database` in environment
3. Check queue jobs table: `SELECT * FROM jobs;`
4. Restart queue workers from Laravel Cloud dashboard

## Performance Optimization

### After Deployment
```bash
# Enable OPcache (usually enabled by default on Laravel Cloud)
# Configure Redis for cache and sessions
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Clear old cache if needed
php artisan optimize:clear
```

### Monitoring
- Use Laravel Cloud built-in monitoring
- Check slow queries in database logs
- Monitor queue worker health
- Set up alerts for failed deployments

## Security Checklist

- ✅ `APP_DEBUG=false` in production
- ✅ `APP_ENV=production` in production
- ✅ Unique `APP_KEY` generated
- ✅ Database credentials secured
- ✅ Admin panel restricted to `@culturaltrip.com` emails
- ✅ HTTPS enabled (automatic on Laravel Cloud)
- ✅ Email verification enabled for admin users
- ✅ File permissions correctly set
- ✅ Secrets stored in GitHub Secrets, not in code

## Support

- Laravel Cloud Docs: https://laravel.com/cloud
- GitHub Actions Docs: https://docs.github.com/actions
- Laravel Docs: https://laravel.com/docs
