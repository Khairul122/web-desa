<section class="hero-section" id="hero" aria-label="Halaman Utama">
  <div class="hero-slides-wrap" aria-hidden="true">
    <?php if (!empty($heroSlides)): ?>
      <?php foreach ($heroSlides as $idx => $slide): ?>
      <div class="hero-slide <?= $idx === 0 ? 'active' : '' ?>" data-slide="<?= $idx ?>">
        <img src="<?= htmlspecialchars($slide['image']) ?>" alt="<?= htmlspecialchars($slide['title'] ?: $brandName) ?>" loading="<?= $idx === 0 ? 'eager' : 'lazy' ?>" fetchpriority="<?= $idx === 0 ? 'high' : 'low' ?>">
      </div>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="hero-slide active"><div style="width:100%;height:100%;background:var(--grad-hero)"></div></div>
    <?php endif; ?>
  </div>
  <div class="hero-overlay" aria-hidden="true"></div>
  <div class="hero-motif" aria-hidden="true"></div>
  <div class="hero-parallax-layer hero-parallax-layer-1" data-parallax-speed="0.15" aria-hidden="true"></div>
  <div class="hero-parallax-layer hero-parallax-layer-2" data-parallax-speed="0.08" aria-hidden="true"></div>
  <div class="hero-content">
    <div class="container">
      <div class="row">
        <div class="col-lg-8 col-xl-7">
          <div class="hero-kicker" data-reveal="trail">
            <i class="bi bi-geo-alt-fill"></i>
            <?php
            $loc = $heroLocationLine ?: trim(($districtName ?? '') . (($districtName && $regencyName) ? ' · ' : '') . ($regencyName ?? ''), ' ·');
            echo htmlspecialchars($loc ?: 'Aceh Utara');
            ?>
          </div>
          <h1 class="hero-title" data-reveal="clip">
            <?php
            $parts = explode(' ', trim($brandName));
            if (count($parts) >= 2) {
                $last = array_pop($parts);
                echo htmlspecialchars(implode(' ', $parts)) . ' <span class="accent-word">' . htmlspecialchars($last) . '</span>';
            } else {
                echo htmlspecialchars($brandName);
            }
            ?>
          </h1>
          <?php if (!empty($heroSummary)): ?>
          <p class="hero-desc" data-reveal="up" data-reveal-delay="200"><?= htmlspecialchars(mb_strimwidth($heroSummary, 0, 180, '...')) ?></p>
          <?php endif; ?>
          <div class="hero-actions" data-reveal="up" data-reveal-delay="350">
            <a href="<?= base_url('/profil') ?>" class="btn btn-accent btn-lg btn-magnetic"><i class="bi bi-map"></i> Jelajahi Gampong</a>
            <a href="<?= base_url('/kontak') ?>" class="btn btn-ghost-custom btn-lg"><i class="bi bi-chat-dots"></i> Hubungi Kami</a>
          </div>
        </div>
      </div>
    </div>
  </div>
  <?php if (count($heroSlides) > 1): ?>
  <div class="hero-nav" role="tablist" aria-label="Slide navigasi">
    <?php foreach ($heroSlides as $idx => $slide): ?>
    <button class="hero-nav-dot <?= $idx === 0 ? 'active' : '' ?>" data-target="<?= $idx ?>" role="tab" aria-selected="<?= $idx === 0 ? 'true' : 'false' ?>" aria-label="Slide <?= $idx + 1 ?>"></button>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
  <div class="hero-stats-bar" aria-label="Statistik ringkas">
    <div class="container">
      <div class="hero-stats-inner">
        <?php if (!empty($totalPenduduk) && $totalPenduduk !== '0'): ?>
        <div class="hero-stat-item"><span class="hero-stat-num" data-counter="<?= (int)$totalPenduduk ?>"><?= number_format((int)$totalPenduduk,0,',','.') ?></span><span class="hero-stat-label">Jiwa Penduduk</span></div>
        <?php endif; ?>
        <?php if (!empty($totalKk) && $totalKk !== '0'): ?>
        <div class="hero-stat-item"><span class="hero-stat-num" data-counter="<?= (int)$totalKk ?>"><?= number_format((int)$totalKk,0,',','.') ?></span><span class="hero-stat-label">Kepala Keluarga</span></div>
        <?php endif; ?>
        <?php if (!empty($luasWilayah) && $luasWilayah !== '0'): ?>
        <div class="hero-stat-item"><span class="hero-stat-num"><?= htmlspecialchars($luasWilayah) ?></span><span class="hero-stat-label">Luas Wilayah</span></div>
        <?php endif; ?>
        <?php if ($establishedYear !== 'N/A'): ?>
        <div class="hero-stat-item"><span class="hero-stat-num"><?= htmlspecialchars($establishedYear) ?></span><span class="hero-stat-label">Tahun Berdiri</span></div>
        <?php endif; ?>
        <?php if (!empty($totalLandingViews)): ?>
        <div class="hero-stat-item"><span class="hero-stat-num"><?= number_format((int)$totalLandingViews,0,',','.') ?></span><span class="hero-stat-label">Total Kunjungan</span></div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>
