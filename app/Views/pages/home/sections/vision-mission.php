<?php if (!empty($visiText) || !empty($missionItems)): ?>
<section class="section visi-misi-section" id="visi-misi">
  <div class="container">
    <div class="text-center mb-5" data-reveal="clip">
      <span class="section-label"><i class="bi bi-compass"></i> Arah Gampong</span>
      <h2 class="section-title mt-2">Visi &amp; Misi</h2>
      <div class="formal-divider formal-divider--center"></div>
    </div>
    <div class="visi-misi-wrap">
      <div class="visi-panel" data-reveal="left">
        <div class="visi-panel-ornament" aria-hidden="true">
          <span class="visi-ornament-chip"><i class="bi bi-stars"></i> Nilai Gampong</span>
          <div class="visi-ornament-grid">
            <span>Pelayanan</span>
            <span>Kebersamaan</span>
            <span>Kemandirian</span>
          </div>
        </div>
        <div class="panel-label"><i class="bi bi-eye"></i> Visi</div>
        <h3><?= !empty($visiText) ? nl2br(htmlspecialchars($visiText)) : 'Visi belum tersedia.' ?></h3>
        <div class="mt-4 pt-2">
          <a href="<?= base_url('/profil/visi-misi') ?>" class="btn btn-ghost-custom btn-sm">
            <i class="bi bi-arrow-right"></i> Lihat Lengkap
          </a>
        </div>
      </div>

      <div class="misi-panel" data-reveal="right">
        <div class="misi-panel-topline">
          <span class="misi-topline-item"><i class="bi bi-flag"></i> Program Prioritas</span>
          <span class="misi-topline-item"><i class="bi bi-people"></i> Berbasis Warga</span>
        </div>
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
        <div class="misi-footnote">
          <i class="bi bi-info-circle"></i>
          <span>Setiap langkah pembangunan diarahkan untuk pelayanan publik yang lebih cepat, tertib, dan berdampak langsung bagi masyarakat.</span>
        </div>
        <?php else: ?>
        <p style="color:var(--muted)">Misi belum tersedia.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>
<?php endif; ?>
