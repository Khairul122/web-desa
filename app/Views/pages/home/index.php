<?php
$brandName = trim((string) ($desa_nama ?? '')) ?: (trim((string) ($website_nama ?? '')) ?: 'Website');
$heroSummary = trim((string) ($sejarahSingkat ?? '')) ?: trim((string) ($website_deskripsi ?? ''));
$profilSummary = trim((string) ($sejarah ?? ''));
$visiText = trim((string) ($visi ?? ''));

$alamatText = trim((string) ($alamatDesa ?? ($desa_alamat ?? '')));
$teleponText = trim((string) ($teleponDesa ?? ($desa_telepon ?? '')));
$emailText = trim((string) ($emailDesa ?? ($desa_email ?? '')));
$waText = trim((string) ($whatsapp_number ?? ''));

$carouselItems = (is_array($carousel ?? null) ? $carousel : []);
$galeriItems = (is_array($galeri ?? null) ? $galeri : []);
$beritaItems = (is_array($berita ?? null) ? $berita : []);
$statistikItems = (is_array($statistik ?? null) ? $statistik : []);

$resolveMediaUrl = static function (string $path, string $folder = ''): string {
    $path = trim($path);
    if ($path === '') {
        return '';
    }
    if (preg_match('#^https?://#i', $path) === 1) {
        return $path;
    }
    if (str_starts_with($path, 'uploads/')) {
        return base_url('/' . ltrim($path, '/'));
    }
    if ($folder !== '' && !str_contains($path, '/')) {
        return upload_url(trim($folder, '/') . '/' . $path);
    }
    return upload_url($path);
};

$heroImage = '';
if (!empty($carouselItems[0]['gambar'])) {
    $heroImage = (string) $carouselItems[0]['gambar'];
} elseif (!empty($galeriItems[0]['gambar'])) {
    $heroImage = (string) $galeriItems[0]['gambar'];
}

$heroImageUrl = '';
if (!empty($carouselItems[0]['gambar'])) {
    $heroImageUrl = $resolveMediaUrl((string) $carouselItems[0]['gambar'], 'carousel');
}
if ($heroImageUrl === '' && !empty($galeriItems[0]['gambar'])) {
    $heroImageUrl = $resolveMediaUrl((string) $galeriItems[0]['gambar'], 'galeri');
}
if ($heroImageUrl === '') {
    $heroImageUrl = $resolveMediaUrl((string) ($logoDesa ?? ''));
}

$aboutImageUrl = !empty($galeriItems[0]['gambar'])
    ? $resolveMediaUrl((string) $galeriItems[0]['gambar'], 'galeri')
    : $heroImageUrl;

$missionItems = !empty($misiList) && is_array($misiList)
    ? array_slice($misiList, 0, 4)
    : [];

$primaryStats = [
    ['icon' => 'bi bi-people-fill', 'label' => 'Data Penduduk', 'value' => (string) ($totalPenduduk ?? '0')],
    ['icon' => 'bi bi-map-fill', 'label' => 'Luas Wilayah', 'value' => (string) ($luasWilayah ?? '0')],
    ['icon' => 'bi bi-house-door-fill', 'label' => 'Kepala Keluarga', 'value' => (string) ($totalKk ?? '0')],
    ['icon' => 'bi bi-eye-fill', 'label' => 'Total Views Konten', 'value' => number_format((int) ($totalLandingViews ?? 0), 0, ',', '.')],
];

// Default service items shown only if DB has no entries
$serviceItems = [
    ['icon' => 'bi bi-shield-check',   'title' => 'Layanan Transparan',    'desc' => 'Informasi publik disusun rapi, mudah diakses, dan selalu update untuk semua warga.'],
    ['icon' => 'bi bi-people',         'title' => 'Prioritas Warga',       'desc' => 'Proses layanan dibuat simpel, ramah, dan responsif agar kebutuhan warga cepat tertangani.'],
    ['icon' => 'bi bi-diagram-3',      'title' => 'Kolaborasi Aktif',      'desc' => 'Program gampong dirancang bersama masyarakat agar manfaatnya terasa nyata di setiap dusun.'],
    ['icon' => 'bi bi-bar-chart-line', 'title' => 'Keputusan Berbasis Data','desc' => 'Arah pembangunan didukung data yang jelas supaya langkah gampong lebih tepat dan terukur.'],
];

