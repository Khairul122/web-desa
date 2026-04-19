<?php
$currentPage = (string) ($page ?? '');
$menuItems = [
    [
        'label' => 'Beranda Admin',
        'icon' => 'bi-speedometer2',
        'url' => base_url('/admin/dashboard'),
        'active' => $currentPage === 'admin-dashboard',
    ],
    [
        'label' => 'Manajemen Berita',
        'icon' => 'bi-newspaper',
        'url' => base_url('/admin/berita'),
        'active' => $currentPage === 'admin-berita',
    ],
    [
        'label' => 'Manajemen Galeri',
        'icon' => 'bi-images',
        'url' => base_url('/admin/galeri'),
        'active' => $currentPage === 'admin-galeri',
    ],
    [
        'label' => 'Carousel Beranda',
        'icon' => 'bi-sliders2-horizontal',
        'url' => base_url('/admin/carousel'),
        'active' => $currentPage === 'admin-carousel',
    ],
    [
        'label' => 'Statistik Gampong',
        'icon' => 'bi-bar-chart-line',
        'url' => base_url('/admin/statistik'),
        'active' => $currentPage === 'admin-statistik',
    ],
    [
        'label' => 'Pesan Kontak',
        'icon' => 'bi-envelope',
        'url' => base_url('/admin/kontak'),
        'active' => $currentPage === 'admin-kontak',
    ],
    [
        'label' => 'Profil',
        'icon' => 'bi-bank',
        'url' => base_url('/admin/profil'),
        'active' => $currentPage === 'admin-profil',
    ],
    [
        'label' => 'Pengaturan Website',
        'icon' => 'bi-gear',
        'url' => base_url('/admin/pengaturan'),
        'active' => $currentPage === 'admin-pengaturan',
    ],
    [
        'label' => 'Manajemen Pengguna',
        'icon' => 'bi-people',
        'url' => base_url('/admin/users'),
        'active' => $currentPage === 'admin-users',
    ],
];
?>

<aside id="adminSidebar" class="admin-sidebar" aria-label="Sidebar admin">
    <div class="admin-sidebar__brand">
        <a href="<?= base_url('/admin/dashboard') ?>" class="admin-brand-link">
            <?php if (!empty($logoDesa ?? '')): ?>
            <span class="admin-brand-logo"><img src="<?= htmlspecialchars(upload_url((string) $logoDesa)) ?>" alt="Logo" width="24" height="24"></span>
            <?php else: ?>
            <span class="admin-brand-logo"><i class="bi bi-buildings"></i></span>
            <?php endif; ?>
            <span>
                <strong><?= htmlspecialchars((string) ($website_nama ?? 'Sistem Website')) ?></strong>
                <small>Panel Admin</small>
            </span>
        </a>
        <button id="sidebarClose" class="admin-sidebar__close d-lg-none" type="button" aria-label="Tutup sidebar">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>

    <nav class="admin-sidebar__menu">
        <?php foreach ($menuItems as $item): ?>
            <a href="<?= htmlspecialchars($item['url']) ?>" class="admin-menu-link <?= $item['active'] ? 'is-active' : '' ?>">
                <i class="bi <?= htmlspecialchars($item['icon']) ?>"></i>
                <span><?= htmlspecialchars($item['label']) ?></span>
            </a>
        <?php endforeach; ?>
    </nav>

    <div class="admin-sidebar__footer">
        <small><?= htmlspecialchars((string) ($desa_nama ?? $website_nama ?? '')) ?></small>
    </div>
</aside>

<div id="adminSidebarBackdrop" class="admin-sidebar-backdrop d-lg-none"></div>
