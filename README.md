# WA4E Resume Application

PHP application for managing resumes with profiles, positions, and education.

## 🚀 Quick Start (Production)

```bash
# Clone and start
git clone https://github.com/MEHDImp/jvscript1.git
cd jvscript1
docker-compose up -d
```

Access: http://localhost:8080

**Login:** `csev@umich.edu` / `php123`

---

## 🔧 CI/CD Pipeline

| Component | Description |
|-----------|-------------|
| **Dockerfile** | Production PHP 8.2 Apache with PDO MySQL |
| **GitHub Actions** | Auto-build and push to GHCR on `main` push |
| **Watchtower** | Auto-updates containers every 30 seconds |

### How it works:
1. Push to `main` branch
2. GitHub Actions builds Docker image
3. Image pushed to `ghcr.io/mehdimp/jvscript1:latest`
4. Watchtower detects new image and updates container

---

## ⚠️ IMPORTANT: Make GHCR Package Public

**Your server needs to pull the image without authentication.** 

Follow these steps after your first successful build:

1. Go to your GitHub repository
2. Click **"Packages"** in the right sidebar
3. Click on `jvscript1` package
4. Click **"Package settings"** (gear icon)
5. Scroll down to **"Danger Zone"**
6. Click **"Change visibility"**
7. Select **"Public"**
8. Type the package name to confirm

> Without this step, `docker-compose up` will fail with authentication errors on your server.

---

## 📁 Files

| File | Purpose |
|------|---------|
| `Dockerfile` | Production Docker image |
| `docker-compose.yml` | Production stack with MySQL + Watchtower |
| `.github/workflows/deploy.yml` | CI/CD pipeline |
| `schema.sql` | Database schema |

---

## 🔒 Environment Variables

| Variable | Default | Description |
|----------|---------|-------------|
| `DB_HOST` | `db` | MySQL hostname |
| `DB_NAME` | `misc` | Database name |
| `DB_USER` | `fred` | Database user |
| `DB_PASS` | `zap` | Database password |
