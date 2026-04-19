<?php
$gridItems = (array)($galeri ?? []);
$brandName = trim((string)($desa_nama ?? '')) ?: (trim((string)($website_nama ?? '')) ?: 'Website');
$galDesc   = trim((string)($pengaturan['gallery_page_description'] ?? '')) ?: 'Dokumentasi kegiatan dan keindahan ' . $brandName;
$canonicalUrl = rtrim((string)BASE_URL, '/') . '/galeri';
$breadcrumbs = [
  ['@type'=>'ListItem','position'=>1,'name'=>'Beranda','item'=>rtrim((string)BASE_URL,'/')],
  ['@type'=>'ListItem','position'=>2,'name'=>'Galeri','item'=>$canonicalUrl],
];
$resolveGalImg = static function($path) {
    if (empty($path)) return '';
    if (preg_match('#^https?://#i',$path)) return $path;
    return upload_url('galeri/' . ltrim($path,'/'));
};

// Collect unique categories
$categories = ['Semua'];
foreach ($gridItems as $g) {
    $cat = trim((string)($g['kategori'] ?? ''));
    if ($cat !== '' && !in_array($cat, $categories)) $categories[] = $cat;
}
?>
<div class="galeri-layout">

  <!-- Page Hero -->
  <div class="page-hero">
    <div class="container page-hero-content">
      <nav class="breadcrumb-nav" aria-label="Breadcrumb">
        <a href="<?= base_url() ?>">Beranda</a>
        <span class="sep"><i class="bi bi-chevron-right"></i></span>
        <span class="current">Galeri</span>
      </nav>
      <h1 class="page-title">Galeri <?= htmlspecialchars($brandName) ?></h1>
      <p class="page-subtitle"><?= htmlspecialchars($galDesc) ?></p>
    </div>
  </div>

  <div class="section">
    <div class="container">
      <?php if (empty($gridItems)): ?>
      <div class="empty-state">
        <i class="bi bi-images"></i>
        <h4>Belum Ada Foto</h4>
        <p>Galeri foto belum tersedia.</p>
      </div>
      <?php else: ?>

      <!-- Category Filters -->
      <?php if (count($categories) > 2): ?>
      <div class="gallery-filters" data-reveal>
        <?php foreach ($categories as $cat): ?>
        <button class="filter-btn <?= $cat === 'Semua' ? 'active' : '' ?>" data-filter="<?= htmlspecialchars($cat) ?>">
          <?= htmlspecialchars($cat) ?>
        </button>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>

      <!-- Masonry Grid -->
      <div class="gallery-full-masonry" id="galleryGrid" data-stagger-diag>
        <?php foreach ($gridItems as $g): ?>
        <?php
          $imgUrl = $resolveGalImg((string)($g['gambar'] ?? ''));
          if ($imgUrl === '') continue;
          $gTitle = trim((string)($g['judul'] ?? ''));
          $gCat   = trim((string)($g['kategori'] ?? ''));
          $gSlug  = trim((string)($g['slug'] ?? ($g['id'] ?? '')));
        ?>
        <div class="gallery-full-item" data-category="<?= htmlspecialchars($gCat) ?>" data-lightbox-src="<?= htmlspecialchars($imgUrl) ?>" data-lightbox-alt="<?= htmlspecialchars($gTitle) ?>" tabindex="0" role="button" aria-label="Lihat foto <?= htmlspecialchars($gTitle) ?>">
          <img src="<?= htmlspecialchars($imgUrl) ?>" alt="<?= htmlspecialchars($gTitle) ?>" loading="lazy">
          <div class="gallery-full-overlay">
            <?php if ($gCat): ?><span class="gallery-item-cat"><?= htmlspecialchars($gCat) ?></span><?php endif; ?>
            <?php if ($gTitle): ?><span class="gallery-item-title"><?= htmlspecialchars($gTitle) ?></span><?php endif; ?>
          </div>
          <button class="gallery-zoom-btn" aria-hidden="true"><i class="bi bi-zoom-in"></i></button>
        </div>
        <?php endforeach; ?>
      </div>

      <!-- Pagination -->
      <?php if (!empty($pagination_html)): ?>
      <div class="pagination-wrap"><?= $pagination_html ?></div>
      <?php elseif (!empty($pager)): ?>
      <div class="pagination-wrap"><?= $pager ?></div>
      <?php endif; ?>

      <?php endif; ?>
    </div>
  </div>
</div>

