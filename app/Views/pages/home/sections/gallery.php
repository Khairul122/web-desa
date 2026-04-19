<?php
$galleryPreviewItems = [];
foreach (array_slice((array) $galeriItems, 0, 8) as $gal) {
    $galImgPath = trim((string) ($gal['gambar'] ?? ''));
    $galTitle = trim((string) ($gal['judul'] ?? ''));
    $galCat = trim((string) ($gal['kategori'] ?? ''));

    $imageCandidates = [];
    if ($galImgPath !== '') {
        $normalizedPath = ltrim(str_replace('\\', '/', $galImgPath), '/');
        $basename = basename($normalizedPath);
        $imageCandidates = array_values(array_unique(array_filter([
            dirname(__DIR__, 5) . '/uploads/' . $normalizedPath,
            dirname(__DIR__, 5) . '/uploads/galeri/' . $normalizedPath,
            dirname(__DIR__, 5) . '/uploads/galeri/' . $basename,
        ], static fn($value) => $value !== '')));
    }

    $hasLocalImage = false;
    foreach ($imageCandidates as $candidate) {
        if (is_file($candidate)) {
            $hasLocalImage = true;
            break;
        }
    }

    $galImgUrl = '';
    if ($galImgPath !== '' && $hasLocalImage) {
        $galImgUrl = $resolveMediaUrl($galImgPath, 'galeri');
    }

    $galleryPreviewItems[] = [
        'url' => $galImgUrl,
        'title' => $galTitle !== '' ? $galTitle : 'Dokumentasi Gampong',
        'category' => $galCat,
    ];
}
?>
<section class="section gallery-section" id="galeri">
  <div class="container">
    <div class="text-center mb-5">
      <span class="section-label"><i class="bi bi-images"></i> Dokumentasi</span>
      <h2 class="section-title mt-2">Galeri Gampong</h2>
      <div class="formal-divider formal-divider--center"></div>
      <p class="section-subtitle mx-auto">Koleksi foto kegiatan dan keindahan <?= htmlspecialchars($brandName) ?>.</p>
      <a href="<?= base_url('/galeri') ?>" class="btn btn-light-outline btn-sm mt-3">
        Lihat Semua <i class="bi bi-arrow-right"></i>
      </a>
    </div>
    <?php if (empty($galleryPreviewItems)): ?>
    <div class="empty-state" data-reveal="fade">
      <i class="bi bi-images"></i>
      <h4>Galeri belum tersedia</h4>
      <p>Data galeri belum ada di database localhost, jadi foto belum bisa ditampilkan.</p>
    </div>
    <?php else: ?>
    <div class="gallery-masonry" data-stagger>
      <?php foreach ($galleryPreviewItems as $gal): ?>
      <?php
        $galImgUrl = (string) $gal['url'];
        $galTitle = (string) $gal['title'];
        $galCat = (string) $gal['category'];
      ?>
      <div class="gallery-item<?= $galImgUrl === '' ? ' gallery-item--static' : '' ?>"<?= $galImgUrl !== '' ? ' data-lightbox-src="' . htmlspecialchars($galImgUrl) . '" data-lightbox-alt="' . htmlspecialchars($galTitle) . '"' : '' ?> tabindex="0" role="button" aria-label="Pratinjau <?= htmlspecialchars($galTitle) ?>">
        <?php if ($galImgUrl !== ''): ?>
        <img src="<?= htmlspecialchars($galImgUrl) ?>" alt="<?= htmlspecialchars($galTitle) ?>" loading="lazy">
        <?php else: ?>
        <div class="gallery-static-media" aria-hidden="true">
          <i class="bi bi-image"></i>
        </div>
        <?php endif; ?>
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
    <?php endif; ?>
  </div>
</section>
