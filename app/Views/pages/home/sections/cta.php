<section class="section cta-section" id="cta">
  <div class="cta-blob cta-blob-1" aria-hidden="true"></div>
  <div class="cta-blob cta-blob-2" aria-hidden="true"></div>
  <div class="container" style="position:relative;z-index:1">
    <div class="cta-content" data-reveal="zoom">
      <span class="section-label section-label--accent" style="margin-bottom:1.25rem">
        <i class="bi bi-hand-thumbs-up"></i> Bergabung Bersama Kami
      </span>
      <h2><?= htmlspecialchars(!empty($pengaturan['cta_title'] ?? '') ? (string)$pengaturan['cta_title'] : 'Bersama Wujudkan ' . $brandName . ' yang Maju') ?></h2>
      <p><?= htmlspecialchars(!empty($pengaturan['cta_description'] ?? '') ? (string)$pengaturan['cta_description'] : 'Sampaikan aspirasi, dukung program, dan jadilah bagian dari kemajuan gampong kita bersama.') ?></p>
      <div class="d-flex gap-3 justify-content-center flex-wrap">
        <a href="<?= base_url('/kontak') ?>" class="btn btn-accent btn-lg btn-magnetic">
          <i class="bi bi-envelope-fill"></i> Hubungi Sekarang
        </a>
        <?php if (!empty($whatsapp_number ?? '')): ?>
        <a href="https://wa.me/<?= preg_replace('/\D/', '', $whatsapp_number) ?>" class="btn btn-ghost-custom btn-lg" target="_blank" rel="noopener noreferrer">
          <i class="bi bi-whatsapp"></i> WhatsApp
        </a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>
