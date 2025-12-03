# 🚀 Laravel Cloud Deployment Checklist

Use this checklist to ensure smooth deployment to Laravel Cloud.

## ✅ Pre-Deployment Checklist

### 1. Code Preparation
- [ ] All tests passing locally: `php artisan test`
- [ ] Code formatted with Pint: `vendor/bin/pint --dirty`
- [ ] Assets build successfully: `npm run build`
- [ ] No security vulnerabilities: `composer audit` && `npm audit`
- [ ] `.env.production.example` reviewed and updated
- [ ] Git repository is clean and committed

### 2. Laravel Cloud Setup
- [ ] Laravel Cloud account created
- [ ] New project created in Laravel Cloud
- [ ] GitHub repository connected
- [ ] Environment variables configured
- [ ] Database provisioned
- [ ] Redis provisioned (for cache)
- [ ] Queue workers configured (2-3 workers recommended)

### 3. GitHub Configuration
- [ ] Repository pushed to GitHub
- [ ] GitHub Secrets added:
  - [ ] `LARAVEL_CLOUD_API_TOKEN`
  - [ ] `LARAVEL_CLOUD_PROJECT_ID`
- [ ] Workflow files committed:
  - [ ] `.github/workflows/deploy.yml`
  - [ ] `.github/workflows/tests.yml`

## 🔧 Laravel Cloud Environment Variables

Copy these to Laravel Cloud environment settings:

```env
# Application
APP_NAME="CulturalTrip"
APP_ENV=production
APP_KEY=[Generate new: php artisan key:generate --show]
APP_DEBUG=false
APP_URL=https://your-project.laravel.cloud

# Locale
APP_LOCALE=id
APP_FALLBACK_LOCALE=en

# Database (Auto-provided by Laravel Cloud)
DB_CONNECTION=mysql
DB_HOST=[auto-provided]
DB_PORT=3306
DB_DATABASE=[auto-provided]
DB_USERNAME=[auto-provided]
DB_PASSWORD=[auto-provided]

# Cache & Session
CACHE_STORE=redis
SESSION_DRIVER=database
QUEUE_CONNECTION=database

# Redis (Auto-provided by Laravel Cloud)
REDIS_CLIENT=phpredis
REDIS_HOST=[auto-provided]
REDIS_PORT=6379
REDIS_PASSWORD=[auto-provided]

# Mail (Configure your mail service)
MAIL_MAILER=smtp
MAIL_HOST=your-mail-host
MAIL_PORT=587
MAIL_USERNAME=your-username
MAIL_PASSWORD=your-password
MAIL_FROM_ADDRESS="noreply@culturaltrip.com"
MAIL_FROM_NAME="CulturalTrip"

# Storage
FILESYSTEM_DISK=public

# Logging
LOG_CHANNEL=stack
LOG_LEVEL=error
```

## 🚀 Deployment Steps

### Step 1: Initial Push
```bash
# Ensure you're on main branch
git checkout main

# Add all files
git add .

# Commit with descriptive message
git commit -m "Setup CI/CD pipeline for Laravel Cloud deployment"

# Push to trigger deployment
git push origin main
```

### Step 2: Monitor Deployment
1. **GitHub Actions**: 
   - Go to: `https://github.com/your-username/cultural-trip/actions`
   - Watch deployment progress
   - Check for any errors

2. **Laravel Cloud Dashboard**:
   - Go to: `https://cloud.laravel.com`
   - Select your project
   - Monitor deployment status
   - Check deployment logs

### Step 3: Post-Deployment Commands

Run these via **Laravel Cloud Console** (Project → Console):

```bash
# 1. Run database migrations
php artisan migrate --force

# 2. Seed initial data (first deployment only)
php artisan db:seed --class=DatabaseSeeder --force

# 3. Link storage directory
php artisan storage:link

# 4. Cache configuration for performance
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 5. Verify admin user email
php artisan tinker --execute="App\Models\User::where('email', 'admin@culturaltrip.com')->update(['email_verified_at' => now()]);"

# 6. Clear any old caches (if re-deploying)
php artisan optimize:clear
```

## ✅ Post-Deployment Verification

### 1. Application Health Check
- [ ] Homepage loads: `https://your-project.laravel.cloud/`
- [ ] Destinations page: `https://your-project.laravel.cloud/destinasi`
- [ ] Individual destination: Click any destination
- [ ] Assets loading correctly (CSS, JS, images)
- [ ] No console errors (check browser DevTools)

