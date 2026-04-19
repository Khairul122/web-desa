<?php
$profil       = is_array($profil_data ?? null) ? $profil_data : [];
$brandName    = trim((string)($desa_nama ?? '')) ?: (trim((string)($website_nama ?? '')) ?: 'Desa');
$strukturRaw  = trim((string)($struktur_organisasi ?? ($profil['struktur_organisasi'] ?? '')));
$profileLabel = trim((string)($pengaturan['menu_profile_label'] ?? '')) ?: 'Profil';
$storyLabel   = trim((string)($pengaturan['menu_story_label']   ?? '')) ?: 'Sejarah';
$visionLabel  = trim((string)($pengaturan['menu_vision_label']  ?? '')) ?: 'Visi & Misi';
$structureLabel = trim((string)($pengaturan['menu_structure_label'] ?? '')) ?: 'Struktur Organisasi';
$canonicalUrl = rtrim((string)BASE_URL,'/') . '/profil/struktur-organisasi';
$breadcrumbs = [
  ['@type'=>'ListItem','position'=>1,'name'=>'Beranda','item'=>rtrim((string)BASE_URL,'/')],
  ['@type'=>'ListItem','position'=>2,'name'=>$profileLabel,'item'=>rtrim((string)BASE_URL,'/'). '/profil'],
  ['@type'=>'ListItem','position'=>3,'name'=>$structureLabel,'item'=>$canonicalUrl],
];
?>
<div class="profil-layout">
  <div class="page-hero">
    <div class="container page-hero-content">
      <nav class="breadcrumb-nav">
        <a href="<?= base_url() ?>">Beranda</a><span class="sep"><i class="bi bi-chevron-right"></i></span>
        <a href="<?= base_url('/profil') ?>"><?= htmlspecialchars($profileLabel) ?></a><span class="sep"><i class="bi bi-chevron-right"></i></span>
        <span class="current"><?= htmlspecialchars($structureLabel) ?></span>
      </nav>
      <h1 class="page-title"><?= htmlspecialchars($structureLabel) ?></h1>
      <p class="page-subtitle">Susunan aparatur dan perangkat <?= htmlspecialchars($brandName) ?>.</p>
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
              <li><a href="<?= base_url('/profil/visi-misi') ?>"><i class="bi bi-eye"></i> <?= htmlspecialchars($visionLabel) ?></a></li>
              <li><a href="<?= base_url('/profil/struktur-organisasi') ?>" class="active"><i class="bi bi-diagram-3"></i> <?= htmlspecialchars($structureLabel) ?></a></li>
            </ul>
          </div>
        </div>
        <div class="col-lg-9">
          <div class="profil-content-card" data-reveal>
            <div class="profil-content-header"><h2><?= htmlspecialchars($structureLabel) ?></h2></div>
            <div class="profil-body">
              <?php if (!empty($strukturRaw)): ?>
                <?= $strukturRaw ?>
              <?php else: ?>
              <div class="empty-state" style="padding:2rem 0">
                <i class="bi bi-diagram-3"></i>
                <p>Struktur organisasi belum tersedia. Silahkan tambahkan melalui panel admin.</p>
              </div>
              <?php endif; ?>
            </div>
          </div>
          <div class="mt-4">
            <a href="<?= base_url('/profil') ?>" class="btn btn-outline-custom"><i class="bi bi-arrow-left"></i> Kembali</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
