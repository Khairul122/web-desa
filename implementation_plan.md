# UI/UX Overhaul – Gampong Munye Pirak

Rancangan perombakan menyeluruh tampilan dan pengalaman pengguna (UI/UX) website resmi **Gampong Munye Pirak, Kecamatan Nisam, Kabupaten Aceh Utara**. Overhaul ini mengganti desain lama berbasis Bootstrap generic + dark-mode toggle dengan sistem desain baru yang **fresh, modern, dinamis, light-mode only, full animasi,** dan **fully responsive** di semua perangkat.

---

## Kondisi Saat Ini (Baseline)

| Aspek | Kondisi Lama |
|---|---|
| Framework CSS | Bootstrap 5.3 + custom overrides |
| Typography | Inter + Playfair Display |
| Mode | Light/Dark toggle (auto by hour) |
| Animasi | AOS scroll-reveal (minimal) |
| Layout hero | Carousel gambar Bootstrap |
| CSS files | `base.css`, `home.css`, `pages.css`, `layout.css`, `responsive.css` |
| JS files | [main.js](file:///c:/laragon/www/website_desa/assets/js/main.js), [animations.js](file:///c:/laragon/www/website_desa/assets/js/animations.js), [three-scenes.js](file:///c:/laragon/www/website_desa/assets/js/three-scenes.js) |
| SEO | JSON-LD GovernmentOrganization, OG/Twitter cards |
| Views | Home (12 sections), Profil (3 sub), Berita, Galeri, Kontak, Auth |

---

## Visi Desain Baru

**"Aceh Digital Village"** — memadukan keindahan alam & budaya Aceh (hijau sawah, biru laut, motif geometris Aceh) dengan estetika web modern: glassmorphism halus, bento-grid, micro-animations berbasis CSS + GSAP/Intersection Observer, dan tipografi modern yang berwibawa namun ramah.

### Palet Warna Utama (Light Mode Only)

```css
/* Design Tokens - CSS Custom Properties */
--color-brand-primary:   #1A7A4A;  /* Hijau Aceh - Primer brand */
--color-brand-secondary: #0E5C8A;  /* Biru laut - Aksen */
--color-brand-accent:    #F5A623;  /* Emas/kuning - CTA & highlight */
--color-brand-earth:     #8B6914;  /* Coklat tanah - Dekoratif */

--color-bg-base:         #F8FAF9;  /* Off-white hangat */
--color-bg-surface:      #FFFFFF;  /* Kartu & panel */
--color-bg-muted:        #EEF3F0;  /* Section alternating */
--color-bg-hero:         #E8F5EE;  /* Hero section bg */

--color-text-primary:    #1A2B22;  /* Teks utama (sangat gelap hijau) */
--color-text-secondary:  #4A5C52;  /* Teks body */
--color-text-muted:      #7A8C82;  /* Placeholder & caption */
--color-text-on-dark:    #FFFFFF;  /* Teks di atas bg gelap */

--color-border:          #D4E3DB;  /* Border halus */
--color-glass-bg:        rgba(255,255,255,0.72);
--color-glass-border:    rgba(255,255,255,0.50);

/* Gradients */
--gradient-hero:         linear-gradient(135deg,#1A7A4A,#0E5C8A);
--gradient-accent:       linear-gradient(135deg,#F5A623,#E8860F);
--gradient-surface:      linear-gradient(180deg,#FFFFFF,#F8FAF9);
--gradient-card-hover:   linear-gradient(135deg,rgba(26,122,74,.06),rgba(14,92,138,.06));
```

### Tipografi

```css
/* Font Stack */
Primary:   'Plus Jakarta Sans', sans-serif  → headings & UI labels
Body:      'DM Sans', sans-serif            → body text, paragraphs
Mono:      'JetBrains Mono', monospace      → kode/statistik angka
Ornament:  'Lora', serif                    → pull-quotes, hero tagline

/* Scale (fluid via clamp) */
--text-xs:   clamp(0.7rem,   1.5vw, 0.75rem)
--text-sm:   clamp(0.8rem,   2vw,   0.875rem)
--text-base: clamp(0.9375rem,2.5vw, 1rem)
--text-lg:   clamp(1.0625rem,3vw,   1.125rem)
--text-xl:   clamp(1.125rem, 3.5vw, 1.25rem)
--text-2xl:  clamp(1.25rem,  4vw,   1.5rem)
--text-3xl:  clamp(1.5rem,   5vw,   2rem)
--text-4xl:  clamp(2rem,     6vw,   3rem)
--text-5xl:  clamp(2.5rem,   8vw,   4rem)
--text-hero: clamp(2.75rem,  9vw,   5.5rem)
```

### Animasi & Motion

| Jenis | Library | Penerapan |
|---|---|---|
| Scroll-reveal | Intersection Observer API (native) | Semua section masuk dengan fade+slide |
| Micro-interactions | CSS Transitions + `@keyframes` | Hover card, button, link underline |
| Counter animasi | Vanilla JS `requestAnimationFrame` | Statistik angka count-up |
| Hero parallax | CSS `transform: translateY()` on scroll | Hero bg & decoration elements |
| Preloader | CSS `@keyframes` | Spinner Aceh motif |
| Stagger animation | CSS `--delay` custom property | Grid item masuk berurutan |
| Page transition | CSS `opacity + translate` | Smooth page enter |
| Floating elements | CSS `animation: float` keyframe | Dekorasi geometris |

> [!IMPORTANT]
> **Semua animasi ditulis pure CSS + vanilla JS.** GSAP/ScrollTrigger TIDAK digunakan untuk menjaga bundle size tetap kecil. AOS.js diganti sepenuhnya dengan custom Intersection Observer.

---

## User Review Required

> [!WARNING]
> **Breaking Change – Penghapusan Dark Mode:** Toggle dark/light mode dan logika auto-theme berbasis jam WIB di [app.php](file:///c:/laragon/www/website_desa/app.php) layout akan **dihapus sepenuhnya**. Website akan sepenuhnya light-mode only. Pastikan tidak ada feature/admin yang bergantung pada `data-theme="dark"`.

> [!IMPORTANT]
> **Penggantian Font CDN:** Font `Inter + Playfair Display` (Google Fonts) diganti dengan `Plus Jakarta Sans + DM Sans`. Pastikan koneksi internet tersedia atau pertimbangkan self-hosting font.

> [!IMPORTANT]
> **AOS.js Dihapus:** Library AOS (unpkg) akan dihapus dari layout dan digantikan oleh custom Intersection Observer. Semua class `data-aos=""` di view lama akan dimigrasi ke class `[data-reveal]`.

---

## Proposed Changes

### 1. Design System & CSS Architecture

#### [MODIFY] [base.css](file:///c:/laragon/www/website_desa/public/css/base.css)

Tulis ulang total menjadi **CSS Custom Properties Design System**:
- Semua token warna, spacing, radius, shadow, z-index didefinisikan di `:root`
- Hapus semua dark-mode rules (`[data-theme="dark"] { ... }`)
- Tambah `@layer base, components, utilities` untuk cascade management
- Reset modern: `*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }`
- Smooth scroll: `html { scroll-behavior: smooth; scroll-padding-top: 80px; }`
- Body base: font `DM Sans`, background `--color-bg-base`, color `--color-text-primary`
- Utility classes: `.container-fluid-max` (max-width 1400px), `.section-pad` (py 80-120px)
- Define `@keyframes` global: `fadeInUp`, `fadeInLeft`, `fadeInRight`, `scaleIn`, `float`, `shimmer`

---

### 2. Layout & Komponen Global

#### [MODIFY] [app.php](file:///c:/laragon/www/website_desa/app/Views/layouts/app.php)

**Head Section:**
- Hapus logika `$_initTheme` dan `data-theme` attribute
- Ganti font link ke `Plus Jakarta Sans` + `DM Sans` + `Lora` via Google Fonts dengan `display=swap`
- Pertahankan & perkuat SEO: OG tags, Twitter Card, JSON-LD GovernmentOrganization, Geo tags tetap ada
- Tambah JSON-LD `BreadcrumbList` support (di-feed dari tiap page via `$breadcrumbs` variable)
- Tambah `<meta name="robots" content="index,follow">`
- Tambah `<link rel="canonical" href="...">` dari `$canonicalUrl` variable
- Hapus AOS CSS `<link href="https://unpkg.com/aos@2.3.1/dist/aos.css">`
- Tambah CSS loading order: `base.css → layout.css → components.css → home.css / pages.css → responsive.css`

**Navbar (redesign total):**
```html
<!-- Konsep baru: Floating Pill Navbar -->
<nav class="site-nav" id="siteNav" role="navigation" aria-label="Navigasi utama">
  <div class="nav-container">
    <a class="nav-brand" href="/">
      <img ...> <span><?= $desa_nama ?></span>
    </a>
    <ul class="nav-menu" role="list">
      <!-- menu items dengan data-active highlight -->
    </ul>
    <div class="nav-actions">
      <!-- hamburger mobile only -->
      <button class="nav-hamburger" ...>
        <span></span><span></span><span></span>
      </button>
    </div>
  </div>
</nav>
```
- Scroll behavior: navbar mulai sebagai **transparent overlay** di hero, berubah menjadi **frosted glass** (backdrop-filter) saat scroll > 80px
- Aktif halaman: highlight dengan underline gradient `--color-brand-primary`
- Mobile: **full-screen overlay drawer** dengan animasi slide-in dari kanan
- Dropdown Profil: animated dropdown dengan panah chevron rotate

**Footer (redesign total):**
- 4-kolom di desktop, 2 kolom di tablet, 1 kolom di mobile
- Tambah seksi "Jam Operasional" dengan ikon jam
- Illustrated divider (SVG wave motif Aceh) antara konten utama dan footer
- Social media icons dengan hover color fill animation
- Bottom bar: copyright + badge "Website Resmi" + link kebijakan privasi

**Tambah komponen baru:**
- `#scroll-progress`: progress bar hijau tipis di paling atas halaman (CSS-only)
- `#back-to-top`: tombol bulat dengan ikon panah, muncul smooth setelah scroll 400px
- `.float-wa`: WhatsApp float button (pertahankan, re-style)
- `#page-preloader`: preloader dengan animasi motif geometris Aceh

---

### 3. CSS Files Architecture

#### [MODIFY] [home.css](file:///c:/laragon/www/website_desa/public/css/home.css)

Tulis ulang total semua section home page:

| Section | Konsep Baru |
|---|---|
| **Hero** | Full-viewport with layered bg: SVG nature illustration + CSS mesh gradient overlay. Floating decoration circles. Animated text reveal character by character (CSS animation-delay). Pills "Kec. Nisam · Kab. Aceh Utara". CTA buttons: primary (gradient) + outline. |
| **Announcement** | Dismissible banner di atas hero, warna amber/kuning, slide-down animation |
| **Quick Actions** | 4 icon-card horizontal scroll on mobile, grid on desktop. Glass morphism cards dengan border-top gradient |
| **About/Cerita Gampong** | 2-column split: kiri foto dengan frame dekoratif + badge floating stats, kanan teks dengan pull-quote bergaya |
| **Services (Layanan)** | Bento Grid layout 2×2 di desktop, 1 kolom di mobile. Setiap card dengan icon berwarna, hover lift shadow |
| **Popular Services** | Horizontal scroll chip/pill buttons di mobile, grid di desktop |
| **Stats** | Full-width section bg gradient hijau gelap, 4 stat counter card transparan, angka count-up animation |
| **Vision & Mission** | Left: visi dalam blockquote bergaya, Right: list misi dengan animated check-icon |
| **Berita** | 3-card masonry-style. Card pertama hero size, 2 card sisanya compact. Hover image scale |
| **Galeri** | CSS Masonry grid (column-count). Hover overlay dengan judul. Lightbox tetap ada |
| **Map** | Rounded iframe dengan shadow card, info address di sebelahnya |
| **CTA** | Full-width gradient banner dengan pattern geometric Aceh, dua tombol aksi |

#### [MODIFY] [pages.css](file:///c:/laragon/www/website_desa/public/css/pages.css)

Styling untuk halaman interior:
- **Page Hero Banner:** setiap halaman interior (Profil, Berita, Galeri, Kontak) punya sub-hero banner 300–400px dengan gradient + judul halaman + breadcrumb
- **Profil Gampong:** layout majalah — foto full-width, teks 2 kolom
- **Visi-Misi:** timeline vertikal animated untuk misi, visi dalam frame besar bergaya
- **Struktur Organisasi:** custom org-chart CSS (bukan Bootstrap) dengan connector lines animasi
- **Berita List:** card grid 3 kolom desktop, 2 tablet, 1 mobile. Filter/search bar. Tag kategori berwarna
- **Detail Berita:** layout majalah — typografi artikel bersih, sidebar related posts, share buttons
- **Galeri:** filter kategori + masonry grid (CSS columns). Infinite scroll optional
- **Kontak:** 2 kolom — form modern kiri, info kontak + map kanan. Form validation animated

#### [MODIFY] [layout.css](file:///c:/laragon/www/website_desa/public/css/layout.css)

- Definisi grid system custom (tanpa Bootstrap grid dependency)
- `.bento-grid`, `.masonry-grid`, `.split-layout`, `.card-grid-3` utility classes
- Navbar dan footer styles (dipindah dari base)
- Section wrapper: `.section`, `.section--alt`, `.section--dark`

#### [MODIFY] [responsive.css](file:///c:/laragon/www/website_desa/public/css/responsive.css)

Breakpoints Mobile-First:

```css
/* Mobile first (default): max-width 479px (iPhone SE, small Android) */
/* @media (min-width: 480px)  — large phones (iPhone 14, Pixel) */
/* @media (min-width: 600px)  — phablet */
/* @media (min-width: 768px)  — tablet portrait (iPad, Android tablet) */
/* @media (min-width: 1024px) — tablet landscape / small laptop */
/* @media (min-width: 1280px) — desktop */
/* @media (min-width: 1440px) — wide desktop */
/* @media (min-width: 1920px) — 2K / 4K screens */
```

Semua komponen harus teruji di:
- 📱 iPhone SE (375px)
- 📱 iPhone 14 Pro (393px)
- 📱 Samsung Galaxy A Series (360px–414px)
- 📱 Android tablet 7" (600px)
- 📱 iPad (768px), iPad Pro (1024px)
- 💻 Laptop 13" (1280px)
- 🖥️ Desktop 1440px, 1920px

---

### 4. New CSS File

#### [NEW] [animations.css](file:///c:/laragon/www/website_desa/public/css/animations.css)

File baru khusus animasi:

```css
/* === Keyframes === */
@keyframes fadeInUp { from{opacity:0;transform:translateY(30px)} to{opacity:1;transform:none} }
@keyframes fadeInLeft { from{opacity:0;transform:translateX(-30px)} to{opacity:1;transform:none} }
@keyframes fadeInRight { from{opacity:0;transform:translateX(30px)} to{opacity:1;transform:none} }
@keyframes scaleIn { from{opacity:0;transform:scale(.85)} to{opacity:1;transform:none} }
@keyframes float { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-14px)} }
@keyframes shimmer { from{background-position:-200% 0} to{background-position:200% 0} }
@keyframes countUp { from{opacity:0;transform:translateY(10px)} to{opacity:1;transform:none} }
@keyframes navDrawerIn { from{transform:translateX(100%)} to{transform:translateX(0)} }
@keyframes preloaderSpin { to{transform:rotate(360deg)} }
@keyframes barProgress { from{width:0} to{width:var(--progress,0%)} }

/* === Reveal System (replaces AOS) === */
[data-reveal] { opacity: 0; transform: translateY(24px); transition: opacity .6s ease, transform .6s ease; }
[data-reveal].is-visible { opacity: 1; transform: none; }
[data-reveal="left"] { transform: translateX(-24px); }
[data-reveal="right"] { transform: translateX(24px); }
[data-reveal="scale"] { transform: scale(.9); }
[data-reveal].is-visible { transform: none !important; }

/* === Stagger dari parent === */
[data-stagger] > * { --stagger-delay: 0ms; opacity:0; transform:translateY(20px);
  transition: opacity .55s ease var(--stagger-delay), transform .55s ease var(--stagger-delay); }
[data-stagger].is-visible > *:nth-child(1) { --stagger-delay:0ms }
[data-stagger].is-visible > *:nth-child(2) { --stagger-delay:80ms }
[data-stagger].is-visible > *:nth-child(3) { --stagger-delay:160ms }
[data-stagger].is-visible > *:nth-child(4) { --stagger-delay:240ms }
[data-stagger].is-visible > * { opacity:1; transform:none; }

/* === Respek prefers-reduced-motion === */
@media (prefers-reduced-motion: reduce) {
  [data-reveal], [data-stagger] > *, * { animation: none !important; transition-duration: 0.01ms !important; }
}
```

---

### 5. JavaScript Overhaul

#### [MODIFY] [main.js](file:///c:/laragon/www/website_desa/public/js/main.js)

Modular vanilla JS (ES6 modules — bundled manual, tidak pakai bundler):

```
main.js (entry point)
  ├── initNavbar()         — scroll behavior, hamburger, active-link
  ├── initReveal()         — Intersection Observer untuk [data-reveal] & [data-stagger]
  ├── initCounters()       — count-up animation untuk .stat-number
  ├── initPreloader()      — hide preloader setelah load
  ├── initScrollProgress() — update --progress CSS var pada #scroll-progress bar
  ├── initHeroParallax()   — parallax on mousemove/scroll untuk hero decoration
  ├── initLightbox()       — native <dialog> lightbox untuk galeri
  ├── initNewsSlider()     — drag-scroll horizontal untuk berita mobile
  └── initBackToTop()      — show/hide + smooth scroll
```

Hapus:
- Semua logika dark-mode toggle
- Dependency pada AOS.js
- [three-scenes.js](file:///c:/laragon/www/website_desa/assets/js/three-scenes.js) (Three.js terlalu berat, ganti dengan CSS/SVG animations)
- [animations.js](file:///c:/laragon/www/website_desa/assets/js/animations.js) lama (diganti ke CSS `animations.css`)

---

### 6. Home Page Sections (Views)

#### [MODIFY] Seluruh file di `/pages/home/sections/`

**[hero.php](file:///c:/laragon/www/website_desa/app/Views/pages/home/sections/hero.php)** — Redesign total:
```html
<section class="hero" id="hero" aria-label="Selamat datang di <?= $brandName ?>">
  <!-- Layered bg: gradient mesh + SVG nature motif floating -->
  <div class="hero__bg" aria-hidden="true">
    <div class="hero__mesh"></div>
    <div class="hero__decor hero__decor--circle1"></div>
    <div class="hero__decor hero__decor--circle2"></div>
    <div class="hero__leaves"></div> <!-- SVG inline daun sawit/padi Aceh -->
  </div>
  <div class="container hero__container">
    <div class="hero__content">
      <!-- Badge lokasi -->
      <div class="hero__badge" data-reveal>
        <i class="bi bi-geo-alt-fill"></i>
        <?= $heroLocationLine ?>
      </div>
      <!-- Judul utama: multi-line, per-kata animated -->
      <h1 class="hero__title" data-reveal="up">
        <span class="hero__title-brand"><?= $brandName ?></span>
        <span class="hero__title-tagline">Gampong Digital</span>
      </h1>
      <p class="hero__desc" data-reveal><?= $heroSummary ?></p>
      <div class="hero__cta" data-stagger>
        <a href="#tentang" class="btn-primary">Jelajahi Gampong</a>
        <a href="/kontak" class="btn-outline">Hubungi Kami</a>
      </div>
      <!-- Floating stat chips -->
      <div class="hero__stats" data-stagger>
        <div class="stat-chip"><?= $totalPenduduk ?> Jiwa</div>
        <div class="stat-chip"><?= $totalKk ?> KK</div>
        <div class="stat-chip">Berdiri <?= $establishedYear ?></div>
      </div>
    </div>
    <!-- Foto utama dengan frame dekoratif -->
    <div class="hero__visual" data-reveal="right">
      <div class="hero__image-frame">
        <img src="<?= $heroImageUrl ?>" alt="<?= $brandName ?>" ...>
        <div class="hero__image-badge">
          <i class="bi bi-patch-check-fill"></i> Resmi & Terpercaya
        </div>
      </div>
    </div>
  </div>
  <!-- Scroll indicator -->
  <div class="hero__scroll-hint" aria-hidden="true">
    <span>Gulir ke bawah</span>
    <div class="hero__scroll-line"></div>
  </div>
</section>
```

**[about.php](file:///c:/laragon/www/website_desa/app/Views/pages/home/sections/about.php)** — Split layout Cerita Gampong:
- Kiri: foto dengan overlay badge "Berdiri sejak {tahun}"
- Kanan: heading, deskripsi, bullet highlights, link ke /profil

**[services.php](file:///c:/laragon/www/website_desa/app/Views/pages/home/sections/services.php)** — Bento Grid:
- 4 card dengan ikon SVG/Bootstrap Icons berwarna
- Card [0]: 2-column span, warna bg berbeda
- Hover: scale shadow + warna bg subtle

**[stats.php](file:///c:/laragon/www/website_desa/app/Views/pages/home/sections/stats.php)** — Ambient dark section:
- Background: `--gradient-hero` (hijau gelap-biru)
- 4 angka besar dengan count-up, label, ikon
- Decorative SVG pattern background (motif geometris Aceh)

**[news.php](file:///c:/laragon/www/website_desa/app/Views/pages/home/sections/news.php)** — Asymmetric card layout:
- Featured card: gambar lebar di atas, judul besar
- 2 compact card: thumbnail kiri, teks kanan
- "Lihat semua berita →" link dengan underline animasi

**[gallery.php](file:///c:/laragon/www/website_desa/app/Views/pages/home/sections/gallery.php)** — CSS Masonry preview:
- 6 gambar dalam grid masonry (column-count: 3)
- Hover: overlay dengan judul + tombol buka
- Lightbox menggunakan `<dialog>` native

**[vision-mission.php](file:///c:/laragon/www/website_desa/app/Views/pages/home/sections/vision-mission.php)** — Elegant layout:
- Visi: dalam blockquote frame dengan ornamental border-left hijau tebal
- Misi: grid 2×2 dengan numbered bullets + ikon

**[cta.php](file:///c:/laragon/www/website_desa/app/Views/pages/home/sections/cta.php)** — Full-width impact section:
- Gradient bg + geometric pattern
- Judul besar + subjudul + 2 CTA button
- Illustrasi SVG sederhana (rumah Aceh/gampong)

---

### 7. Interior Pages (Views)

#### [MODIFY] [/pages/profil/index.php](file:///c:/laragon/www/website_desa/app/Views/pages/profil/index.php)
- Sub-hero banner dengan judul "Cerita Gampong" + breadcrumb
- Layout 2 kolom: kiri teks panjang (HTML dari DB), kanan sidebar info singkat
- Foto profil gampong dengan caption
- JSON-LD `Article` schema untuk konten profil

#### [MODIFY] [/pages/profil/visi-misi.php](file:///c:/laragon/www/website_desa/app/Views/pages/profil/visi-misi.php)
- Sub-hero banner
- Visi: card besar bergaya magazine
- Misi: animated list dengan stagger reveal
- JSON-LD `AboutPage` schema

#### [MODIFY] [/pages/profil/struktur-organisasi.php](file:///c:/laragon/www/website_desa/app/Views/pages/profil/struktur-organisasi.php)
- Sub-hero banner
- Org chart custom CSS: Kepala Gampong di puncak, branch ke bawah
- Setiap node: foto, nama, jabatan
- Mobile: collapses ke accordion list

#### [MODIFY] `/pages/berita/index.php` (diasumsikan ada)
- Sub-hero banner
- Grid 3 kolom (desktop), 2 (tablet), 1 (mobile)
- Tag kategori dengan warna pastel berbeda
- Pagination dengan style modern

#### [MODIFY] `/pages/galeri/index.php`
- Sub-hero banner
- Filter tombol kategori (All / Kegiatan / Alam / Infrastruktur)
- CSS Masonry / Grid dengan JS filter
- Lightbox `<dialog>`

#### [MODIFY] `/pages/kontak/index.php`
- Sub-hero banner
- 2 kolom: form (kiri) + info kontak (kanan)
- Form fields: validasi HTML5 + CSS animated label (floating label pattern)
- Info kontak: icon + teks, jam operasional, map embed
- JSON-LD `ContactPage` + `LocalBusiness` schema

---

### 8. SEO Enhancements

#### [MODIFY] [app.php](file:///c:/laragon/www/website_desa/app/Views/layouts/app.php) — SEO Section

Tambahan dari baseline:

```php
// Canonical URL (cegah duplicate content)
$canonicalUrl = rtrim(BASE_URL, '/') . ($requestUri ?? parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH));
```

```html
<!-- Canonical -->
<link rel="canonical" href="<?= htmlspecialchars($canonicalUrl) ?>">

<!-- Meta tambahan -->
<meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large">
<meta name="theme-color" content="#1A7A4A">

<!-- Structured data BreadcrumbList (dynamic per halaman) -->
<?php if (!empty($breadcrumbs)): ?>
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "BreadcrumbList",
  "itemListElement": <?= json_encode($breadcrumbs, JSON_UNESCAPED_UNICODE) ?>
}
</script>
<?php endif; ?>
```

SEO per halaman yang diperkuat:
| Halaman | Schema | Perubahan |
|---|---|---|
| Home | `GovernmentOrganization` + `WebSite` (SitelinksSearchBox) | Tambah `WebSite` schema |
| Profil | `AboutPage` | Baru |
| Visi-Misi | `AboutPage` | Baru |
| Struktur | `AboutPage` | Baru |
| Berita (list) | `ItemList` of `NewsArticle` | Baru |
| Berita (detail) | `NewsArticle` (author, datePublished, image) | Baru |
| Galeri | `ImageGallery` | Baru |
| Kontak | `ContactPage` + `LocalBusiness` | Baru |

**Open Graph Image:**
- Semua halaman merujuk ke gambar OG yang relevan via `$ogImage` variable

**Performance SEO:**
- Semua gambar pakai `loading="lazy"` kecuali above-the-fold
- Hero image pakai `fetchpriority="high"` + `<link rel="preload">`
- Font menggunakan `display=swap`
- CSS & JS non-critical dimuat dengan `defer`

---

### 9. Responsiveness Detail

#### Strategi Mobile-First

```
Mobile (≤479px): 1 kolom, font lebih kecil, navbar drawer full-screen
Phablet (480-599px): 1-2 kolom, horizontal scroll untuk chips
Tablet P (600-767px): 2 kolom, tablet-friendly tap targets
Tablet L (768-1023px): 2-3 kolom, nav masih hamburger
Desktop S (1024-1279px): layout penuh, nav tanpa hamburger
Desktop M (1280-1439px): layout optimal
Desktop L (1440+): max-width container, lebih banyak whitespace
```

#### Touch UX:
- Semua tombol min 44×44px touch target
- Swipe gesture untuk galeri mobile (via pointer events)
- Tap highlight dihapus (`-webkit-tap-highlight-color: transparent`)
- Dropdown menu: tap-to-open di mobile, hover di desktop

---

### 10. Auth Pages

#### [MODIFY] `/pages/auth/` (login.php, dll)

- `auth.css` sudah terpisah, pertahankan & refresh:
  - Hapus dark mode styles
  - Light mode: white/glass card di atas hero gradient hijau
  - Animasi masuk: `scaleIn` + `fadeIn` card

---

## Verification Plan

### Automated Tests

Tidak ada automated test suite yang ada di project ini. Verification dilakukan manual via browser.

### Manual Verification – Langkah Per Langkah

#### A. Local Server Setup
```
1. Pastikan Laragon berjalan (Apache + PHP)
2. Buka http://website_desa.test atau http://localhost/website_desa
```

#### B. Verifikasi Visual Per Halaman

| # | URL | Yang Diperiksa |
|---|---|---|
| 1 | `/` | Hero animasi, stat counter, semua section tampil benar |
| 2 | `/profil` | Sub-hero, layout 2 kolom, foto profil gampong |
| 3 | `/profil/visi-misi` | Visi card, misi list animasi |
| 4 | `/profil/struktur-organisasi` | Org chart, foto, nama jabatan |
| 5 | `/berita` | Grid berita, tag, pagination |
| 6 | `/galeri` | Masonry grid, lightbox buka/tutup |
| 7 | `/kontak` | Form, info kontak, peta muncul |

#### C. Responsiveness Test

Gunakan DevTools Chrome/Firefox → Toggle Device Toolbar:
1. iPhone SE (375×667) — Mobile portrait
2. iPhone 14 Pro (393×852) — Mobile portrait
3. Samsung Galaxy A52 (412×915) — Mobile portrait  
4. iPad (768×1024) — Tablet portrait
5. iPad Pro (1024×1366) — Tablet landscape
6. Desktop 1280×800
7. Desktop 1440×900

**Yang diperiksa setiap device:**
- [ ] Navbar hamburger muncul di mobile/tablet, hidden di desktop
- [ ] Drawer menu overlay berfungsi dan bisa ditutup
- [ ] Semua gambar tidak overflow/terpotong
- [ ] Tombol CTA dapat di-tap (tidak terlalu kecil)
- [ ] Grid melipat dengan benar per breakpoint
- [ ] Hero text tidak overflow ke luar viewport
- [ ] Footer 4-col (desktop) → 2-col (tablet) → 1-col (mobile)

#### D. Animasi Test

Di halaman Home, scroll dari atas ke bawah:
- [ ] Hero title reveal on load
- [ ] Setiap section muncul saat di-scroll ke viewport
- [ ] Stat counter angka berputar saat section stats terlihat
- [ ] Navbar berubah menjadi frosted glass saat scroll > 80px
- [ ] Back-to-top button muncul saat scroll > 400px
- [ ] Hover card: lift shadow, bg color berubah halus

#### E. SEO Validation

1. Buka halaman Home → DevTools → View Page Source
   - [ ] Ada `<title>` yang benar
   - [ ] Ada `<meta name="description">`
   - [ ] Ada `<meta property="og:image">`
   - [ ] Ada `<link rel="canonical">`
   - [ ] Ada `<script type="application/ld+json">` (GovernmentOrganization)

2. Paste URL ke https://validator.schema.org/ → cek tidak ada error pada JSON-LD

3. Paste URL ke https://www.opengraph.xyz/ → cek OG image & title tampil benar

#### F. Accessibility Check

1. Buka DevTools → Lighthouse → Accessibility audit → Score ≥ 85
2. Cek semua gambar punya `alt` attribute
3. Cek heading hierarchy: satu `<h1>` per halaman
4. Cek form fields punya label

#### G. Performance Check

1. Chrome DevTools → Lighthouse → Performance
   - Target: LCP ≤ 2.5s, CLS ≤ 0.1, FID/INP ≤ 200ms
2. Pastikan tidak ada layout shift akibat font loading (font-display: swap)
3. Gambar hero pakai `fetchpriority="high"` dan tidak ada preload conflicts
