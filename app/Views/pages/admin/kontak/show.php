<?php
$item = is_array($kontak_item ?? null) ? $kontak_item : [];
$id = (int) ($item['id'] ?? 0);
?>

<section class="admin-dashboard">
    <article class="admin-panel" data-reveal>
        <header class="admin-panel__head">
            <h2>Detail Pesan Kontak</h2>
            <a href="<?= base_url('/admin/kontak') ?>" class="btn btn-sm admin-btn-light"><i class="bi bi-arrow-left"></i><span>Kembali</span></a>
        </header>

        <ul class="admin-kontak-meta ps-0 mb-3">
            <li><strong>Nama:</strong> <?= htmlspecialchars((string) ($item['nama'] ?? '-')) ?></li>
            <li><strong>Email:</strong> <?= htmlspecialchars((string) ($item['email'] ?? '-')) ?></li>
            <li><strong>Telepon:</strong> <?= htmlspecialchars((string) ($item['telepon'] ?? '-')) ?></li>
            <li><strong>Subjek:</strong> <?= htmlspecialchars((string) ($item['subjek'] ?? '-')) ?></li>
            <li><strong>Tanggal:</strong> <?= htmlspecialchars(date_id((string) ($item['created_at'] ?? 'now'))) ?></li>
        </ul>

        <h3 class="h6">Isi Pesan</h3>
        <div class="admin-kontak-body mb-3"><?= htmlspecialchars((string) ($item['pesan'] ?? '-')) ?></div>

        <form method="POST" action="<?= base_url('/admin/kontak/status/' . $id) ?>" class="d-flex gap-2 flex-wrap">
            <?= csrf_field() ?>
            <select name="status" class="form-select admin-kontak-status-select">
                <?php foreach (['baru' => 'Baru', 'dibaca' => 'Dibaca', 'dibalas' => 'Dibalas'] as $value => $label): ?>
                    <option value="<?= $value ?>" <?= ((string) ($item['status'] ?? '') === $value) ? 'selected' : '' ?>><?= $label ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn admin-btn-primary"><i class="bi bi-check2-circle"></i><span>Perbarui Status</span></button>
        </form>
    </article>
</section>
