<section class="section map-section" id="lokasi">
  <div class="container">
    <div class="text-center mb-5">
      <span class="section-label"><i class="bi bi-pin-map-fill"></i> Lokasi</span>
      <h2 class="section-title mt-2"><?= htmlspecialchars($mapSectionTitle) ?></h2>
      <div class="formal-divider formal-divider--center"></div>
      <p class="section-subtitle mx-auto"><?= htmlspecialchars($mapSectionDescription) ?></p>
    </div>
    <?php if (!$hasMapSection): ?>
    <div class="empty-state" data-reveal="fade">
      <i class="bi bi-pin-map"></i>
      <h4>Lokasi belum tersedia</h4>
      <p>Peta lokasi <?= htmlspecialchars($brandName) ?> akan tampil di sini setelah tautan embed ditambahkan.</p>
    </div>
    <?php endif; ?>
    <div class="map-wrap" data-reveal="up">
      <?php if ($hasMapSection): ?>
      <iframe
        src="<?= htmlspecialchars($mapEmbedUrl) ?>"
        allowfullscreen
        loading="lazy"
        referrerpolicy="no-referrer-when-downgrade"
        title="Peta lokasi <?= htmlspecialchars($brandName) ?>">
      </iframe>
      <?php endif; ?>
    </div>
  </div>
</section>
