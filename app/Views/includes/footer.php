<footer class="bg-dark text-white py-5 mt-5">
    <div class="container">
        <?php $brandName = trim((string) ($website_nama ?? ($desa_nama ?? 'Website'))); ?>
        <?php $menuProfileLabel = trim((string) ($pengaturan['menu_profile_label'] ?? '')) ?: 'Profil'; ?>
        <?php $footerDescription = trim((string) ($pengaturan['footer_brand_description'] ?? ($website_deskripsi ?? ''))); ?>
        <div class="row">
            <div class="col-md-4 mb-4">
                <h5 class="mb-3">Tentang Website</h5>
                <p class="text-muted"><?= htmlspecialchars($footerDescription) ?></p>
            </div>
            <div class="col-md-4 mb-4">
                <h5 class="mb-3">Tautan Cepat</h5>
                <ul class="list-unstyled">
                    <li><a href="<?= base_url('/profil') ?>" class="text-muted text-decoration-none"><?= htmlspecialchars($menuProfileLabel) ?></a></li>
                    <li><a href="<?= base_url('/berita') ?>" class="text-muted text-decoration-none">Berita</a></li>
                    <li><a href="<?= base_url('/galeri') ?>" class="text-muted text-decoration-none">Galeri</a></li>
                    <li><a href="<?= base_url('/kontak') ?>" class="text-muted text-decoration-none">Kontak</a></li>
                </ul>
            </div>
            <div class="col-md-4 mb-4">
                <h5 class="mb-3">Kontak</h5>
                <ul class="list-unstyled text-muted">
                    <li><i class="bi bi-geo-alt me-2"></i> Alamat Gampong</li>
                    <li><i class="bi bi-telephone me-2"></i> +62 123 4567 890</li>
                    <li><i class="bi bi-envelope me-2"></i> info@desa.id</li>
                </ul>
            </div>
        </div>
        <hr class="border-secondary">
        <div class="text-center text-muted">
            <small>&copy; <?= date('Y') ?> <?= htmlspecialchars($brandName) ?>.</small>
        </div>
    </div>
</footer>
