<?php
$settings = is_array($settings ?? null) ? $settings : [];

$iconOptions = [
    'beranda' => 'Beranda',
    'informasi' => 'Informasi',
    'berita' => 'Berita',
    'galeri' => 'Galeri',
    'kontak' => 'Kontak',
    'telepon' => 'Telepon',
    'whatsapp' => 'WhatsApp',
    'lokasi' => 'Lokasi',
    'dokumen' => 'Dokumen',
    'layanan' => 'Layanan',
    'pengumuman' => 'Pengumuman',
    'pengaturan' => 'Pengaturan',
];

$resolveIconKey = static function (string $raw): string {
    $value = strtolower(trim($raw));
    if ($value === '') {
        return 'layanan';
    }

    $legacyMap = [
        'bi bi-house-door' => 'beranda',
        'bi bi-house-door-fill' => 'beranda',
        'bi bi-info-circle' => 'informasi',
        'bi bi-newspaper' => 'berita',
        'bi bi-images' => 'galeri',
        'bi bi-envelope' => 'kontak',
        'bi bi-telephone' => 'telepon',
        'bi bi-whatsapp' => 'whatsapp',
        'bi bi-geo-alt' => 'lokasi',
        'bi bi-file-earmark-text' => 'dokumen',
        'bi bi-grid' => 'layanan',
        'bi bi-megaphone' => 'pengumuman',
        'bi bi-gear' => 'pengaturan',
    ];

    return $legacyMap[$value] ?? $value;
};
?>

