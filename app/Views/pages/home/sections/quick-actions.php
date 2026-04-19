<?php
$hasWa    = $quickWhatsApp !== '';
$hasPhone = $quickPhone !== '';
$hasMap   = $quickLocation !== '';
$actions  = [];
if ($hasWa)    $actions[] = ['icon' => 'bi-whatsapp',       'label' => 'WhatsApp', 'url' => 'https://wa.me/' . preg_replace('/\D/', '', $quickWhatsApp)];
if ($hasPhone) $actions[] = ['icon' => 'bi-telephone-fill', 'label' => 'Telepon',  'url' => 'tel:' . preg_replace('/\s/', '', $quickPhone)];
if ($hasMap)   $actions[] = ['icon' => 'bi-geo-alt-fill',   'label' => 'Lokasi',   'url' => $quickLocation];
$actions[] = ['icon' => 'bi-newspaper',       'label' => 'Berita',  'url' => base_url('/berita')];
$actions[] = ['icon' => 'bi-images',          'label' => 'Galeri',  'url' => base_url('/galeri')];
$actions[] = ['icon' => 'bi-envelope-fill',   'label' => 'Kontak',  'url' => base_url('/kontak')];
$actions[] = ['icon' => 'bi-person-lines-fill','label' => 'Profil', 'url' => base_url('/profil')];
$actions   = array_slice($actions, 0, 4);
?>
<?php if (count($actions) >= 2): ?>
<div class="quick-actions-section">
  <div class="container">
    <div class="quick-actions-grid" data-stagger>
      <?php foreach ($actions as $act): ?>
      <a href="<?= htmlspecialchars($act['url']) ?>" class="quick-action-card"
         target="<?= str_starts_with($act['url'], 'http') ? '_blank' : '_self' ?>"
         rel="noopener noreferrer">
        <div class="qa-icon"><i class="bi <?= htmlspecialchars($act['icon']) ?>"></i></div>
        <span class="qa-label"><?= htmlspecialchars($act['label']) ?></span>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</div>
<?php endif; ?>
