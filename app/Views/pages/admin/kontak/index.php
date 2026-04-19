<?php
$items = is_array($kontak ?? null) ? $kontak : [];
$filters = is_array($filters ?? null) ? $filters : [];
?>

<section class="admin-dashboard">
    <article class="admin-panel" data-reveal>
        <header class="admin-panel__head">
            <h2>Pesan Kontak Masuk</h2>
        </header>

        <form method="GET" action="<?= base_url('/admin/kontak') ?>" class="admin-filter-grid">
            <div>
                <label class="form-label" for="kontak-status">Status</label>
                <select id="kontak-status" name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <?php foreach (['baru' => 'Baru', 'dibaca' => 'Dibaca', 'dibalas' => 'Dibalas'] as $value => $label): ?>
                        <option value="<?= $value ?>" <?= (($filters['status'] ?? '') === $value) ? 'selected' : '' ?>><?= $label ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="admin-filter-grid__search">
                <label class="form-label" for="kontak-q">Pencarian</label>
                <input type="text" id="kontak-q" class="form-control" name="q" value="<?= htmlspecialchars((string) ($filters['q'] ?? '')) ?>" placeholder="Cari nama, email, subjek, atau isi pesan...">
            </div>
            <div class="admin-filter-grid__actions">
                <button type="submit" class="btn admin-btn-primary"><i class="bi bi-search"></i><span>Cari</span></button>
                <a href="<?= base_url('/admin/kontak') ?>" class="btn admin-btn-light"><i class="bi bi-arrow-clockwise"></i><span>Reset</span></a>
            </div>
        </form>
    </article>

    <article class="admin-panel" data-reveal>
        <?php if (empty($items)): ?>
            <?php
            $emptyMessage = 'Belum ada pesan kontak yang masuk.';
            $emptyIcon = '';
            $emptyClass = 'admin-empty';
            require APP_PATH . '/Views/includes/ui/empty-state.php';
            ?>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table admin-table align-middle">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Subjek</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item): ?>
                            <tr>
                                <td><?= htmlspecialchars((string) ($item['nama'] ?? '-')) ?></td>
                                <td><?= htmlspecialchars((string) ($item['email'] ?? '-')) ?></td>
                                <td><?= htmlspecialchars(limit((string) ($item['subjek'] ?? '-'), 80)) ?></td>
                                <td>
                                    <span class="status-badge <?= ((string) ($item['status'] ?? 'baru') === 'baru') ? 'is-new' : 'is-read' ?>">
                                        <?= strtoupper(htmlspecialchars((string) ($item['status'] ?? 'baru'))) ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars(date_id((string) ($item['created_at'] ?? 'now'))) ?></td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="<?= base_url('/admin/kontak/show/' . (int) ($item['id'] ?? 0)) ?>" class="btn btn-sm admin-btn-light"><i class="bi bi-eye"></i></a>
                                        <form method="POST" action="<?= base_url('/admin/kontak/delete/' . (int) ($item['id'] ?? 0)) ?>" class="js-confirm-submit" data-confirm-title="Hapus Pesan" data-confirm-message="Hapus pesan kontak ini? Data yang dihapus tidak dapat dipulihkan.">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-sm admin-btn-danger"><i class="bi bi-trash3"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </article>
</section>
