<?php
$profil    = is_array($profil_data ?? null) ? $profil_data : [];
$brandName = trim((string)($desa_nama ?? '')) ?: (trim((string)($website_nama ?? '')) ?: 'Desa');
$alamat    = trim((string)($profil['alamat'] ?? ($desa_alamat ?? '')));
$telepon   = trim((string)($profil['telepon'] ?? ($desa_telepon ?? '')));
$email     = trim((string)($profil['email']   ?? ($desa_email   ?? '')));
$sejarahText = trim((string)($sejarah ?? ''));
$kecamatan = trim((string)($pengaturan['district_name'] ?? ''));
$kabupaten = trim((string)($pengaturan['regency_name']  ?? ''));
$luasText  = trim((string)($luasWilayah ?? ''));
$penduduk  = trim((string)($totalPenduduk ?? ''));
$totalKkText = trim((string)($totalKk ?? ''));
$profileLabel    = trim((string)($pengaturan['menu_profile_label'] ?? '')) ?: 'Profil';
$storyLabel      = trim((string)($pengaturan['menu_story_label']   ?? '')) ?: 'Sejarah';
$visionLabel     = trim((string)($pengaturan['menu_vision_label']  ?? '')) ?: 'Visi & Misi';
$structureLabel  = trim((string)($pengaturan['menu_structure_label'] ?? '')) ?: 'Struktur Organisasi';
$canonicalUrl = rtrim((string)BASE_URL,'/') . '/profil';
$breadcrumbs = [
  ['@type'=>'ListItem','position'=>1,'name'=>'Beranda','item'=>rtrim((string)BASE_URL,'/')],
  ['@type'=>'ListItem','position'=>2,'name'=>$profileLabel,'item'=>$canonicalUrl],
];
?>
<div class="profil-layout">
  <!-- Page Hero -->
  <div class="page-hero">
    <div class="container page-hero-content">
      <nav class="breadcrumb-nav"><a href="<?= base_url() ?>">Beranda</a><span class="sep"><i class="bi bi-chevron-right"></i></span><span class="current"><?= htmlspecialchars($storyLabel) ?></span></nav>
      <h1 class="page-title"><?= htmlspecialchars($profileLabel) ?> <?= htmlspecialchars($brandName) ?></h1>
      <p class="page-subtitle">Mengenal lebih dekat sejarah dan perjalanan <?= htmlspecialchars($brandName) ?>.</p>
    </div>
  </div>

  <div class="section">
    <div class="container">

      <!-- Facts Bar -->
      <?php if ($penduduk || $luasText || $kecamatan || $kabupaten): ?>
      <div class="profil-facts-bar mb-5" data-stagger-flip>
        <?php if ($luasText): ?>
        <div class="profil-fact"><span class="profil-fact-num"><?= htmlspecialchars($luasText) ?></span><span class="profil-fact-label">Luas Wilayah</span></div>
        <?php endif; ?>
        <?php if ($penduduk): ?>
        <div class="profil-fact"><span class="profil-fact-num"><?= number_format((int)$penduduk,0,',','.') ?></span><span class="profil-fact-label">Total Penduduk</span></div>
        <?php endif; ?>
        <?php if ($kecamatan): ?>
        <div class="profil-fact"><span class="profil-fact-num" style="font-size:var(--text-xl)"><?= htmlspecialchars($kecamatan) ?></span><span class="profil-fact-label">Kecamatan</span></div>
        <?php endif; ?>
        <?php if ($kabupaten): ?>
        <div class="profil-fact"><span class="profil-fact-num" style="font-size:var(--text-xl)"><?= htmlspecialchars($kabupaten) ?></span><span class="profil-fact-label">Kabupaten</span></div>
        <?php endif; ?>
      </div>
      <?php endif; ?>

      <div class="row g-5">
        <!-- Sidebar Nav -->
        <div class="col-lg-3">
          <div class="profil-nav-sidebar" data-reveal="left">
            <div class="profil-nav-header"><h6><?= htmlspecialchars($profileLabel) ?></h6></div>
            <ul class="profil-nav-links">
              <li><a href="<?= base_url('/profil') ?>" class="active"><i class="bi bi-book"></i> <?= htmlspecialchars($storyLabel) ?></a></li>
              <li><a href="<?= base_url('/profil/visi-misi') ?>"><i class="bi bi-eye"></i> <?= htmlspecialchars($visionLabel) ?></a></li>
              <li><a href="<?= base_url('/profil/struktur-organisasi') ?>"><i class="bi bi-diagram-3"></i> <?= htmlspecialchars($structureLabel) ?></a></li>
            </ul>
          </div>
        </div>

        <!-- Content -->
        <div class="col-lg-9">
          <div class="profil-content-card" data-reveal>
            <div class="profil-content-header">
              <h2><?= htmlspecialchars($storyLabel) ?> <?= htmlspecialchars($brandName) ?></h2>
            </div>
            <div class="profil-body">
              <?php if (!empty($sejarahText)): ?>
                <?= nl2br(htmlspecialchars($sejarahText)) ?>
              <?php else: ?>
              <div class="empty-state" style="padding:2rem 0">
                <i class="bi bi-book"></i>
                <p>Sejarah gampong belum tersedia. Silahkan tambahkan melalui panel admin.</p>
              </div>
              <?php endif; ?>
            </div>

            <!-- Contact Info -->
            <?php if ($alamat || $telepon || $email): ?>
            <div class="p-3 pt-0">
              <div class="row g-3">
                <?php if ($alamat): ?>
                <div class="col-sm-4"><div class="d-flex gap-2 p-3 rounded" style="background:var(--color-bg);border:1px solid var(--border)"><i class="bi bi-geo-alt-fill text-primary mt-1"></i><div><small style="font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--muted)">Alamat</small><p style="margin:0;font-size:.88rem"><?= nl2br(htmlspecialchars($alamat)) ?></p></div></div></div>
                <?php endif; ?>
                <?php if ($telepon): ?>
                <div class="col-sm-4"><div class="d-flex gap-2 p-3 rounded" style="background:var(--color-bg);border:1px solid var(--border)"><i class="bi bi-telephone-fill text-primary mt-1"></i><div><small style="font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--muted)">Telepon</small><p style="margin:0;font-size:.88rem"><?= htmlspecialchars($telepon) ?></p></div></div></div>
                <?php endif; ?>
                <?php if ($email): ?>
                <div class="col-sm-4"><div class="d-flex gap-2 p-3 rounded" style="background:var(--color-bg);border:1px solid var(--border)"><i class="bi bi-envelope-fill text-primary mt-1"></i><div><small style="font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--muted)">Email</small><p style="margin:0;font-size:.88rem"><?= htmlspecialchars($email) ?></p></div></div></div>
                <?php endif; ?>
              </div>
            </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