### 2. Admin Panel Access
- [ ] Admin panel accessible: `https://your-project.laravel.cloud/admin`
- [ ] Login works with: `admin@culturaltrip.com` / `password`
- [ ] All Filament resources visible:
  - [ ] Destinations
  - [ ] Reviews
  - [ ] Provinces
  - [ ] Categories
  - [ ] Users

### 3. Functional Testing
- [ ] User registration works
- [ ] User login works
- [ ] Review submission works (logged in user)
- [ ] Review voting works
- [ ] Review approval notification sent (test in admin)

### 4. Performance Check
- [ ] Page load time < 2 seconds
- [ ] Images loading properly
- [ ] No N+1 query issues
- [ ] Queue workers processing jobs

## 🔒 Security Verification

- [ ] `APP_DEBUG=false` confirmed
- [ ] HTTPS enabled (automatic on Laravel Cloud)
- [ ] Admin panel restricted to `@culturaltrip.com` emails
- [ ] Email verification required for admin access
- [ ] Database credentials secured
- [ ] API tokens not exposed in logs

## 🐛 Common Issues & Solutions

### ❌ Deployment Failed
**Check**:
- [ ] GitHub Secrets correctly configured
- [ ] Laravel Cloud API token valid
- [ ] Project ID correct
- [ ] Review GitHub Actions logs
- [ ] Review Laravel Cloud deployment logs

### ❌ 403 Forbidden - Admin Panel
**Solution**:
```bash
# Verify admin email
php artisan tinker --execute="User::where('email', 'admin@culturaltrip.com')->first()"

# Verify email if needed
php artisan tinker --execute="User::where('email', 'admin@culturaltrip.com')->update(['email_verified_at' => now()])"
```

### ❌ Assets Not Loading
**Solution**:
```bash
# Rebuild assets locally and push
npm run build
git add public/build
git commit -m "Rebuild production assets"
git push origin main

# Or run on Laravel Cloud
php artisan storage:link
```

### ❌ Queue Not Processing
**Check**:
- [ ] Queue worker running in Laravel Cloud
- [ ] `QUEUE_CONNECTION=database` in environment
- [ ] Jobs table exists: `php artisan migrate`
- [ ] Restart queue workers from dashboard

### ❌ Database Connection Error
**Check**:
- [ ] Database credentials in environment variables
- [ ] Database provisioned in Laravel Cloud
- [ ] Migrations run successfully
- [ ] Check Laravel Cloud database status

## 📊 Monitoring Setup

### Laravel Cloud Built-in
- [ ] Check deployment history
- [ ] Monitor queue worker health
- [ ] Review application logs
- [ ] Check database performance

### External (Optional)
- [ ] Setup error tracking (e.g., Sentry, Bugsnag)
- [ ] Setup uptime monitoring (e.g., Uptime Robot)
- [ ] Configure log aggregation
- [ ] Setup performance monitoring (e.g., New Relic)

## 🔄 Future Deployments

### Regular Updates
```bash
# 1. Make changes locally
git add .
git commit -m "Description of changes"

# 2. Run tests
php artisan test

# 3. Format code
vendor/bin/pint --dirty

# 4. Push to staging
git push origin main

# 5. Test on staging
# Visit: https://staging.laravel.cloud

# 6. Deploy to production
git checkout production
git merge main
git push origin production
```

### Rollback (if needed)
```bash
# Via Laravel Cloud Dashboard
# Go to Deployments → Find last good deployment → Rollback

# Or via Git
git revert HEAD
git push origin production
```

## 📝 Notes

- **First deployment takes longer** (5-10 minutes)
- **Subsequent deployments are faster** (2-3 minutes)
- **Always test on staging first** before production
- **Keep GitHub Secrets secure** - rotate if compromised
- **Monitor logs** after deployment for any issues
- **Database migrations are irreversible** - backup before major changes

## 🎯 Success Criteria

Deployment is successful when:
- ✅ All tests pass in CI/CD
- ✅ Application loads without errors
- ✅ Admin panel accessible
- ✅ User features working
- ✅ Queue processing jobs
- ✅ No security vulnerabilities
- ✅ Performance acceptable (< 2s load time)

## 📚 Documentation References

- [DEPLOYMENT.md](../DEPLOYMENT.md) - Detailed deployment guide
- [.github/GITHUB_SECRETS.md](.github/GITHUB_SECRETS.md) - Secrets setup
- [.github/CI_CD_SUMMARY.md](.github/CI_CD_SUMMARY.md) - Pipeline overview
- [Laravel Cloud Docs](https://laravel.com/cloud)

---

**Last Updated**: December 2, 2025  
**Version**: 1.0.0  
**Status**: ✅ Production Ready
