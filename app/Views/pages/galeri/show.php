<?php
$item    = is_array($galeri_item ?? null) ? $galeri_item : [];
$title   = (string)($item['judul'] ?? 'Galeri');
$imgPath = (string)($item['gambar'] ?? '');
$imgUrl  = $imgPath !== '' ? upload_url('galeri/' . $imgPath) : '';
$cat     = (string)($item['kategori'] ?? '');
$desc    = (string)($item['deskripsi'] ?? '');
$views   = (int)($item['views'] ?? 0);
$og_title = $title; $og_image = $imgUrl; $og_type = 'article';
$canonicalUrl = rtrim((string)BASE_URL,'/') . '/galeri/' . ($item['slug'] ?? '');
$breadcrumbs = [
  ['@type'=>'ListItem','position'=>1,'name'=>'Beranda','item'=>rtrim((string)BASE_URL,'/')],
  ['@type'=>'ListItem','position'=>2,'name'=>'Galeri','item'=>rtrim((string)BASE_URL,'/'). '/galeri'],
  ['@type'=>'ListItem','position'=>3,'name'=>$title,'item'=>$canonicalUrl],
];
?>
<div class="galeri-layout">
  <div class="page-hero">
    <div class="container page-hero-content">
      <nav class="breadcrumb-nav" aria-label="Breadcrumb">
        <a href="<?= base_url() ?>">Beranda</a>
        <span class="sep"><i class="bi bi-chevron-right"></i></span>
        <a href="<?= base_url('/galeri') ?>">Galeri</a>
        <span class="sep"><i class="bi bi-chevron-right"></i></span>
        <span class="current"><?= htmlspecialchars(mb_strimwidth($title,0,40,'...')) ?></span>
      </nav>
      <h1 class="page-title"><?= htmlspecialchars($title) ?></h1>
      <?php if ($cat): ?><p class="page-subtitle"><?= htmlspecialchars($cat) ?></p><?php endif; ?>
    </div>
  </div>
  <div class="section">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-9" data-reveal="zoom">
          <?php if ($imgUrl): ?>
          <div style="border-radius:var(--radius-xl);overflow:hidden;box-shadow:var(--shadow-lg);border:1px solid var(--border);margin-bottom:1.5rem">
            <img src="<?= htmlspecialchars($imgUrl) ?>" alt="<?= htmlspecialchars($title) ?>" style="width:100%;max-height:620px;object-fit:contain;display:block;background:var(--color-dark)" loading="lazy">
          </div>
          <?php endif; ?>
          <?php if ($desc): ?>
          <div class="profil-content-card mb-4">
            <div class="profil-body" style="padding:1.75rem">
              <p style="font-size:1rem;line-height:1.85;color:var(--color-body);margin:0"><?= nl2br(htmlspecialchars($desc)) ?></p>
            </div>
          </div>
          <?php endif; ?>
          <div class="d-flex gap-3 mt-3 align-items-center flex-wrap">
            <a href="<?= base_url('/galeri') ?>" class="btn btn-primary-custom"><i class="bi bi-arrow-left"></i> Kembali ke Galeri</a>
            <?php if ($views): ?>
            <span style="font-size:.82rem;color:var(--muted)"><i class="bi bi-eye" style="color:var(--accent)"></i> <?= number_format($views,0,',','.') ?> dilihat</span>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
