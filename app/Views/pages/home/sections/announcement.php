<?php if ($announcementActive && $announcementText !== ''): ?>
<div class="announcement-bar" role="alert" aria-label="Pengumuman penting">
  <div class="announcement-marquee">
    <?php for ($r = 0; $r < 4; $r++): ?>
    <span>
      <i class="bi bi-megaphone-fill"></i>
      <?= htmlspecialchars($announcementText) ?>
      <?php if (!empty($announcementLink)): ?>
        &nbsp;— <a href="<?= htmlspecialchars($announcementLink) ?>">Selengkapnya</a>
      <?php endif; ?>
      &nbsp;&nbsp;&nbsp;
    </span>
    <?php endfor; ?>
  </div>
</div>
<?php endif; ?>
