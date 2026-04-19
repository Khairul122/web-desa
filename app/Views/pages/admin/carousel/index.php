<?php
$items = is_array($carousel ?? null) ? $carousel : [];
?>

<section class="admin-dashboard">
    <article class="admin-panel" data-reveal>
        <header class="admin-panel__head">
            <h2>Daftar Slide Beranda Gampong</h2>
            <a href="<?= base_url('/admin/carousel/create') ?>" class="btn btn-sm admin-btn-primary">
                <i class="bi bi-plus-circle"></i>
                <span>Tambah Slide</span>
            </a>
        </header>

        <?php if (empty($items)): ?>
            <?php
            $emptyMessage = 'Belum ada slide. Tambahkan slide untuk beranda Gampong.';
            $emptyIcon = '';
            $emptyClass = 'admin-empty';
            require APP_PATH . '/Views/includes/ui/empty-state.php';
            ?>
        <?php else: ?>
            <div class="admin-media-grid">
                <?php foreach ($items as $item): ?>
                    <?php $gambar = (string) ($item['gambar'] ?? ''); ?>
                    <article class="admin-media-card">
                        <div class="admin-media-card__preview">
                            <?php if ($gambar !== ''): ?>
                                <img src="<?= upload_url('carousel/' . $gambar) ?>" alt="<?= htmlspecialchars((string) ($item['judul'] ?? 'Slide')) ?>" loading="lazy" decoding="async" width="640" height="360">
                            <?php else: ?>
                                <div class="d-flex align-items-center justify-content-center w-100 h-100 text-muted"><i class="bi bi-image"></i></div>
                            <?php endif; ?>
                        </div>
                        <div class="admin-media-card__body">
                            <h3><?= htmlspecialchars((string) ($item['judul'] ?? 'Tanpa judul')) ?></h3>
                            <p><?= htmlspecialchars(limit((string) ($item['deskripsi'] ?? '-'), 90)) ?></p>
                            <div class="admin-media-card__meta">
                                <span class="status-badge <?= ((int) ($item['is_active'] ?? 0) === 1) ? 'is-publish' : 'is-draft' ?>">
                                    <?= ((int) ($item['is_active'] ?? 0) === 1) ? 'AKTIF' : 'NONAKTIF' ?>
                                </span>
                                <small>Urutan: <?= (int) ($item['urutan'] ?? 0) ?></small>
                            </div>
                            <form method="POST" action="<?= base_url('/admin/carousel/delete/' . (int) ($item['id'] ?? 0)) ?>" class="mt-2 js-confirm-submit" data-confirm-title="Hapus Slide" data-confirm-message="Hapus slide carousel ini sekarang?">
                                <?= csrf_field() ?>
                                <div class="d-flex gap-2">
                                    <a href="<?= base_url('/admin/carousel/edit/' . (int) ($item['id'] ?? 0)) ?>" class="btn btn-sm admin-btn-light"><i class="bi bi-pencil-square"></i></a>
                                    <button type="submit" class="btn btn-sm admin-btn-danger"><i class="bi bi-trash3"></i></button>
                                </div>
                            </form>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </article>
</section>
