<header class="admin-topbar">
    <div class="admin-topbar__left">
        <button id="sidebarToggle" class="admin-topbar__menu-btn d-lg-none" type="button" aria-label="Buka sidebar">
            <i class="bi bi-list"></i>
        </button>
        <div>
            <h1 class="admin-topbar__title"><?= htmlspecialchars((string) ($title ?? 'Beranda Admin')) ?></h1>
        </div>
    </div>

    <div class="admin-topbar__right">
        <div class="dropdown admin-user-dropdown">
            <button
                class="btn admin-user-chip dropdown-toggle"
                type="button"
                data-bs-toggle="dropdown"
                aria-expanded="false"
            >
                <span class="admin-user-chip__avatar">
                    <?= strtoupper(substr((string) ($user_name ?? 'A'), 0, 1)) ?>
                </span>
                <div class="admin-user-chip__meta">
                    <strong><?= htmlspecialchars((string) ($user_name ?? 'Administrator')) ?></strong>
                    <small><?= htmlspecialchars(strtoupper((string) ($user_role ?? 'ADMIN'))) ?></small>
                </div>
            </button>
            <ul class="dropdown-menu dropdown-menu-end admin-user-menu">
                <li>
                    <a class="dropdown-item" href="<?= base_url('/admin/profile') ?>">
                        <i class="bi bi-person-circle me-2"></i>Profil
                    </a>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form action="<?= base_url('/logout') ?>" method="POST" class="m-0">
                        <?= csrf_field() ?>
                        <button class="dropdown-item text-danger" type="submit">
                            <i class="bi bi-box-arrow-right me-2"></i>Keluar
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</header>
