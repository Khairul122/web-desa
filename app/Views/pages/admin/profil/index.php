<?php
$item = is_array($profil_data ?? null) ? $profil_data : [];
$id = (int) ($profil_id ?? ($item['id'] ?? 1));
?>

<section class="admin-dashboard">
    <article class="admin-panel" data-reveal>
        <header class="admin-panel__head">
            <h2>Profil Gampong</h2>
        </header>

        <form method="POST" action="<?= base_url('/admin/profil/update/' . $id) ?>" class="admin-form-grid">
            <?= csrf_field() ?>

            <div>
                <label class="form-label" for="profil-nama">Nama Gampong</label>
                <input type="text" id="profil-nama" name="nama_desa" class="form-control" value="<?= htmlspecialchars((string) ($item['nama_desa'] ?? '')) ?>" required>
            </div>

            <div>
                <label class="form-label" for="profil-telepon">Telepon</label>
                <input type="text" id="profil-telepon" name="telepon" class="form-control" value="<?= htmlspecialchars((string) ($item['telepon'] ?? '')) ?>">
            </div>

            <div>
                <label class="form-label" for="profil-email">Email</label>
                <input type="email" id="profil-email" name="email" class="form-control" value="<?= htmlspecialchars((string) ($item['email'] ?? '')) ?>">
            </div>

            <div class="admin-form-grid__full">
                <label class="form-label" for="profil-alamat">Alamat</label>
                <textarea id="profil-alamat" name="alamat" class="form-control" rows="2"><?= htmlspecialchars((string) ($item['alamat'] ?? '')) ?></textarea>
            </div>

            <div class="admin-form-grid__full">
                <label class="form-label" for="profil-sejarah">Sejarah</label>
                <textarea id="profil-sejarah" name="sejarah" class="form-control js-richtext" data-editor-height="360" data-editor-module="profil" rows="8"><?= htmlspecialchars((string) ($item['sejarah'] ?? '')) ?></textarea>
            </div>

            <div class="admin-form-grid__full">
                <label class="form-label" for="profil-visi-misi">Visi & Misi</label>
                <textarea id="profil-visi-misi" name="visi_misi" class="form-control js-richtext" data-editor-height="320" data-editor-module="profil" rows="7"><?= htmlspecialchars((string) ($item['visi_misi'] ?? '')) ?></textarea>
            </div>

            <div class="admin-form-grid__full">
                <label class="form-label" for="profil-struktur">Struktur Organisasi</label>
                <textarea id="profil-struktur" name="struktur_organisasi" class="form-control js-richtext" data-editor-height="320" data-editor-module="profil" rows="7"><?= htmlspecialchars((string) ($item['struktur_organisasi'] ?? '')) ?></textarea>
            </div>

            <div class="admin-form-grid__actions">
                <button type="submit" class="btn admin-btn-primary"><i class="bi bi-save2"></i>Simpan Profil Gampong</button>
            </div>
        </form>
    </article>
</section>
