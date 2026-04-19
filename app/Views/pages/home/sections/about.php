<section class="section about-section" id="tentang">
  <div class="container">
    <div class="row align-items-center g-5">
      <div class="col-lg-5" data-reveal="clip-left">
        <div class="about-image-wrap">
          <?php if (!empty($aboutImageUrl)): ?>
          <img src="<?= htmlspecialchars($aboutImageUrl) ?>" alt="<?= htmlspecialchars($brandName) ?>" class="about-image-main" loading="lazy" width="600" height="750">
          <?php else: ?>
          <div class="about-image-main d-flex align-items-center justify-content-center" style="background:var(--grad-hero);aspect-ratio:4/5;border-radius:var(--radius-xl)">
            <i class="bi bi-building" style="font-size:4rem;color:rgba(255,255,255,0.3)"></i>
          </div>
          <?php endif; ?>
          <?php if ($establishedYear !== 'N/A'): ?>
          <div class="about-image-badge">
            <div class="badge-icon"><i class="bi bi-calendar-check"></i></div>
            <div>
              <strong><?= htmlspecialchars($establishedYear) ?></strong>
              <span>Tahun Berdiri</span>
            </div>
          </div>
          <?php endif; ?>
          <div class="about-image-decor" aria-hidden="true"></div>
        </div>
      </div>

      <div class="col-lg-7">
        <div class="about-content" data-reveal="right">
          <span class="section-label"><i class="bi bi-house-heart"></i> Tentang Gampong</span>
          <h2 class="section-title mt-2"><?= htmlspecialchars($brandName) ?></h2>
          <div class="formal-divider"></div>

          <?php if (!empty($profilSummary)): ?>
          <p style="font-size:var(--text-lg);color:var(--color-body);line-height:1.8">
            <?= nl2br(htmlspecialchars(mb_strimwidth($profilSummary, 0, 400, '...'))) ?>
          </p>
          <?php endif; ?>

          <div class="about-facts">
            <?php if (!empty($districtName)): ?>
            <span class="about-fact-chip"><i class="bi bi-geo-alt"></i> <?= htmlspecialchars($districtName) ?></span>
            <?php endif; ?>
            <?php if (!empty($regencyName)): ?>
            <span class="about-fact-chip"><i class="bi bi-map"></i> <?= htmlspecialchars($regencyName) ?></span>
            <?php endif; ?>
            <?php if ($farmArea !== 'N/A' && !empty($farmArea)): ?>
            <span class="about-fact-chip"><i class="bi bi-flower1"></i> <?= htmlspecialchars($farmArea) ?> Pertanian</span>
            <?php endif; ?>
            <?php if (!empty($luasWilayah) && $luasWilayah !== '0'): ?>
            <span class="about-fact-chip"><i class="bi bi-aspect-ratio"></i> <?= htmlspecialchars($luasWilayah) ?> Luas</span>
            <?php endif; ?>
          </div>

          <?php if (!empty($visiText)): ?>
          <div class="about-vision-box" data-reveal="fade" data-reveal-delay="300">
            <p><?= htmlspecialchars(mb_strimwidth($visiText, 0, 240, '...')) ?></p>
          </div>
          <?php endif; ?>

          <div class="mt-4 d-flex gap-3 flex-wrap">
            <a href="<?= base_url('/profil') ?>" class="btn btn-primary-custom btn-magnetic"><i class="bi bi-arrow-right"></i> Selengkapnya</a>
            <a href="<?= base_url('/profil/visi-misi') ?>" class="btn btn-light-outline"><i class="bi bi-eye"></i> Visi &amp; Misi</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