<section class="admin-dashboard">
    <article class="admin-panel" data-reveal>
        <header class="admin-panel__head">
            <h2>Pengaturan Website</h2>
        </header>

        <form method="POST" action="<?= base_url('/admin/pengaturan') ?>" enctype="multipart/form-data" class="admin-form-grid">
            <?= csrf_field() ?>

            <div>
                <label class="form-label" for="set-nama">Nama Portal Gampong</label>
                <input type="text" id="set-nama" name="website_nama" class="form-control" value="<?= htmlspecialchars((string) ($settings['website_nama'] ?? '')) ?>">
            </div>

            <div>
                <label class="form-label" for="set-wa">Nomor WhatsApp</label>
                <input type="text" id="set-wa" name="whatsapp_number" class="form-control" value="<?= htmlspecialchars((string) ($settings['whatsapp_number'] ?? '')) ?>">
            </div>

            <div>
                <label class="form-label" for="set-office-hours">Jam Operasional Footer</label>
                <input type="text" id="set-office-hours" name="office_hours_text" class="form-control" value="<?= htmlspecialchars((string) ($settings['office_hours_text'] ?? '')) ?>" placeholder="Opsional, contoh: Senin - Jumat, 08.00 - 16.00 WIB">
            </div>

            <div>
                <label class="form-label" for="set-lokasi">URL Lokasi (Google Maps)</label>
                <input type="url" id="set-lokasi" name="quick_location_url" class="form-control" value="<?= htmlspecialchars((string) ($settings['quick_location_url'] ?? '')) ?>">
            </div>

            <div class="admin-form-grid__full">
                <label class="form-label" for="set-deskripsi">Deskripsi Gampong</label>
                <textarea id="set-deskripsi" name="website_deskripsi" class="form-control" rows="3"><?= htmlspecialchars((string) ($settings['website_deskripsi'] ?? '')) ?></textarea>
            </div>

            <div class="admin-form-grid__full">
                <label class="form-label" for="set-footer-desc">Deskripsi Footer</label>
                <textarea id="set-footer-desc" name="footer_brand_description" class="form-control" rows="2" placeholder="Opsional, tampil di footer website"><?= htmlspecialchars((string) ($settings['footer_brand_description'] ?? '')) ?></textarea>
            </div>

            <div class="admin-form-grid__full">
                <label class="form-label" for="set-keywords">Keywords</label>
                <input type="text" id="set-keywords" name="website_keywords" class="form-control" value="<?= htmlspecialchars((string) ($settings['website_keywords'] ?? '')) ?>">
            </div>

            <div class="admin-form-grid__full">
                <h3 class="h6 mb-2">Banner Pengumuman Penting</h3>
            </div>

            <div class="admin-form-grid__full">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="set-ann-active" name="important_announcement_active" value="1" <?= (($settings['important_announcement_active'] ?? '0') === '1') ? 'checked' : '' ?>>
                    <label class="form-check-label" for="set-ann-active">Aktifkan Banner Pengumuman</label>
                </div>
            </div>

            <div class="admin-form-grid__full">
                <label class="form-label" for="set-ann-text">Teks Pengumuman</label>
                <textarea id="set-ann-text" name="important_announcement_text" class="form-control" rows="2"><?= htmlspecialchars((string) ($settings['important_announcement_text'] ?? '')) ?></textarea>
            </div>

            <div class="admin-form-grid__full">
                <label class="form-label" for="set-ann-link">Link Pengumuman</label>
                <input type="url" id="set-ann-link" name="important_announcement_link" class="form-control" value="<?= htmlspecialchars((string) ($settings['important_announcement_link'] ?? '')) ?>">
            </div>

            <div class="admin-form-grid__full">
                <h3 class="h6 mb-2">Peta Lokasi Landing Page</h3>
            </div>

            <div>
                <label class="form-label" for="set-map-title">Judul Bagian Peta</label>
                <input type="text" id="set-map-title" name="map_section_title" class="form-control" value="<?= htmlspecialchars((string) ($settings['map_section_title'] ?? '')) ?>">
            </div>

            <div class="admin-form-grid__full">
                <label class="form-label" for="set-map-desc">Deskripsi Bagian Peta</label>
                <textarea id="set-map-desc" name="map_section_description" class="form-control" rows="2"><?= htmlspecialchars((string) ($settings['map_section_description'] ?? '')) ?></textarea>
            </div>

            <div class="admin-form-grid__full">
                <label class="form-label" for="set-map-embed">Link Embed Google Maps</label>
                <textarea id="set-map-embed" name="map_embed_url" class="form-control" rows="3"><?= htmlspecialchars((string) ($settings['map_embed_url'] ?? '')) ?></textarea>
            </div>

            <div class="admin-form-grid__full">
                <h3 class="h6 mb-2">Layanan Populer</h3>
            </div>

            <?php for ($i = 1; $i <= 4; $i++): ?>
                <div>
                    <label class="form-label" for="service-<?= $i ?>-label">Label Layanan <?= $i ?></label>
                    <input type="text" id="service-<?= $i ?>-label" name="popular_service_<?= $i ?>_label" class="form-control" value="<?= htmlspecialchars((string) ($settings['popular_service_' . $i . '_label'] ?? '')) ?>">
                </div>
                <div>
                    <label class="form-label" for="service-<?= $i ?>-icon">Icon Layanan <?= $i ?></label>
                    <?php $selectedIcon = $resolveIconKey((string) ($settings['popular_service_' . $i . '_icon'] ?? 'layanan')); ?>
                    <select id="service-<?= $i ?>-icon" name="popular_service_<?= $i ?>_icon" class="form-select">
                        <?php foreach ($iconOptions as $iconKey => $iconLabel): ?>
                            <option value="<?= htmlspecialchars($iconKey) ?>" <?= $selectedIcon === $iconKey ? 'selected' : '' ?>><?= htmlspecialchars($iconLabel) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="admin-form-grid__full">
                    <label class="form-label" for="service-<?= $i ?>-url">URL Layanan <?= $i ?></label>
                    <input type="text" id="service-<?= $i ?>-url" name="popular_service_<?= $i ?>_url" class="form-control" value="<?= htmlspecialchars((string) ($settings['popular_service_' . $i . '_url'] ?? '')) ?>">
                </div>
            <?php endfor; ?>

            <div class="admin-form-grid__full">
                <h3 class="h6 mb-2">Layanan Unggulan Beranda</h3>
            </div>

            <?php for ($i = 1; $i <= 4; $i++): ?>
                <div>
                    <label class="form-label" for="service-item-<?= $i ?>-title">Judul Layanan <?= $i ?></label>
                    <input type="text" id="service-item-<?= $i ?>-title" name="service_item_<?= $i ?>_title" class="form-control" value="<?= htmlspecialchars((string) ($settings['service_item_' . $i . '_title'] ?? '')) ?>" placeholder="Opsional, judul layanan">
                </div>
                <div>
                    <label class="form-label" for="service-item-<?= $i ?>-icon">Icon Layanan <?= $i ?></label>
                    <?php $selectedServiceIcon = $resolveIconKey((string) ($settings['service_item_' . $i . '_icon'] ?? 'layanan')); ?>
                    <select id="service-item-<?= $i ?>-icon" name="service_item_<?= $i ?>_icon" class="form-select">
                        <?php foreach ($iconOptions as $iconKey => $iconLabel): ?>
                            <option value="<?= htmlspecialchars($iconKey) ?>" <?= $selectedServiceIcon === $iconKey ? 'selected' : '' ?>><?= htmlspecialchars($iconLabel) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="admin-form-grid__full">
                    <label class="form-label" for="service-item-<?= $i ?>-desc">Deskripsi Layanan <?= $i ?></label>
                    <textarea id="service-item-<?= $i ?>-desc" name="service_item_<?= $i ?>_desc" class="form-control" rows="2" placeholder="Opsional, deskripsi layanan"><?= htmlspecialchars((string) ($settings['service_item_' . $i . '_desc'] ?? '')) ?></textarea>
                </div>
            <?php endfor; ?>

            <div>
                <label class="form-label" for="set-fb">Facebook</label>
                <input type="text" id="set-fb" name="social_facebook" class="form-control" value="<?= htmlspecialchars((string) ($settings['social_facebook'] ?? '')) ?>" placeholder="Opsional, contoh: https://facebook.com/namahalaman">
            </div>

            <div>
                <label class="form-label" for="set-ig">Instagram</label>
                <input type="text" id="set-ig" name="social_instagram" class="form-control" value="<?= htmlspecialchars((string) ($settings['social_instagram'] ?? '')) ?>" placeholder="Opsional, contoh: https://instagram.com/akun">
            </div>

            <div>
                <label class="form-label" for="set-yt">YouTube</label>
                <input type="text" id="set-yt" name="social_youtube" class="form-control" value="<?= htmlspecialchars((string) ($settings['social_youtube'] ?? '')) ?>" placeholder="Opsional, contoh: https://youtube.com/@channel">
            </div>

            <div>
                <label class="form-label" for="set-logo">Logo Gampong</label>
                <input type="file" id="set-logo" name="logo_desa" class="form-control" accept="image/*">
                <small class="text-muted d-block mt-1">Input file memang akan kosong lagi setelah disimpan (aturan keamanan browser).</small>
                <?php if (!empty($settings['logo_desa'] ?? '')): ?>
                    <div class="mt-2 d-flex align-items-center gap-2">
                        <img src="<?= htmlspecialchars(upload_url((string) $settings['logo_desa'])) ?>" alt="Logo saat ini" style="width:40px;height:40px;object-fit:cover;border-radius:8px;">
                        <small class="text-muted">Logo saat ini: <?= htmlspecialchars((string) $settings['logo_desa']) ?></small>
                    </div>
                <?php endif; ?>
            </div>

            <div>
                <label class="form-label" for="set-footer-badge">Teks Badge Footer</label>
                <input type="text" id="set-footer-badge" name="footer_badge_text" class="form-control" value="<?= htmlspecialchars((string) ($settings['footer_badge_text'] ?? '')) ?>" placeholder="Opsional, contoh: Website Resmi">
            </div>

            <div>
                <label class="form-label" for="set-privacy-label">Label Link Privasi</label>
                <input type="text" id="set-privacy-label" name="privacy_policy_label" class="form-control" value="<?= htmlspecialchars((string) ($settings['privacy_policy_label'] ?? '')) ?>" placeholder="Opsional, contoh: Kebijakan Privasi">
            </div>

            <div>
                <label class="form-label" for="set-privacy-url">URL Kebijakan Privasi</label>
                <input type="text" id="set-privacy-url" name="privacy_policy_url" class="form-control" value="<?= htmlspecialchars((string) ($settings['privacy_policy_url'] ?? '')) ?>" placeholder="Opsional, contoh: /kontak atau https://domain.com/privacy">
            </div>

            <div class="admin-form-grid__full">
                <h3 class="h6 mb-2">Teks Navigasi & Halaman</h3>
            </div>

            <div>
                <label class="form-label" for="set-menu-profile">Label Menu Profil</label>
                <input type="text" id="set-menu-profile" name="menu_profile_label" class="form-control" value="<?= htmlspecialchars((string) ($settings['menu_profile_label'] ?? '')) ?>" placeholder="Opsional, contoh: Profil Desa">
            </div>

            <div>
                <label class="form-label" for="set-menu-story">Label Menu Cerita</label>
                <input type="text" id="set-menu-story" name="menu_story_label" class="form-control" value="<?= htmlspecialchars((string) ($settings['menu_story_label'] ?? '')) ?>" placeholder="Opsional, contoh: Cerita Desa">
            </div>

            <div>
                <label class="form-label" for="set-menu-vision">Label Menu Visi Misi</label>
                <input type="text" id="set-menu-vision" name="menu_vision_label" class="form-control" value="<?= htmlspecialchars((string) ($settings['menu_vision_label'] ?? '')) ?>" placeholder="Opsional, contoh: Visi & Misi Desa">
            </div>

            <div>
                <label class="form-label" for="set-menu-structure">Label Menu Struktur</label>
                <input type="text" id="set-menu-structure" name="menu_structure_label" class="form-control" value="<?= htmlspecialchars((string) ($settings['menu_structure_label'] ?? '')) ?>" placeholder="Opsional, contoh: Struktur Pemerintahan">
            </div>

            <div class="admin-form-grid__full">
                <label class="form-label" for="set-contact-title">Judul Hero Halaman Kontak</label>
                <input type="text" id="set-contact-title" name="contact_hero_title" class="form-control" value="<?= htmlspecialchars((string) ($settings['contact_hero_title'] ?? '')) ?>" placeholder="Opsional, contoh: Hubungi Pemerintah Desa">
            </div>

            <div class="admin-form-grid__full">
                <label class="form-label" for="set-contact-desc">Deskripsi Hero Halaman Kontak</label>
                <textarea id="set-contact-desc" name="contact_hero_description" class="form-control" rows="2" placeholder="Opsional, teks pembuka di halaman kontak"><?= htmlspecialchars((string) ($settings['contact_hero_description'] ?? '')) ?></textarea>
            </div>

            <div class="admin-form-grid__full">
                <label class="form-label" for="set-login-visual-title">Judul Visual Halaman Login</label>
                <input type="text" id="set-login-visual-title" name="login_visual_title" class="form-control" value="<?= htmlspecialchars((string) ($settings['login_visual_title'] ?? '')) ?>" placeholder="Opsional, contoh: Pusat Administrasi Digital Pemerintah Desa">
            </div>

            <div class="admin-form-grid__full">
                <label class="form-label" for="set-login-form-desc">Deskripsi Form Halaman Login</label>
                <textarea id="set-login-form-desc" name="login_form_description" class="form-control" rows="2" placeholder="Opsional, deskripsi singkat di bawah judul form login"><?= htmlspecialchars((string) ($settings['login_form_description'] ?? '')) ?></textarea>
            </div>

            <div class="admin-form-grid__full">
                <label class="form-label" for="set-hero-explore-label">Label Tombol Jelajahi Hero</label>
                <input type="text" id="set-hero-explore-label" name="hero_explore_label" class="form-control" value="<?= htmlspecialchars((string) ($settings['hero_explore_label'] ?? '')) ?>" placeholder="Opsional, contoh: Jelajahi Desa">
            </div>

            <div>
                <label class="form-label" for="set-home-services-title">Judul Layanan Beranda</label>
                <input type="text" id="set-home-services-title" name="home_services_title" class="form-control" value="<?= htmlspecialchars((string) ($settings['home_services_title'] ?? '')) ?>" placeholder="Opsional, contoh: Layanan Unggulan">
            </div>

            <div class="admin-form-grid__full">
                <label class="form-label" for="set-home-services-subtitle">Subjudul Layanan Beranda</label>
                <textarea id="set-home-services-subtitle" name="home_services_subtitle" class="form-control" rows="2" placeholder="Opsional, deskripsi section layanan"><?= htmlspecialchars((string) ($settings['home_services_subtitle'] ?? '')) ?></textarea>
            </div>

            <div>
                <label class="form-label" for="set-home-vision-title">Judul Visi Misi Beranda</label>
                <input type="text" id="set-home-vision-title" name="home_vision_title" class="form-control" value="<?= htmlspecialchars((string) ($settings['home_vision_title'] ?? '')) ?>" placeholder="Opsional, contoh: Visi & Misi">
            </div>

            <div class="admin-form-grid__full">
                <label class="form-label" for="set-home-vision-subtitle">Subjudul Visi Misi Beranda</label>
                <textarea id="set-home-vision-subtitle" name="home_vision_subtitle" class="form-control" rows="2" placeholder="Opsional, deskripsi section visi misi"><?= htmlspecialchars((string) ($settings['home_vision_subtitle'] ?? '')) ?></textarea>
            </div>

            <div>
                <label class="form-label" for="set-home-news-title">Judul Berita Beranda</label>
                <input type="text" id="set-home-news-title" name="home_news_title" class="form-control" value="<?= htmlspecialchars((string) ($settings['home_news_title'] ?? '')) ?>" placeholder="Opsional, contoh: Berita Terbaru">
            </div>

            <div class="admin-form-grid__full">
                <label class="form-label" for="set-home-news-subtitle">Subjudul Berita Beranda</label>
                <textarea id="set-home-news-subtitle" name="home_news_subtitle" class="form-control" rows="2" placeholder="Opsional, deskripsi section berita"><?= htmlspecialchars((string) ($settings['home_news_subtitle'] ?? '')) ?></textarea>
            </div>

            <div>
                <label class="form-label" for="set-home-gallery-title">Judul Galeri Beranda</label>
                <input type="text" id="set-home-gallery-title" name="home_gallery_title" class="form-control" value="<?= htmlspecialchars((string) ($settings['home_gallery_title'] ?? '')) ?>" placeholder="Opsional, contoh: Galeri">
            </div>

            <div class="admin-form-grid__full">
                <label class="form-label" for="set-home-gallery-subtitle">Subjudul Galeri Beranda</label>
                <textarea id="set-home-gallery-subtitle" name="home_gallery_subtitle" class="form-control" rows="2" placeholder="Opsional, deskripsi section galeri"><?= htmlspecialchars((string) ($settings['home_gallery_subtitle'] ?? '')) ?></textarea>
            </div>

            <div>
                <label class="form-label" for="set-home-stats-title">Judul Statistik Beranda</label>
                <input type="text" id="set-home-stats-title" name="home_stats_title" class="form-control" value="<?= htmlspecialchars((string) ($settings['home_stats_title'] ?? '')) ?>" placeholder="Opsional, contoh: Statistik Desa">
            </div>

            <div class="admin-form-grid__full">
                <label class="form-label" for="set-home-stats-subtitle">Subjudul Statistik Beranda</label>
                <textarea id="set-home-stats-subtitle" name="home_stats_subtitle" class="form-control" rows="2" placeholder="Opsional, deskripsi section statistik"><?= htmlspecialchars((string) ($settings['home_stats_subtitle'] ?? '')) ?></textarea>
            </div>

            <div>
                <label class="form-label" for="set-news-page-title">Judul Halaman Berita</label>
                <input type="text" id="set-news-page-title" name="news_page_title" class="form-control" value="<?= htmlspecialchars((string) ($settings['news_page_title'] ?? '')) ?>" placeholder="Opsional, contoh: Berita Terbaru Desa">
            </div>

            <div class="admin-form-grid__full">
                <label class="form-label" for="set-news-page-desc">Deskripsi Halaman Berita</label>
                <textarea id="set-news-page-desc" name="news_page_description" class="form-control" rows="2" placeholder="Opsional, deskripsi hero halaman berita"><?= htmlspecialchars((string) ($settings['news_page_description'] ?? '')) ?></textarea>
            </div>

            <div class="admin-form-grid__full">
                <label class="form-label" for="set-gallery-page-desc">Deskripsi Halaman Galeri</label>
                <textarea id="set-gallery-page-desc" name="gallery_page_description" class="form-control" rows="2" placeholder="Opsional, deskripsi hero halaman galeri"><?= htmlspecialchars((string) ($settings['gallery_page_description'] ?? '')) ?></textarea>
            </div>

            <div class="admin-form-grid__full">
                <label class="form-label" for="set-profile-empty-history">Teks Kosong Sejarah Profil</label>
                <textarea id="set-profile-empty-history" name="profile_empty_history_text" class="form-control" rows="2" placeholder="Opsional, tampil saat sejarah belum ada"><?= htmlspecialchars((string) ($settings['profile_empty_history_text'] ?? '')) ?></textarea>
            </div>

            <div>
                <label class="form-label" for="set-profile-info-title">Judul Kotak Info Profil</label>
                <input type="text" id="set-profile-info-title" name="profile_info_title" class="form-control" value="<?= htmlspecialchars((string) ($settings['profile_info_title'] ?? '')) ?>" placeholder="Opsional, contoh: Info Profil">
            </div>

            <div class="admin-form-grid__full">
                <label class="form-label" for="set-vision-page-desc">Deskripsi Halaman Visi Misi</label>
                <textarea id="set-vision-page-desc" name="vision_page_description" class="form-control" rows="2" placeholder="Opsional, deskripsi pembuka halaman visi misi"><?= htmlspecialchars((string) ($settings['vision_page_description'] ?? '')) ?></textarea>
            </div>

            <div>
                <label class="form-label" for="set-vision-empty-text">Teks Kosong Visi</label>
                <input type="text" id="set-vision-empty-text" name="vision_empty_text" class="form-control" value="<?= htmlspecialchars((string) ($settings['vision_empty_text'] ?? '')) ?>" placeholder="Opsional, tampil saat visi kosong">
            </div>

            <div>
                <label class="form-label" for="set-mission-empty-text">Teks Kosong Misi</label>
                <input type="text" id="set-mission-empty-text" name="mission_empty_text" class="form-control" value="<?= htmlspecialchars((string) ($settings['mission_empty_text'] ?? '')) ?>" placeholder="Opsional, tampil saat misi kosong">
            </div>

            <div class="admin-form-grid__full">
                <label class="form-label" for="set-structure-page-desc">Deskripsi Halaman Struktur</label>
                <textarea id="set-structure-page-desc" name="structure_page_description" class="form-control" rows="2" placeholder="Opsional, deskripsi pembuka halaman struktur"><?= htmlspecialchars((string) ($settings['structure_page_description'] ?? '')) ?></textarea>
            </div>

            <div class="admin-form-grid__full">
                <label class="form-label" for="set-structure-section-title">Judul Section Struktur</label>
                <input type="text" id="set-structure-section-title" name="structure_section_title" class="form-control" value="<?= htmlspecialchars((string) ($settings['structure_section_title'] ?? '')) ?>" placeholder="Opsional, judul bagan struktur">
            </div>

            <div>
                <label class="form-label" for="set-structure-image-alt">Alt Gambar Struktur</label>
                <input type="text" id="set-structure-image-alt" name="structure_image_alt" class="form-control" value="<?= htmlspecialchars((string) ($settings['structure_image_alt'] ?? '')) ?>" placeholder="Opsional, alt text gambar struktur">
            </div>

            <div>
                <label class="form-label" for="set-structure-leader-subtitle">Subjudul Leader Struktur</label>
                <input type="text" id="set-structure-leader-subtitle" name="structure_leader_subtitle" class="form-control" value="<?= htmlspecialchars((string) ($settings['structure_leader_subtitle'] ?? '')) ?>" placeholder="Opsional, contoh: Pimpinan Pemerintahan">
            </div>

            <div class="admin-form-grid__full">
                <label class="form-label" for="set-structure-leader-desc">Deskripsi Leader Struktur</label>
                <textarea id="set-structure-leader-desc" name="structure_leader_description" class="form-control" rows="2" placeholder="Opsional, deskripsi pada kartu leader"><?= htmlspecialchars((string) ($settings['structure_leader_description'] ?? '')) ?></textarea>
            </div>

            <div class="admin-form-grid__full">
                <label class="form-label" for="set-structure-empty-text">Teks Kosong Struktur</label>
                <textarea id="set-structure-empty-text" name="structure_empty_text" class="form-control" rows="2" placeholder="Opsional, tampil saat struktur belum ada"><?= htmlspecialchars((string) ($settings['structure_empty_text'] ?? '')) ?></textarea>
            </div>

            <div class="admin-form-grid__actions">
                <button type="submit" class="btn admin-btn-primary"><i class="bi bi-save2"></i><span>Simpan Pengaturan</span></button>
            </div>
        </form>
    </article>
</section>
