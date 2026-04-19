<?php
$requestUri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$canonicalUrl = $canonicalUrl ?? (rtrim((string) BASE_URL, '/') . $requestUri);
$breadcrumbs = (is_array($breadcrumbs ?? null) ? $breadcrumbs : []);
$profileMenuLabel = trim((string) ($pengaturan['menu_profile_label'] ?? '')) ?: 'Profil';
$storyMenuLabel = trim((string) ($pengaturan['menu_story_label'] ?? '')) ?: 'Sejarah';
$visionMenuLabel = trim((string) ($pengaturan['menu_vision_label'] ?? '')) ?: 'Visi & Misi';
$structureMenuLabel = trim((string) ($pengaturan['menu_structure_label'] ?? '')) ?: 'Struktur';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars((string) ($title ?? ($website_nama ?? 'Website'))) ?></title>
  <meta name="description" content="<?= htmlspecialchars((string) ($website_deskripsi ?? '')) ?>">
  <meta name="keywords" content="<?= htmlspecialchars((string) ($pengaturan['website_keywords'] ?? '')) ?>">
  <meta name="author" content="<?= htmlspecialchars((string) ($website_nama ?? '')) ?>">

  <?php
  $logoUrl = !empty($logoDesa ?? '') ? resolve_upload_url((string) $logoDesa) : '';
  $siteTitle = htmlspecialchars((string) ($og_title ?? ($title ?? ($website_nama ?? 'Website'))));
  $desaName = htmlspecialchars((string) ($desa_nama ?? ($website_nama ?? 'Website')));
  $metaDescription = htmlspecialchars((string) ($og_desc ?? ($website_deskripsi ?? '')));
  $ogImage = $og_image ?? $logoUrl;
  $ogType = $og_type ?? 'website';
  $ogUrl = (string) BASE_URL;
  $geoRegion = htmlspecialchars((string) ($pengaturan['geo_region'] ?? ''));
  $geoPlacename = htmlspecialchars((string) ($pengaturan['geo_placename'] ?? ($desa_nama ?? '')));
  $geoPosition = htmlspecialchars((string) ($pengaturan['geo_position'] ?? ''));
  $geoLatitude = htmlspecialchars((string) ($pengaturan['geo_latitude'] ?? ''));
  $geoLongitude = htmlspecialchars((string) ($pengaturan['geo_longitude'] ?? ''));
  ?>
  <meta property="og:type" content="<?= htmlspecialchars($ogType) ?>">
  <meta property="og:title" content="<?= $siteTitle ?>">
  <meta property="og:description" content="<?= $metaDescription ?>">
  <meta property="og:url" content="<?= htmlspecialchars($ogUrl) ?>">
  <meta property="og:site_name" content="<?= $desaName ?>">
  <meta property="og:image" content="<?= htmlspecialchars($ogImage) ?>">
  <meta property="og:locale" content="id_ID">
  <meta name="geo.region" content="<?= $geoRegion ?>">
  <meta name="geo.placename" content="<?= $geoPlacename ?>">
  <meta name="geo.position" content="<?= $geoPosition ?>">
  <meta name="ICBM" content="<?= $geoLatitude ?>, <?= $geoLongitude ?>">
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="<?= $siteTitle ?>">
  <meta name="twitter:description" content="<?= $metaDescription ?>">
  <meta name="twitter:image" content="<?= htmlspecialchars($ogImage) ?>">
  <meta name="theme-color" content="#1B3A6B">
  <meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large">
  <link rel="canonical" href="<?= htmlspecialchars((string) $canonicalUrl) ?>">
  <link rel="manifest" href="<?= base_url('/public/manifest.json') ?>">

  <?php
  $jsonOrgName = addslashes(htmlspecialchars((string) ($desa_nama ?? ($website_nama ?? 'Website'))));
  $jsonOrgUrl  = base_url();
  $jsonOrgLogo = addslashes(htmlspecialchars($logoUrl));
  ?>
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "GovernmentOrganization",
    "name": "<?= $jsonOrgName ?>",
    "url": "<?= $jsonOrgUrl ?>",
    "logo": "<?= $jsonOrgLogo ?>",
    "address": {
      "@type": "PostalAddress",
      "addressLocality": "Nisam",
      "addressRegion": "Aceh Utara",
      "addressCountry": "ID"
    }
  }
  </script>

  <?php if (!empty($breadcrumbs)): ?>
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "BreadcrumbList",
    "itemListElement": <?= json_encode($breadcrumbs, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>
  }
  </script>
  <?php endif; ?>

  <?php if ($logoUrl !== ''): ?>
  <link rel="shortcut icon" href="<?= htmlspecialchars($logoUrl) ?>" type="image/x-icon">
  <?php endif; ?>

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=EB+Garamond:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400;1,600;1,700&family=Lato:ital,wght@0,300;0,400;0,700;0,900;1,400&display=swap" rel="stylesheet">

  <!-- Bootstrap + Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

  <?php
  $isAuthPage = ($page ?? '') === 'auth-login';
  $cssFiles = $isAuthPage
    ? ['/public/css/base.css', '/public/css/auth.css', '/public/css/responsive.css']
    : [
        '/public/css/base.css',
        '/public/css/layout.css',
        '/public/css/animations.css',
        '/public/css/home.css',
        '/public/css/pages.css',
        '/public/css/responsive.css',
      ];
  $styleVersion = 0;
  foreach ($cssFiles as $f) {
    $mtime = @filemtime(dirname(__DIR__, 3) . $f) ?: 0;
    if ($mtime > $styleVersion) $styleVersion = $mtime;
  }
  if ($styleVersion === 0) $styleVersion = time();
  ?>
  <?php foreach ($cssFiles as $cssFile): ?>
  <link rel="stylesheet" href="<?= CSS_PATH . '/' . basename($cssFile) ?>?v=<?= $styleVersion ?>">
  <?php endforeach; ?>
