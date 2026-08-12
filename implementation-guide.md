# YonksTEAM — WordPress Implementation Guide

**Version:** 2.0  
**Date:** August 12, 2026  
**Based on:** Content Design Document v2.0 — BrandScript: Burned-Out Advisor  
**Stack:** WordPress + GitHub + Custom Block Theme

---

## Table of Contents

1. [Architecture Overview](#1-architecture-overview)
2. [One-Click Install Compatibility](#2-one-click-install-compatibility)
3. [GitHub Repository Structure](#3-github-repository-structure)
4. [WordPress Theme Architecture](#4-wordpress-theme-architecture)
5. [Page Templates & Block Mapping](#5-page-templates--block-mapping)
6. [Custom Gutenberg Blocks](#6-custom-gutenberg-blocks)
7. [Plugin Stack](#7-plugin-stack)
8. [Content Migration Strategy](#8-content-migration-strategy)
9. [GitHub → WordPress Deployment Workflow](#9-github--wordpress-deployment-workflow)
10. [Component Library (Reusable Block Patterns)](#10-component-library-reusable-block-patterns)
11. [Styling Architecture](#11-styling-architecture)
12. [SEO Implementation](#12-seo-implementation)
13. [Forms & Conversion Tracking](#13-forms--conversion-tracking)
14. [Performance Budget](#14-performance-budget)
15. [Launch Checklist](#15-launch-checklist)

---

## 1. Architecture Overview

### 1.1 High-Level Stack

```
┌─────────────────────────────────────────────────────────┐
│                    WordPress CMS                         │
│  (Content management, user auth, form entries, SEO)      │
├─────────────────────────────────────────────────────────┤
│                 Custom Block Theme                        │
│  (theme.json, block templates, custom blocks, styles)    │
├─────────────────────────────────────────────────────────┤
│                    GitHub                                 │
│  (Version control, code review, CI/CD deployment)        │
└─────────────────────────────────────────────────────────┘
```

### 1.2 Architecture Decision Record

| Decision | Choice | Rationale |
|----------|--------|-----------|
| Theme type | Custom block theme (FSE-compatible) | WordPress is moving toward Full Site Editing. Block themes are the modern standard and will be supported long-term. |
| Page builder | No page builder | Page builders lock content into shortcodes and proprietary data. Gutenberg + custom blocks gives us full control and portability. |
| Custom blocks | ACF Blocks or @wordpress/create-block | ACF Blocks are faster to build for content-heavy sites. Native blocks are better for polished UI components. Hybrid approach recommended. |
| CSS approach | Tailwind CSS (via theme.json + build step) | Need to match the content design's warmth, spacing, and typography. Tailwind gives us a design system out of the box. |
| Deployment | GitHub Actions → Deployer/RSync | Automated, auditable, repeatable. No FTP. |
| Forms | Gravity Forms or Fluent Forms | Gravity Forms is the industry standard. Fluent Forms is lighter. Both have webhook integrations. |

### 1.3 Theme Type Comparison

| Approach | Pros | Cons | Verdict |
|----------|------|------|---------|
| Classic theme (PHP templates) | Mature, well-understood, plugin-compatible | No FSE, harder to maintain, out of date | ❌ |
| Block theme (FSE) | Future-proof, native Gutenberg, theme.json | Requires WordPress 6.0+, some plugins not FSE-aware | ✅ **Recommended** |
| Hybrid theme (block + classic) | Best of both, gradual migration | More complex, confusing architecture | ✅ Acceptable fallback |
| Page builder (Elementor/Beaver) | Visual editing, fast to build | Lock-in, performance bloat, hard to version-control | ❌ |

---

## 2. One-Click Install Compatibility

### 2.1 What "One-Click Install" Means

WordPress offers a **one-click install** feature when you:

1. Go to **Appearance → Themes → Add New → Upload Theme**
2. Upload a `.zip` file of the theme
3. WordPress extracts it, activates it, and it **just works**

For this to work, the theme must be **fully self-contained** with **no build step required**. Every CSS and JS file must be pre-compiled and included in the repository.

### 2.2 The Problem: Build Steps

❌ **What the original guide assumed:**
```
wp-content/themes/yonksteam/
├── src/                 # Source files (Tailwind, SCSS, etc.)
│   └── css/
│       └── tailwind.css  # ↳ Needs `npm run build` to compile
├── package.json          # ↳ Needs `npm install` first
└── assets/css/style.css  # ↳ Output — NOT committed to GitHub
```

If someone downloads the repo as a ZIP and uploads it to WordPress, the `assets/css/style.css` file doesn't exist yet. The theme shows up blank or broken.

### 2.3 The Solution: Dual-Mode Architecture

This implementation uses a **dual-mode approach**:

| Mode | How | Who Uses It |
|------|-----|-------------|
| **One-Click Install** | Pre-compiled CSS lives in the repo. The theme is ready to upload and activate. No build step required. | Anyone downloading from GitHub Releases or the WordPress admin |
| **Developer Workflow** | `npm install && npm run build` recompiles assets. Tailwind, minification, etc. run on commit. | Developers working locally or via CI/CD |

**How it works:**

1. The `assets/css/style.css` file is **committed to the repo** (pre-compiled)
2. When a developer runs `npm run build`, it overwrites `assets/css/style.css` with the latest compiled version
3. The `.gitignore` does **NOT** ignore `assets/css/style.css` — it's tracked
4. Developers can still use Tailwind utilities in their PHP/HTML files; the compiled CSS already contains all utility classes they need
5. For one-click install: download the repo ZIP → upload to WordPress → activate → works instantly

### 2.4 One-Click Install Workflow

```
GitHub Repository
       │
       ├── Download ZIP (from GitHub UI or Releases page)
       │
       ▼
WordPress Admin → Appearance → Themes → Add New → Upload Theme
       │
       ▼
Select yonksteam.zip → Install → Activate
       │
       ▼
✅ Theme is live. Pages are pre-built with block templates.
   Custom blocks are registered. No setup required.
```

### 2.5 What the User Gets After One-Click Install

| Feature | Works Immediately? | Notes |
|---------|-------------------|-------|
| Theme activated | ✅ | Styles, layout, typography all pre-compiled |
| Block templates | ✅ | Home, About, For Advisors, etc. rendered via FSE |
| Custom Gutenberg blocks | ✅ | ACF blocks register on `init`. ACF Pro must be installed separately. |
| Block patterns | ✅ | Pre-built patterns available in the editor |
| Navigation menus | ✅ | Pre-configured in the theme |
| SEO metadata | ⚠️ | Yoast config added via code, but page titles need manual entry |
| Forms | ⚠️ | Gravity Forms forms need to be created manually in the admin |
| ACF field groups | ⚠️ | ACF Pro must be installed first. Field groups sync from JSON. |

> **Important for one-click install:** The theme is fully functional on its own. However, for the custom blocks to render their fields, **Advanced Custom Fields Pro** must be installed and activated. The theme includes ACF field group JSON files that sync automatically when ACF Pro is active.

### 2.6 GitHub Releases for One-Click Distribution

For the cleanest one-click experience, publish **GitHub Releases** with a pre-built ZIP:

```bash
# Create a release ZIP (include only the theme, not the whole repo)
cd wp-content/themes/
zip -r yonksteam-2.0.0.zip yonksteam/ \
  --exclude="yonksteam/node_modules/*" \
  --exclude="yonksteam/src/*" \
  --exclude="yonksteam/package.json" \
  --exclude="yonksteam/package-lock.json" \
  --exclude="yonksteam/postcss.config.js" \
  --exclude="yonksteam/tailwind.config.js" \
  --exclude="yonksteam/.gitignore"
```

This creates a clean ZIP containing only the theme files — no source maps, no build tools, no unnecessary files. Upload this ZIP to the GitHub Release, and users can download and install it directly via **Appearance → Themes → Add New → Upload Theme**.

### 2.7 Automatic Release ZIP via GitHub Actions

To automate the release ZIP, add this workflow to `.github/workflows/release-zip.yml`:

```yaml
name: Build Release ZIP

on:
  release:
    types: [published]

jobs:
  build:
    runs-on: ubuntu-latest
    
    steps:
      - name: Checkout code
        uses: actions/checkout@v4
      
      - name: Setup Node
        uses: actions/setup-node@v4
        with:
          node-version: 20
      
      - name: Install dependencies
        run: npm ci
        working-directory: wp-content/themes/yonksteam
      
      - name: Build assets
        run: npm run build
        working-directory: wp-content/themes/yonksteam
      
      - name: Create clean ZIP
        run: |
          cd wp-content/themes
          zip -r ../../yonksteam-release.zip yonksteam/ \
            --exclude="yonksteam/node_modules/*" \
            --exclude="yonksteam/src/*" \
            --exclude="yonksteam/package.json" \
            --exclude="yonksteam/package-lock.json" \
            --exclude="yonksteam/postcss.config.js" \
            --exclude="yonksteam/tailwind.config.js" \
            --exclude="yonksteam/.gitignore"
      
      - name: Upload ZIP to Release
        uses: actions/upload-release-asset@v1
        env:
          GITHUB_TOKEN: ${{ secrets.GITHUB_TOKEN }}
        with:
          upload_url: ${{ github.event.release.upload_url }}
          asset_path: ./yonksteam-release.zip
          asset_name: yonksteam-${{ github.event.release.tag_name }}.zip
          asset_content_type: application/zip
```

This workflow automatically:
1. Runs `npm run build` to compile fresh CSS
2. Strips out `node_modules/`, `src/`, `package.json`, and build config files
3. Uploads the cleaned ZIP to the GitHub Release page
4. Users download the ZIP and install via WordPress admin — one click

---

## 3. GitHub Repository Structure

### 3.1 Repository Layout

```
yonksteam-wordpress/
├── .github/
│   └── workflows/
│       ├── release-zip.yml           # Auto-generate one-click-install ZIP on release
│       ├── deploy-staging.yml        # Deploy to staging on PR merge
│       └── deploy-production.yml     # Deploy to production on tag/release
├── release-zip.sh                    # Script to manually generate a clean ZIP
├── README.md                         # Setup instructions for both workflows
├── wp-content/
│   ├── themes/
│   │   └── yonksteam/
│   │       ├── .gitignore
│   │       ├── style.css              # Theme header
│   │       ├── theme.json             # Global styles, color palette, typography
│   │       ├── index.php              # Fallback template
│   │       ├── functions.php          # Theme setup, block registration, enqueues
│   │       ├── parts/                 # Block theme parts (header, footer)
│   │       │   ├── header.html
│   │       │   └── footer.html
│   │       ├── templates/             # Block theme templates
│   │       │   ├── index.html
│   │       │   ├── home.html
│   │       │   ├── page.html
│   │       │   ├── single.html
│   │       │   ├── archive.html
│   │       │   ├── page-about.html
│   │       │   ├── page-for-advisors.html
│   │       │   ├── page-exit-to-client.html
│   │       │   ├── page-newsletter.html
│   │       │   └── page-contact.html
│   │       ├── blocks/                # Custom Gutenberg / ACF blocks
│   │       │   ├── hero-block/
│   │       │   ├── recognition-block/
│   │       │   ├── empathy-story/
│   │       │   ├── authority-block/
│   │       │   ├── plan-steps/
│   │       │   ├── success-failure-split/
│   │       │   ├── transformation-statement/
│   │       │   ├── two-paths/
│   │       │   ├── testimonial-block/
│   │       │   └── cta-section/
│   │       ├── patterns/              # Block patterns (reusable component library)
│   │       │   ├── hero-default.php
│   │       │   ├── hero-for-advisors.php
│   │       │   ├── cta-section.php
│   │       │   ├── newsletter-signup.php
│   │       │   └── ...
│       │   ├── assets/
│       │   │   ├── css/
│       │   │   │   ├── style.css      # ✅ PRE-COMPILED — committed to repo
│       │   │   │   │                   #    Works on upload. No build step needed.
│       │   │   │   └── editor.css     # ✅ PRE-COMPILED — editor preview styles
│       │   │   ├── js/
│       │   │   │   ├── navigation.js
│       │   │   │   └── blocks.js
│       │   │   └── images/
│       │   │       ├── logo.svg
│       │   │       └── favicon.ico
│       │   ├── src/                   # Source files for development build
│       │   │   ├── css/
│       │   │   │   ├── tailwind.css   # Tailwind input (compiles → assets/css/style.css)
│       │   │   │   └── blocks/        # Per-block source CSS
│       │   │   └── js/
│       │   ├── package.json           # Build tools (optional — not needed for one-click)
│       │   └── postcss.config.js      # PostCSS config (optional)
│       │
│       │   # ⚠️ IMPORTANT: assets/css/style.css is COMMITTED to the repo
│       │   # The .gitignore does NOT exclude it. This makes one-click install work.
│       │   # Developers run `npm run build` to regenerate it from src/.
│       │
│       └── .gitkeep
│   ├── plugins/
│   │   └── (managed via Composer or .gitignore)
│   └── uploads/
│       └── .gitignore
├── composer.json                      # WordPress core + plugins as dependencies
├── .gitignore
└── README.md
```

### 3.2 .gitignore Strategy

```gitignore
# WordPress core (managed via Composer)
wp/
wp-admin/
wp-includes/
xmlrpc.php
wp-config.php

# Uploads
wp-content/uploads/

# Build artifacts (source maps only)
*.map

# Dependencies
node_modules/

# Environment
.env
.env.local
.phpunit.result.cache

# OS files
.DS_Store
Thumbs.db
```

> **Note:** `assets/css/style.css` is **NOT** in `.gitignore`. The compiled CSS is committed to the repo so one-click install works.

### 3.3 Composer-Based WordPress Management

```json
{
  "name": "yonksteam/wordpress",
  "require": {
    "php": ">=8.1",
    "johnpbloch/wordpress": "^6.5",
    "wpackagist-plugin/wordpress-seo": "^22.0",
    "wpackagist-plugin/disable-comments": "^2.4",
    "wpackagist-plugin/limit-login-attempts-reloaded": "^2.26"
  },
  "extra": {
    "wordpress-install-dir": "wp"
  }
}
```

> **Note for one-click install:** Composer is only needed for the developer workflow. The theme itself doesn't require Composer. Gravity Forms and ACF Pro are premium plugins and need to be installed manually.

---

## 3. WordPress Theme Architecture

### 3.1 Theme Header (`style.css`)

```css
/*
Theme Name: YonksTEAM
Theme URI: https://yonks.team
Author: Jason & Tyler Younker
Author URI: https://yonks.team
Description: Custom block theme for YonksTEAM — the brand of Jason and Tyler Younker, helping burned-out advisors get their lives back.
Version: 2.0.0
Requires at least: 6.5
Tested up to: 6.6
Requires PHP: 8.1
License: GPL v2 or later
Text Domain: yonksteam
*/
```

> **One-click install:** This file is the minimum WordPress needs to recognize the theme. When the ZIP is uploaded, WordPress reads this header and displays the theme in the admin. No build step required.

### 3.2 theme.json — Global Styles Configuration

```json
{
  "version": 2,
  "settings": {
    "color": {
      "palette": [
        {
          "slug": "primary",
          "color": "#1a1a2e",
          "name": "Deep Navy"
        },
        {
          "slug": "secondary",
          "color": "#c97b3a",
          "name": "Warm Amber"
        },
        {
          "slug": "accent",
          "color": "#d4946a",
          "name": "Terracotta"
        },
        {
          "slug": "background",
          "color": "#faf8f5",
          "name": "Warm Off-White"
        },
        {
          "slug": "surface",
          "color": "#f5f0eb",
          "name": "Light Warm"
        },
        {
          "slug": "foreground",
          "color": "#1a1a2e",
          "name": "Text Primary"
        },
        {
          "slug": "muted",
          "color": "#6b6b7d",
          "name": "Text Muted"
        },
        {
          "slug": "white",
          "color": "#ffffff",
          "name": "White"
        }
      ]
    },
    "typography": {
      "fontFamilies": [
        {
          "fontFamily": "\"Chronicle Display\", Georgia, \"Times New Roman\", serif",
          "slug": "heading",
          "name": "Chronicle (Headings)"
        },
        {
          "fontFamily": "Inter, -apple-system, BlinkMacSystemFont, \"Segoe UI\", sans-serif",
          "slug": "body",
          "name": "Inter (Body)"
        }
      ],
      "fontSizes": [
        { "slug": "small", "size": "0.875rem" },
        { "slug": "medium", "size": "1rem" },
        { "slug": "large", "size": "1.25rem" },
        { "slug": "xl", "size": "1.5rem" },
        { "slug": "2xl", "size": "2rem" },
        { "slug": "3xl", "size": "2.5rem" },
        { "slug": "4xl", "size": "3.5rem" },
        { "slug": "5xl", "size": "4.5rem" }
      ],
      "lineHeight": {
        "body": 1.7,
        "heading": 1.3
      }
    },
    "spacing": {
      "padding": true,
      "margin": true,
      "units": ["px", "em", "rem", "vh", "vw"]
    },
    "layout": {
      "contentSize": "720px",
      "wideSize": "1140px"
    }
  },
  "styles": {
    "blocks": {
      "core/paragraph": {
        "typography": {
          "fontFamily": "var(--wp--preset--font-family--body)",
          "lineHeight": "1.7"
        }
      },
      "core/heading": {
        "typography": {
          "fontFamily": "var(--wp--preset--font-family--heading)"
        }
      }
    }
  }
}
```

### 3.3 functions.php — Theme Setup

```php
<?php
/**
 * YonksTEAM Theme Functions
 */

// Theme setup
function yonksteam_setup() {
    // Block theme support
    add_theme_support('wp-block-styles');
    add_theme_support('align-wide');
    add_theme_support('editor-styles');
    
    // Editor styles
    add_editor_style('assets/css/editor.css');
    
    // Navigation menus (for fallback)
    register_nav_menus([
        'primary' => __('Primary Navigation', 'yonksteam'),
        'footer'  => __('Footer Navigation', 'yonksteam'),
    ]);
    
    // Translation ready
    load_theme_textdomain('yonksteam');
}
add_action('after_setup_theme', 'yonksteam_setup');

// Register custom blocks
function yonksteam_register_blocks() {
    $blocks = [
        'hero-block',
        'recognition-block',
        'empathy-story',
        'authority-block',
        'plan-steps',
        'success-failure-split',
        'transformation-statement',
        'two-paths',
        'testimonial-block',
        'cta-section',
    ];
    
    foreach ($blocks as $block) {
        register_block_type(__DIR__ . "/blocks/{$block}");
    }
}
add_action('init', 'yonksteam_register_blocks');

// Enqueue pre-compiled assets
function yonksteam_enqueue_assets() {
    // ✅ Pre-compiled CSS — works immediately on one-click install
    // No build step required. The file is committed to the repository.
    $theme_version = wp_get_theme()->get('Version');
    
    // Theme stylesheet (style.css — theme header)
    wp_enqueue_style('yonksteam-style', get_stylesheet_uri(), [], $theme_version);
    
    // Main compiled CSS (all custom styles, Tailwind utilities, block styles)
    wp_enqueue_style(
        'yonksteam-main',
        get_template_directory_uri() . '/assets/css/style.css',
        ['yonksteam-style'],
        $theme_version
    );
    
    // Navigation JS
    wp_enqueue_script(
        'yonksteam-navigation',
        get_template_directory_uri() . '/assets/js/navigation.js',
        [],
        $theme_version,
        true
    );
}
add_action('wp_enqueue_scripts', 'yonksteam_enqueue_assets');

// ACF JSON sync directory (auto-syncs field groups from JSON files)
function yonksteam_acf_json_save($path) {
    return get_template_directory() . '/acf-json';
}
add_filter('acf/settings/save_json', 'yonksteam_acf_json_save');

function yonksteam_acf_json_load($paths) {
    $paths[] = get_template_directory() . '/acf-json';
    return $paths;
}
add_filter('acf/settings/load_json', 'yonksteam_acf_json_load');
```

---

## 4. Page Templates & Block Mapping

### 4.1 Template-to-Page Mapping

| URL Path | WordPress Template | Content Source | Page Title |
|----------|-------------------|----------------|------------|
| `/` | `home.html` (FSE) or `front-page.php` | Block template + custom blocks | N/A (Front Page) |
| `/for-advisors` | `page-for-advisors.html` | Custom page content | "For Advisors" |
| `/exit-to-client` | `page-exit-to-client.html` | Custom page content | "Exit to Client" |
| `/about` | `page-about.html` | Custom page content | "About" |
| `/newsletter` | `page-newsletter.html` | Custom page content | "The Next Season" |
| `/blog` | `archive.html` (FSE) or `index.php` | Native WordPress posts | "Blog" |
| `/blog/{slug}` | `single.html` (FSE) or `single.php` | Native WordPress posts | Post title |
| `/contact` | `page-contact.html` | Gravity Forms shortcode/block | "Start the Conversation" |

### 4.2 Block Composition per Page Template

**Homepage (`home.html` or `front-page.php`):**

```
┌─────────────────────────────────────────────────────┐
│  Header (block theme part)                          │
├─────────────────────────────────────────────────────┤
│  Hero Block                                         │
│  ┌───────────────────────────────────────────────┐  │
│  │ Headline: "You hate work you used to love."   │  │
│  │ Subheadline: "You can say that here."         │  │
│  │ CTAs: Start the Conversation / Read Our Story │  │
│  │ Photo: Jason & Tyler (candid, warm)           │  │
│  └───────────────────────────────────────────────┘  │
├─────────────────────────────────────────────────────┤
│  Recognition Block                                   │
│  ┌───────────────────────────────────────────────┐  │
│  │ Headline: "A practice you built to fund your  │  │
│  │ life shouldn't be the thing keeping you from  │  │
│  │ living it."                                   │  │
│  │ Body: Problem description (spacious, no       │  │
│  │ competing visuals)                            │  │
│  └───────────────────────────────────────────────┘  │
├─────────────────────────────────────────────────────┤
│  Empathy Story Block                                 │
│  ┌───────────────────────────────────────────────┐  │
│  │ Headline: "We know exactly what that feels    │  │
│  │ like."                                        │  │
│  │ Tyler's story (marching band)                 │  │
│  │ Jason's story (Uber)                          │  │
│  │ Split photo or side-by-side shots             │  │
│  └───────────────────────────────────────────────┘  │
├─────────────────────────────────────────────────────┤
│  Authority Block                                     │
│  ┌───────────────────────────────────────────────┐  │
│  │ Headline: "The way out we teach is the way    │  │
│  │ we took."                                     │  │
│  │ 3 credential blocks (travel, Tyler's $3B,     │  │
│  │ Jason's CEPA)                                 │  │
│  └───────────────────────────────────────────────┘  │
├─────────────────────────────────────────────────────┤
│  Plan Steps Block                                    │
│  ┌───────────────────────────────────────────────┐  │
│  │ Headline: "Here's how it works."              │  │
│  │ 3 steps (Say it / Picture it / Wake up)       │  │
│  │ Numbered, visual, clear                       │  │
│  └───────────────────────────────────────────────┘  │
├─────────────────────────────────────────────────────┤
│  Success/Failure Split Block                         │
│  ┌───────────────────────────────────────────────┐  │
│  │ Two-column layout                             │  │
│  │ Left: Success (green/positive)                │  │
│  │ Right: Failure (softer, cautionary)           │  │
│  └───────────────────────────────────────────────┘  │
├─────────────────────────────────────────────────────┤
│  Transformation Statement Block                      │
│  ┌───────────────────────────────────────────────┐  │
│  │ From: "The perfect life on paper, and done    │  │
│  │ inside."                                      │  │
│  │ To: "A life that feels as good as it looks."  │  │
│  └───────────────────────────────────────────────┘  │
├─────────────────────────────────────────────────────┤
│  CTA Section Block                                   │
│  ┌───────────────────────────────────────────────┐  │
│  │ Headline: "Ready to want the day again?"      │  │
│  │ CTAs: Start the Conversation / Newsletter      │  │
│  └───────────────────────────────────────────────┘  │
├─────────────────────────────────────────────────────┤
│  Footer (block theme part)                          │
└─────────────────────────────────────────────────────┘
```

**For Advisors (`page-for-advisors.html`):**

```
┌─────────────────────────────────────────────────────┐
│  Header                                              │
├─────────────────────────────────────────────────────┤
│  Hero Block (advisor variant)                        │
│  ┌───────────────────────────────────────────────┐  │
│  │ Headline: "You built a practice that clients  │  │
│  │ trust. Now you need a way out that doesn't    │  │
│  │ betray them."                                 │  │
│  └───────────────────────────────────────────────┘  │
├─────────────────────────────────────────────────────┤
│  Advisor-Specific Problem Block                      │
│  ┌───────────────────────────────────────────────┐  │
│  │ Headline: "The clock is ticking on the old    │  │
│  │ model."                                       │  │
│  │ Body: AI fear, ugly exits, client scattering  │  │
│  └───────────────────────────────────────────────┘  │
├─────────────────────────────────────────────────────┤
│  Two Paths Block                                     │
│  ┌───────────────────────────────────────────────┐  │
│  │ Path 1: Rebuild the practice (keep it,        │  │
│  │ modernize with AI, get evenings back)         │  │
│  │ Path 2: Exit to Client (sell to clients,      │  │
│  │ they become owners)                           │  │
│  └───────────────────────────────────────────────┘  │
├─────────────────────────────────────────────────────┤
│  CTA Section                                         │
│  ┌───────────────────────────────────────────────┐  │
│  │ "Start the conversation"                      │  │
│  └───────────────────────────────────────────────┘  │
├─────────────────────────────────────────────────────┤
│  Footer                                              │
└─────────────────────────────────────────────────────┘
```

**Exit to Client (`page-exit-to-client.html`):**

```
┌─────────────────────────────────────────────────────┐
│  Header                                              │
├─────────────────────────────────────────────────────┤
│  Hero Block (exit variant)                           │
│  ┌───────────────────────────────────────────────┐  │
│  │ Headline: "Exit to Client. The sale that      │  │
│  │ serves everyone."                             │  │
│  └───────────────────────────────────────────────┘  │
├─────────────────────────────────────────────────────┤
│  Comparison Table Block                              │
│  ┌───────────────────────────────────────────────┐  │
│  │ Traditional Exit vs Exit to Client            │  │
│  │ 5-row comparison table                        │  │
│  └───────────────────────────────────────────────┘  │
├─────────────────────────────────────────────────────┤
│  How It Works (Steps)                                │
│  ┌───────────────────────────────────────────────┐  │
│  │ Step-by-step explanation of the model         │  │
│  └───────────────────────────────────────────────┘  │
├─────────────────────────────────────────────────────┤
│  CTA Section                                         │
│  ┌───────────────────────────────────────────────┐  │
│  │ "Start the conversation about Exit to Client" │  │
│  └───────────────────────────────────────────────┘  │
├─────────────────────────────────────────────────────┤
│  Footer                                              │
└─────────────────────────────────────────────────────┘
```

**About (`page-about.html`):**

```
┌─────────────────────────────────────────────────────┐
│  Header                                              │
├─────────────────────────────────────────────────────┤
│  Hero Block (about variant)                          │
│  ┌───────────────────────────────────────────────┐  │
│  │ Headline: "We got out. We can show you how."  │  │
│  └───────────────────────────────────────────────┘  │
├─────────────────────────────────────────────────────┤
│  Story Chapters (Chapter 1–5)                        │
│  ┌───────────────────────────────────────────────┐  │
│  │ Chapter 1: The Burnout                        │  │
│  │ Chapter 2: The Escape                         │  │
│  │ Chapter 3: The Credentials                    │  │
│  │ Chapter 4: The Model                          │  │
│  │ Chapter 5: The Mission                        │  │
│  └───────────────────────────────────────────────┘  │
├─────────────────────────────────────────────────────┤
│  Team Section (Two-column layout)                    │
│  ┌───────────────────────────────────────────────┐  │
│  │ Jason (yonks) | Tyler (MrsYonks)             │  │
│  │ Photo + bio + background for each             │  │
│  └───────────────────────────────────────────────┘  │
├─────────────────────────────────────────────────────┤
│  CTA Section                                         │
│  ┌───────────────────────────────────────────────┐  │
│  │ "Ready to write your next chapter?"           │  │
│  │ "Start the Conversation"                      │  │
│  └───────────────────────────────────────────────┘  │
├─────────────────────────────────────────────────────┤
│  Footer                                              │
└─────────────────────────────────────────────────────┘
```

**Newsletter (`page-newsletter.html`):**

```
┌─────────────────────────────────────────────────────┐
│  Header                                              │
├─────────────────────────────────────────────────────┤
│  Hero Block (newsletter variant)                     │
│  ┌───────────────────────────────────────────────┐  │
│  │ Headline: "The Next Season."                  │  │
│  │ Subheadline: "One newsletter. From the two    │  │
│  │ of us. About the life after the practice."    │  │
│  └───────────────────────────────────────────────┘  │
├─────────────────────────────────────────────────────┤
│  Newsletter Sign-up Block (form)                     │
│  ┌───────────────────────────────────────────────┐  │
│  │ Email field + "Join The Next Season" button   │  │
│  │ Social proof: "Join [X] subscribers"          │  │
│  └───────────────────────────────────────────────┘  │
├─────────────────────────────────────────────────────┤
│  Footer                                              │
└─────────────────────────────────────────────────────┘
```

**Contact (`page-contact.html`):**

```
┌─────────────────────────────────────────────────────┐
│  Header                                              │
├─────────────────────────────────────────────────────┤
│  Hero Block (contact variant)                        │
│  ┌───────────────────────────────────────────────┐  │
│  │ Headline: "Start the conversation."           │  │
│  │ Body: "We'll respond with honesty, not a      │  │
│  │ sales pitch."                                 │  │
│  └───────────────────────────────────────────────┘  │
├─────────────────────────────────────────────────────┤
│  Contact Form (Gravity Forms)                        │
│  ┌───────────────────────────────────────────────┐  │
│  │ Fields: Name, Email, Phone (optional),        │  │
│  │ Textarea: "Tell us about your practice..."    │  │
│  └───────────────────────────────────────────────┘  │
├─────────────────────────────────────────────────────┤
│  Footer                                              │
└─────────────────────────────────────────────────────┘
```

---

## 5. Custom Gutenberg Blocks

### 5.1 Block Inventory

| Block Name | Slug | Type | Fields | Used On |
|-----------|------|------|--------|---------|
| Hero Block | `yonksteam/hero-block` | ACF Block | Headline, Subheadline, Body, CTA Primary, CTA Secondary, Image, Variant (select) | Homepage, all pages |
| Recognition Block | `yonksteam/recognition-block` | ACF Block | Headline, Body, optional quote | Homepage, For Advisors |
| Empathy Story | `yonksteam/empathy-story` | ACF Block | Headline, Story 1 (title + body + image), Story 2 (title + body + image) | Homepage, About |
| Authority Block | `yonksteam/authority-block` | ACF Block | Headline, Credential 1/2/3 (icon + title + body) | Homepage, About |
| Plan Steps | `yonksteam/plan-steps` | ACF Block | Headline, Subheadline, Step 1/2/3 (number + title + body) | Homepage, For Advisors |
| Success/Failure Split | `yonksteam/success-failure-split` | ACF Block | Headline, Success items (repeater), Failure items (repeater) | Homepage |
| Transformation Statement | `yonksteam/transformation-statement` | ACF Block | From text, To text, Body | Homepage |
| Two Paths | `yonksteam/two-paths` | ACF Block | Headline, Path 1 (title + body + CTA), Path 2 (title + body + CTA) | For Advisors |
| Testimonial Block | `yonksteam/testimonial-block` | ACF Block | Quote, Name, Title, Photo, optional link | Homepage, Exit to Client |
| CTA Section | `yonksteam/cta-section` | ACF Block | Headline, Body, CTA 1 (text + link), CTA 2 (text + link), Background color | Bottom of most pages |
| Comparison Table | `yonksteam/comparison-table` | ACF Block | Headline, Rows (repeater: traditional field, exit-to-client field) | Exit to Client |
| Team Section | `yonksteam/team-section` | ACF Block or Core | Photo, Name, Role, Bio, Social link (×2) | About |

### 5.2 ACF Block Registration Example

**`blocks/hero-block/block.json`:**

```json
{
  "name": "acf/hero-block",
  "title": "Hero Block",
  "description": "A full-width hero section with headline, subheadline, CTAs, and optional image.",
  "style": "file:./hero-block.css",
  "category": "yonksteam",
  "icon": "star-filled",
  "keywords": ["hero", "header", "cta"],
  "acf": {
    "mode": "preview",
    "renderTemplate": "hero-block.php"
  },
  "supports": {
    "align": ["full"],
    "anchor": true
  },
  "attributes": {
    "variant": {
      "type": "string",
      "default": "default"
    }
  }
}
```

**`blocks/hero-block/hero-block.php`:**

```php
<?php
$headline = get_field('headline');
$subheadline = get_field('subheadline');
$body = get_field('body');
$cta_primary = get_field('cta_primary');
$cta_secondary = get_field('cta_secondary');
$image = get_field('image');
$variant = $block['variant'] ?? 'default';
?>

<div class="hero-block hero-block--<?php echo esc_attr($variant); ?> alignfull">
  <div class="hero-block__inner">
    <div class="hero-block__content">
      <?php if ($headline): ?>
        <h1 class="hero-block__headline"><?php echo esc_html($headline); ?></h1>
      <?php endif; ?>
      
      <?php if ($subheadline): ?>
        <p class="hero-block__subheadline"><?php echo esc_html($subheadline); ?></p>
      <?php endif; ?>
      
      <?php if ($body): ?>
        <div class="hero-block__body"><?php echo wp_kses_post($body); ?></div>
      <?php endif; ?>
      
      <div class="hero-block__actions">
        <?php if ($cta_primary): ?>
          <a href="<?php echo esc_url($cta_primary['url']); ?>" class="btn btn--primary">
            <?php echo esc_html($cta_primary['title']); ?>
          </a>
        <?php endif; ?>
        
        <?php if ($cta_secondary): ?>
          <a href="<?php echo esc_url($cta_secondary['url']); ?>" class="btn btn--secondary">
            <?php echo esc_html($cta_secondary['title']); ?>
          </a>
        <?php endif; ?>
      </div>
    </div>
    
    <?php if ($image): ?>
      <div class="hero-block__image">
        <img src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($image['alt']); ?>" />
      </div>
    <?php endif; ?>
  </div>
</div>
```

### 5.3 Block Field Groups (ACF)

**Hero Block Fields:**
| Field Name | Type | Settings |
|-----------|------|----------|
| Headline | Text | Required, max 80 chars |
| Subheadline | Text | Optional, max 120 chars |
| Body | WYSIWYG | Optional |
| CTA Primary | Link | Required |
| CTA Secondary | Link | Optional |
| Image | Image | Recommended, size: large |
| Variant | Select | Default, Advisor, Exit, About, Newsletter, Contact |

**Plan Steps Fields:**
| Field Name | Type | Settings |
|-----------|------|----------|
| Headline | Text | Required |
| Subheadline | Text | Optional |
| Step 1 Title | Text | Required |
| Step 1 Body | Textarea | Required |
| Step 2 Title | Text | Required |
| Step 2 Body | Textarea | Required |
| Step 3 Title | Text | Required |
| Step 3 Body | Textarea | Required |

**Success/Failure Split Fields:**
| Field Name | Type | Settings |
|-----------|------|----------|
| Headline | Text | Optional |
| Success Items | Repeater | Subfields: Text (textarea) |
| Failure Items | Repeater | Subfields: Text (textarea) |

---

## 6. Plugin Stack

### 6.1 Required Plugins

| Plugin | Purpose | Cost | Notes |
|--------|---------|------|-------|
| **Advanced Custom Fields Pro** | Custom Gutenberg blocks | Paid (~$49–$199/yr) | Required for ACF Block approach. Non-negotiable. |
| **Gravity Forms** | Contact form, newsletter sign-up | Paid (~$59/yr) | Industry standard. Webhook support for CRM. |
| **Yoast SEO** | Metadata, OG tags, sitemap, readability | Free | Structured data support. |
| **FluentCRM** | Newsletter management | Free/Premium | Self-hosted alternative to Mailchimp. Keeps data on your server. |
| **WP Rocket** | Caching, performance | Paid (~$59/yr) | Page cache, minification, lazy load. |
| **Disable Comments** | Remove WordPress comments globally | Free | Clean install. |
| **Limit Login Attempts Reloaded** | Security | Free | Brute force protection. |
| **WPS Hide Login** | Custom login URL | Free | Security through obscurity. |
| **UpdraftPlus** | Backups | Free | Scheduled backups to cloud storage. |

### 6.2 Optional / Nice-to-Have Plugins

| Plugin | Purpose | Notes |
|--------|---------|-------|
| **Safe SVG** | Upload SVG logos safely | For the YonksTEAM logo |
| **Redirection** | Manage 301 redirects | For old site URL migration |
| **MainWP** | Manage multiple WP sites | If YonksTEAM manages multiple sites |
| **FluentSmtp** | Reliable email delivery | Ensures form emails reach inbox |

### 6.3 Plugin Management Strategy

- **Version-controlled in GitHub:** Only custom/essential plugins as Composer dependencies
- **Not version-controlled:** Premium plugins (Gravity Forms, ACF Pro) — installed manually or via license key in CI
- **Auto-update:** All plugins set to auto-update minor versions

---

## 7. Content Migration Strategy

### 7.1 What's Migrating from the Old Site

| Old Site Content | Action | Destination |
|-----------------|--------|-------------|
| "Welcome! We're glad you're here" | Archive — no longer on-brand | Standalone blog post or archive |
| Full biography text | Restructure into About page chapters | `/about` |
| REtoken / DAO history | Include as Chapter 3 in About | `/about` |
| Investment clubs (CO, OH, TN) | Include in authority/credibility | Homepage authority block, About |
| BMT Guide reference | Brief mention in About Chapter 4 | `/about` |
| Retken mention | Remove — not relevant to current positioning | N/A |
| "Building ♾️ WeOwnNet 🌐 ecosystem" | Keep as footer tagline | Footer |

### 7.2 New Content to Create

| Content | Type | Priority | Notes |
|---------|------|----------|-------|
| Homepage hero copy | Text | P0 | Core brand hook |
| Tyler's marching band story | Text | P0 | Empathy section |
| Jason's Uber story | Text | P0 | Empathy section |
| 3-step plan copy | Text | P0 | Plan section |
| Success/Failure lists | Text | P0 | Split section |
| Exit to Client explanation | Text | P0 | Product page |
| For Advisors page copy | Text | P0 | Audience-specific |
| Blog launch posts | Text | P1 | 3–5 posts |
| Newsletter sign-up page | Text + Form | P1 | |
| Photo selection & upload | Media | P0 | Candid, warm, real |
| Logo & brand assets | Media | P0 | |

### 7.3 Migration Workflow

```
1. Set up WordPress with theme + plugins (via GitHub)
2. Create pages from template (assign page templates)
3. Add content via Gutenberg using custom blocks
4. Create forms in Gravity Forms
5. Set up FluentCRM lists and automations
6. Configure Yoast SEO (metadata, sitemap, OG)
7. Redirect old site URLs where applicable
8. Test all forms, links, and tracking
9. Launch
```

### 7.4 Content Migration via WP-CLI (for scale)

```bash
# Create pages from template
wp post create --post_type=page --post_title="For Advisors" --post_template="page-for-advisors.html" --post_status=draft

wp post create --post_type=page --post_title="Exit to Client" --post_template="page-exit-to-client.html" --post_status=draft

wp post create --post_type=page --post_title="About" --post_template="page-about.html" --post_status=draft

wp post create --post_type=page --post_title="The Next Season" --post_template="page-newsletter.html" --post_status=draft

wp post create --post_type=page --post_title="Start the Conversation" --post_template="page-contact.html" --post_status=draft

# Set front page
wp option update show_on_front 'page'
wp option update page_on_front $(wp post list --post_type=page --name=home --format=ids)
wp option update page_for_posts $(wp post list --post_type=page --name=blog --format=ids)
```

---

## 8. GitHub → WordPress Deployment Workflow

### 8.1 Deployment Diagram

```
Developer pushes to GitHub
          │
          ▼
    GitHub Actions triggered
          │
          ├──→ Run build (npm install, npm run build)
          │
          ├──→ Run linters (PHPCS, ESLint)
          │
          └──→ Deploy to server
                    │
                    ├──→ Staging: push to staging.yonks.team
                    └──→ Production: push to yonks.team (on tag/release)
```

### 8.2 GitHub Actions Workflow (Production)

**`.github/workflows/deploy-production.yml`:**

```yaml
name: Deploy to Production

on:
  release:
    types: [published]

jobs:
  deploy:
    runs-on: ubuntu-latest
    
    steps:
      - name: Checkout code
        uses: actions/checkout@v4
      
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.2'
      
      - name: Install Composer dependencies
        run: composer install --no-dev --optimize-autoloader
      
      - name: Install NPM dependencies
        run: npm ci
      
      - name: Build assets
        run: npm run build
      
      - name: Deploy via Deployer
        uses: deployphp/action@v1
        with:
          dep: deploy production
          private_key: ${{ secrets.SSH_PRIVATE_KEY }}
          known_hosts: ${{ secrets.SSH_KNOWN_HOSTS }}
        env:
          DEPLOYER_IP: ${{ secrets.DEPLOYER_IP }}
```

### 8.3 Deployer Configuration (`deploy.php`)

```php
<?php
namespace Deployer;

require 'recipe/common.php';

// Project name
set('application', 'yonksteam');

// Project repository
set('repository', 'git@github.com:yonksteam/yonksteam-wordpress.git');

// Shared files/dirs between deploys
set('shared_files', ['wp-config.php']);
set('shared_dirs', ['wp-content/uploads']);

// Writable dirs
set('writable_dirs', ['wp-content/uploads']);

// Hosts
host('production')
    ->setHostname('yonks.team')
    ->set('remote_user', 'deploy')
    ->set('deploy_path', '/var/www/yonks.team');

// Tasks
task('build:assets', function () {
    cd('{{release_path}}');
    run('npm ci && npm run build');
});

task('deploy', [
    'deploy:prepare',
    'deploy:vendors',
    'build:assets',
    'deploy:shared',
    'deploy:writable',
    'deploy:symlink',
    'deploy:clear_paths',
    'deploy:unlock',
    'cleanup',
    'success',
]);

after('deploy:failed', 'deploy:unlock');
```

### 8.4 Environment Configuration

**`.env.production` (not committed to GitHub):**

```
WP_ENV=production
WP_HOME=https://yonks.team
WP_SITEURL=https://yonks.team/wp
DB_NAME=yonksteam_prod
DB_USER=yonksteam
DB_PASSWORD=***
DB_HOST=localhost
```

**`wp-config.php` (managed by Deployer as shared file):**

```php
<?php
$env = parse_ini_file('.env');
define('DB_NAME', $env['DB_NAME']);
define('DB_USER', $env['DB_USER']);
define('DB_PASSWORD', $env['DB_PASSWORD']);
define('DB_HOST', $env['DB_HOST']);
define('WP_HOME', $env['WP_HOME']);
define('WP_SITEURL', $env['WP_SITEURL']);
define('WP_ENVIRONMENT_TYPE', $env['WP_ENV']);

// Security salts (generated per environment)
define('AUTH_KEY',        '...');
define('SECURE_AUTH_KEY', '...');
define('LOGGED_IN_KEY',   '...');
define('NONCE_KEY',       '...');
define('AUTH_SALT',       '...');
define('SECURE_AUTH_SALT','...');
define('LOGGED_IN_SALT',  '...');
define('NONCE_SALT',      '...');

$table_prefix = 'yk_';

// Disable file editing in admin
define('DISALLOW_FILE_EDIT', true);

// Enable debug for staging, disable for production
if (WP_ENVIRONMENT_TYPE === 'staging') {
    define('WP_DEBUG', true);
    define('WP_DEBUG_LOG', true);
    define('WP_DEBUG_DISPLAY', false);
}

if (!defined('ABSPATH')) {
    define('ABSPATH', __DIR__ . '/wp/');
}
require_once ABSPATH . 'wp-settings.php';
```

---

## 9. Recommended Hosting & Environment

### 9.1 Hosting Requirements

| Requirement | Minimum | Recommended |
|-------------|---------|-------------|
| PHP | 8.1 | 8.2+ |
| MySQL | 8.0 | 8.0+ |
| Memory | 256MB | 512MB+ |
| Disk | 10GB SSD | 20GB+ SSD |
| Web Server | Apache/Nginx | Nginx (faster) |
| SSL | Let's Encrypt | Auto-renewing |
| CDN | Optional | Cloudflare (free) |

### 9.2 Recommended Hosting

| Provider | Why | Cost |
|----------|-----|------|
| **WP Engine** | Purpose-built for WordPress, staging envs, Git integration | $20–$50/mo |
| **Cloudways** | Managed VPS, flexible, good for single-site | $12–$40/mo |
| **Kinsta** | Premium, great performance, excellent support | $35+/mo |
| **Flywheel** | Good for agencies, staging included | $25+/mo |
| **DigitalOcean + RunCloud** | DIY, most control, cheapest | $12–$24/mo + $6/mo |

### 9.3 Environment Architecture

```
┌─────────────────────────────────────────────────────────────────┐
│                         Cloudflare (CDN + DNS)                    │
│  - SSL termination                                               │
│  - Page caching (static assets)                                  │
│  - WAF (Web Application Firewall)                                │
│  - Rate limiting                                                 │
└───────────────────────────┬─────────────────────────────────────┘
                            │
┌───────────────────────────▼─────────────────────────────────────┐
│                      Nginx (Web Server)                          │
│  - FastCGI cache for pages                                       │
│  - Gzip compression                                              │
│  - Security headers (HSTS, CSP, X-Frame-Options)                 │
└───────────────────────────┬─────────────────────────────────────┘
                            │
┌───────────────────────────▼─────────────────────────────────────┐
│                     WordPress (PHP 8.2+)                         │
│  - Custom block theme                                            │
│  - ACF Pro + Gravity Forms + Yoast + FluentCRM                   │
│  - WP Rocket for page caching                                    │
└───────────────────────────┬─────────────────────────────────────┘
                            │
┌───────────────────────────▼─────────────────────────────────────┐
│                       MySQL 8.0+                                 │
│  - InnoDB engine                                                 │
│  - Query cache disabled (use Redis instead)                      │
└─────────────────────────────────────────────────────────────────┘
```

---

## 10. Component Library (Reusable Block Patterns)

### 10.1 Block Pattern Registration

Block patterns allow the editor to insert pre-designed component combinations. Register them in `functions.php`:

```php
function yonksteam_register_block_patterns() {
    // Register pattern category
    register_block_pattern_category('yonksteam', [
        'label' => __('YonksTEAM', 'yonksteam'),
    ]);
    
    // Register patterns
    $patterns = [
        'hero-default'        => 'Hero — Default (Homepage)',
        'hero-for-advisors'   => 'Hero — For Advisors',
        'hero-exit-to-client' => 'Hero — Exit to Client',
        'hero-about'          => 'Hero — About',
        'hero-newsletter'     => 'Hero — Newsletter',
        'hero-contact'        => 'Hero — Contact',
        'cta-section'         => 'CTA Section',
        'newsletter-signup'   => 'Newsletter Sign-up',
        'two-column-story'    => 'Two-Column Story Section',
        'credential-grid'     => 'Credential Grid (3 columns)',
    ];
    
    foreach ($patterns as $slug => $title) {
        register_block_pattern(
            "yonksteam/{$slug}",
            [
                'title'      => __($title, 'yonksteam'),
                'categories' => ['yonksteam'],
                'content'    => file_get_contents(__DIR__ . "/patterns/{$slug}.php"),
            ]
        );
    }
}
add_action('init', 'yonksteam_register_block_patterns');
```

### 10.2 Pattern File Example

**`patterns/hero-default.php`:**

```php
<?php
/**
 * Title: Hero — Default (Homepage)
 * Slug: yonksteam/hero-default
 * Categories: yonksteam
 */
?>
<!-- wp:acf/hero-block {"name":"acf/hero-block","variant":"default"} -->
<!-- wp:acf/hero-block-fields -->
<!-- Headline: "You hate work you used to love." -->
<!-- Subheadline: "You can say that here." -->
<!-- CTA Primary: "Start the Conversation →" /contact -->
<!-- CTA Secondary: "Read Our Story →" /about -->
<!-- /wp:acf/hero-block-fields -->
<!-- /wp:acf/hero-block -->
```

### 10.3 Component Library Index

| Pattern | Blocks Used | Description |
|---------|------------|-------------|
| `hero-default` | Hero Block | Homepage hero with primary CTAs |
| `hero-for-advisors` | Hero Block | Advisor-specific hero copy |
| `hero-exit-to-client` | Hero Block | Exit to Client hero |
| `hero-about` | Hero Block | About page hero |
| `hero-newsletter` | Hero Block | Newsletter sign-up hero |
| `hero-contact` | Hero Block | Contact page hero |
| `cta-section` | CTA Section | Bottom-of-page CTA (2 buttons) |
| `newsletter-signup` | Core + Gravity Forms | Email form + social proof |
| `two-column-story` | Core Columns | Side-by-side story layout |
| `credential-grid` | Core Columns + ACF | 3-column credential display |

---

## 11. Styling Architecture

### 11.1 CSS Strategy (Dual-Mode)

| Layer | Tool | Purpose | One-Click? |
|-------|------|---------|------------|
| Global styles | `theme.json` | Colors, typography, spacing, layout | ✅ Works immediately |
| Base styles | Tailwind CSS (pre-compiled) | Utility classes, reset, design tokens | ✅ Pre-compiled in `assets/css/style.css` |
| Block styles | Per-block CSS | Component-specific styling | ✅ Compiled into `assets/css/style.css` |
| Editor styles | `editor.css` | Gutenberg editor preview | ✅ Pre-compiled |

### 11.2 How the Dual-Mode CSS Pipeline Works

**One-click install (no build step):**
```
assets/css/style.css  ← Pre-compiled, committed to GitHub
     ↓
WordPress enqueues it via functions.php
     ↓
✅ Theme renders correctly on first activation
```

**Developer workflow (with build step):**
```
src/css/tailwind.css  ← Tailwind input with @tailwind directives
     ↓
npm run build  ← Runs Tailwind CLI
     ↓
assets/css/style.css  ← Overwritten with latest compiled output
     ↓
Committed to GitHub  ← New version is tracked
```

### 11.3 Build Script (package.json)

```json
{
  "name": "yonksteam-theme",
  "version": "2.0.0",
  "scripts": {
    "build": "tailwindcss -i ./src/css/tailwind.css -o ./assets/css/style.css --minify",
    "dev": "tailwindcss -i ./src/css/tailwind.css -o ./assets/css/style.css --watch",
    "build:blocks": "tailwindcss -i ./src/css/blocks/hero-block.css -o ./assets/css/blocks/hero-block.css --minify",
    "prebuild": "echo 'Building compiled CSS...'"
  },
  "devDependencies": {
    "tailwindcss": "^3.4"
  }
}
```

> **Note for one-click install:** `package.json` and `node_modules/` are excluded from the release ZIP. They are not needed. The pre-compiled `assets/css/style.css` is already present.

### 11.4 Tailwind Input File (src/css/tailwind.css)

```css
@tailwind base;
@tailwind components;
@tailwind utilities;

/* Custom global styles */
@layer base {
  html {
    scroll-behavior: smooth;
  }
  
  body {
    @apply font-body text-foreground bg-background antialiased;
  }
  
  h1, h2, h3, h4, h5, h6 {
    @apply font-heading text-primary;
  }
}

@layer components {
  .btn {
    @apply inline-flex items-center gap-2 px-6 py-3 rounded font-body font-semibold 
           no-underline transition-all duration-200 cursor-pointer border-2;
  }
  
  .btn--primary {
    @apply bg-secondary text-white border-secondary;
  }
  
  .btn--primary:hover {
    @apply bg-accent border-accent;
  }
  
  .btn--secondary {
    @apply bg-transparent text-primary border-primary;
  }
  
  .btn--secondary:hover {
    @apply bg-primary text-white;
  }
  
  .section-padding {
    @apply py-section px-4;
  }
  
  .container-content {
    @apply mx-auto max-w-content;
  }
  
  .container-wide {
    @apply mx-auto max-w-wide;
  }
}
```

### 11.5 postcss.config.js

```js
module.exports = {
  plugins: {
    tailwindcss: {},
    autoprefixer: {},
  },
};
```

### 11.6 Tailwind Configuration

**`tailwind.config.js`:**

```js
module.exports = {
  content: [
    './blocks/**/*.php',
    './templates/**/*.html',
    './parts/**/*.html',
    './patterns/**/*.php',
    './*.php',
  ],
  theme: {
    extend: {
      colors: {
        primary: '#1a1a2e',
        secondary: '#c97b3a',
        accent: '#d4946a',
        background: '#faf8f5',
        surface: '#f5f0eb',
        muted: '#6b6b7d',
      },
      fontFamily: {
        heading: ['Chronicle Display', 'Georgia', 'serif'],
        body: ['Inter', '-apple-system', 'BlinkMacSystemFont', 'sans-serif'],
      },
      fontSize: {
        'display': ['4.5rem', { lineHeight: '1.1' }],
        'hero': ['3.5rem', { lineHeight: '1.15' }],
        'h1': ['2.5rem', { lineHeight: '1.2' }],
        'h2': ['2rem', { lineHeight: '1.25' }],
        'h3': ['1.5rem', { lineHeight: '1.3' }],
        'body': ['1rem', { lineHeight: '1.7' }],
        'large': ['1.25rem', { lineHeight: '1.6' }],
        'small': ['0.875rem', { lineHeight: '1.5' }],
      },
      spacing: {
        'section': '6rem',
        'section-sm': '3rem',
        'section-lg': '8rem',
      },
      maxWidth: {
        'content': '720px',
        'wide': '1140px',
      },
    },
  },
  plugins: [],
};
```

> **Note:** `tailwind.config.js` is only needed for the developer build workflow. It is excluded from the release ZIP. The pre-compiled `assets/css/style.css` already contains all the utility classes the theme uses.

### 11.7 Block-Specific CSS Example

**`blocks/hero-block/hero-block.css`:**

```css
.hero-block {
  padding: 6rem 1.5rem;
  background-color: var(--wp--preset--color--background);
}

.hero-block--default {
  background: linear-gradient(135deg, var(--wp--preset--color--background) 0%, var(--wp--preset--color--surface) 100%);
}

.hero-block__inner {
  max-width: var(--wp--preset--max-width--wide);
  margin: 0 auto;
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 4rem;
  align-items: center;
}

.hero-block__headline {
  font-family: var(--wp--preset--font-family--heading);
  font-size: var(--wp--preset--font-size--5xl);
  line-height: 1.1;
  color: var(--wp--preset--color--primary);
  margin-bottom: 1rem;
}

.hero-block__subheadline {
  font-size: var(--wp--preset--font-size--xl);
  color: var(--wp--preset--color--secondary);
  margin-bottom: 1.5rem;
  font-weight: 500;
}

.hero-block__body {
  font-size: var(--wp--preset--font-size--large);
  color: var(--wp--preset--color--foreground);
  line-height: 1.7;
  margin-bottom: 2rem;
}

.hero-block__actions {
  display: flex;
  gap: 1rem;
  flex-wrap: wrap;
}

.hero-block__image img {
  width: 100%;
  height: auto;
  border-radius: 0.5rem;
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
}

/* Mobile */
@media (max-width: 768px) {
  .hero-block {
    padding: 3rem 1.25rem;
  }
  
  .hero-block__inner {
    grid-template-columns: 1fr;
    gap: 2rem;
  }
  
  .hero-block__headline {
    font-size: var(--wp--preset--font-size--3xl);
  }
  
  .hero-block__image {
    order: -1;
  }
}
```

### 11.8 Button Styles

```css
.btn {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.875rem 2rem;
  border-radius: 0.25rem;
  font-family: var(--wp--preset--font-family--body);
  font-size: 1rem;
  font-weight: 600;
  text-decoration: none;
  transition: all 0.2s ease;
  cursor: pointer;
  border: 2px solid transparent;
}

.btn--primary {
  background-color: var(--wp--preset--color--secondary);
  color: white;
  border-color: var(--wp--preset--color--secondary);
}

.btn--primary:hover {
  background-color: var(--wp--preset--color--accent);
  border-color: var(--wp--preset--color--accent);
  color: white;
}

.btn--secondary {
  background-color: transparent;
  color: var(--wp--preset--color--primary);
  border-color: var(--wp--preset--color--primary);
}

.btn--secondary:hover {
  background-color: var(--wp--preset--color--primary);
  color: white;
}

/* Arrow on CTA buttons */
.btn--arrow::after {
  content: "→";
  transition: transform 0.2s ease;
}

.btn--arrow:hover::after {
  transform: translateX(4px);
}
```

---

## 12. SEO Implementation

### 12.1 Yoast SEO Configuration

| Setting | Value |
|---------|-------|
| Title separator | "—" (em dash) |
| Homepage title | YonksTEAM — Jason & Tyler Younker |
| Homepage description | Jason & Tyler Younker help successful but burned-out CPAs and financial advisors get their lives back. |
| Post types | Post only (no pages in sitemap) |
| Taxonomies | Categories only |
| Breadcrumbs | Enabled |
| OpenGraph | Enabled |
| Twitter cards | Enabled (summary_large_image) |
| XML sitemap | Enabled |
| Indexing | All pages indexed, noindex on tag/author archives |

### 12.2 Structured Data

```json
{
  "@context": "https://schema.org",
  "@type": "Organization",
  "name": "YonksTEAM",
  "url": "https://yonks.team",
  "description": "Jason and Tyler Younker help successful but burned-out CPAs and financial advisors get their lives back.",
  "founders": [
    {
      "@type": "Person",
      "name": "Jason Younker",
      "alternateName": "yonks",
      "jobTitle": "Co-Founder, Certified Exit Planning Advisor (in training)"
    },
    {
      "@type": "Person",
      "name": "Tyler Younker",
      "alternateName": "MrsYonks",
      "jobTitle": "Co-Founder, FINRA Arbitrator"
    }
  ],
  "sameAs": [
    "https://linkedin.com/in/...",
    "https://twitter.com/..."
  ]
}
```

### 12.3 Per-Page SEO Metadata

| Page | Focus Keyphrase | Slug | Meta Description |
|------|----------------|------|-----------------|
| Home | burned-out advisor | — | You hate work you used to love. Jason & Tyler Younker help burned-out advisors get their lives back. Start the conversation. |
| For Advisors | advisor exit planning | /for-advisors | Rebuild your practice around your life or sell it to the clients who trust you. Exit to Client. |
| Exit to Client | sell practice to clients | /exit-to-client | Sell your financial advisory practice to your clients. They become owners instead of casualties. A better exit. |
| About | Jason and Tyler Younker | /about | The story of how two burned-out professionals escaped their careers and now help others do the same. |
| Newsletter | advisor newsletter | /newsletter | The Next Season. One newsletter about the life after the practice. Real stories, honest talk. |
| Contact | start the conversation | /contact | Ready to get your life back? Tell us about your practice. We'll respond with honesty. |

---

## 13. Forms & Conversion Tracking

### 13.1 Form Inventory

| Form | Location | Fields | Integration |
|------|----------|--------|-------------|
| Start the Conversation | `/contact` | Name, Email, Phone (optional), Textarea | Gravity Forms → FluentCRM |
| Newsletter Sign-up | `/newsletter` | Email | Gravity Forms → FluentCRM |
| Newsletter Sign-up (inline) | Footer | Email | Gravity Forms → FluentCRM |

### 13.2 Gravity Forms Configuration

**Start the Conversation Form:**
- Title: "Start the Conversation"
- Fields:
  - Name (First, Last) — required
  - Email — required, validation
  - Phone — optional
  - Textarea "Tell us about your practice and what's on your mind" — required, 500 char limit
- Confirmation: "Thanks for reaching out. We'll respond within 48 hours with honesty — not a sales pitch."
- Notification: Sent to hello@yonks.team
- Webhook: POST to FluentCRM API

**Newsletter Sign-up Form:**
- Title: "The Next Season"
- Fields:
  - Email — required
  - First Name — optional
- Confirmation: "You're in. Welcome to The Next Season."
- Webhook: POST to FluentCRM API

### 13.3 Analytics & Conversion Tracking

| Tool | Purpose | Setup |
|------|---------|-------|
| Google Analytics 4 | Traffic, behavior, conversions | GA4 property + GTM |
| Google Tag Manager | Tag management | GTM container |
| Gravity Forms + GA4 | Form conversion tracking | GTM event on form submission |
| Facebook Pixel (optional) | Retargeting | Pixel ID in GTM |

### 13.4 Gravity Forms + GA4 Tracking (via GTM)

```javascript
// Add to form confirmation or in theme's footer
document.addEventListener('gform_post_render', function(event) {
    if (event.detail.formId === 1) { // Contact form
        // Push to dataLayer
        window.dataLayer = window.dataLayer || [];
        window.dataLayer.push({
            'event': 'form_submission',
            'formType': 'start_the_conversation'
        });
    }
    
    if (event.detail.formId === 2) { // Newsletter
        window.dataLayer.push({
            'event': 'form_submission',
            'formType': 'newsletter_signup'
        });
    }
});
```

---

## 14. Performance Budget

### 14.1 Targets

| Metric | Target | Mobile Target |
|--------|--------|---------------|
| First Contentful Paint (FCP) | < 1.5s | < 2.0s |
| Largest Contentful Paint (LCP) | < 2.0s | < 2.5s |
| First Input Delay (FID) | < 50ms | < 100ms |
| Cumulative Layout Shift (CLS) | < 0.1 | < 0.1 |
| Total Blocking Time (TBT) | < 150ms | < 300ms |
| Page weight | < 500KB | < 1MB |
| HTTP requests | < 30 | < 30 |
| Google PageSpeed Score | > 90 | > 80 |

### 14.2 Performance Strategy

| Technique | Implementation | Impact |
|-----------|---------------|--------|
| Caching | WP Rocket page cache + Cloudflare | High |
| Image optimization | WebP format, srcset, lazy loading | High |
| Font loading | woff2 format, swap display, preload | Medium |
| CSS delivery | Critical CSS inline, defer non-critical | Medium |
| JS delivery | Defer non-critical JS, no render-blocking | Medium |
| CDN | Cloudflare | Medium |
| Server response | Nginx + PHP 8.2 + FastCGI cache | High |
| Database | Query optimization, indexing, no unnecessary queries | Medium |
| Plugin audit | Minimal plugins, lightweight alternatives | High |

### 14.3 Image Optimization Guidelines

| Image Type | Max Width | Format | Quality |
|-----------|-----------|--------|---------|
| Hero images | 1920px | WebP | 80% |
| Content images | 1140px | WebP | 80% |
| Team photos | 600px | WebP (JPEG fallback) | 85% |
| Thumbnails | 300px | WebP | 75% |
| Logo | 200px | SVG | — |

---

## 15. Launch Checklist

### 15.1 Pre-Launch Checklist

**Content:**
- [ ] All pages created with correct templates
- [ ] All content added via custom blocks per the content design document
- [ ] Blog posts: minimum 3 launch posts written
- [ ] Newsletter sign-up form live and tested
- [ ] Contact form live and tested
- [ ] All images uploaded, optimized, and WebP versions generated
- [ ] Logo and favicon uploaded

**Technical:**
- [ ] GitHub repository set up with correct structure
- [ ] Theme deployed to staging environment
- [ ] All custom blocks registered and working
- [ ] theme.json colors/typography match design spec
- [ ] Mobile responsive — test all pages at 375px, 768px, 1024px, 1440px
- [ ] Page templates render correctly
- [ ] Navigation menus set up
- [ ] 404 page styled
- [ ] SSL certificate installed and enforced
- [ ] Caching plugin configured and tested
- [ ] CDN connected (Cloudflare)

**SEO:**
- [ ] Yoast SEO configured
- [ ] XML sitemap generated and submitted to Google Search Console
- [ ] All pages have unique meta titles and descriptions
- [ ] Open Graph tags set for all pages
- [ ] Structured data (Organization + Person) added
- [ ] 301 redirects from old site URLs configured
- [ ] Google Analytics 4 installed via GTM
- [ ] Google Search Console set up

**Performance:**
- [ ] PageSpeed Insights score > 90 (desktop)
- [ ] PageSpeed Insights score > 80 (mobile)
- [ ] All images optimized
- [ ] CSS/JS minified
- [ ] Gzip compression enabled
- [ ] Lazy loading enabled for images
- [ ] WebP images serving correctly

**Security:**
- [ ] WP admin URL changed (WPS Hide Login)
- [ ] Login attempt limiting enabled
- [ ] SSL enforced
- [ ] Security headers set (HSTS, CSP, X-Frame-Options)
- [ ] Disable XML-RPC (if not needed)
- [ ] Disable file editing in admin
- [ ] Regular backups configured (UpdraftPlus)

**Forms & Tracking:**
- [ ] Contact form sends email notification
- [ ] Newsletter sign-up adds to FluentCRM list
- [ ] GA4 events firing on form submissions
- [ ] Form spam protection enabled (honeypot + reCAPTCHA)
- [ ] Email deliverability tested (FluentSmtp)

**Final:**
- [ ] Cross-browser testing (Chrome, Firefox, Safari, Edge)
- [ ] All links clickable and correct
- [ ] No broken images
- [ ] Favicon showing in browser tab
- [ ] Viewport meta tag correct
- [ ] DNS propagated and site accessible at https://yonks.team
- [ ] Old site redirects in place
- [ ] Team has admin access and can edit content

### 15.2 Post-Launch (First 30 Days)

- [ ] Monitor analytics for bounce rate, scroll depth, conversions
- [ ] Review form submissions — are people using "Start the Conversation"?
- [ ] Send first newsletter issue to initial subscribers
- [ ] Check for 404 errors in Search Console
- [ ] Monitor PageSpeed scores and Core Web Vitals
- [ ] Publish 2–3 blog posts
- [ ] Review and update this document based on launch learnings

---

## Appendix A: Quick-Start Setup

There are two ways to get started, depending on your role.

### Option A: One-Click Install (End User)

```
1. Go to the GitHub Releases page: https://github.com/yonksteam/yonksteam-wordpress/releases
2. Download yonksteam-X.X.X.zip (the clean release ZIP)
3. In WordPress admin: Appearance → Themes → Add New → Upload Theme
4. Choose the ZIP file → Install Now → Activate
5. ✅ Theme is live. All styles, templates, and custom blocks are ready.

For the full experience, also install:
   - Advanced Custom Fields Pro (required for custom blocks)
   - Gravity Forms (for contact/newsletter forms)
   - Yoast SEO (for metadata and sitemaps)
```

### Option B: Developer Workflow (Git + Build)

```bash
# Step 1: Clone & Install

git clone git@github.com:yonksteam/yonksteam-wordpress.git
cd yonksteam-wordpress

# Install PHP dependencies
composer install

# Install Node dependencies
npm install

# Build assets (compiles Tailwind → assets/css/style.css)
npm run build

# Set up .env file
cp .env.example .env
# Edit .env with your database credentials

# Run WordPress installer (or set up via browser)
# Visit https://yourdomain.com/wp-admin/install.php

# Step 2: Activate Theme & Plugins

wp theme activate yonksteam

wp plugin activate advanced-custom-fields-pro
wp plugin activate gravity-forms
wp plugin activate wordpress-seo
wp plugin activate fluent-crm
wp plugin activate wp-rocket
wp plugin activate disable-comments
wp plugin activate limit-login-attempts-reloaded
wp plugin activate wps-hide-login

# Step 3: Create Pages

wp post create --post_type=page --post_title="Home" --post_status=publish
wp post create --post_type=page --post_title="For Advisors" --post_status=publish
wp post create --post_type=page --post_title="Exit to Client" --post_status=publish
wp post create --post_type=page --post_title="About" --post_status=publish
wp post create --post_type=page --post_title="The Next Season" --post_status=publish
wp post create --post_type=page --post_title="Blog" --post_status=publish
wp post create --post_type=page --post_title="Start the Conversation" --post_status=publish

# Set front page and blog page
wp option update show_on_front 'page'
wp option update page_on_front 1
wp option update page_for_posts 7

# Step 4: Import ACF Field Groups

# ACF field groups are stored as JSON in /wp-content/themes/yonksteam/acf-json/
# ACF will auto-sync them when the JSON files are present
```

---

## Appendix B: File Reference

| File Path | Purpose |
|-----------|---------|
| `wp-content/themes/yonksteam/style.css` | Theme header |
| `wp-content/themes/yonksteam/theme.json` | Global styles, colors, typography |
| `wp-content/themes/yonksteam/functions.php` | Theme setup, block registration, patterns |
| `wp-content/themes/yonksteam/templates/home.html` | Homepage template |
| `wp-content/themes/yonksteam/templates/page-for-advisors.html` | For Advisors template |
| `wp-content/themes/yonksteam/templates/page-exit-to-client.html` | Exit to Client template |
| `wp-content/themes/yonksteam/templates/page-about.html` | About template |
| `wp-content/themes/yonksteam/templates/page-newsletter.html` | Newsletter template |
| `wp-content/themes/yonksteam/templates/page-contact.html` | Contact template |
| `wp-content/themes/yonksteam/parts/header.html` | Site header (block theme part) |
| `wp-content/themes/yonksteam/parts/footer.html` | Site footer (block theme part) |
| `wp-content/themes/yonksteam/blocks/hero-block/` | Hero block (ACF) |
| `wp-content/themes/yonksteam/blocks/recognition-block/` | Recognition block (ACF) |
| `wp-content/themes/yonksteam/blocks/empathy-story/` | Empathy story block (ACF) |
| `wp-content/themes/yonksteam/blocks/authority-block/` | Authority block (ACF) |
| `wp-content/themes/yonksteam/blocks/plan-steps/` | Plan steps block (ACF) |
| `wp-content/themes/yonksteam/blocks/success-failure-split/` | Success/failure split block (ACF) |
| `wp-content/themes/yonksteam/blocks/transformation-statement/` | Transformation statement block (ACF) |
| `wp-content/themes/yonksteam/blocks/two-paths/` | Two paths block (ACF) |
| `wp-content/themes/yonksteam/blocks/cta-section/` | CTA section block (ACF) |
| `wp-content/themes/yonksteam/patterns/` | Block patterns (reusable components) |
| `.github/workflows/deploy-production.yml` | GitHub Actions deploy workflow |
| `deploy.php` | Deployer configuration |
| `composer.json` | PHP dependencies |
| `package.json` | Node dependencies (Tailwind, build tools) |

---

**End of Implementation Guide**

---

*This document bridges the content design to WordPress technical reality. Every decision here serves the core goal: a burned-out advisor lands on the site, feels seen, and starts the conversation.*