<?php
$listBerita = is_array($berita ?? null) ? $berita : [];
$filters = is_array($filters ?? null) ? $filters : [];
$pagination = is_array($pagination ?? null) ? $pagination : [];
$kategoriOptions = is_array($kategori_options ?? null) ? $kategori_options : [];
$baseParams = is_array($base_params ?? null) ? $base_params : [];

$currentPage = (int) ($pagination['current_page'] ?? 1);
$lastPage = (int) ($pagination['last_page'] ?? 1);

$buildPageUrl = static function (int $page) use ($baseParams): string {
    $params = $baseParams;
    $params['page'] = $page;
    $query = http_build_query(array_filter($params, static fn($v) => $v !== '' && $v !== null));
    return base_url('/admin/berita') . ($query !== '' ? '?' . $query : '');
};
?>

<section class="admin-dashboard admin-berita-page">
    <article class="admin-panel" data-reveal>
        <header class="admin-panel__head">
            <h2>Filter & Pencarian Berita</h2>
            <div class="d-flex align-items-center gap-2">
                <a href="<?= base_url('/admin/berita/create') ?>" class="btn btn-sm admin-btn-primary">
                    <i class="bi bi-plus-circle"></i>
                    <span>Tambah Publikasi</span>
                </a>
            </div>
        </header>

        <form method="GET" action="<?= base_url('/admin/berita') ?>" class="admin-filter-grid">
            <div>
                <label for="filter-status" class="form-label">Status</label>
                <select id="filter-status" name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="publish" <?= (($filters['status'] ?? '') === 'publish') ? 'selected' : '' ?>>Terbit</option>
                    <option value="draft" <?= (($filters['status'] ?? '') === 'draft') ? 'selected' : '' ?>>Draf</option>
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
                    <?php foreach ([10, 20, 30, 50] as $size): ?>
                        <option value="<?= $size ?>" <?= ((int) ($filters['per_page'] ?? 10) === $size) ? 'selected' : '' ?>><?= $size ?></option>
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
                    placeholder="Cari judul, slug, atau konten..."
                >
            </div>

            <div class="admin-filter-grid__actions">
                <button type="submit" class="btn admin-btn-primary">
                    <i class="bi bi-search"></i>
                    <span>Terapkan</span>
                </button>
                <a href="<?= base_url('/admin/berita') ?>" class="btn admin-btn-light">
                    <i class="bi bi-arrow-clockwise"></i>
                    <span>Atur Ulang</span>
                </a>
            </div>
        </form>
    </article>

    <article class="admin-panel" data-reveal>
        <header class="admin-panel__head">
            <h2>Daftar Publikasi Gampong</h2>
        </header>

        <?php if (empty($listBerita)): ?>
            <?php
            $emptyMessage = 'Belum ada hasil yang cocok. Coba ubah filter atau buat publikasi baru.';
            $emptyIcon = '';
            $emptyClass = 'admin-empty';
            require APP_PATH . '/Views/includes/ui/empty-state.php';
            ?>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table admin-table align-middle">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Judul</th>
                            <th>Kategori</th>
                            <th>Penulis</th>
                            <th>Status</th>
                            <th>Views</th>
                            <th>Tanggal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $rowNumber = (int) (($pagination['from'] ?? 1)); ?>
                        <?php foreach ($listBerita as $item): ?>
                            <tr>
                                <td><?= $rowNumber++ ?></td>
                                <td>
                                    <div class="admin-title-cell">
                                        <strong><?= htmlspecialchars((string) ($item['judul'] ?? '-')) ?></strong>
                                        <small><?= htmlspecialchars((string) ($item['slug'] ?? '-')) ?></small>
                                    </div>
                                </td>
                                <td><?= htmlspecialchars((string) ($item['kategori'] ?? '-')) ?></td>
                                <td><?= htmlspecialchars((string) ($item['penulis'] ?? '-')) ?></td>
                                <td>
                                    <span class="status-badge <?= (($item['status'] ?? '') === 'publish') ? 'is-publish' : 'is-draft' ?>">
                                        <?= htmlspecialchars(strtoupper((string) ($item['status'] ?? 'draft'))) ?>
                                    </span>
                                </td>
                                <td><?= number_format((int) ($item['views'] ?? 0), 0, ',', '.') ?></td>
                                <td><?= htmlspecialchars((string) date_id((string) ($item['created_at'] ?? 'now'))) ?></td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="<?= base_url('/admin/berita/edit/' . (int) ($item['id'] ?? 0)) ?>" class="btn btn-sm admin-btn-light">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <form method="POST" action="<?= base_url('/admin/berita/delete/' . (int) ($item['id'] ?? 0)) ?>" class="js-confirm-submit" data-confirm-title="Hapus Publikasi" data-confirm-message="Yakin ingin menghapus publikasi ini? Tindakan ini tidak bisa dibatalkan.">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-sm admin-btn-danger"><i class="bi bi-trash3"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($lastPage > 1): ?>
                <nav aria-label="Pagination berita" class="admin-pagination-wrap">
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