</head>
<body data-page="<?= htmlspecialchars($page ?? '') ?>" class="<?= $isAuthPage ? 'auth-page-body' : '' ?>">

<?php if (!$isAuthPage): ?>

<a href="#main-content" class="skip-nav">Lewati ke konten utama</a>
<div id="scroll-progress" aria-hidden="true"></div>

<!-- Preloader -->
<div id="page-preloader" role="status" aria-label="Memuat halaman">
  <div class="preloader-inner">
    <div class="preloader-logo"><?= htmlspecialchars($desa_nama ?? $website_nama ?? 'Gampong') ?></div>
    <div class="preloader-tagline">Gampong Digital · Aceh Utara</div>
    <div class="spinner"></div>
  </div>
</div>

<?php $isProfilActive = str_starts_with($requestUri, '/profil'); ?>

<!-- Navigation -->
<nav class="site-nav" id="siteNav" role="navigation" aria-label="Navigasi utama">
  <div class="container nav-container">
    <a class="nav-brand" href="<?= base_url() ?>">
      <?php if ($logoUrl !== ''): ?>
      <img src="<?= htmlspecialchars($logoUrl) ?>" alt="Logo <?= htmlspecialchars($desa_nama ?? $website_nama ?? '') ?>" width="40" height="40" decoding="async" fetchpriority="high">
      <?php else: ?>
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 40 40" width="40" height="40" aria-hidden="true" style="flex-shrink:0">
        <rect width="40" height="40" rx="10" fill="rgba(255,255,255,0.15)"/>
        <rect x="6" y="22" width="6" height="12" rx="1" fill="#C9870C"/>
        <rect x="17" y="16" width="6" height="18" rx="1" fill="white"/>
        <rect x="28" y="22" width="6" height="12" rx="1" fill="#C9870C"/>
        <polygon points="20,5 8,22 32,22" fill="white" opacity="0.9"/>
        <rect x="18" y="28" width="4" height="6" rx="1" fill="#1B3A6B"/>
      </svg>
      <?php endif; ?>
      <span><?= htmlspecialchars($desa_nama ?? $website_nama ?? 'Website') ?></span>
    </a>

    <button class="nav-hamburger" id="navHamburger" type="button" aria-controls="siteNavMenu" aria-expanded="false" aria-label="Buka navigasi">
      <span></span><span></span><span></span>
    </button>

    <div class="nav-overlay" id="navOverlay" hidden></div>

    <div class="nav-menu-wrap" id="siteNavMenu">
      <div class="nav-drawer-head" aria-hidden="true">
        <span>Menu Navigasi</span>
        <button type="button" class="nav-drawer-close" id="navDrawerClose" aria-label="Tutup">
          <i class="bi bi-x-lg"></i>
        </button>
      </div>
      <ul class="nav-menu" role="list">
        <li><a class="nav-link <?= is_active('/') ?>" href="<?= base_url() ?>">Beranda</a></li>
        <li class="has-dropdown <?= $isProfilActive ? 'active' : '' ?>">
          <button class="nav-link nav-link-dropdown" type="button" aria-expanded="false">
            <?= htmlspecialchars($profileMenuLabel) ?> <i class="bi bi-chevron-down"></i>
          </button>
          <ul class="nav-submenu" role="list">
            <li><a href="<?= base_url('/profil') ?>"><?= htmlspecialchars($storyMenuLabel) ?></a></li>
            <li><a href="<?= base_url('/profil/visi-misi') ?>"><?= htmlspecialchars($visionMenuLabel) ?></a></li>
            <li><a href="<?= base_url('/profil/struktur-organisasi') ?>"><?= htmlspecialchars($structureMenuLabel) ?></a></li>
          </ul>
        </li>
        <li><a class="nav-link <?= is_active('/berita') ?>" href="<?= base_url('/berita') ?>">Berita</a></li>
        <li><a class="nav-link <?= is_active('/galeri') ?>" href="<?= base_url('/galeri') ?>">Galeri</a></li>
        <li><a class="nav-link <?= is_active('/kontak') ?>" href="<?= base_url('/kontak') ?>">Kontak</a></li>
        <li><a class="nav-link nav-link-login <?= is_active('/login') ?>" href="<?= base_url('/login') ?>"><i class="bi bi-person-fill"></i> Masuk</a></li>
      </ul>
    </div>
  </div>
