<?php
$mode = (string) ($mode ?? 'create');
$item = is_array($carousel_item ?? null) ? $carousel_item : [];
$isEdit = $mode === 'edit';
$action = $isEdit ? base_url('/admin/carousel/update/' . (int) ($item['id'] ?? 0)) : base_url('/admin/carousel');
?>

<section class="admin-dashboard">
    <article class="admin-panel" data-reveal>
        <header class="admin-panel__head">
            <h2><?= $isEdit ? 'Edit Slide Carousel' : 'Tambah Slide Carousel' ?></h2>
            <a href="<?= base_url('/admin/carousel') ?>" class="btn btn-sm admin-btn-light"><i class="bi bi-arrow-left"></i><span>Kembali</span></a>
        </header>

        <form method="POST" action="<?= $action ?>" enctype="multipart/form-data" class="admin-form-grid">
            <?= csrf_field() ?>

            <div>
                <label class="form-label" for="carousel-judul">Judul</label>
                <input type="text" class="form-control" id="carousel-judul" name="judul" value="<?= htmlspecialchars((string) ($item['judul'] ?? '')) ?>">
            </div>

            <div>
                <label class="form-label" for="carousel-urutan">Urutan</label>
                <input type="number" class="form-control" id="carousel-urutan" name="urutan" value="<?= (int) ($item['urutan'] ?? 0) ?>" min="0">
            </div>

            <div class="admin-form-grid__full">
                <label class="form-label" for="carousel-deskripsi">Deskripsi</label>
                <textarea class="form-control" id="carousel-deskripsi" name="deskripsi" rows="4"><?= htmlspecialchars((string) ($item['deskripsi'] ?? '')) ?></textarea>
            </div>

            <div class="admin-form-grid__full">
                <label class="form-label" for="carousel-gambar">Gambar Slide <?= $isEdit ? '(opsional)' : '' ?></label>
                <input type="file" class="form-control" id="carousel-gambar" name="gambar" accept="image/*" <?= $isEdit ? '' : 'required' ?>>
                <?php if ($isEdit && !empty($item['gambar'])): ?>
                    <small class="text-muted">Gambar saat ini: <?= htmlspecialchars((string) $item['gambar']) ?></small>
                <?php endif; ?>
            </div>

            <div class="admin-form-grid__full">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="1" id="carousel-active" name="is_active" <?= ((int) ($item['is_active'] ?? 1) === 1) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="carousel-active">Slide aktif</label>
                </div>
            </div>

            <div class="admin-form-grid__actions">
                <button type="submit" class="btn admin-btn-primary"><i class="bi bi-save2"></i><span><?= $isEdit ? 'Perbarui Slide' : 'Simpan Slide' ?></span></button>
            </div>
        </form>
    </article>
</section>
