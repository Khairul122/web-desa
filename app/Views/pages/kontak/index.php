<?php
$brandName  = trim((string)($desa_nama ?? '')) ?: (trim((string)($website_nama ?? '')) ?: 'Website');
$alamat     = trim((string)($alamatDesa ?? ($desa_alamat ?? '')));
$telepon    = trim((string)($teleponDesa ?? ($desa_telepon ?? '')));
$emailDesa2 = trim((string)($emailDesa ?? ($desa_email ?? '')));
$waNum      = trim((string)($whatsapp_number ?? ''));
$jamKantor  = trim((string)($pengaturan['office_hours_text'] ?? ''));
$waLink     = $waNum !== '' ? 'https://wa.me/' . preg_replace('/\D/', '', $waNum) : '';
$formErrors = (array)(flash('errors') ?? []);
$oldName    = htmlspecialchars((string)(old('nama') ?? ''));
$oldEmail   = htmlspecialchars((string)(old('email') ?? ''));
$oldSubjek  = htmlspecialchars((string)(old('subjek') ?? ''));
$oldPesan   = htmlspecialchars((string)(old('pesan') ?? ''));
$canonicalUrl = rtrim((string)BASE_URL, '/') . '/kontak';
$breadcrumbs  = [
    ['@type' => 'ListItem', 'position' => 1, 'name' => 'Beranda', 'item' => rtrim((string)BASE_URL, '/')],
    ['@type' => 'ListItem', 'position' => 2, 'name' => 'Kontak',  'item' => $canonicalUrl],
];
?>
<div class="kontak-layout">
  <!-- Page Hero -->
  <div class="page-hero">
    <div class="container page-hero-content">
      <nav class="breadcrumb-nav" aria-label="Breadcrumb">
        <a href="<?= base_url() ?>">Beranda</a>
        <span class="sep"><i class="bi bi-chevron-right"></i></span>
        <span class="current">Kontak</span>
      </nav>
      <h1 class="page-title">Hubungi Kami</h1>
      <p class="page-subtitle">Kami siap membantu dan mendengar aspirasi seluruh warga.</p>
    </div>
  </div>

  <div class="section">
    <div class="container">

      <!-- Flash Messages -->
      <?php $successMsg = flash('success'); $errorMsg = flash('error'); ?>
      <?php if (!empty($successMsg)): ?>
      <div class="alert mb-4 d-flex align-items-center gap-2" role="alert" style="border-radius:var(--radius);border:1px solid rgba(16,185,129,.3);background:rgba(16,185,129,.08);color:#065f46;padding:1rem 1.25rem">
        <i class="bi bi-check-circle-fill"></i> <?= htmlspecialchars((string)$successMsg) ?>
      </div>
      <?php endif; ?>
      <?php if (!empty($errorMsg)): ?>
      <div class="alert mb-4 d-flex align-items-center gap-2" role="alert" style="border-radius:var(--radius);border:1px solid rgba(239,68,68,.3);background:rgba(239,68,68,.08);color:#991b1b;padding:1rem 1.25rem">
        <i class="bi bi-exclamation-circle-fill"></i> <?= htmlspecialchars((string)$errorMsg) ?>
      </div>
      <?php endif; ?>

      <div class="row g-4">
        <!-- Info Card -->
        <div class="col-lg-5">
          <div class="kontak-info-card">
            <div class="kontak-info-content">
              <h3>Informasi Kontak</h3>
              <p>Jangan ragu untuk menghubungi kami kapan saja.</p>
              <ul class="kontak-info-list">
                <li class="kontak-info-item">
                  <div class="kontak-info-icon"><i class="bi bi-geo-alt-fill"></i></div>
                  <div class="kontak-info-text">
                    <strong>Alamat</strong>
                    <span><?= !empty($alamat) ? nl2br(htmlspecialchars($alamat)) : 'Informasi alamat belum tersedia.' ?></span>
                  </div>
                </li>

                <li class="kontak-info-item">
                  <div class="kontak-info-icon"><i class="bi bi-telephone-fill"></i></div>
                  <div class="kontak-info-text">
                    <strong>Telepon / WhatsApp</strong>
                    <span>
                      <?php if (!empty($telepon) || !empty($waNum)): ?>
                        <?= htmlspecialchars($telepon ?: $waNum) ?>
                        <?php if (!empty($waLink)): ?>
                        &nbsp;<a href="<?= htmlspecialchars($waLink) ?>" target="_blank" rel="noopener" style="color:var(--accent);font-size:.8rem"><i class="bi bi-whatsapp"></i> Chat</a>
                        <?php endif; ?>
                      <?php else: ?>
                        Nomor telepon belum tersedia.
                      <?php endif; ?>
                    </span>
                  </div>
                </li>

                <li class="kontak-info-item">
                  <div class="kontak-info-icon"><i class="bi bi-envelope-fill"></i></div>
                  <div class="kontak-info-text">
                    <strong>Email</strong>
                    <span><?= !empty($emailDesa2) ? htmlspecialchars($emailDesa2) : 'Email belum tersedia.' ?></span>
                  </div>
                </li>

                <?php if (!empty($jamKantor)): ?>
                <li class="kontak-info-item">
                  <div class="kontak-info-icon"><i class="bi bi-clock-fill"></i></div>
                  <div class="kontak-info-text">
                    <strong>Jam Kantor</strong>
                    <span><?= nl2br(htmlspecialchars($jamKantor)) ?></span>
                  </div>
                </li>
                <?php else: ?>
                <li class="kontak-info-item">
                  <div class="kontak-info-icon"><i class="bi bi-clock-fill"></i></div>
                  <div class="kontak-info-text">
                    <strong>Jam Kantor</strong>
                    <span>Senin – Jumat, 08.00 – 16.00 WIB</span>
                  </div>
                </li>
                <?php endif; ?>
              </ul>
            </div>
          </div>
        </div>

        <!-- Form Card -->
        <div class="col-lg-7">
          <div class="kontak-form-card">
            <h3>Kirim Pesan</h3>
            <p>Isi formulir berikut dan kami akan merespons sesegera mungkin.</p>
            <form action="<?= base_url('/kontak') ?>" method="POST" novalidate>
              <?= csrf_field() ?>
              <div class="row g-3">
                <div class="col-sm-6 form-group">
                  <label for="nama" class="form-label">Nama Lengkap <span style="color:var(--accent)">*</span></label>
                  <input type="text" id="nama" name="nama" class="form-control <?= isset($formErrors['nama']) ? 'is-invalid' : '' ?>" value="<?= $oldName ?>" placeholder="Nama Anda" required>
                  <?php if (isset($formErrors['nama'])): ?><div class="invalid-feedback"><?= htmlspecialchars((string)$formErrors['nama']) ?></div><?php endif; ?>
                </div>
                <div class="col-sm-6 form-group">
                  <label for="email" class="form-label">Email <span style="color:var(--accent)">*</span></label>
                  <input type="email" id="email" name="email" class="form-control <?= isset($formErrors['email']) ? 'is-invalid' : '' ?>" value="<?= $oldEmail ?>" placeholder="email@contoh.com" required>
                  <?php if (isset($formErrors['email'])): ?><div class="invalid-feedback"><?= htmlspecialchars((string)$formErrors['email']) ?></div><?php endif; ?>
                </div>
                <div class="col-12 form-group">
                  <label for="subjek" class="form-label">Subjek <span style="color:var(--accent)">*</span></label>
                  <input type="text" id="subjek" name="subjek" class="form-control <?= isset($formErrors['subjek']) ? 'is-invalid' : '' ?>" value="<?= $oldSubjek ?>" placeholder="Perihal pesan" required>
                  <?php if (isset($formErrors['subjek'])): ?><div class="invalid-feedback"><?= htmlspecialchars((string)$formErrors['subjek']) ?></div><?php endif; ?>
                </div>
                <div class="col-12 form-group">
                  <label for="pesan" class="form-label">Pesan <span style="color:var(--accent)">*</span></label>
                  <textarea id="pesan" name="pesan" class="form-control <?= isset($formErrors['pesan']) ? 'is-invalid' : '' ?>" rows="5" placeholder="Tulis pesan Anda..." required><?= $oldPesan ?></textarea>
                  <?php if (isset($formErrors['pesan'])): ?><div class="invalid-feedback"><?= htmlspecialchars((string)$formErrors['pesan']) ?></div><?php endif; ?>
                </div>
                <div class="col-12">
                  <button type="submit" class="btn btn-primary-custom btn-lg btn-magnetic" style="width:100%;justify-content:center">
                    <i class="bi bi-send-fill"></i> Kirim Pesan
                  </button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>