</nav>

<?php endif; ?>

<!-- Main Content -->
<main id="main-content">
  <?= $content ?? '' ?>
</main>

<?php if (!$isAuthPage): ?>

<!-- Footer -->
<footer class="site-footer">
  <div class="footer-motif" aria-hidden="true"></div>
  <div class="container" style="position:relative;z-index:1">
    <div class="row g-4 gy-5">
      <div class="col-xl-4 col-lg-5 col-md-6">
        <div class="footer-brand">
          <h5>
            <?php if ($logoUrl !== ''): ?>
            <img src="<?= htmlspecialchars($logoUrl) ?>" alt="Logo" width="32" height="32" class="footer-brand-logo" loading="lazy">
            <?php else: ?>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" width="32" height="32" aria-hidden="true" style="flex-shrink:0">
              <rect width="32" height="32" rx="8" fill="rgba(255,255,255,0.12)"/>
              <rect x="5" y="18" width="5" height="9" rx="1" fill="#C9870C"/>
              <rect x="14" y="13" width="5" height="14" rx="1" fill="white"/>
              <rect x="23" y="18" width="5" height="9" rx="1" fill="#C9870C"/>
              <polygon points="16,4 6,18 27,18" fill="white" opacity="0.85"/>
              <rect x="15" y="22" width="3" height="5" rx="1" fill="#1B3A6B"/>
            </svg>
            <?php endif; ?>
            <?= htmlspecialchars($desa_nama ?? $website_nama ?? 'Gampong Muenye Pirak') ?>
          </h5>
          <p><?= htmlspecialchars((string) ($pengaturan['footer_brand_description'] ?? ($website_deskripsi ?? ''))) ?></p>
          <div class="footer-social">
            <?php if (!empty($social_facebook ?? '')): ?>
            <a href="<?= htmlspecialchars((string) $social_facebook) ?>" aria-label="Facebook" rel="noopener noreferrer" target="_blank"><i class="bi bi-facebook"></i></a>
            <?php endif; ?>
            <?php if (!empty($social_instagram ?? '')): ?>
            <a href="<?= htmlspecialchars((string) $social_instagram) ?>" aria-label="Instagram" rel="noopener noreferrer" target="_blank"><i class="bi bi-instagram"></i></a>
            <?php endif; ?>
            <?php if (!empty($social_youtube ?? '')): ?>
            <a href="<?= htmlspecialchars((string) $social_youtube) ?>" aria-label="YouTube" rel="noopener noreferrer" target="_blank"><i class="bi bi-youtube"></i></a>
            <?php endif; ?>
            <?php if (!empty($whatsapp_number ?? '')): ?>
            <a href="https://wa.me/<?= preg_replace('/\D/', '', $whatsapp_number) ?>" aria-label="WhatsApp" rel="noopener noreferrer" target="_blank"><i class="bi bi-whatsapp"></i></a>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <div class="col-xl-2 col-lg-3 col-md-6 col-6">
        <h6 class="footer-heading">Jelajahi</h6>
        <ul class="footer-links">
          <li><a href="<?= base_url() ?>"><i class="bi bi-chevron-right"></i>Beranda</a></li>
          <li><a href="<?= base_url('/profil') ?>"><i class="bi bi-chevron-right"></i><?= htmlspecialchars($profileMenuLabel) ?></a></li>
          <li><a href="<?= base_url('/berita') ?>"><i class="bi bi-chevron-right"></i>Berita</a></li>
          <li><a href="<?= base_url('/galeri') ?>"><i class="bi bi-chevron-right"></i>Galeri</a></li>
          <li><a href="<?= base_url('/kontak') ?>"><i class="bi bi-chevron-right"></i>Kontak</a></li>
        </ul>
      </div>

      <div class="col-xl-2 col-lg-4 col-md-6 col-6">
        <h6 class="footer-heading">Profil</h6>
        <ul class="footer-links">
          <li><a href="<?= base_url('/profil') ?>"><i class="bi bi-chevron-right"></i><?= htmlspecialchars($storyMenuLabel) ?></a></li>
          <li><a href="<?= base_url('/profil/visi-misi') ?>"><i class="bi bi-chevron-right"></i><?= htmlspecialchars($visionMenuLabel) ?></a></li>
          <li><a href="<?= base_url('/profil/struktur-organisasi') ?>"><i class="bi bi-chevron-right"></i><?= htmlspecialchars($structureMenuLabel) ?></a></li>
        </ul>
      </div>

      <div class="col-xl-4 col-lg-12">
        <h6 class="footer-heading">Hubungi Kami</h6>
        <div class="d-grid gap-1">
          <?php if (!empty($alamatDesa ?? $desa_alamat ?? '')): ?>
          <div class="footer-contact-item"><i class="bi bi-geo-alt-fill"></i><span><?= htmlspecialchars($alamatDesa ?? $desa_alamat ?? '') ?></span></div>
          <?php endif; ?>
          <?php if (!empty($teleponDesa ?? $desa_telepon ?? '')): ?>
          <div class="footer-contact-item"><i class="bi bi-telephone-fill"></i><span><?= htmlspecialchars($teleponDesa ?? $desa_telepon ?? '') ?></span></div>
          <?php endif; ?>
          <?php if (!empty($emailDesa ?? $desa_email ?? '')): ?>
          <div class="footer-contact-item"><i class="bi bi-envelope-fill"></i><span><?= htmlspecialchars($emailDesa ?? $desa_email ?? '') ?></span></div>
          <?php endif; ?>
          <?php if (!empty($whatsapp_number ?? '')): ?>
          <div class="footer-contact-item"><i class="bi bi-whatsapp"></i><span><?= htmlspecialchars($whatsapp_number) ?></span></div>
          <?php endif; ?>
          <?php if (!empty($pengaturan['office_hours_text'] ?? '')): ?>
          <div class="footer-contact-item"><i class="bi bi-clock-fill"></i><span><?= htmlspecialchars((string) $pengaturan['office_hours_text']) ?></span></div>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <div class="footer-bottom">
      <p>&copy; <?= date('Y') ?> <?= htmlspecialchars((string) ($website_nama ?? ($desa_nama ?? 'Website'))) ?>. Seluruh hak dilindungi.</p>
      <div class="footer-bottom-right">
        <?php if (!empty($pengaturan['footer_badge_text'] ?? '')): ?>
        <span class="footer-bottom-badge"><i class="bi bi-shield-check"></i> <?= htmlspecialchars((string) $pengaturan['footer_badge_text']) ?></span>
        <?php endif; ?>
        <?php if (!empty($pengaturan['privacy_policy_label'] ?? '')): ?>
        <a href="<?= htmlspecialchars((string) (!empty($pengaturan['privacy_policy_url'] ?? '') ? $pengaturan['privacy_policy_url'] : base_url('/kontak'))) ?>" class="footer-privacy-link"><?= htmlspecialchars((string) $pengaturan['privacy_policy_label']) ?></a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</footer>

