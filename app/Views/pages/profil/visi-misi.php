<?php
$brandName    = trim((string)($desa_nama ?? '')) ?: (trim((string)($website_nama ?? '')) ?: 'Desa');
$visiText     = trim((string)($visi ?? ''));
$misiItems    = is_array($misi_list ?? null) ? $misi_list : [];
$profileLabel = trim((string)($pengaturan['menu_profile_label'] ?? '')) ?: 'Profil';
$storyLabel   = trim((string)($pengaturan['menu_story_label']   ?? '')) ?: 'Sejarah';
$visionLabel  = trim((string)($pengaturan['menu_vision_label']  ?? '')) ?: 'Visi & Misi';
$structureLabel = trim((string)($pengaturan['menu_structure_label'] ?? '')) ?: 'Struktur Organisasi';
$canonicalUrl = rtrim((string)BASE_URL,'/') . '/profil/visi-misi';
$breadcrumbs = [
  ['@type'=>'ListItem','position'=>1,'name'=>'Beranda','item'=>rtrim((string)BASE_URL,'/')],
  ['@type'=>'ListItem','position'=>2,'name'=>$profileLabel,'item'=>rtrim((string)BASE_URL,'/'). '/profil'],
  ['@type'=>'ListItem','position'=>3,'name'=>$visionLabel,'item'=>$canonicalUrl],
];
?>
<div class="profil-layout">
  <div class="page-hero">
    <div class="container page-hero-content">
      <nav class="breadcrumb-nav">
        <a href="<?= base_url() ?>">Beranda</a><span class="sep"><i class="bi bi-chevron-right"></i></span>
        <a href="<?= base_url('/profil') ?>"><?= htmlspecialchars($profileLabel) ?></a><span class="sep"><i class="bi bi-chevron-right"></i></span>
        <span class="current"><?= htmlspecialchars($visionLabel) ?></span>
      </nav>
      <h1 class="page-title"><?= htmlspecialchars($visionLabel) ?></h1>
      <p class="page-subtitle">Arah dan komitmen <?= htmlspecialchars($brandName) ?> untuk masa depan.</p>
    </div>
  </div>

  <div class="section">
    <div class="container">
      <div class="row g-5">
        <div class="col-lg-3">
          <div class="profil-nav-sidebar" data-reveal="left">
            <div class="profil-nav-header"><h6><?= htmlspecialchars($profileLabel) ?></h6></div>
            <ul class="profil-nav-links">
              <li><a href="<?= base_url('/profil') ?>"><i class="bi bi-book"></i> <?= htmlspecialchars($storyLabel) ?></a></li>
              <li><a href="<?= base_url('/profil/visi-misi') ?>" class="active"><i class="bi bi-eye"></i> <?= htmlspecialchars($visionLabel) ?></a></li>
              <li><a href="<?= base_url('/profil/struktur-organisasi') ?>"><i class="bi bi-diagram-3"></i> <?= htmlspecialchars($structureLabel) ?></a></li>
            </ul>
          </div>
        </div>
        <div class="col-lg-9">
          <!-- Visi Block -->
          <div class="visi-misi-page-card" data-reveal="flip">
            <div class="visi-block">
              <div class="visi-block-label"><i class="bi bi-eye-fill"></i> Visi <?= htmlspecialchars($brandName) ?></div>
              <h2><?= !empty($visiText) ? nl2br(htmlspecialchars($visiText)) : 'Visi belum tersedia.' ?></h2>
            </div>
          </div>

          <!-- Misi Block -->
          <?php if (!empty($misiItems)): ?>
          <div class="visi-misi-page-card mt-4" data-reveal="up" data-reveal-delay="150">
            <div class="misi-block">
              <div class="misi-block-label"><i class="bi bi-list-check"></i> Misi <?= htmlspecialchars($brandName) ?></div>
              <ul class="misi-list-page" data-stagger>
                <?php foreach ($misiItems as $i => $misi): ?>
                <li class="misi-page-item">
                  <span class="misi-page-num"><?= $i + 1 ?></span>
                  <span class="misi-page-text"><?= htmlspecialchars(trim((string)$misi)) ?></span>
                </li>
                <?php endforeach; ?>
              </ul>
            </div>
          </div>
          <?php endif; ?>

          <div class="mt-4">
            <a href="<?= base_url('/profil') ?>" class="btn btn-primary-custom"><i class="bi bi-arrow-left"></i> Kembali</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
