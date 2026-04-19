<?php
$item     = is_array($berita_item ?? null) ? $berita_item : [];
$related  = is_array($related_berita ?? null) ? array_values($related_berita) : [];
$title    = (string)($item['judul'] ?? 'Berita');
$thumb    = (string)($item['thumbnail'] ?? '');
$content  = (string)($item['konten'] ?? '');
$date     = (string)($item['created_at'] ?? 'now');
$category = (string)($item['kategori'] ?? 'Kabar');
$thumbUrl = $thumb !== '' ? upload_url('artikel/' . $thumb) : '';
$slug     = (string)($item['slug'] ?? '');
$views    = (int)($item['views'] ?? 0);
$pageUrl  = rtrim((string)BASE_URL, '/') . '/berita/' . $slug;

$og_title  = $title;
$og_desc   = strip_tags(mb_strimwidth($content, 0, 160, '...'));
$og_image  = $thumbUrl;
$og_type   = 'article';
$canonicalUrl = $pageUrl;
$breadcrumbs = [
  ['@type'=>'ListItem','position'=>1,'name'=>'Beranda','item'=>rtrim((string)BASE_URL,'/')],
  ['@type'=>'ListItem','position'=>2,'name'=>'Berita','item'=>rtrim((string)BASE_URL,'/'). '/berita'],
  ['@type'=>'ListItem','position'=>3,'name'=>$title,'item'=>$pageUrl],
];
$shareUrl  = urlencode($pageUrl);
$shareText = urlencode($title);
?>

<!-- Article Reading Progress Bar -->
<div class="article-progress-bar" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-label="Progress membaca"></div>

<div class="article-layout">

  <!-- Page Hero -->
  <div class="page-hero">
    <div class="container page-hero-content">
      <nav class="breadcrumb-nav" aria-label="Breadcrumb">
        <a href="<?= base_url() ?>">Beranda</a>
        <span class="sep"><i class="bi bi-chevron-right"></i></span>
        <a href="<?= base_url('/berita') ?>">Berita</a>
        <span class="sep"><i class="bi bi-chevron-right"></i></span>
        <span class="current"><?= htmlspecialchars(mb_strimwidth($title, 0, 45, '...')) ?></span>
      </nav>
      <h1 class="page-title"><?= htmlspecialchars($title) ?></h1>
      <div class="berita-meta mt-2">
        <?php if ($category): ?><span class="berita-card-cat" style="color:var(--accent)"><?= htmlspecialchars($category) ?></span><?php endif; ?>
        <span><i class="bi bi-calendar3"></i> <?= htmlspecialchars(date('d M Y', strtotime($date))) ?></span>
        <?php if ($views): ?><span><i class="bi bi-eye"></i> <?= number_format($views,0,',','.') ?> baca</span><?php endif; ?>
      </div>
    </div>
  </div>

  <div class="section">
    <div class="container">
      <div class="row g-5">
        <div class="col-lg-8">

          <?php if ($thumbUrl): ?>
          <img src="<?= htmlspecialchars($thumbUrl) ?>" alt="<?= htmlspecialchars($title) ?>" class="article-hero-img mb-4" loading="lazy">
          <?php endif; ?>

          <div class="article-card" data-reveal>
            <div class="article-header">
              <div class="article-header-meta">
                <?php if ($category): ?><span class="berita-featured-badge"><?= htmlspecialchars($category) ?></span><?php endif; ?>
                <span class="berita-meta"><i class="bi bi-calendar3"></i> <?= htmlspecialchars(date('d M Y', strtotime($date))) ?></span>
                <?php if ($views): ?><span class="berita-meta"><i class="bi bi-eye"></i> <?= number_format($views,0,',','.') ?></span><?php endif; ?>
              </div>
              <h2 class="article-title"><?= htmlspecialchars($title) ?></h2>
            </div>

            <!-- Share Bar Top -->
            <div class="share-bar">
              <span>Bagikan:</span>
              <a href="https://www.facebook.com/sharer/sharer.php?u=<?= $shareUrl ?>" target="_blank" rel="noopener" class="share-btn"><i class="bi bi-facebook"></i> Facebook</a>
              <a href="https://twitter.com/intent/tweet?url=<?= $shareUrl ?>&text=<?= $shareText ?>" target="_blank" rel="noopener" class="share-btn"><i class="bi bi-twitter-x"></i> X</a>
              <a href="https://wa.me/?text=<?= $shareText ?>%20<?= $shareUrl ?>" target="_blank" rel="noopener" class="share-btn"><i class="bi bi-whatsapp"></i> WA</a>
              <button class="share-btn" data-copy-url><i class="bi bi-link-45deg"></i> Salin</button>
            </div>

            <div class="article-content">
              <?= $content ?>
            </div>

            <!-- Share Bar Bottom -->
            <div class="share-bar">
              <span>Bagikan:</span>
              <a href="https://www.facebook.com/sharer/sharer.php?u=<?= $shareUrl ?>" target="_blank" rel="noopener" class="share-btn"><i class="bi bi-facebook"></i> Facebook</a>
              <a href="https://wa.me/?text=<?= $shareText ?>%20<?= $shareUrl ?>" target="_blank" rel="noopener" class="share-btn"><i class="bi bi-whatsapp"></i> WhatsApp</a>
              <button class="share-btn" data-copy-url><i class="bi bi-link-45deg"></i> Salin Link</button>
            </div>
          </div>

          <div class="mt-4">
            <a href="<?= base_url('/berita') ?>" class="btn btn-outline-custom"><i class="bi bi-arrow-left"></i> Kembali ke Berita</a>
          </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
          <?php if (!empty($related)): ?>
          <div class="article-sidebar-card" data-reveal="right">
            <div class="sidebar-header">
              <h6>Berita Lainnya</h6>
            </div>
            <div class="related-list">
              <?php foreach ($related as $rel): ?>
              <?php
                $relThumb = (string)($rel['thumbnail'] ?? '');
                $relImg   = $relThumb !== '' ? upload_url('artikel/' . $relThumb) : '';
              ?>
              <a href="<?= base_url('/berita/' . ($rel['slug'] ?? '')) ?>" class="related-item">
                <?php if ($relImg): ?>
                <img src="<?= htmlspecialchars($relImg) ?>" alt="" class="related-item-img" loading="lazy">
                <?php endif; ?>
                <div class="related-item-body">
                  <p class="related-item-title"><?= htmlspecialchars((string)($rel['judul'] ?? '')) ?></p>
                  <div class="related-item-meta">
                    <i class="bi bi-calendar3" style="color:var(--accent)"></i>
                    <?= htmlspecialchars(date('d M Y', strtotime((string)($rel['created_at'] ?? 'now')))) ?>
                  </div>
                </div>
              </a>
              <?php endforeach; ?>
            </div>
          </div>
          <?php endif; ?>

          <!-- Back button -->
          <div class="mt-3">
            <a href="<?= base_url('/berita') ?>" class="btn btn-light-outline btn-sm w-100 justify-content-center">
              <i class="bi bi-arrow-left"></i> Semua Berita
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
