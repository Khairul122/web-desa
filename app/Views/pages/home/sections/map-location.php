<?php if ($hasMapSection): ?>
<section class="section map-section" id="lokasi">
  <div class="container">
    <div class="text-center mb-5" data-reveal="clip">
      <span class="section-label"><i class="bi bi-pin-map-fill"></i> Lokasi</span>
      <h2 class="section-title mt-2"><?= htmlspecialchars($mapSectionTitle) ?></h2>
      <div class="formal-divider formal-divider--center"></div>
      <p class="section-subtitle mx-auto"><?= htmlspecialchars($mapSectionDescription) ?></p>
    </div>
    <div class="section-accent-title section-accent-title--center" data-reveal="up" data-reveal-delay="80">
      <span>Lokasi Gampong</span>
    </div>
    <div class="map-wrap" data-reveal="up">
      <iframe
        src="<?= htmlspecialchars($mapEmbedUrl) ?>"
        allowfullscreen
        loading="lazy"
        referrerpolicy="no-referrer-when-downgrade"
        title="Peta lokasi <?= htmlspecialchars($brandName) ?>">
      </iframe>
    </div>
  </div>
</section>
<?php endif; ?>
