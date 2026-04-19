<?php if (!empty($beritaItems)): ?>
<?php
$featuredNews = $beritaItems[0] ?? null;
$restNews     = array_slice($beritaItems, 1, 3);
$resolveNewsImg = static function($item) use ($resolveMediaUrl): string {
    $thumb = trim((string)($item['thumbnail'] ?? ''));
    return $thumb !== '' ? $resolveMediaUrl($thumb, 'artikel') : '';
};
?>
<section class="section news-section" id="berita">
  <div class="container">
    <div class="section-header" data-reveal="clip">
      <div class="section-header-left">
        <span class="section-label"><i class="bi bi-newspaper"></i> Informasi</span>
        <h2 class="section-title mt-2">Berita Gampong</h2>
        <div class="formal-divider"></div>
        <p class="section-subtitle">Ikuti kabar dan kegiatan terkini dari <?= htmlspecialchars($brandName) ?>.</p>
      </div>
      <a href="<?= base_url('/berita') ?>" class="btn btn-light-outline btn-sm flex-shrink-0">
        Lihat Semua <i class="bi bi-arrow-right"></i>
      </a>
    </div>
    <div class="section-intro-text" data-reveal="up" data-reveal-delay="80">
      <p>Rangkaian informasi terbaru seputar kegiatan, program, dan perkembangan yang sedang berlangsung di <?= htmlspecialchars($brandName) ?>.</p>
    </div>

    <?php if ($featuredNews): ?>
    <?php $featImg = $resolveNewsImg($featuredNews); ?>
    <a href="<?= base_url('/berita/' . ($featuredNews['slug'] ?? '')) ?>" class="news-featured" data-reveal="flip">
      <div class="news-featured-img">
        <?php if ($featImg): ?>
        <img src="<?= htmlspecialchars($featImg) ?>" alt="<?= htmlspecialchars((string)($featuredNews['judul'] ?? '')) ?>" loading="lazy">
        <?php else: ?>
        <div style="width:100%;height:100%;background:var(--grad-hero);display:flex;align-items:center;justify-content:center">
          <i class="bi bi-newspaper" style="font-size:3rem;color:rgba(255,255,255,0.3)"></i>
        </div>
        <?php endif; ?>
      </div>
      <div class="news-featured-content">
        <?php if (!empty($featuredNews['kategori'])): ?>
        <span class="news-cat-badge"><i class="bi bi-star-fill"></i> <?= htmlspecialchars((string)$featuredNews['kategori']) ?></span>
        <?php else: ?>
        <span class="news-cat-badge"><i class="bi bi-star-fill"></i> Artikel Utama</span>
        <?php endif; ?>
        <p class="news-featured-title"><?= htmlspecialchars((string)($featuredNews['judul'] ?? '')) ?></p>
        <p class="news-featured-excerpt"><?= htmlspecialchars(strip_tags(mb_strimwidth((string)($featuredNews['konten'] ?? ''), 0, 220, '...'))) ?></p>
        <div class="news-meta">
          <span><i class="bi bi-calendar3"></i> <?= htmlspecialchars(date('d M Y', strtotime((string)($featuredNews['created_at'] ?? 'now')))) ?></span>
          <?php if (!empty($featuredNews['views'])): ?>
          <span><i class="bi bi-eye"></i> <?= number_format((int)$featuredNews['views'], 0, ',', '.') ?> baca</span>
          <?php endif; ?>
        </div>
      </div>
    </a>
    <?php endif; ?>

    <?php if (!empty($restNews)): ?>
    <div class="news-grid mt-3" data-stagger>
      <?php foreach ($restNews as $item): ?>
      <?php $img = $resolveNewsImg($item); ?>
      <a href="<?= base_url('/berita/' . ($item['slug'] ?? '')) ?>" class="news-card">
        <div class="news-card-img">
          <?php if ($img): ?>
          <img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars((string)($item['judul'] ?? '')) ?>" loading="lazy">
          <?php else: ?>
          <div style="width:100%;height:100%;background:var(--color-bg);display:flex;align-items:center;justify-content:center;aspect-ratio:16/9">
            <i class="bi bi-newspaper" style="font-size:2rem;color:var(--muted)"></i>
          </div>
          <?php endif; ?>
        </div>
        <div class="news-card-body">
          <?php if (!empty($item['kategori'])): ?>
          <span class="news-cat-badge" style="margin-bottom:.5rem"><?= htmlspecialchars((string)$item['kategori']) ?></span>
          <?php endif; ?>
          <p class="news-card-title"><?= htmlspecialchars((string)($item['judul'] ?? '')) ?></p>
          <p class="news-card-excerpt"><?= htmlspecialchars(strip_tags(mb_strimwidth((string)($item['konten'] ?? ''), 0, 120, '...'))) ?></p>
          <div class="news-meta mt-auto">
            <span><i class="bi bi-calendar3"></i> <?= htmlspecialchars(date('d M Y', strtotime((string)($item['created_at'] ?? 'now')))) ?></span>
          </div>
        </div>
      </a>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

  </div>
</section>
<?php endif; ?>
