<?php
$listGaleri = is_array($galeri ?? null) ? $galeri : [];
$filters = is_array($filters ?? null) ? $filters : [];
$pagination = is_array($pagination ?? null) ? $pagination : [];
$kategoriOptions = is_array($kategori_options ?? null) ? $kategori_options : [];
$metrics = is_array($metrics ?? null) ? $metrics : [];
$baseParams = is_array($base_params ?? null) ? $base_params : [];

$currentPage = (int) ($pagination['current_page'] ?? 1);
$lastPage = (int) ($pagination['last_page'] ?? 1);

$buildPageUrl = static function (int $page) use ($baseParams): string {
    $params = $baseParams;
    $params['page'] = $page;
    $query = http_build_query(array_filter($params, static fn($v) => $v !== '' && $v !== null));
    return base_url('/admin/galeri') . ($query !== '' ? '?' . $query : '');
};
?>

<section class="admin-dashboard admin-galeri-page">
    <div class="admin-dashboard__cards admin-dashboard__cards--four">
        <article class="metric-card is-blue" data-reveal>
            <div class="metric-card__icon"><i class="bi bi-collection"></i></div>
            <div class="metric-card__meta">
                <span>Total Media</span>
                <strong data-counter="<?= (int) ($metrics['total'] ?? 0) ?>">0</strong>
            </div>
        </article>
        <article class="metric-card is-green" data-reveal>
            <div class="metric-card__icon"><i class="bi bi-image"></i></div>
            <div class="metric-card__meta">
                <span>Total Foto</span>
                <strong data-counter="<?= (int) ($metrics['image'] ?? 0) ?>">0</strong>
            </div>
        </article>
        <article class="metric-card is-indigo" data-reveal>
            <div class="metric-card__icon"><i class="bi bi-play-btn"></i></div>
            <div class="metric-card__meta">
                <span>Total Video</span>
                <strong data-counter="<?= (int) ($metrics['video'] ?? 0) ?>">0</strong>
            </div>
        </article>
        <article class="metric-card is-amber" data-reveal>
            <div class="metric-card__icon"><i class="bi bi-eye"></i></div>
            <div class="metric-card__meta">
                <span>Total Views Dokumentasi</span>
                <strong data-counter="<?= (int) ($metrics['views'] ?? 0) ?>">0</strong>
            </div>
        </article>
    </div>

    <article class="admin-panel mt-3" data-reveal>
            <header class="admin-panel__head">
                <h2>Tambah Media Dokumentasi</h2>
            </header>

        <form method="POST" action="<?= base_url('/admin/galeri') ?>" enctype="multipart/form-data" class="admin-filter-grid">
            <?= csrf_field() ?>

            <div>
                <label for="galeri-judul" class="form-label">Judul</label>
                <input type="text" class="form-control" id="galeri-judul" name="judul" placeholder="Contoh: Kegiatan Gotong Royong" required>
            </div>
            <div>
                <label for="galeri-kategori" class="form-label">Kategori</label>
                <input type="text" class="form-control" id="galeri-kategori" name="kategori" placeholder="Contoh: Kegiatan">
            </div>
            <div>
                <label for="galeri-file" class="form-label">Upload Media</label>
                <input type="file" class="form-control" id="galeri-file" name="media_file" accept="image/*,video/*">
            </div>
            <div class="admin-filter-grid__search">
                <label for="galeri-video-url" class="form-label">Atau URL Video</label>
                <input type="url" class="form-control" id="galeri-video-url" name="video_url" placeholder="https://youtube.com/... atau https://vimeo.com/...">
            </div>
            <div class="admin-filter-grid__search">
                <label for="galeri-deskripsi" class="form-label">Deskripsi</label>
                <textarea class="form-control" id="galeri-deskripsi" name="deskripsi" rows="3" placeholder="Deskripsi singkat media"></textarea>
            </div>
            <div class="admin-filter-grid__actions">
                <button type="submit" class="btn admin-btn-primary">
                    <i class="bi bi-cloud-upload"></i>
                    <span>Publikasikan Media</span>
                </button>
            </div>
        </form>
    </article>

    <article class="admin-panel mt-3" data-reveal>
        <header class="admin-panel__head">
            <h2>Filter & Pencarian</h2>
        </header>

        <form method="GET" action="<?= base_url('/admin/galeri') ?>" class="admin-filter-grid">
            <div>
                <label for="filter-media" class="form-label">Jenis Media</label>
                <select id="filter-media" name="media" class="form-select">
                    <option value="">Semua Media</option>
                    <option value="image" <?= (($filters['media'] ?? '') === 'image') ? 'selected' : '' ?>>Foto</option>
                    <option value="video" <?= (($filters['media'] ?? '') === 'video') ? 'selected' : '' ?>>Video</option>
                </select>
            </div>
            <div>
                <label for="filter-kategori" class="form-label">Kategori</label>
                <select id="filter-kategori" name="kategori" class="form-select">
                    <option value="">Semua Kategori</option>
                    <?php foreach ($kategoriOptions as $kategori): ?>
                        <option value="<?= htmlspecialchars((string) $kategori) ?>" <?= (($filters['kategori'] ?? '') === (string) $kategori) ? 'selected' : '' ?>>
                            <?= htmlspecialchars((string) $kategori) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="filter-per-page" class="form-label">Per Halaman</label>
                <select id="filter-per-page" name="per_page" class="form-select">
                    <?php foreach ([12, 24, 36, 48] as $size): ?>
                        <option value="<?= $size ?>" <?= ((int) ($filters['per_page'] ?? 12) === $size) ? 'selected' : '' ?>><?= $size ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="admin-filter-grid__search">
                <label for="filter-q" class="form-label">Pencarian</label>
                <input
                    type="text"
                    id="filter-q"
                    name="q"
                    class="form-control"
                    value="<?= htmlspecialchars((string) ($filters['q'] ?? '')) ?>"
                    placeholder="Cari judul, kategori, deskripsi, atau nama file..."
                >
            </div>
            <div class="admin-filter-grid__actions">
                <button type="submit" class="btn admin-btn-primary">
                    <i class="bi bi-search"></i>
                    <span>Terapkan</span>
                </button>
                <a href="<?= base_url('/admin/galeri') ?>" class="btn admin-btn-light">
                    <i class="bi bi-arrow-clockwise"></i>
                    <span>Atur Ulang</span>
                </a>
            </div>
        </form>
    </article>

    <article class="admin-panel mt-3" data-reveal>
        <header class="admin-panel__head">
            <h2>Daftar Media Dokumentasi</h2>
        </header>

        <?php if (empty($listGaleri)): ?>
            <?php
            $emptyMessage = 'Belum ada hasil yang cocok. Coba ubah filter atau tambah media baru.';
            $emptyIcon = '';
            $emptyClass = 'admin-empty';
            require APP_PATH . '/Views/includes/ui/empty-state.php';
            ?>
        <?php else: ?>
            <div class="admin-media-grid">
                <?php foreach ($listGaleri as $item): ?>
                    <article class="admin-media-card">
                        <div class="admin-media-card__preview">
                            <?php if (($item['media_type'] ?? '') === 'video'): ?>
                                <?php if (($item['is_external'] ?? false) === true): ?>
                                    <a href="<?= htmlspecialchars((string) ($item['media_url'] ?? '#')) ?>" target="_blank" rel="noopener noreferrer" class="admin-media-card__external-link">
                                        <i class="bi bi-play-circle"></i>
                                        <span>Tonton Video</span>
                                    </a>
                                <?php else: ?>
                                    <video controls preload="metadata" src="<?= htmlspecialchars((string) ($item['media_url'] ?? '')) ?>" width="640" height="360"></video>
                                <?php endif; ?>
                            <?php else: ?>
                                <img src="<?= htmlspecialchars((string) ($item['media_url'] ?? '')) ?>" alt="<?= htmlspecialchars((string) ($item['judul'] ?? 'Media galeri')) ?>" loading="lazy" decoding="async" width="640" height="480">
                            <?php endif; ?>
                        </div>
                        <div class="admin-media-card__body">
                            <h3><?= htmlspecialchars((string) ($item['judul'] ?? '-')) ?></h3>
                            <p><?= htmlspecialchars(limit((string) ($item['deskripsi'] ?? '-'), 120)) ?></p>
                            <div class="admin-media-card__meta">
                                <span class="status-badge <?= (($item['media_type'] ?? '') === 'video') ? 'is-new' : 'is-publish' ?>">
                                    <?= strtoupper((string) ($item['media_type'] ?? 'image')) ?>
                                </span>
                                <small><?= htmlspecialchars((string) ($item['kategori'] ?? 'Tanpa Kategori')) ?> • <?= htmlspecialchars((string) date_id((string) ($item['created_at'] ?? 'now'))) ?></small>
                                <small><i class="bi bi-eye"></i> <?= number_format((int) ($item['views'] ?? 0), 0, ',', '.') ?> views</small>
                            </div>
                            <form method="POST" action="<?= base_url('/admin/galeri/delete/' . (int) ($item['id'] ?? 0)) ?>" class="mt-2 js-confirm-submit" data-confirm-title="Hapus Media" data-confirm-message="Yakin ingin menghapus media ini? Tindakan ini tidak bisa dibatalkan.">
                                <div class="d-flex gap-2">
                                    <a href="<?= base_url('/admin/galeri/edit/' . (int) ($item['id'] ?? 0)) ?>" class="btn btn-sm admin-btn-light">
                                        <i class="bi bi-pencil-square"></i>
                                        <span>Edit</span>
                                    </a>
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-sm admin-btn-danger">
                                        <i class="bi bi-trash3"></i>
                                        <span>Hapus</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>

            <?php if ($lastPage > 1): ?>
                <nav aria-label="Pagination galeri" class="admin-pagination-wrap">
                    <ul class="pagination admin-pagination mb-0">
                        <li class="page-item <?= $currentPage <= 1 ? 'disabled' : '' ?>">
                            <a class="page-link" href="<?= $currentPage <= 1 ? '#' : htmlspecialchars($buildPageUrl($currentPage - 1)) ?>" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>

                        <?php
                        $start = max(1, $currentPage - 2);
                        $end = min($lastPage, $currentPage + 2);
                        for ($i = $start; $i <= $end; $i++):
                        ?>
                            <li class="page-item <?= $i === $currentPage ? 'active' : '' ?>">
                                <a class="page-link" href="<?= htmlspecialchars($buildPageUrl($i)) ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>

                        <li class="page-item <?= $currentPage >= $lastPage ? 'disabled' : '' ?>">
                            <a class="page-link" href="<?= $currentPage >= $lastPage ? '#' : htmlspecialchars($buildPageUrl($currentPage + 1)) ?>" aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            <?php endif; ?>
        <?php endif; ?>
    </article>
</section>
