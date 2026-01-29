# دليل العقار (Dalel) - Property Guide
## Complete Project Documentation for Dynamic Development

---

## 📋 Table of Contents

1. [Project Overview](#project-overview)
2. [File Structure](#file-structure)
3. [Design System](#design-system)
4. [Pages Overview](#pages-overview)
5. [Components Documentation](#components-documentation)
6. [JavaScript Modules](#javascript-modules)
7. [Making It Dynamic](#making-it-dynamic)
8. [Database Schema](#database-schema)
9. [API Endpoints](#api-endpoints)
10. [Authentication Flow](#authentication-flow)
11. [Development Guidelines](#development-guidelines)

---

## 🎯 Project Overview

**Dalel (دليل العقار)** is a Kuwait-focused real estate platform that allows users to:
- Browse property listings (apartments, houses, land, buildings, chalets, commercial)
- Filter by type, location, price, and purpose (rent/sale/exchange)
- View detailed property pages
- Contact property owners/agents
- Post new listings
- Browse real estate agents/offices

### Tech Stack (Current - Static)
- **HTML5** - Semantic markup with RTL support
- **CSS3** - Custom properties (variables), Flexbox, Grid
- **Vanilla JavaScript** - No frameworks
- **LocalStorage** - For temporary data persistence

### Language & Direction
- **Primary Language:** Arabic (ar)
- **Text Direction:** RTL (Right-to-Left)
- **All pages use:** `<html lang="ar" dir="rtl">`

---

## 📁 File Structure

```
dalel/
├── index.html                 # Root redirect to pages/index.html
├── PROJECT_DOCUMENTATION.md   # This file
│
├── pages/
│   ├── index.html            # Homepage - Hero search, featured listings
│   ├── listings.html         # Search results/listings page
│   ├── ad.html               # Single property detail page
│   ├── new-listing.html      # Multi-step form to add new listing
│   ├── login.html            # Phone number login
│   ├── otp.html              # OTP verification
│   ├── register.html         # User registration
│   ├── agents.html           # List of real estate agents/offices
│   ├── agent.html            # Single agent profile
│   ├── about.html            # About us page
│   ├── contact.html          # Contact us page
│   └── terms.html            # Terms and conditions
│
├── assets/
│   ├── css/
│   │   ├── tokens.css        # Design tokens (colors, spacing, typography)
│   │   ├── base.css          # Reset, body styles, utilities
│   │   ├── layout.css        # Container, grid, breakpoints
│   │   ├── components.css    # Reusable component styles
│   │   └── pages/
│   │       ├── home.css
│   │       ├── listings.css
│   │       ├── ad.css
│   │       ├── new-listing.css
│   │       ├── login.css
│   │       ├── otp.css
│   │       ├── agents.css
│   │       ├── agent.css
│   │       └── static.css    # About, contact, terms
│   │
│   ├── js/
│   │   ├── app.js            # Global initialization
│   │   ├── storage.js        # LocalStorage helpers
│   │   ├── components/
│   │   │   ├── drawer.js     # Mobile drawer menu
│   │   │   ├── cards.js      # Listing card renderer
│   │   │   └── filters.js    # Filter dropdowns
│   │   └── pages/
│   │       ├── home.js
│   │       ├── listings.js
│   │       ├── login.js
│   │       ├── otp.js
│   │       └── new-listing.js
│   │
│   └── img/
│       ├── logo.png          # Main logo
│       ├── favicon.png       # Browser favicon
│       ├── chracter.png      # Mascot character
│       ├── background.png    # Hero background
│       ├── kwt.png           # Kuwait flag
│       └── ad.png            # Placeholder property image
```

---

## 🎨 Design System

### Brand Colors

| Token | Value | Usage |
|-------|-------|-------|
| `--c-primary` | `#5ba3d0` | Primary buttons, links, accents |
| `--c-primary-hover` | `#4a93c0` | Primary hover state |
| `--c-primary-light` | `#e6f3fa` | Light backgrounds, selections |
| `--c-primary-dark` | `#1e4164` | Headlines, active states |

### Neutral Colors

| Token | Value | Usage |
|-------|-------|-------|
| `--c-bg` | `#f7f9fc` | Page background |
| `--c-bg-white` | `#ffffff` | Cards, inputs |
| `--c-border` | `#e2e6eb` | Borders |
| `--c-text` | `#1a2332` | Primary text |
| `--c-text-secondary` | `#5a6577` | Secondary text |
| `--c-muted` | `#8b95a5` | Muted/placeholder text |

### Semantic Colors

| Token | Value | Usage |
|-------|-------|-------|
| `--c-danger` | `#e74c3c` | Errors, featured tags |
| `--c-success` | `#25d366` | Success, WhatsApp |
| `--c-warning` | `#fbbf24` | Warnings, premium features |

### Spacing Scale

```css
--space-4: 4px;
--space-8: 8px;
--space-12: 12px;
--space-16: 16px;
--space-20: 20px;
--space-24: 24px;
--space-32: 32px;
--space-40: 40px;
--space-48: 48px;
--space-64: 64px;
```

### Border Radii

```css
--radius-sm: 8px;    /* Small elements */
--radius-md: 12px;   /* Buttons, inputs */
--radius-lg: 16px;   /* Cards */
--radius-xl: 20px;   /* Large cards */
--radius-full: 50%;  /* Circles */
```

### Typography

- **Font Family:** `"IBM Plex Sans Arabic"` (Arabic-first)
- **Fallbacks:** `-apple-system, BlinkMacSystemFont, Arial, sans-serif`

| Token | Size | Usage |
|-------|------|-------|
| `--font-size-xs` | 12px | Labels, badges |
| `--font-size-sm` | 14px | Secondary text |
| `--font-size-base` | 15px | Body text |
| `--font-size-lg` | 18px | Subheadings |
| `--font-size-xl` | 20px | Section titles |
| `--font-size-2xl` | 24px | Page titles |
| `--font-size-3xl` | 32px | Hero text |

### Responsive Breakpoints

```css
/* Mobile first approach */
@media (min-width: 641px)  { /* Tablet */ }
@media (min-width: 1025px) { /* Desktop */ }
```

---

## 📄 Pages Overview

### 1. Homepage (`pages/index.html`)

**Purpose:** Landing page with hero search and featured listings

**Sections:**
- Navbar with logo, links, language toggle
- Hero section with search form
  - Purpose tabs (للإيجار / للبيع / للبدل)
  - Location dropdown
  - Property type dropdown
  - Search button
- Featured listings grid
- Footer with logo, links, character

**Dynamic Data Needed:**
- Featured listings (latest or promoted)
- Location options
- Property type options

### 2. Listings Page (`pages/listings.html`)

**Purpose:** Search results with filters

**Sections:**
- Breadcrumb navigation
- Page title with result count
- Filter bar (purpose, type, price, location)
- Listing cards grid
- "Load more" button

**Dynamic Data Needed:**
- Filtered listings with pagination
- Active filter state
- Total count

### 3. Property Detail (`pages/ad.html`)

**Purpose:** Single listing full details

**Sections:**
- Image gallery/slider
- Title, price, meta info
- Description
- Contact buttons (call, WhatsApp)
- Related listings

**Dynamic Data Needed:**
- Property details
- Owner/agent contact info
- Related listings

### 4. New Listing (`pages/new-listing.html`)

**Purpose:** Multi-step form to post new listing

**Steps:**
1. Property type & purpose selection
2. Location, price, description, images
3. Contact info & publish

**Form Fields:**
- `propertyType`: apartment, house, land, building, chalet, commercial
- `purpose`: rent, sale, exchange
- `location`: Kuwait areas
- `price`: optional number
- `description`: max 400 chars
- `images`: up to 10 files
- `phone`: required
- `featured`: boolean (paid upgrade)

### 5. Login (`pages/login.html`)

**Purpose:** Phone number authentication

**Fields:**
- Country code (+965)
- Phone number

### 6. OTP (`pages/otp.html`)

**Purpose:** Verify phone with SMS code

**Features:**
- 4-digit OTP boxes
- Auto-focus next input
- Resend timer
- Paste support

### 7. Agents (`pages/agents.html`)

**Purpose:** List real estate offices/agents

**Dynamic Data Needed:**
- Agent profiles with:
  - Name, photo, office name
  - Listing count
  - Contact info

### 8. Agent Profile (`pages/agent.html`)

**Purpose:** Single agent with their listings

**Dynamic Data Needed:**
- Agent details
- Agent's listings

### 9. Static Pages

- `about.html` - Company info
- `contact.html` - Contact form, office address
- `terms.html` - Terms & conditions

---

## 🧩 Components Documentation

### Navbar (`.c-navbar`)

```html
<nav class="c-navbar">
  <div class="l-container c-navbar__inner">
    <a href="index.html" class="c-navbar__brand">
      <img src="../assets/img/logo.png" alt="دليل العقار" class="c-navbar__logo-img">
    </a>
    <div class="c-navbar__links">
      <a href="#" class="c-navbar__link">الرئيسية</a>
      <!-- More links -->
    </div>
    <button class="c-navbar__hamburger" id="openDrawer">
      <!-- Hamburger icon -->
    </button>
  </div>
</nav>
```

### Mobile Drawer (`.c-drawer`)

```html
<div class="c-drawer-overlay" id="drawerOverlay"></div>
<aside class="c-drawer" id="drawer">
  <div class="c-drawer__header">
    <img src="../assets/img/logo.png" alt="Logo">
    <button class="c-drawer__close" id="closeDrawer">×</button>
  </div>
  <nav class="c-drawer__nav">
    <a href="#" class="c-drawer__link">Link</a>
  </nav>
</aside>
```

### Listing Card (`.c-card`)

```html
<article class="c-card c-card--featured">
  <div class="c-card__image">
    <img src="..." alt="Property">
    <span class="c-card__tag">مميز</span>
  </div>
  <div class="c-card__content">
    <h3 class="c-card__title">بيت للبيع في صباح السالم</h3>
    <p class="c-card__desc">وصف قصير...</p>
    <div class="c-card__footer">
      <span class="c-card__price">150,000 د.ك</span>
      <span class="c-card__meta">6 ساعة</span>
    </div>
  </div>
</article>
```

### Button Variants

```html
<!-- Primary -->
<button class="c-btn c-btn--primary">بحث</button>

<!-- Outline -->
<button class="c-btn c-btn--outline">إلغاء</button>

<!-- Large -->
<button class="c-btn c-btn--primary c-btn--lg">نشر</button>

<!-- Full width -->
<button class="c-btn c-btn--primary c-btn--block">دخول</button>
```

### Form Inputs

```html
<!-- Text input -->
<input type="text" class="c-input" placeholder="...">

<!-- Select -->
<select class="c-select">
  <option value="">اختر...</option>
</select>

<!-- Textarea -->
<textarea class="c-textarea" maxlength="400"></textarea>
```

### Filter Chips

```html
<div class="c-chips">
  <button class="c-chip c-chip--active">للإيجار</button>
  <button class="c-chip">للبيع</button>
</div>
```

---

## 📜 JavaScript Modules

### Global (`app.js`)

Initializes global components on page load.

### Storage (`storage.js`)

```javascript
// Get with default
Storage.get('filters', { purpose: 'rent' })

// Set
Storage.set('filters', { purpose: 'sale', type: 'apartment' })

// Remove
Storage.remove('filters')

// Clear all
Storage.clear()
```

### Drawer (`components/drawer.js`)

Handles mobile menu open/close with overlay.

### Cards (`components/cards.js`)

```javascript
// Renders listing cards from data array
DalelCards.createCard(listing, isHorizontal)
DalelCards.renderGrid(container, listings)
```

### Filters (`components/filters.js`)

Manages filter state and UI updates.

---

## 🚀 Making It Dynamic

### Recommended Stack

| Layer | Technology | Why |
|-------|-----------|-----|
| **Frontend** | Next.js 14+ (App Router) | SSR, API routes, Arabic support |
| **Backend** | Node.js + Express OR Next.js API | Same language as frontend |
| **Database** | PostgreSQL + Prisma | Relational data, type safety |
| **Auth** | NextAuth.js + Twilio | Phone OTP authentication |
| **Storage** | AWS S3 / Cloudinary | Image uploads |
| **Search** | Meilisearch / Algolia | Fast Arabic search |
| **Hosting** | Vercel / Railway | Easy deployment |

### Alternative Stack (Simpler)

| Layer | Technology |
|-------|-----------|
| **Full Stack** | Supabase (Auth + DB + Storage) |
| **Frontend** | React / Vue + Vite |
| **Hosting** | Netlify + Supabase |

---

## 🗄️ Database Schema

### Users Table

```sql
CREATE TABLE users (
  id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
  phone VARCHAR(15) UNIQUE NOT NULL,
  name VARCHAR(100),
  email VARCHAR(255),
  avatar_url TEXT,
  role ENUM('user', 'agent', 'admin') DEFAULT 'user',
  is_verified BOOLEAN DEFAULT false,
  created_at TIMESTAMP DEFAULT NOW(),
  updated_at TIMESTAMP DEFAULT NOW()
);
```

### Listings Table

```sql
CREATE TABLE listings (
  id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
  user_id UUID REFERENCES users(id) ON DELETE CASCADE,
  
  -- Property Info
  title VARCHAR(200) NOT NULL,
  description TEXT,
  property_type ENUM('apartment', 'house', 'land', 'building', 'chalet', 'commercial') NOT NULL,
  purpose ENUM('rent', 'sale', 'exchange') NOT NULL,
  
  -- Location
  governorate VARCHAR(50) NOT NULL,  -- الجهراء، حولي، etc.
  area VARCHAR(100),                  -- صباح السالم، السالمية، etc.
  block VARCHAR(20),
  street VARCHAR(100),
  
  -- Pricing
  price DECIMAL(12, 2),
  price_type ENUM('fixed', 'negotiable', 'contact') DEFAULT 'fixed',
  
  -- Features
  bedrooms INTEGER,
  bathrooms INTEGER,
  area_sqm INTEGER,
  floor_number INTEGER,
  
  -- Status
  status ENUM('pending', 'active', 'sold', 'rented', 'expired', 'rejected') DEFAULT 'pending',
  is_featured BOOLEAN DEFAULT false,
  featured_until TIMESTAMP,
  
  -- Stats
  views INTEGER DEFAULT 0,
  
  -- Timestamps
  created_at TIMESTAMP DEFAULT NOW(),
  updated_at TIMESTAMP DEFAULT NOW(),
  expires_at TIMESTAMP
);

-- Index for search
CREATE INDEX idx_listings_search ON listings(property_type, purpose, governorate, status);
CREATE INDEX idx_listings_user ON listings(user_id);
```

### Listing Images Table

```sql
CREATE TABLE listing_images (
  id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
  listing_id UUID REFERENCES listings(id) ON DELETE CASCADE,
  url TEXT NOT NULL,
  thumbnail_url TEXT,
  sort_order INTEGER DEFAULT 0,
  created_at TIMESTAMP DEFAULT NOW()
);
```

### Agents Table

```sql
CREATE TABLE agents (
  id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
  user_id UUID REFERENCES users(id) ON DELETE CASCADE,
  
  office_name VARCHAR(200) NOT NULL,
  office_name_en VARCHAR(200),
  license_number VARCHAR(50),
  description TEXT,
  
  logo_url TEXT,
  cover_url TEXT,
  
  phone VARCHAR(15),
  whatsapp VARCHAR(15),
  email VARCHAR(255),
  website VARCHAR(255),
  
  address TEXT,
  governorate VARCHAR(50),
  
  is_verified BOOLEAN DEFAULT false,
  is_featured BOOLEAN DEFAULT false,
  
  created_at TIMESTAMP DEFAULT NOW(),
  updated_at TIMESTAMP DEFAULT NOW()
);
```

### Favorites Table

```sql
CREATE TABLE favorites (
  id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
  user_id UUID REFERENCES users(id) ON DELETE CASCADE,
  listing_id UUID REFERENCES listings(id) ON DELETE CASCADE,
  created_at TIMESTAMP DEFAULT NOW(),
  
  UNIQUE(user_id, listing_id)
);
```

### OTP Table

```sql
CREATE TABLE otp_codes (
  id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
  phone VARCHAR(15) NOT NULL,
  code VARCHAR(6) NOT NULL,
  attempts INTEGER DEFAULT 0,
  expires_at TIMESTAMP NOT NULL,
  verified_at TIMESTAMP,
  created_at TIMESTAMP DEFAULT NOW()
);
```

### Governorates (Locations)

```sql
CREATE TABLE governorates (
  id SERIAL PRIMARY KEY,
  name_ar VARCHAR(50) NOT NULL,
  name_en VARCHAR(50),
  sort_order INTEGER DEFAULT 0
);

CREATE TABLE areas (
  id SERIAL PRIMARY KEY,
  governorate_id INTEGER REFERENCES governorates(id),
  name_ar VARCHAR(100) NOT NULL,
  name_en VARCHAR(100),
  sort_order INTEGER DEFAULT 0
);

-- Seed Kuwait governorates
INSERT INTO governorates (name_ar, name_en, sort_order) VALUES
  ('العاصمة', 'Capital', 1),
  ('حولي', 'Hawalli', 2),
  ('الفروانية', 'Farwaniya', 3),
  ('مبارك الكبير', 'Mubarak Al-Kabeer', 4),
  ('الأحمدي', 'Ahmadi', 5),
  ('الجهراء', 'Jahra', 6);
```

---

## 🔌 API Endpoints

### Authentication

```
POST /api/auth/send-otp
  Body: { phone: "+96550123456" }
  Response: { success: true, expiresIn: 120 }

POST /api/auth/verify-otp
  Body: { phone: "+96550123456", code: "1234" }
  Response: { token: "jwt...", user: {...} }

POST /api/auth/logout
  Response: { success: true }

GET /api/auth/me
  Headers: { Authorization: "Bearer jwt..." }
  Response: { user: {...} }
```

### Listings

```
GET /api/listings
  Query: ?purpose=rent&type=apartment&governorate=hawalli&page=1&limit=20
  Response: { listings: [...], total: 150, page: 1, pages: 8 }

GET /api/listings/:id
  Response: { listing: {...}, related: [...] }

POST /api/listings
  Headers: { Authorization: "Bearer jwt..." }
  Body: FormData (title, description, images[], ...)
  Response: { listing: {...} }

PUT /api/listings/:id
  Headers: { Authorization: "Bearer jwt..." }
  Body: { title, description, ... }
  Response: { listing: {...} }

DELETE /api/listings/:id
  Headers: { Authorization: "Bearer jwt..." }
  Response: { success: true }

POST /api/listings/:id/view
  Response: { views: 124 }
```

### Agents

```
GET /api/agents
  Query: ?governorate=hawalli&page=1
  Response: { agents: [...], total: 50 }

GET /api/agents/:id
  Response: { agent: {...}, listings: [...] }
```

### User

```
GET /api/user/listings
  Headers: { Authorization: "Bearer jwt..." }
  Response: { listings: [...] }

GET /api/user/favorites
  Headers: { Authorization: "Bearer jwt..." }
  Response: { favorites: [...] }

POST /api/user/favorites/:listingId
  Headers: { Authorization: "Bearer jwt..." }
  Response: { success: true }

DELETE /api/user/favorites/:listingId
  Headers: { Authorization: "Bearer jwt..." }
  Response: { success: true }
```

### Locations

```
GET /api/locations/governorates
  Response: { governorates: [...] }

GET /api/locations/areas/:governorateId
  Response: { areas: [...] }
```

### Upload

```
POST /api/upload/image
  Headers: { Authorization: "Bearer jwt..." }
  Body: FormData (file)
  Response: { url: "https://...", thumbnail: "https://..." }
```

---

## 🔐 Authentication Flow

### Phone OTP Flow

```
1. User enters phone number
   └─> POST /api/auth/send-otp
   └─> SMS sent via Twilio/MSG91

2. User enters 4-digit code
   └─> POST /api/auth/verify-otp
   └─> If new user: create account
   └─> Return JWT token

3. Token stored in:
   └─> httpOnly cookie (recommended)
   └─> or localStorage (less secure)

4. Protected routes check:
   └─> JWT validity
   └─> User exists in DB
```

### JWT Payload

```json
{
  "sub": "user-uuid",
  "phone": "+96550123456",
  "role": "user",
  "iat": 1234567890,
  "exp": 1234654290
}
```

---

## 📝 Development Guidelines

### Code Style

1. **Arabic Content**
   - All UI text in Arabic
   - Use `lang="ar"` and `dir="rtl"`
   - Store Arabic in DB as UTF-8

2. **CSS**
   - Use design tokens (CSS variables)
   - BEM-like naming: `.c-component__element--modifier`
   - Mobile-first responsive

3. **JavaScript**
   - Use async/await for API calls
   - Handle errors gracefully
   - Show loading states

### Naming Conventions

| Type | Convention | Example |
|------|-----------|---------|
| CSS Class (Component) | `.c-componentName` | `.c-card` |
| CSS Class (Page) | `.p-pageName` | `.p-listings` |
| CSS Class (Layout) | `.l-layoutName` | `.l-container` |
| CSS Class (Utility) | `.u-utilityName` | `.u-hidden` |
| JS Module | `PascalCase` | `DalelCards` |
| DB Table | `snake_case` | `listing_images` |
| API Route | `kebab-case` | `/api/send-otp` |

### Performance Tips

1. **Images**
   - Use WebP format
   - Generate thumbnails (300x200)
   - Lazy load below fold

2. **Database**
   - Index search columns
   - Paginate all lists
   - Cache popular queries

3. **Frontend**
   - Code split by route
   - Preload critical assets
   - Use skeleton loaders

### Security Checklist

- [ ] Rate limit OTP requests (max 3/minute)
- [ ] Validate phone format (+965XXXXXXXX)
- [ ] Sanitize user input
- [ ] Validate file uploads (type, size)
- [ ] Use HTTPS only
- [ ] Set secure cookie flags
- [ ] CORS whitelist
- [ ] SQL injection prevention (use ORM)
- [ ] XSS prevention (escape HTML)

---

## 🎯 Priority Features for MVP

### Phase 1 (Core)
1. ✅ Phone authentication
2. ✅ Browse listings
3. ✅ View listing details
4. ✅ Post new listing
5. ✅ Basic search/filter

### Phase 2 (Engagement)
1. ⬜ User dashboard
2. ⬜ Favorites
3. ⬜ View history
4. ⬜ Push notifications
5. ⬜ Share listings

### Phase 3 (Monetization)
1. ⬜ Featured listings (paid)
2. ⬜ Agent subscriptions
3. ⬜ Promoted placements
4. ⬜ Analytics dashboard

### Phase 4 (Growth)
1. ⬜ Mobile app (React Native)
2. ⬜ Arabic SEO
3. ⬜ Social login
4. ⬜ Chat between users
5. ⬜ Map integration

---

## 🖼️ Asset Checklist

| Asset | Location | Dimensions |
|-------|----------|-----------|
| Logo | `assets/img/logo.png` | 200×80 px |
| Favicon | `assets/img/favicon.png` | 32×32 px |
| Character | `assets/img/chracter.png` | 300×400 px |
| Background | `assets/img/background.png` | 1920×600 px |
| Kuwait Flag | `assets/img/kwt.png` | 40×28 px |
| Placeholder | `assets/img/ad.png` | 800×600 px |

---

## 🤝 Handoff Notes

### What's Done
- Complete static HTML/CSS for all pages
- Responsive design (mobile, tablet, desktop)
- RTL Arabic layout
- Design system with tokens
- Basic JS interactions (drawer, tabs, forms)
- Multi-step listing form

### What's Needed
- Backend API implementation
- Database setup
- Authentication system
- Image upload service
- Search functionality
- Admin panel (optional)

### Quick Start for New Developer

```bash
# 1. Review the static pages
cd dalel
open pages/index.html

# 2. Study the design system
open assets/css/tokens.css

# 3. Check component patterns
open assets/css/components.css

# 4. Start building API
# Recommended: Next.js with App Router
npx create-next-app@latest dalel-app --typescript

# 5. Copy assets folder
cp -r assets dalel-app/public/

# 6. Convert HTML pages to React components
# Start with pages/index.html → app/page.tsx
```

---

**Good luck building! 🚀**

للتواصل: [Add your contact]

