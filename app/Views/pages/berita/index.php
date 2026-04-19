<?php
$featured = $berita[0] ?? null;
$items    = array_slice($berita ?? [], 1);
$brandName = trim((string)($desa_nama ?? '')) ?: (trim((string)($website_nama ?? '')) ?: 'Website');
$newsTitle = trim((string)($pengaturan['news_page_title'] ?? '')) ?: 'Berita Gampong';
$newsDesc  = trim((string)($pengaturan['news_page_description'] ?? '')) ?: 'Ikuti kabar dan kegiatan terkini dari ' . $brandName;
$canonicalUrl = rtrim((string)BASE_URL, '/') . '/berita';
$breadcrumbs = [
  ['@type'=>'ListItem','position'=>1,'name'=>'Beranda','item'=>rtrim((string)BASE_URL,'/')],
  ['@type'=>'ListItem','position'=>2,'name'=>'Berita','item'=>$canonicalUrl],
];
$resolveThumb = static function($thumb) {
    if (empty($thumb)) return '';
    if (preg_match('#^https?://#i',$thumb)) return $thumb;
    return upload_url('artikel/' . ltrim($thumb,'/'));
};
?>
<div class="berita-layout">

  <!-- Page Hero -->
  <div class="page-hero">
    <div class="container page-hero-content">
      <nav class="breadcrumb-nav" aria-label="Breadcrumb">
        <a href="<?= base_url() ?>">Beranda</a>
        <span class="sep"><i class="bi bi-chevron-right"></i></span>
        <span class="current">Berita</span>
      </nav>
      <h1 class="page-title"><?= htmlspecialchars($newsTitle) ?></h1>
      <p class="page-subtitle"><?= htmlspecialchars($newsDesc) ?></p>
    </div>
  </div>

  <div class="section">
    <div class="container">
      <?php if (empty($berita)): ?>
      <div class="empty-state">
        <i class="bi bi-newspaper"></i>
        <h4>Belum Ada Berita</h4>
        <p>Belum ada berita yang dipublikasikan.</p>
      </div>
      <?php else: ?>

      <!-- Featured -->
      <?php if ($featured): ?>
      <?php $featImg = $resolveThumb((string)($featured['thumbnail'] ?? '')); ?>
      <a href="<?= base_url('/berita/' . ($featured['slug'] ?? '')) ?>" class="berita-featured" data-reveal="flip">
        <div class="berita-featured-img">
          <?php if ($featImg): ?>
          <img src="<?= htmlspecialchars($featImg) ?>" alt="<?= htmlspecialchars((string)($featured['judul'] ?? '')) ?>" loading="lazy">
          <?php else: ?>
          <div style="width:100%;height:100%;background:var(--grad-hero);display:flex;align-items:center;justify-content:center"><i class="bi bi-newspaper" style="font-size:3rem;color:rgba(255,255,255,0.25)"></i></div>
          <?php endif; ?>
        </div>
        <div class="berita-featured-body">
          <span class="berita-featured-badge featured-badge"><i class="bi bi-star-fill"></i> Artikel Utama</span>
          <?php if (!empty($featured['kategori'])): ?>
          <span class="berita-featured-badge" style="margin-left:.5rem"><?= htmlspecialchars((string)$featured['kategori']) ?></span>
          <?php endif; ?>
          <p class="berita-featured-title mt-2"><?= htmlspecialchars((string)($featured['judul'] ?? '')) ?></p>
          <p class="berita-featured-excerpt"><?= htmlspecialchars(strip_tags(mb_strimwidth((string)($featured['konten'] ?? ''), 0, 280, '...'))) ?></p>
          <div class="berita-meta">
            <span><i class="bi bi-calendar3"></i> <?= htmlspecialchars(date('d M Y', strtotime((string)($featured['created_at'] ?? 'now')))) ?></span>
            <?php if (!empty($featured['views'])): ?>
            <span><i class="bi bi-eye"></i> <?= number_format((int)$featured['views'],0,',','.') ?> baca</span>
            <?php endif; ?>
          </div>
        </div>
      </a>
      <?php endif; ?>

      <!-- Grid -->
      <?php if (!empty($items)): ?>
      <div class="berita-grid mt-4" data-stagger-diag>
        <?php foreach ($items as $item): ?>
        <?php $img = $resolveThumb((string)($item['thumbnail'] ?? '')); ?>
        <a href="<?= base_url('/berita/' . ($item['slug'] ?? '')) ?>" class="berita-card">
          <div class="berita-card-img">
            <?php if ($img): ?>
            <img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars((string)($item['judul'] ?? '')) ?>" loading="lazy">
            <?php else: ?>
            <div style="width:100%;height:100%;background:var(--color-bg-alt);display:flex;align-items:center;justify-content:center;aspect-ratio:16/9"><i class="bi bi-newspaper" style="font-size:2rem;color:var(--muted)"></i></div>
            <?php endif; ?>
          </div>
          <div class="berita-card-body">
            <?php if (!empty($item['kategori'])): ?><span class="berita-card-cat"><?= htmlspecialchars((string)$item['kategori']) ?></span><?php endif; ?>
            <p class="berita-card-title"><?= htmlspecialchars((string)($item['judul'] ?? '')) ?></p>
            <p class="berita-card-excerpt"><?= htmlspecialchars(strip_tags(mb_strimwidth((string)($item['konten'] ?? ''), 0, 120, '...'))) ?></p>
            <div class="berita-meta mt-2">
              <span><i class="bi bi-calendar3"></i> <?= htmlspecialchars(date('d M Y', strtotime((string)($item['created_at'] ?? 'now')))) ?></span>
              <?php if (!empty($item['views'])): ?><span><i class="bi bi-eye"></i> <?= number_format((int)$item['views'],0,',','.') ?></span><?php endif; ?>
            </div>
          </div>
        </a>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>

      <!-- Pagination -->
      <?php if (!empty($pagination_html)): ?>
      <div class="pagination-wrap"><?= $pagination_html ?></div>
      <?php elseif (!empty($pager)): ?>
      <div class="pagination-wrap"><nav><?= $pager ?></nav></div>
      <?php endif; ?>

      <?php endif; ?>
    </div>
  </div>
</div>
