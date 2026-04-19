<?php if (!empty($visiText) || !empty($missionItems)): ?>
<section class="section visi-misi-section" id="visi-misi">
  <div class="container">
    <div class="text-center mb-5" data-reveal="clip">
      <span class="section-label"><i class="bi bi-compass"></i> Arah Gampong</span>
      <h2 class="section-title mt-2">Visi &amp; Misi</h2>
      <div class="formal-divider formal-divider--center"></div>
    </div>
    <div class="visi-misi-scene" aria-hidden="true">
      <div class="visi-misi-floating-note visi-misi-floating-note--left">
        <span class="floating-note-label">Arah Utama</span>
        <strong>Pelayanan, kebersamaan, dan pembangunan berkelanjutan.</strong>
      </div>
      <div class="visi-misi-floating-badges">
        <span>Pelayanan</span>
        <span>Kebersamaan</span>
        <span>Kemandirian</span>
      </div>
      <div class="visi-misi-floating-note visi-misi-floating-note--right">
        <span class="floating-note-label">Fokus Misi</span>
        <strong>Program yang dekat dengan kebutuhan warga dan tertata.</strong>
      </div>
    </div>
    <div class="visi-misi-wrap">
      <div class="visi-panel" data-reveal="left">
        <div class="panel-label"><i class="bi bi-eye"></i> Visi</div>
        <h3><?= !empty($visiText) ? nl2br(htmlspecialchars($visiText)) : 'Visi belum tersedia.' ?></h3>
        <div class="mt-4 pt-2">
          <a href="<?= base_url('/profil/visi-misi') ?>" class="btn btn-ghost-custom btn-sm">
            <i class="bi bi-arrow-right"></i> Lihat Lengkap
          </a>
        </div>
      </div>

      <div class="misi-panel" data-reveal="right">
        <div class="panel-label"><i class="bi bi-list-check"></i> Misi</div>
        <?php if (!empty($missionItems)): ?>
        <ul class="misi-list" data-stagger>
          <?php foreach ($missionItems as $i => $misi): ?>
          <li class="misi-item">
            <span class="misi-num"><?= $i + 1 ?></span>
            <span class="misi-text"><?= htmlspecialchars(trim((string)$misi)) ?></span>
          </li>
          <?php endforeach; ?>
        </ul>
        <?php else: ?>
        <p style="color:var(--muted)">Misi belum tersedia.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>
<?php endif; ?>
