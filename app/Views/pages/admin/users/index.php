<?php
$items = is_array($users ?? null) ? $users : [];
?>

<section class="admin-dashboard">
    <article class="admin-panel" data-reveal>
        <header class="admin-panel__head">
            <h2>Daftar Pengguna</h2>
            <a href="<?= base_url('/admin/users/create') ?>" class="btn btn-sm admin-btn-primary">
                <i class="bi bi-plus-circle"></i>
                <span>Tambah Pengguna</span>
            </a>
        </header>

        <?php if (empty($items)): ?>
            <?php
            $emptyMessage = 'Belum ada pengguna terdaftar.';
            $emptyIcon = '';
            $emptyClass = 'admin-empty';
            require APP_PATH . '/Views/includes/ui/empty-state.php';
            ?>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table admin-table align-middle">
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Nama Lengkap</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item): ?>
                            <tr>
                                <td><?= htmlspecialchars((string) ($item['username'] ?? '-')) ?></td>
                                <td><?= htmlspecialchars((string) ($item['nama_lengkap'] ?? '-')) ?></td>
                                <td><?= htmlspecialchars((string) ($item['email'] ?? '-')) ?></td>
                                <td><span class="status-badge is-read"><?= strtoupper(htmlspecialchars((string) ($item['role'] ?? 'author'))) ?></span></td>
                                <td>
                                    <span class="status-badge <?= ((int) ($item['is_active'] ?? 0) === 1) ? 'is-publish' : 'is-draft' ?>">
                                        <?= ((int) ($item['is_active'] ?? 0) === 1) ? 'AKTIF' : 'NONAKTIF' ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="<?= base_url('/admin/users/edit/' . (int) ($item['id'] ?? 0)) ?>" class="btn btn-sm admin-btn-light admin-action-btn"><i class="bi bi-pencil-square"></i></a>
                                        <form method="POST" action="<?= base_url('/admin/users/delete/' . (int) ($item['id'] ?? 0)) ?>" class="js-confirm-submit" data-confirm-title="Hapus Pengguna" data-confirm-message="Yakin ingin menghapus pengguna ini?">
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