$announcementActive = ((string) ($pengaturan['important_announcement_active'] ?? '0')) === '1';
$announcementText = trim((string) ($pengaturan['important_announcement_text'] ?? ''));
$announcementLink = trim((string) ($pengaturan['important_announcement_link'] ?? ''));

$quickWhatsApp = trim((string) ($whatsapp_number ?? ''));
$quickPhone = trim((string) ($teleponText ?? ''));
$quickLocation = trim((string) ($pengaturan['quick_location_url'] ?? ''));
if ($quickLocation === '' && $alamatText !== '') {
    $quickLocation = 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode($alamatText);
}

$districtName = trim((string) ($pengaturan['district_name'] ?? ''));

$regencyName = trim((string) ($pengaturan['regency_name'] ?? ''));

$heroLocationLine = trim((string) ($pengaturan['hero_location_line'] ?? ''));
if ($heroLocationLine === '') {
    $heroLocationLine = trim($districtName . ' · ' . $regencyName, ' ·');
}

$establishedYear = trim((string) ($pengaturan['desa_berdiri_tahun'] ?? ''));
if ($establishedYear === '') {
    $establishedYear = 'N/A';
}

$farmArea = trim((string) ($pengaturan['lahan_pertanian'] ?? ''));
if ($farmArea === '') {
    $farmArea = 'N/A';
}

$popularServices = [];
$iconClassMap = [
    'beranda' => 'bi bi-house-door',
    'informasi' => 'bi bi-info-circle',
    'berita' => 'bi bi-newspaper',
    'galeri' => 'bi bi-images',
    'kontak' => 'bi bi-envelope',
    'telepon' => 'bi bi-telephone',
    'whatsapp' => 'bi bi-whatsapp',
    'lokasi' => 'bi bi-geo-alt',
    'dokumen' => 'bi bi-file-earmark-text',
    'layanan' => 'bi bi-grid',
    'pengumuman' => 'bi bi-megaphone',
    'pengaturan' => 'bi bi-gear',
];

$resolveIconClass = static function (string $raw) use ($iconClassMap): string {
    $value = strtolower(trim($raw));
    if (isset($iconClassMap[$value])) {
        return $iconClassMap[$value];
    }

    if (str_starts_with($value, 'bi ')) {
        return $value;
    }

    return $iconClassMap['layanan'];
};

$serviceItemsFromDb = [];
for ($i = 1; $i <= 4; $i++) {
    $title = trim((string) ($pengaturan['service_item_' . $i . '_title'] ?? ''));
    $desc = trim((string) ($pengaturan['service_item_' . $i . '_desc'] ?? ''));
    $icon = trim((string) ($pengaturan['service_item_' . $i . '_icon'] ?? ''));

    if ($title === '' || $desc === '') {
        continue;
    }

    $serviceItemsFromDb[] = [
        'icon' => $resolveIconClass($icon),
        'title' => $title,
        'desc' => $desc,
    ];
}

if (!empty($serviceItemsFromDb)) {
    $serviceItems = $serviceItemsFromDb;
}

for ($i = 1; $i <= 4; $i++) {
    $label = trim((string) ($pengaturan['popular_service_' . $i . '_label'] ?? ''));
    $url = trim((string) ($pengaturan['popular_service_' . $i . '_url'] ?? ''));
    $icon = trim((string) ($pengaturan['popular_service_' . $i . '_icon'] ?? ''));
    if ($label === '' || $url === '') {
        continue;
    }
    $icon = $resolveIconClass($icon);
    if (!preg_match('#^(https?:)?//#i', $url) && !str_starts_with($url, '/')) {
        $url = '/' . ltrim($url, '/');
    }
    $popularServices[] = [
        'label' => $label,
        'url' => preg_match('#^(https?:)?//#i', $url) ? $url : base_url($url),
        'icon' => $icon,
    ];
}

