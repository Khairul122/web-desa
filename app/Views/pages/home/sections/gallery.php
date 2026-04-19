<?php if (!empty($galeriItems)): ?>
<section class="section gallery-section" id="galeri">
  <div class="container">
    <div class="section-header" data-reveal="clip-right">
      <div class="section-header-left">
        <span class="section-label"><i class="bi bi-images"></i> Dokumentasi</span>
        <h2 class="section-title mt-2">Galeri Gampong</h2>
        <div class="formal-divider"></div>
        <p class="section-subtitle">Koleksi foto kegiatan dan keindahan <?= htmlspecialchars($brandName) ?>.</p>
      </div>
      <a href="<?= base_url('/galeri') ?>" class="btn btn-light-outline btn-sm flex-shrink-0">
        Lihat Semua <i class="bi bi-arrow-right"></i>
      </a>
    </div>
    <div class="gallery-masonry" data-stagger>
      <?php foreach (array_slice($galeriItems, 0, 8) as $gal): ?>
      <?php
        $galImgPath = trim((string)($gal['gambar'] ?? ''));
        $galImgUrl  = $galImgPath !== '' ? $resolveMediaUrl($galImgPath, 'galeri') : '';
        if ($galImgUrl === '') continue;
        $galTitle = trim((string)($gal['judul'] ?? ''));
        $galCat   = trim((string)($gal['kategori'] ?? ''));
      ?>
      <div class="gallery-item" data-lightbox-src="<?= htmlspecialchars($galImgUrl) ?>" data-lightbox-alt="<?= htmlspecialchars($galTitle) ?>" tabindex="0" role="button" aria-label="Lihat foto <?= htmlspecialchars($galTitle) ?>">
        <img src="<?= htmlspecialchars($galImgUrl) ?>" alt="<?= htmlspecialchars($galTitle) ?>" loading="lazy">
        <div class="gallery-overlay">
          <div class="gallery-overlay-text">
            <?php if ($galCat): ?><span style="color:var(--accent);font-size:0.68rem;font-weight:700;display:block;margin-bottom:0.2rem"><?= htmlspecialchars($galCat) ?></span><?php endif; ?>
            <?= htmlspecialchars($galTitle) ?>
          </div>
        </div>
        <div class="gallery-zoom" aria-hidden="true"><i class="bi bi-zoom-in"></i></div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>
