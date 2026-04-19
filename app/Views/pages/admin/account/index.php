<?php
$item = is_array($account ?? null) ? $account : [];
?>

<section class="admin-dashboard">
    <article class="admin-panel" data-reveal>
        <header class="admin-panel__head">
            <h2>Profil Akun Admin</h2>
            <span>Kelola informasi akun yang sedang login</span>
        </header>

        <form method="POST" action="<?= base_url('/admin/profile') ?>" class="admin-form-grid">
            <?= csrf_field() ?>

            <div>
                <label class="form-label" for="acc-nama">Nama Lengkap</label>
                <input type="text" class="form-control" id="acc-nama" name="nama_lengkap" value="<?= htmlspecialchars((string) ($item['nama_lengkap'] ?? '')) ?>" required>
            </div>

            <div>
                <label class="form-label" for="acc-username">Username</label>
                <input type="text" class="form-control" id="acc-username" name="username" value="<?= htmlspecialchars((string) ($item['username'] ?? '')) ?>" required>
            </div>

            <div>
                <label class="form-label" for="acc-email">Email</label>
                <input type="email" class="form-control" id="acc-email" name="email" value="<?= htmlspecialchars((string) ($item['email'] ?? '')) ?>">
            </div>

            <div>
                <label class="form-label" for="acc-role">Role</label>
                <input type="text" class="form-control" id="acc-role" value="<?= htmlspecialchars(strtoupper((string) ($item['role'] ?? 'admin'))) ?>" readonly>
            </div>

            <div class="admin-form-grid__full">
                <label class="form-label" for="acc-password">Password Baru (opsional)</label>
                <input type="password" class="form-control" id="acc-password" name="password" placeholder="Isi jika ingin mengganti password">
            </div>

            <div class="admin-form-grid__actions">
                <button type="submit" class="btn admin-btn-primary"><i class="bi bi-save2"></i><span>Simpan Perubahan</span></button>
            </div>
        </form>
    </article>
</section>
