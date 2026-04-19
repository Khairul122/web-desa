<section class="section services-section" id="layanan">
  <div class="container" style="position:relative;z-index:1">
    <div class="text-center mb-5" data-reveal="up">
      <span class="section-label"><i class="bi bi-grid-3x3-gap"></i> Layanan</span>
      <h2 class="section-title mt-2">Layanan Gampong</h2>
      <div class="formal-divider formal-divider--center"></div>
      <p class="section-subtitle mx-auto">Kami hadir untuk memberikan pelayanan terbaik bagi seluruh warga <?= htmlspecialchars($brandName) ?>.</p>
    </div>
    <div class="services-grid" data-stagger-diag>
      <?php foreach ($serviceItems as $srv): ?>
      <div class="service-card tilt-card">
        <div class="tilt-card__shine" aria-hidden="true"></div>
        <div class="service-icon-wrap"><i class="<?= htmlspecialchars($srv['icon']) ?>"></i></div>
        <h4><?= htmlspecialchars($srv['title']) ?></h4>
        <p><?= htmlspecialchars($srv['desc']) ?></p>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
