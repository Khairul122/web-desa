<?php

namespace App\Http\Controllers;

use App\Models\Galeri;

class GaleriController extends Controller
{
    public function index()
    {
        $galeri = Galeri::all() ?: [];

        return $this->view('pages/galeri/index', $this->siteData([
            'title' => 'Galeri Gampong',
            'page' => 'galeri',
            'galeri' => $galeri,
        ]));
    }

    public function show(string $slug)
    {
        $db = \App\Core\Database::getInstance();

        $id = (int) preg_replace('/[^0-9]/', '', $slug);
        if ($id <= 0) {
            return $this->redirect(base_url('/galeri'));
        }

        $item = $db->table('galeri')->where('id', $id)->first();
        if (!$item) {
            return $this->redirect(base_url('/galeri'));
        }

        try {
            $db->table('galeri')->where('id', $id)->update([
                'views' => (int) ($item['views'] ?? 0) + 1,
            ]);
            $item['views'] = (int) ($item['views'] ?? 0) + 1;
        } catch (\Throwable $e) {
        }

        $this->recordContentView('galeri', $id);

        $related = $db
            ->table('galeri')
            ->orderBy('created_at', 'DESC')
            ->limit(8)
            ->get() ?: [];

        return $this->view('pages/galeri/show', $this->siteData([
            'title' => (string) ($item['judul'] ?? 'Galeri Gampong'),
            'page' => 'galeri',
            'galeri_item' => $item,
            'related_galeri' => array_filter($related, static fn($row) => (int) ($row['id'] ?? 0) !== $id),
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
