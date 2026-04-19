<?php

namespace App\Http\Controllers;

use App\Models\Berita;

class BeritaController extends Controller
{
    public function index()
    {
        $berita = Berita::all(18) ?: [];
        $featured = $berita[0] ?? null;

        return $this->view('pages/berita/index', $this->siteData([
            'title' => 'Berita Gampong',
            'page' => 'berita',
            'berita' => $berita,
            'featured_berita' => $featured,
        ]));
    }

    public function show(string $slug)
    {
        $db = \App\Core\Database::getInstance();

        $item = $db
            ->table('berita')
            ->where('slug', $slug)
            ->where('status', 'publish')
            ->first();

        if (!$item) {
            return $this->redirect(base_url('/berita'));
        }

        $beritaId = (int) ($item['id'] ?? 0);
        if ($beritaId > 0) {
            $this->recordContentView('berita', $beritaId);
            try {
                $db->table('berita')->where('id', $beritaId)->update([
                    'views' => (int) ($item['views'] ?? 0) + 1,
                ]);
                $item['views'] = (int) ($item['views'] ?? 0) + 1;
            } catch (\Throwable $e) {
            }
        }

        $related = $db
            ->table('berita')
            ->where('status', 'publish')
            ->orderBy('created_at', 'DESC')
            ->limit(4)
            ->get() ?: [];

        return $this->view('pages/berita/show', $this->siteData([
            'title' => (string) ($item['judul'] ?? 'Berita Gampong'),
            'page' => 'berita',
            'berita_item' => $item,
            'related_berita' => array_filter($related, static fn($row) => (string) ($row['slug'] ?? '') !== $slug),
        ]));
    }

    private function recordContentView(string $type, int $id): void
    {
        try {
            $pdo = \App\Core\Database::getInstance()->getConnection();
            $sql = "INSERT INTO content_views_daily (content_type, content_id, view_date, view_count, created_at, updated_at)
                    VALUES (:type, :content_id, :view_date, 1, :created_at, :updated_at)
                    ON DUPLICATE KEY UPDATE view_count = view_count + 1, updated_at = VALUES(updated_at)";
            $stmt = $pdo->prepare($sql);
            $now = date('Y-m-d H:i:s');
            $stmt->execute([
                ':type' => $type,
                ':content_id' => $id,
                ':view_date' => date('Y-m-d'),
                ':created_at' => $now,
                ':updated_at' => $now,
            ]);
        } catch (\Throwable $e) {
        }
    }
}
