<?php
$mode = (string) ($mode ?? 'edit');
$item = is_array($galeri_item ?? null) ? $galeri_item : [];
$isEdit = $mode === 'edit';
$action = $isEdit ? base_url('/admin/galeri/update/' . (int) ($item['id'] ?? 0)) : base_url('/admin/galeri');
?>

<section class="admin-dashboard admin-galeri-form-page">
    <article class="admin-panel" data-reveal>
        <header class="admin-panel__head">
            <h2><?= $isEdit ? 'Edit Media Dokumentasi' : 'Tambah Media Dokumentasi' ?></h2>
            <a href="<?= base_url('/admin/galeri') ?>" class="btn btn-sm admin-btn-light">
                <i class="bi bi-arrow-left"></i>
                <span>Kembali</span>
            </a>
        </header>

        <form method="POST" action="<?= $action ?>" enctype="multipart/form-data" class="admin-form-grid">
            <?= csrf_field() ?>

            <div class="admin-form-grid__full">
                <label class="form-label" for="galeri-judul">Judul</label>
                <input type="text" class="form-control" id="galeri-judul" name="judul" value="<?= htmlspecialchars((string) ($item['judul'] ?? '')) ?>" required>
            </div>

            <div>
                <label class="form-label" for="galeri-kategori">Kategori</label>
                <input type="text" class="form-control" id="galeri-kategori" name="kategori" value="<?= htmlspecialchars((string) ($item['kategori'] ?? '')) ?>">
            </div>

            <div>
                <label class="form-label" for="galeri-file">Upload Media Baru (opsional)</label>
                <input type="file" class="form-control" id="galeri-file" name="media_file" accept="image/*,video/*">
            </div>

            <div class="admin-form-grid__full">
                <label class="form-label" for="galeri-video-url">Atau URL Video Baru (opsional)</label>
                <input type="url" class="form-control" id="galeri-video-url" name="video_url" placeholder="https://youtube.com/...">
                <small class="text-muted">Biarkan kosong jika tidak ingin mengganti media.</small>
            </div>

            <?php if ($isEdit): ?>
                <div class="admin-form-grid__full">
                    <label class="form-label">Media Saat Ini</label>
                    <div class="admin-current-media">
                        <?php if (($item['media_type'] ?? '') === 'video'): ?>
                            <?php if (($item['is_external'] ?? false) === true): ?>
                                <a href="<?= htmlspecialchars((string) ($item['media_url'] ?? '#')) ?>" target="_blank" rel="noopener noreferrer" class="admin-media-card__external-link">
                                    <i class="bi bi-play-circle"></i>
                                    <span>Tonton Video Saat Ini</span>
                                </a>
                            <?php else: ?>
                                <video controls preload="metadata" src="<?= htmlspecialchars((string) ($item['media_url'] ?? '')) ?>" width="640" height="360"></video>
                            <?php endif; ?>
                        <?php else: ?>
                            <img src="<?= htmlspecialchars((string) ($item['media_url'] ?? '')) ?>" alt="<?= htmlspecialchars((string) ($item['judul'] ?? 'Media')) ?>" loading="lazy" decoding="async" width="960" height="720">
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <div class="admin-form-grid__full">
                <label class="form-label" for="galeri-deskripsi">Deskripsi</label>
                <textarea class="form-control" id="galeri-deskripsi" name="deskripsi" rows="5"><?= htmlspecialchars((string) ($item['deskripsi'] ?? '')) ?></textarea>
            </div>

            <div class="admin-form-grid__actions">
                <button type="submit" class="btn admin-btn-primary">
                    <i class="bi bi-save2"></i>
                    <span><?= $isEdit ? 'Perbarui Media' : 'Publikasikan Media' ?></span>
                </button>
            </div>
        </form>
    </article>
</section>
