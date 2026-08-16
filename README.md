# YonksTEAM

YonksTEAM is the WordPress-powered brand site for Jason and Tyler Younker, helping burned-out advisors get their lives back.

**Current theme version: 2.1.0** — modern blue design system, 12 page templates, 10 custom ACF blocks, Inner Circle & Agent Operator product pages.

---

## 🚀 One-Click Theme Install (ZIP)

Grab the latest release ZIP (`yonksteam-2.1.0.zip`) from the GitHub Releases page or build it yourself:

```bash
bash release-zip.sh
```

Then in WordPress: **Appearance → Themes → Add New → Upload Theme**, upload the ZIP, activate.

### Required plugins (activate before/after — blocks degrade gracefully without them)
- **ACF (free or Pro)** — powers the 10 custom blocks. Pro unlocks repeater fields & inline field editing in blocks; the free version works for most blocks but the success/failure repeater and two-paths links need Pro for full editor control.
- **Fluent Forms (free)** — contact + newsletter forms (`wp:fluentform/form` blocks)
- **Yoast SEO (free)** — recommended
- **FluentCRM (free tier)** — newsletter list management (optional, recommended)
- **FluentCart** — for the Inner Circle ($12/yr) and Agent Operator ($97) product checkouts

After activating: go to **Custom Fields → Field Groups** and click **Sync** to load the 10 field groups from `acf-json/`.

---

## 📄 Pages & Templates

| Page | Template | Built with |
|------|----------|-----------|
| Home | `templates/home.html` | ACF blocks + patterns |
| For Advisors | `templates/page-for-advisors.html` | ACF blocks |
| Exit to Client | `templates/page-exit-to-client.html` | ACF blocks |
| Inner Circle | `templates/page-inner-circle.html` | ACF hero + core blocks |
| Agent Operator | `templates/page-agent-operator.html` | ACF hero + core blocks |
| About | `templates/page-about.html` | ACF blocks |
| Newsletter | `templates/page-newsletter.html` | ACF hero + pattern |
| Contact | `templates/page-contact.html` | ACF hero + Fluent Forms |
| Blog archive | `templates/archive.html` | Query loop |
| Blog post | `templates/single.html` | Post content |

Create WordPress pages with the matching slugs (`inner-circle`, `agent-operator`, `for-advisors`, etc.) and the templates apply automatically.

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