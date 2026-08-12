# YonksTEAM

YonksTEAM is the WordPress-powered brand site for Jason and Tyler Younker, helping burned-out advisors get their lives back.

---

## 🚀 Option A — One-Click Install (Composer)

```bash
composer install
```

This will install WordPress core (`^6.5`) into the `wp/` directory along with the required plugins:
- Yoast SEO (`^22.0`)
- Disable Comments (`^2.4`)
- Limit Login Attempts Reloaded (`^2.26`)

---

## 🛠 Option B — Developer Workflow

### Prerequisites
- PHP 8.1+
- Composer
- Node.js 20+
- Local development environment (Local WP, Laravel Herd, etc.)

### Setup

```bash
# 1. Install PHP dependencies & WordPress core
composer install

# 2. Install Node dependencies
cd wp-content/themes/yonksteam
npm install

# 3. Build theme assets
npm run build
```

### Development

```bash
# Start the dev server with hot-reload
npm run dev
```

### Build a Release ZIP

```bash
# From the project root, run:
bash release-zip.sh
```

Or from the GitHub UI: create a new release and the `release-zip.yml` workflow will automatically build and attach the ZIP.

---

## 📖 Implementation Guide

See [implementation-guide.md](implementation-guide.md) for the full project architecture, coding standards, and deployment instructions.