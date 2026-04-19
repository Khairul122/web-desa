<?php
$metrics = is_array($metrics ?? null) ? $metrics : [];
$beritaTerbaru = is_array($berita_terbaru ?? null) ? $berita_terbaru : [];
$pesanTerbaru = is_array($pesan_terbaru ?? null) ? $pesan_terbaru : [];
$periodViews = is_array($period_views ?? null) ? $period_views : [];
$topContentViews = is_array($top_content_views ?? null) ? $top_content_views : [];

$cards = [
    [
        'label' => 'Berita Terbit',
        'value' => (int) ($metrics['berita_publish'] ?? 0),
        'icon' => 'bi-newspaper',
        'tone' => 'is-blue',
    ],
    [
        'label' => 'Berita Draf',
        'value' => (int) ($metrics['berita_draft'] ?? 0),
        'icon' => 'bi-pencil-square',
        'tone' => 'is-amber',
    ],
    [
        'label' => 'Total Dokumentasi',
        'value' => (int) ($metrics['galeri_total'] ?? 0),
        'icon' => 'bi-images',
        'tone' => 'is-green',
    ],
    [
        'label' => 'Pesan Baru',
        'value' => (int) ($metrics['pesan_baru'] ?? 0),
        'icon' => 'bi-envelope-paper',
        'tone' => 'is-red',
    ],
    [
        'label' => 'User Aktif',
        'value' => (int) ($metrics['user_aktif'] ?? 0),
        'icon' => 'bi-people',
        'tone' => 'is-indigo',
    ],
    [
        'label' => 'Total Views Konten',
        'value' => (int) ($metrics['total_views'] ?? 0),
        'icon' => 'bi-eye',
        'tone' => 'is-blue',
    ],
];
?>

<section class="admin-dashboard">
    <div class="admin-dashboard__cards">
        <?php foreach ($cards as $card): ?>
            <article class="metric-card <?= htmlspecialchars($card['tone']) ?>" data-reveal>
                <div class="metric-card__icon"><i class="bi <?= htmlspecialchars($card['icon']) ?>"></i></div>
                <div class="metric-card__meta">
                    <span><?= htmlspecialchars($card['label']) ?></span>
                    <strong data-counter="<?= (int) $card['value'] ?>">0</strong>
                </div>
            </article>
        <?php endforeach; ?>
    </div>

    <div class="admin-dashboard__grid">
        <article class="admin-panel" data-reveal>
            <header class="admin-panel__head">
                <h2>Berita Terbaru</h2>
                <span><?= count($beritaTerbaru) ?> data</span>
            </header>

            <?php if (empty($beritaTerbaru)): ?>
                <?php
                $emptyMessage = 'Belum ada berita terbaru. Yuk buat publikasi pertama untuk warga.';
                $emptyIcon = '';
                $emptyClass = 'admin-empty';
                require APP_PATH . '/Views/includes/ui/empty-state.php';
                ?>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table admin-table align-middle">
                        <thead>
                            <tr>
                                <th>Judul</th>
                                <th>Kategori</th>
                                <th>Status</th>
                                <th>Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($beritaTerbaru as $berita): ?>
                                <tr>
                                    <td><?= htmlspecialchars((string) ($berita['judul'] ?? '-')) ?></td>
                                    <td><?= htmlspecialchars((string) ($berita['kategori'] ?? '-')) ?></td>
                                    <td>
                                        <span class="status-badge <?= (($berita['status'] ?? '') === 'publish') ? 'is-publish' : 'is-draft' ?>">
                                            <?= htmlspecialchars((string) strtoupper((string) ($berita['status'] ?? 'draft'))) ?>
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars((string) date_id((string) ($berita['created_at'] ?? 'now'))) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </article>

        <article class="admin-panel" data-reveal>
            <header class="admin-panel__head">
                <h2>Pesan Kontak Terbaru</h2>
                <span><?= count($pesanTerbaru) ?> data</span>
            </header>

            <?php if (empty($pesanTerbaru)): ?>
                <?php
                $emptyMessage = 'Belum ada pesan baru. Saat ada pesan masuk, ringkasannya tampil di sini.';
                $emptyIcon = '';
                $emptyClass = 'admin-empty';
                require APP_PATH . '/Views/includes/ui/empty-state.php';
                ?>
            <?php else: ?>
                <ul class="admin-list">
                    <?php foreach ($pesanTerbaru as $pesan): ?>
                        <li>
                            <div class="admin-list__title">
                                <strong><?= htmlspecialchars((string) ($pesan['nama'] ?? 'Pengunjung')) ?></strong>
                                <span class="status-badge <?= (($pesan['status'] ?? 'baru') === 'baru') ? 'is-new' : 'is-read' ?>">
                                    <?= htmlspecialchars((string) strtoupper((string) ($pesan['status'] ?? 'baru'))) ?>
                                </span>
                            </div>
                            <p><?= htmlspecialchars(limit((string) ($pesan['subjek'] ?? '-'), 70)) ?></p>
                            <small><?= htmlspecialchars((string) ($pesan['email'] ?? '-')) ?> • <?= htmlspecialchars((string) date_id((string) ($pesan['created_at'] ?? 'now'))) ?></small>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </article>
    </div>

    <div class="admin-dashboard__grid mt-3">
        <article class="admin-panel" data-reveal>
            <header class="admin-panel__head">
                <h2>Statistik Kunjungan Portal Gampong (14 Hari Terakhir)</h2>
                <span>Berita & Galeri</span>
            </header>

            <?php if (empty($periodViews)): ?>
                <?php
                $emptyMessage = 'Belum ada data akses harian untuk periode ini.';
                $emptyIcon = '';
                $emptyClass = 'admin-empty';
                require APP_PATH . '/Views/includes/ui/empty-state.php';
                ?>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table admin-table align-middle">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Views Berita</th>
                                <th>Views Galeri</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($periodViews as $row): ?>
                                <tr>
                                    <td><?= htmlspecialchars((string) date_id((string) ($row['view_date'] ?? 'now'))) ?></td>
                                    <td><?= number_format((int) ($row['berita_views'] ?? 0), 0, ',', '.') ?></td>
                                    <td><?= number_format((int) ($row['galeri_views'] ?? 0), 0, ',', '.') ?></td>
                                    <td><strong><?= number_format((int) ($row['total_views'] ?? 0), 0, ',', '.') ?></strong></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </article>

        <article class="admin-panel" data-reveal>
            <header class="admin-panel__head">
                <h2>Top Konten (30 Hari)</h2>
                <span>Paling sering diakses</span>
            </header>

            <?php if (empty($topContentViews)): ?>
                <?php
                $emptyMessage = 'Belum ada data top konten.';
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
                                <th>Jenis</th>
                                <th>Judul</th>
                                <th>Views</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; ?>
                            <?php foreach ($topContentViews as $row): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= htmlspecialchars(strtoupper((string) ($row['content_type'] ?? '-'))) ?></td>
                                    <td><?= htmlspecialchars((string) ($row['judul'] ?? '-')) ?></td>
                                    <td><strong><?= number_format((int) ($row['total_views'] ?? 0), 0, ',', '.') ?></strong></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </article>
    </div>
</section>