if (empty($popularServices)) {
    $popularServices = [
        ['label' => 'Surat Pengantar', 'url' => base_url('/kontak'), 'icon' => 'bi bi-file-earmark-text'],
        ['label' => 'Kontak', 'url' => base_url('/kontak'), 'icon' => 'bi bi-telephone'],
        ['label' => 'Berita', 'url' => base_url('/berita'), 'icon' => 'bi bi-newspaper'],
        ['label' => 'Galeri Kegiatan', 'url' => base_url('/galeri'), 'icon' => 'bi bi-images'],
    ];
}

$mapSectionTitle = trim((string) ($pengaturan['map_section_title'] ?? ''));
if ($mapSectionTitle === '') {
    $mapSectionTitle = 'Lokasi ' . (trim((string) ($desa_nama ?? '')) ?: (trim((string) ($website_nama ?? '')) ?: 'Website'));
}

$mapSectionDescription = trim((string) ($pengaturan['map_section_description'] ?? ''));
if ($mapSectionDescription === '') {
    $mapSectionDescription = 'Temukan lokasi kantor melalui peta interaktif berikut.';
}

$mapEmbedUrl = trim((string) ($pengaturan['map_embed_url'] ?? ''));
if ($mapEmbedUrl !== '' && preg_match('/<iframe[^>]*src=["\']([^"\']+)["\']/i', $mapEmbedUrl, $matches) === 1) {
    $mapEmbedUrl = trim((string) ($matches[1] ?? ''));
}

$isValidMapEmbed = filter_var($mapEmbedUrl, FILTER_VALIDATE_URL) !== false;
if ($isValidMapEmbed) {
    $mapHost = strtolower((string) parse_url($mapEmbedUrl, PHP_URL_HOST));
    $mapPath = strtolower((string) parse_url($mapEmbedUrl, PHP_URL_PATH));
    $isValidMapEmbed = str_contains($mapHost, 'google.com') && str_contains($mapPath, '/maps/embed');
}

if (!$isValidMapEmbed) {
    $mapEmbedUrl = '';
}

$hasMapSection = $mapEmbedUrl !== '';

$canonicalUrl = rtrim((string) BASE_URL, '/') . '/';
$breadcrumbs = [
    ['@type' => 'ListItem', 'position' => 1, 'name' => 'Beranda', 'item' => rtrim((string) BASE_URL, '/') . '/'],
];

$heroSlides = [];
if (!empty($carouselItems)) {
    foreach ($carouselItems as $slide) {
        $imagePath = (string) ($slide['gambar'] ?? '');
        $imageUrl = $resolveMediaUrl($imagePath, 'carousel');
        if ($imageUrl === '') {
            continue;
        }
        $heroSlides[] = [
            'image' => $imageUrl,
            'title' => trim((string) ($slide['judul'] ?? '')),
            'desc' => trim((string) ($slide['deskripsi'] ?? '')),
        ];
    }
}

if (empty($heroSlides)) {
    if ($heroImageUrl !== '') {
        $heroSlides[] = [
            'image' => $heroImageUrl,
            'title' => '',
            'desc' => '',
        ];
    }
}
?>

<div class="home-modern">
<?php require APP_PATH . '/Views/pages/home/sections/announcement.php'; ?>
<?php require APP_PATH . '/Views/pages/home/sections/hero.php'; ?>
<?php require APP_PATH . '/Views/pages/home/sections/quick-actions.php'; ?>
<?php require APP_PATH . '/Views/pages/home/sections/about.php'; ?>
<?php require APP_PATH . '/Views/pages/home/sections/services.php'; ?>
<?php require APP_PATH . '/Views/pages/home/sections/popular-services.php'; ?>
<?php require APP_PATH . '/Views/pages/home/sections/stats.php'; ?>
<?php require APP_PATH . '/Views/pages/home/sections/vision-mission.php'; ?>
<?php require APP_PATH . '/Views/pages/home/sections/news.php'; ?>
<?php require APP_PATH . '/Views/pages/home/sections/gallery.php'; ?>
<?php require APP_PATH . '/Views/pages/home/sections/map-location.php'; ?>
<?php require APP_PATH . '/Views/pages/home/sections/cta.php'; ?>
</div>
