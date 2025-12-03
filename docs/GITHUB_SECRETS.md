# GitHub Secrets Configuration

Configure these secrets in your GitHub repository to enable automated deployment to Laravel Cloud.

**Location**: Repository Settings → Secrets and variables → Actions → New repository secret

## Required Secrets

### LARAVEL_CLOUD_API_TOKEN
**Description**: API token for authenticating with Laravel Cloud API

**How to get**:
1. Login to [Laravel Cloud Dashboard](https://cloud.laravel.com)
2. Go to Account Settings (top right)
3. Navigate to "API Tokens" section
4. Click "Create New Token"
5. Name: "GitHub Actions - CulturalTrip"
6. Copy the token (it will only be shown once!)
7. Add to GitHub Secrets

**Format**: `lc_xxxxxxxxxxxxxxxxxxxxxxxxxxxxx`

---

### LARAVEL_CLOUD_PROJECT_ID
**Description**: Unique identifier for your Laravel Cloud project

**How to get**:
1. Login to [Laravel Cloud Dashboard](https://cloud.laravel.com)
2. Select your "CulturalTrip" project
3. Go to Settings
4. Find "Project ID" (usually at the top)
5. Copy the project ID
6. Add to GitHub Secrets

**Format**: `12345` (numeric ID)

---

## Setting Secrets in GitHub

### Via GitHub Web Interface
1. Go to your repository on GitHub
2. Click "Settings" tab
3. Click "Secrets and variables" → "Actions" in left sidebar
4. Click "New repository secret"
5. Enter secret name (exactly as shown above)
6. Paste secret value
7. Click "Add secret"

### Verification
After adding secrets, you can verify they exist (but cannot view values):
- Go to Settings → Secrets and variables → Actions
- You should see:
  - ✅ LARAVEL_CLOUD_API_TOKEN
  - ✅ LARAVEL_CLOUD_PROJECT_ID

## Security Notes

⚠️ **Never commit secrets to your repository**
⚠️ **Never share secrets in public channels**
⚠️ **Rotate tokens if compromised**
✅ **Use environment-specific tokens for staging/production**
✅ **Review token permissions regularly**

## Testing Deployment

After configuring secrets, test the deployment:

```bash
# Trigger deployment to staging
git push origin main

# Or trigger manual deployment
# Go to Actions → Deploy to Laravel Cloud → Run workflow
```

Monitor deployment:
- GitHub: Repository → Actions tab
- Laravel Cloud: Project Dashboard → Deployments

## Troubleshooting

### "Invalid API Token" Error
- Verify token is copied correctly (no extra spaces)
- Check token hasn't expired
- Regenerate token in Laravel Cloud if needed

### "Project Not Found" Error
- Verify project ID is correct
- Check project exists in Laravel Cloud
- Ensure API token has access to the project

### Deployment Not Triggering
- Verify you pushed to `main` or `production` branch
- Check workflow file exists: `.github/workflows/deploy.yml`
- Ensure secrets are named exactly as expected

## Additional Resources

- [Laravel Cloud Documentation](https://laravel.com/cloud)
- [GitHub Actions Secrets](https://docs.github.com/en/actions/security-guides/encrypted-secrets)
- [Project DEPLOYMENT.md](./DEPLOYMENT.md) - Full deployment guide