<!-- Back to Top -->
<a href="#" class="back-to-top" id="back-to-top" aria-label="Kembali ke atas">
  <i class="bi bi-chevron-up"></i>
</a>

<!-- WhatsApp Float -->
<?php if (!empty($whatsapp_number ?? '')): ?>
<a href="https://wa.me/<?= preg_replace('/\D/', '', $whatsapp_number) ?>" class="float-wa" id="floatWa" aria-label="Hubungi via WhatsApp" target="_blank" rel="noopener noreferrer">
  <i class="bi bi-whatsapp"></i>
</a>
<?php endif; ?>

<!-- Image Lightbox -->
<dialog id="imageLightbox" class="image-lightbox" aria-label="Pratinjau gambar">
  <div class="image-lightbox-shell">
    <button type="button" class="image-lightbox-close" id="imageLightboxClose" aria-label="Tutup pratinjau">
      <i class="bi bi-x-lg"></i>
    </button>
    <figure class="image-lightbox-figure">
      <img id="imageLightboxImg" src="" alt="">
      <figcaption id="imageLightboxCaption" class="image-lightbox-caption" hidden></figcaption>
    </figure>
  </div>
</dialog>

<?php endif; ?>

<!-- Scripts -->
<script>window.BASE_URL = "<?= rtrim((string) BASE_URL, '/') ?>";</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" defer></script>
<?php
$mainJsVersion = @filemtime(dirname(__DIR__, 3) . '/public/js/main.js') ?: time();
$authJsVersion = @filemtime(dirname(__DIR__, 3) . '/public/js/auth-login.js') ?: time();
?>
<?php if (!$isAuthPage): ?>
  <script src="<?= JS_PATH ?>/main.js?v=<?= $mainJsVersion ?>" defer></script>
<?php else: ?>
  <script src="<?= JS_PATH ?>/auth-login.js?v=<?= $authJsVersion ?>" defer></script>
<?php endif; ?>

</body>
</html>
