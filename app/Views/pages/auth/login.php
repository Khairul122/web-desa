<?php
$brandName = trim((string) ($desa_nama ?? '')) ?: (trim((string) ($website_nama ?? '')) ?: 'Website');
$brandDescription = trim((string) ($website_deskripsi ?? ''));
$logoUrl = !empty($logoDesa ?? '') ? upload_url((string) $logoDesa) : '';
$oldIdentifier = (string) old_input('identifier', '');
$oldRemember = (string) old_input('remember', '') === '1';
$alamatText = trim((string) ($alamatDesa ?? $desa_alamat ?? ''));
$teleponText = trim((string) ($teleponDesa ?? $desa_telepon ?? ''));
$emailText = trim((string) ($emailDesa ?? $desa_email ?? ''));
$jamLayanan = trim((string) ($pengaturan['office_hours_text'] ?? ''));
$loginVisualTitle = trim((string) ($pengaturan['login_visual_title'] ?? ''));
$loginFormDescription = trim((string) ($pengaturan['login_form_description'] ?? ''));
$todayLabel = (new DateTime('now'))->format('d M Y');
$hourNow = (int) date('G');
$greeting = 'Selamat Datang';
if ($hourNow < 11) {
    $greeting = 'Selamat Pagi';
} elseif ($hourNow < 15) {
    $greeting = 'Selamat Siang';
} elseif ($hourNow < 19) {
    $greeting = 'Selamat Sore';
} else {
    $greeting = 'Selamat Malam';
}
?>

