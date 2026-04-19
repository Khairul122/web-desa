<nav class="navbar navbar-expand-lg navbar-light bg-white fixed-top shadow-sm">
    <div class="container">
        <?php $logoUrl = !empty($logoDesa ?? '') ? resolve_upload_url((string) $logoDesa) : ''; ?>
        <?php $profileMenuLabel = trim((string) ($pengaturan['menu_profile_label'] ?? '')) ?: 'Profil'; ?>
        <?php $storyMenuLabel = trim((string) ($pengaturan['menu_story_label'] ?? '')) ?: 'Profil'; ?>
        <?php $visionMenuLabel = trim((string) ($pengaturan['menu_vision_label'] ?? '')) ?: 'Visi & Misi'; ?>
        <?php $structureMenuLabel = trim((string) ($pengaturan['menu_structure_label'] ?? '')) ?: 'Struktur Organisasi'; ?>
        <a class="navbar-brand fw-bold" href="<?= base_url() ?>">
            <?php if ($logoUrl !== ''): ?>
            <img src="<?= htmlspecialchars($logoUrl) ?>" alt="Logo <?= htmlspecialchars((string) ($desa_nama ?? $website_nama ?? 'Desa')) ?>" width="40" height="40" class="me-2" decoding="async" fetchpriority="high">
            <?php endif; ?>
            <?= htmlspecialchars((string) ($desa_nama ?? $website_nama ?? 'Website')) ?>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link <?= is_active('/') ?>" href="<?= base_url() ?>">Beranda</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle <?= is_active('/profil') ?>" href="#" role="button" data-bs-toggle="dropdown">
                        <?= htmlspecialchars($profileMenuLabel) ?>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="<?= base_url('/profil') ?>"><?= htmlspecialchars($storyMenuLabel) ?></a></li>
                        <li><a class="dropdown-item" href="<?= base_url('/profil/visi-misi') ?>"><?= htmlspecialchars($visionMenuLabel) ?></a></li>
                        <li><a class="dropdown-item" href="<?= base_url('/profil/struktur-organisasi') ?>"><?= htmlspecialchars($structureMenuLabel) ?></a></li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= is_active('/berita') ?>" href="<?= base_url('/berita') ?>">Berita</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= is_active('/galeri') ?>" href="<?= base_url('/galeri') ?>">Galeri</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= is_active('/kontak') ?>" href="<?= base_url('/kontak') ?>">Kontak</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
