<?php
$items = is_array($statistik ?? null) ? $statistik : [];
?>

<section class="admin-dashboard">
    <article class="admin-panel" data-reveal>
        <header class="admin-panel__head">
            <h2>Daftar Statistik Gampong</h2>
            <a href="<?= base_url('/admin/statistik/create') ?>" class="btn btn-sm admin-btn-primary">
                <i class="bi bi-plus-circle"></i>
                <span>Tambah Statistik</span>
            </a>
        </header>

        <?php if (empty($items)): ?>
            <?php
            $emptyMessage = 'Belum ada data statistik. Tambahkan statistik agar halaman publik lebih informatif.';
            $emptyIcon = '';
            $emptyClass = 'admin-empty';
            require APP_PATH . '/Views/includes/ui/empty-state.php';
            ?>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table admin-table align-middle">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Statistik</th>
                            <th>Nilai</th>
                            <th>Icon</th>
                            <th>Urutan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $index => $item): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td><?= htmlspecialchars((string) ($item['nama_statistik'] ?? '-')) ?></td>
                                <td><?= htmlspecialchars((string) ($item['nilai_statistik'] ?? '-')) ?></td>
                                <td><?= htmlspecialchars(statistik_icon_label((string) ($item['icon'] ?? 'warga'))) ?></td>
                                <td><?= (int) ($item['urutan'] ?? 0) ?></td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="<?= base_url('/admin/statistik/edit/' . (int) ($item['id'] ?? 0)) ?>" class="btn btn-sm admin-btn-light admin-action-btn"><i class="bi bi-pencil-square"></i></a>
                                        <form method="POST" action="<?= base_url('/admin/statistik/delete/' . (int) ($item['id'] ?? 0)) ?>" class="js-confirm-submit" data-confirm-title="Hapus Statistik" data-confirm-message="Yakin ingin menghapus data statistik ini?">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-sm admin-btn-danger admin-action-btn"><i class="bi bi-trash3"></i></button>
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
