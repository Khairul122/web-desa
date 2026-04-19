<?php
$normalizeStatIcon = static function (string $icon): string {
    $icon = trim($icon);
    if ($icon === '') {
        return 'bi bi-bar-chart-fill';
    }

    if (str_contains($icon, ' ')) {
        return $icon;
    }

    if (str_starts_with($icon, 'bi-')) {
        return 'bi ' . $icon;
    }

    if (str_starts_with($icon, 'bi')) {
        return preg_replace('/^bi(?!\s)/', 'bi ', $icon) ?: 'bi bi-bar-chart-fill';
    }

    return 'bi bi-bar-chart-fill';
};

$displayStats = [];
if (!empty($statistikItems)) {
    foreach ($statistikItems as $s) {
        $displayStats[] = [
            'icon'  => $normalizeStatIcon((string)($s['icon'] ?? 'bi bi-bar-chart-fill')),
            'label' => trim((string)($s['nama_statistik'] ?? ($s['label'] ?? ''))),
            'value' => trim((string)($s['nilai_statistik'] ?? ($s['value'] ?? ''))),
            'unit'  => trim((string)($s['unit'] ?? '')),
        ];
    }
}
if (empty($displayStats)) {
    $displayStats = [];
    if (!empty($totalPenduduk) && $totalPenduduk !== '0') {
        $displayStats[] = ['icon' => 'bi bi-people-fill', 'label' => 'Total Penduduk', 'value' => (string)$totalPenduduk, 'unit' => 'Jiwa'];
    }
    if (!empty($totalKk) && $totalKk !== '0') {
        $displayStats[] = ['icon' => 'bi bi-house-door-fill', 'label' => 'Kepala Keluarga', 'value' => (string)$totalKk, 'unit' => 'KK'];
    }
    if (!empty($luasWilayah) && $luasWilayah !== '0') {
        $displayStats[] = ['icon' => 'bi bi-map-fill', 'label' => 'Luas Wilayah', 'value' => (string)$luasWilayah, 'unit' => ''];
    }
    if (!empty($totalLandingViews)) {
        $displayStats[] = ['icon' => 'bi bi-eye-fill', 'label' => 'Total Kunjungan', 'value' => number_format((int)($totalLandingViews ?? 0), 0, ',', '.'), 'unit' => ''];
    }
}
?>
<?php if (!empty($displayStats)): ?>
<section class="section stats-section" id="statistik" style="position:relative">
  <div class="container" style="position:relative;z-index:1">
    <div class="text-center mb-5" data-reveal="zoom">
      <span class="section-label" style="background:rgba(201,135,12,0.15);border-color:rgba(201,135,12,0.30);color:var(--accent-light)">
        <i class="bi bi-bar-chart-line"></i> Data Resmi
      </span>
      <h2 class="section-title mt-2" style="color:white">Statistik <?= htmlspecialchars($brandName) ?></h2>
      <div class="formal-divider formal-divider--center" style="background:var(--grad-accent)"></div>
      <p class="section-subtitle mx-auto" style="color:rgba(255,255,255,0.60)">Data resmi yang mencerminkan kondisi dan potensi gampong kami.</p>
    </div>
    <div class="stats-grid" data-stagger-flip>
      <?php foreach ($displayStats as $stat): ?>
      <div class="stat-card">
        <div class="stat-icon"><i class="<?= htmlspecialchars($stat['icon']) ?>"></i></div>
        <span class="stat-number">
          <span data-counter="<?= preg_replace('/[^0-9]/', '', $stat['value']) ?>"><?= htmlspecialchars($stat['value']) ?></span><?php if (!empty($stat['unit'])): ?><small class="stat-unit"> <?= htmlspecialchars($stat['unit']) ?></small><?php endif; ?>
        </span>
        <span class="stat-label"><?= htmlspecialchars($stat['label']) ?></span>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>
