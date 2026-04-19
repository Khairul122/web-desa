<?php if (!empty($popularServices)): ?>
<div class="section-sm popular-services-section" id="popular-services">
  <div class="container">
    <div class="text-center mb-4" data-reveal="up">
      <span class="section-label"><i class="bi bi-lightning-charge"></i> Akses Cepat</span>
      <h3 class="section-title mt-2" style="font-size:var(--text-3xl)">Layanan Populer</h3>
    </div>
    <div class="popular-services-wrap" data-stagger>
      <?php foreach ($popularServices as $ps): ?>
      <a href="<?= htmlspecialchars($ps['url']) ?>" class="popular-service-pill"
         target="<?= preg_match('#^https?://#i', $ps['url']) ? '_blank' : '_self' ?>"
         rel="noopener noreferrer">
        <span class="ps-icon"><i class="<?= htmlspecialchars($ps['icon']) ?>"></i></span>
        <?= htmlspecialchars($ps['label']) ?>
        <i class="bi bi-arrow-right-short" style="margin-left:0.2rem;opacity:0.5"></i>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</div>
<?php endif; ?>
