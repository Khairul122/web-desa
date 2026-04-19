<?php
$mode = (string) ($mode ?? 'create');
$item = is_array($berita_item ?? null) ? $berita_item : [];
$isEdit = $mode === 'edit';
$action = $isEdit ? base_url('/admin/berita/update/' . (int) ($item['id'] ?? 0)) : base_url('/admin/berita');
?>

<section class="admin-dashboard admin-berita-form-page">
    <article class="admin-panel" data-reveal>
        <header class="admin-panel__head">
            <h2><?= $isEdit ? 'Edit Publikasi' : 'Tambah Publikasi Baru' ?></h2>
            <a href="<?= base_url('/admin/berita') ?>" class="btn btn-sm admin-btn-light">
                <i class="bi bi-arrow-left"></i>
                <span>Kembali</span>
            </a>
        </header>

        <form method="POST" action="<?= $action ?>" enctype="multipart/form-data" class="admin-form-grid">
            <?= csrf_field() ?>

            <div class="admin-form-grid__full">
                <label class="form-label" for="berita-judul">Judul</label>
                <input type="text" class="form-control" id="berita-judul" name="judul" value="<?= htmlspecialchars((string) ($item['judul'] ?? '')) ?>" required>
            </div>

            <div>
                <label class="form-label" for="berita-kategori">Kategori</label>
                <input type="text" class="form-control" id="berita-kategori" name="kategori" value="<?= htmlspecialchars((string) ($item['kategori'] ?? '')) ?>">
            </div>

            <div>
                <label class="form-label" for="berita-status">Status</label>
                <select class="form-select" id="berita-status" name="status">
                    <option value="draft" <?= (($item['status'] ?? 'draft') === 'draft') ? 'selected' : '' ?>>Draf</option>
                    <option value="publish" <?= (($item['status'] ?? '') === 'publish') ? 'selected' : '' ?>>Terbit</option>
                </select>
            </div>

            <div class="admin-form-grid__full">
                <label class="form-label" for="berita-thumbnail">Thumbnail</label>
                <input type="file" class="form-control" id="berita-thumbnail" name="thumbnail" accept="image/*">
                <?php if ($isEdit && !empty($item['thumbnail'])): ?>
                    <small class="text-muted">Thumbnail saat ini: <?= htmlspecialchars((string) $item['thumbnail']) ?></small>
                <?php endif; ?>
            </div>

            <div class="admin-form-grid__full">
                <label class="form-label" for="berita-konten">Konten</label>
                <textarea class="form-control js-richtext" id="berita-konten" name="konten" rows="12" data-editor-height="420" data-editor-module="berita" required><?= htmlspecialchars((string) ($item['konten'] ?? '')) ?></textarea>
            </div>

            <div class="admin-form-grid__actions">
                <button type="submit" class="btn admin-btn-primary">
                    <i class="bi bi-save2"></i>
                    <span><?= $isEdit ? 'Perbarui Publikasi' : 'Simpan Publikasi' ?></span>
                </button>
            </div>
        </form>
    </article>
</section>
