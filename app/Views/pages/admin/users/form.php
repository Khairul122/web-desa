<?php
$mode = (string) ($mode ?? 'create');
$item = is_array($user_item ?? null) ? $user_item : [];
$isEdit = $mode === 'edit';
$action = $isEdit ? base_url('/admin/users/update/' . (int) ($item['id'] ?? 0)) : base_url('/admin/users');
?>

<section class="admin-dashboard">
    <article class="admin-panel" data-reveal>
        <header class="admin-panel__head">
            <h2><?= $isEdit ? 'Edit Pengguna' : 'Tambah Pengguna' ?></h2>
            <a href="<?= base_url('/admin/users') ?>" class="btn btn-sm admin-btn-light"><i class="bi bi-arrow-left"></i><span>Kembali</span></a>
        </header>

        <form method="POST" action="<?= $action ?>" class="admin-form-grid">
            <?= csrf_field() ?>

            <div>
                <label class="form-label" for="user-username">Username</label>
                <input type="text" class="form-control" id="user-username" name="username" value="<?= htmlspecialchars((string) ($item['username'] ?? '')) ?>" required>
            </div>

            <div>
                <label class="form-label" for="user-nama">Nama Lengkap</label>
                <input type="text" class="form-control" id="user-nama" name="nama_lengkap" value="<?= htmlspecialchars((string) ($item['nama_lengkap'] ?? '')) ?>" required>
            </div>

            <div>
                <label class="form-label" for="user-email">Email</label>
                <input type="email" class="form-control" id="user-email" name="email" value="<?= htmlspecialchars((string) ($item['email'] ?? '')) ?>">
            </div>

            <div>
                <label class="form-label" for="user-role">Role</label>
                <select class="form-select" id="user-role" name="role">
                    <?php foreach (['admin' => 'Admin', 'editor' => 'Editor', 'author' => 'Author'] as $val => $label): ?>
                        <option value="<?= $val ?>" <?= ((string) ($item['role'] ?? 'author') === $val) ? 'selected' : '' ?>><?= $label ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="admin-form-grid__full">
                <label class="form-label" for="user-password">Password <?= $isEdit ? '(kosongkan jika tidak diubah)' : '' ?></label>
                <input type="password" class="form-control" id="user-password" name="password" <?= $isEdit ? '' : 'required' ?>>
            </div>

            <div class="admin-form-grid__full">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="user-active" name="is_active" value="1" <?= ((int) ($item['is_active'] ?? 1) === 1) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="user-active">Status aktif</label>
                </div>
            </div>

            <div class="admin-form-grid__actions">
                <button type="submit" class="btn admin-btn-primary"><i class="bi bi-save2"></i><span><?= $isEdit ? 'Perbarui Pengguna' : 'Simpan Pengguna' ?></span></button>
            </div>
        </form>
    </article>
</section>