<div class="auth-wrapper">
    <section class="auth-visual" aria-label="Informasi portal administrasi resmi">
        <div class="auth-orb-top"></div>
        <div class="auth-mesh" aria-hidden="true">
            <span class="auth-mesh-blob auth-mesh-blob-1"></span>
            <span class="auth-mesh-blob auth-mesh-blob-2"></span>
            <span class="auth-mesh-blob auth-mesh-blob-3"></span>
        </div>
        <div class="auth-visual-inner">
            <div class="auth-brand auth-brand--official auth-parallax-layer" data-parallax-depth="16">
                <?php if ($logoUrl !== ''): ?>
                <img src="<?= htmlspecialchars($logoUrl) ?>" alt="Logo <?= htmlspecialchars($brandName) ?>" class="auth-brand-logo" loading="lazy" decoding="async">
                <?php endif; ?>
                <div class="auth-brand-meta">
                    <small><?= htmlspecialchars($greeting) ?></small>
                    <strong><?= htmlspecialchars($brandName) ?></strong>
                </div>
            </div>

            <h1 class="auth-visual-title auth-parallax-layer" data-parallax-depth="14"><?= htmlspecialchars($loginVisualTitle !== '' ? $loginVisualTitle : ('Portal Administrasi Resmi ' . $brandName)) ?></h1>
            <p class="auth-visual-desc"><?= htmlspecialchars($brandDescription !== '' ? $brandDescription : ('Halaman autentikasi resmi untuk pengelolaan konten dan layanan administrasi ' . $brandName . '.')) ?></p>

            <div class="auth-live-grid auth-parallax-layer" data-parallax-depth="8" aria-label="Status sistem">
                <div class="auth-live-card">
                    <small>Status Sistem</small>
                    <strong><span class="auth-dot" aria-hidden="true"></span>Aktif</strong>
                </div>
                <div class="auth-live-card">
                    <small>Waktu Perangkat</small>
                    <strong id="authLiveClock">--:--:--</strong>
                </div>
                <div class="auth-live-card">
                    <small>Tanggal</small>
                    <strong><?= htmlspecialchars($todayLabel) ?></strong>
                </div>
                <div class="auth-live-card">
                    <small>Perangkat</small>
                    <strong id="authDeviceType">Memuat...</strong>
                </div>
            </div>

            <ul class="auth-official-points" aria-label="Keunggulan platform">
                <li>
                    <i class="bi bi-shield-check"></i>
                    <div>
                        <strong>Standar Keamanan Sistem</strong>
                        <span>Menerapkan proteksi CSRF, manajemen sesi, dan pengendalian akses berbasis peran.</span>
                    </div>
                </li>
                <li>
                    <i class="bi bi-journal-text"></i>
                    <div>
                        <strong>Panel Administrasi Terpadu</strong>
                        <span>Pengelolaan berita, galeri, profil, dan layanan dilakukan dalam satu sistem terpusat.</span>
                    </div>
                </li>
                <li>
                    <i class="bi bi-activity"></i>
                    <div>
                        <strong>Operasional Stabil</strong>
                        <span>Dioptimalkan untuk perangkat desktop dan mobile guna mendukung operasional harian.</span>
                    </div>
                </li>
            </ul>

            <div class="auth-office-meta">
                <?php if ($alamatText !== ''): ?>
                    <div class="auth-office-item"><i class="bi bi-geo-alt"></i><span><?= htmlspecialchars($alamatText) ?></span></div>
                <?php endif; ?>
                <?php if ($teleponText !== ''): ?>
                    <div class="auth-office-item"><i class="bi bi-telephone"></i><span><?= htmlspecialchars($teleponText) ?></span></div>
                <?php endif; ?>
                <?php if ($emailText !== ''): ?>
                    <div class="auth-office-item"><i class="bi bi-envelope"></i><span><?= htmlspecialchars($emailText) ?></span></div>
                <?php endif; ?>
                <?php if ($jamLayanan !== ''): ?>
                <div class="auth-office-item"><i class="bi bi-clock"></i><span><?= htmlspecialchars($jamLayanan) ?></span></div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <section class="auth-panel" aria-label="Form autentikasi sistem">
        <div class="auth-card auth-card-wide">
            <div class="auth-header">
                <div class="auth-header-icon auth-parallax-card" data-card-depth="5">
                    <i class="bi bi-person-lock fs-2"></i>
                </div>
                <h2>Autentikasi Akses Sistem</h2>
                <p class="text-muted"><?= htmlspecialchars($loginFormDescription !== '' ? $loginFormDescription : ('Silakan masuk menggunakan akun resmi yang terdaftar pada sistem ' . $brandName . '.')) ?></p>
            </div>

            <?php if (has_flash('success')): ?>
                <div class="alert alert-success d-flex align-items-center rounded-3 mb-4 auth-alert" role="alert">
                    <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                    <div><?= success() ?></div>
                </div>
            <?php endif; ?>

            <?php if (has_flash('error')): ?>
                <div class="alert alert-danger d-flex align-items-center rounded-3 mb-4 auth-alert" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                    <div><?= error() ?></div>
                </div>
            <?php endif; ?>

            <?php if (has_flash('errors')): ?>
                <div class="alert alert-danger rounded-3 mb-4 auth-alert" role="alert">
                    <div class="d-flex align-items-center mb-2">
                        <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                        <strong>Validasi Formulir:</strong>
                    </div>
                    <ul class="mb-0 ps-4 small">
                        <?php foreach ((array) flash('errors') as $fieldErrors): ?>
                            <?php foreach ((array) $fieldErrors as $message): ?>
                                <li><?= htmlspecialchars((string) $message) ?></li>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?= base_url('/login') ?>" class="auth-form needs-validation" novalidate>
                <?= csrf_field() ?>

                <div class="auth-field">
                    <label for="identifier" class="form-label text-dark fw-medium">Nama Pengguna atau Email Resmi</label>
                    <div class="auth-input-wrap">
                        <i class="bi bi-person auth-field-icon" aria-hidden="true"></i>
                        <input
                            type="text"
                            id="identifier"
                            name="identifier"
                            class="form-control"
                            value="<?= htmlspecialchars($oldIdentifier) ?>"
                            placeholder="Masukkan nama pengguna atau email resmi"
                            autocomplete="username"
                            required
                        >
                    </div>
                </div>

                <div class="auth-field">
                    <label for="password" class="form-label text-dark fw-medium">Password</label>
                    <div class="auth-input-wrap">
                        <i class="bi bi-key auth-field-icon" aria-hidden="true"></i>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-control"
                            placeholder="Masukkan kata sandi"
                            autocomplete="current-password"
                            required
                        >
                        <button type="button" class="auth-field-toggle" id="togglePassword" aria-label="Tampilkan atau sembunyikan kata sandi akun">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                    <small class="auth-field-hint" id="capsLockHint" hidden>
                        <i class="bi bi-exclamation-circle"></i> Perhatian: tombol Caps Lock aktif
                    </small>
                </div>

                <div class="auth-options">
                    <div class="form-check auth-check m-0">
                        <input class="form-check-input" type="checkbox" value="1" id="remember" name="remember" <?= $oldRemember ? 'checked' : '' ?>>
                        <label class="form-check-label small text-muted user-select-none" for="remember">Pertahankan sesi pada perangkat ini</label>
                    </div>
                    <span class="auth-meta-text">Koneksi aman dengan enkripsi TLS</span>
                </div>

                <button type="submit" class="btn auth-submit" id="loginSubmitButton">
                    Masuk ke Dashboard <i class="bi bi-box-arrow-in-right"></i>
                </button>

                <div class="text-center mt-4 pt-3 border-top">
                    <a href="<?= base_url() ?>" class="text-decoration-none small text-muted go-back-link"><i class="bi bi-arrow-left me-1"></i> Kembali ke Beranda Website Resmi</a>
                </div>
            </form>
        </div>
    </section>
</div>

<script>
window.AUTH_LOGIN_CONFIG = {
    ui: {
        submitVerifyingText: 'Memverifikasi kredensial akun...'
    },
    timing: {
        clockRefreshMs: 1000,
        timeThemeRefreshMs: 60000
    },
    breakpoints: {
        desktopParallaxMinWidth: 1100
    }
};
</script>
