<?php
$mode = (string) ($mode ?? 'create');
$item = is_array($statistik_item ?? null) ? $statistik_item : [];
$isEdit = $mode === 'edit';
$action = $isEdit ? base_url('/admin/statistik/update/' . (int) ($item['id'] ?? 0)) : base_url('/admin/statistik');
$currentIcon = statistik_icon_key((string) ($item['icon'] ?? 'warga'));
$iconOptions = statistik_icon_definitions();
?>

<section class="admin-dashboard">
    <article class="admin-panel" data-reveal>
        <header class="admin-panel__head">
            <h2><?= $isEdit ? 'Edit Statistik Gampong' : 'Tambah Statistik Gampong' ?></h2>
            <a href="<?= base_url('/admin/statistik') ?>" class="btn btn-sm admin-btn-light"><i class="bi bi-arrow-left"></i><span>Kembali</span></a>
        </header>

        <form method="POST" action="<?= $action ?>" class="admin-form-grid">
            <?= csrf_field() ?>

            <div>
                <label class="form-label" for="stat-nama">Nama Statistik</label>
                <input type="text" class="form-control" id="stat-nama" name="nama_statistik" value="<?= htmlspecialchars((string) ($item['nama_statistik'] ?? '')) ?>" required>
            </div>

            <div>
                <label class="form-label" for="stat-nilai">Nilai Statistik</label>
                <input type="text" class="form-control" id="stat-nilai" name="nilai_statistik" value="<?= htmlspecialchars((string) ($item['nilai_statistik'] ?? '')) ?>" required>
            </div>

            <div>
                <label class="form-label" for="stat-icon">Icon</label>
                <select class="form-select" id="stat-icon" name="icon">
                    <?php foreach ($iconOptions as $iconKey => $meta): ?>
                        <option value="<?= htmlspecialchars((string) $iconKey) ?>" <?= $currentIcon === (string) $iconKey ? 'selected' : '' ?>>
                            <?= htmlspecialchars((string) ($meta['label'] ?? $iconKey)) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="form-label" for="stat-urutan">Urutan</label>
                <input type="number" class="form-control" id="stat-urutan" name="urutan" value="<?= (int) ($item['urutan'] ?? 0) ?>" min="0">
            </div>

            <div class="admin-form-grid__actions">
                <button type="submit" class="btn admin-btn-primary"><i class="bi bi-save2"></i><span><?= $isEdit ? 'Perbarui Statistik' : 'Simpan Statistik' ?></span></button>
            </div>
        </form>
    </article>
</section>
