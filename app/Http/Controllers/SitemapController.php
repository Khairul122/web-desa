<?php

namespace App\Http\Controllers;

use App\Core\Database;
use App\Models\Berita;
use App\Models\Galeri;

class SitemapController extends Controller
{
    public function index()
    {
        $db = Database::getInstance();
        
        // Ambil data berita yang terbit
        $berita = $db->table('berita')->where('status', 'publish')->get();
        
        // Ambil data galeri
        $galeri = $db->table('galeri')->get();

        if (ob_get_length()) ob_clean();
        header('Content-Type: application/xml; charset=utf-8');
        
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        
        // Homepage
        $xml .= $this->getUrlXml(base_url(), '1.0', 'daily');
        
        // Halaman Statis
        $xml .= $this->getUrlXml(base_url('/profil'), '0.8', 'monthly');
        $xml .= $this->getUrlXml(base_url('/profil/visi-misi'), '0.8', 'monthly');
        $xml .= $this->getUrlXml(base_url('/profil/struktur-organisasi'), '0.8', 'monthly');
        $xml .= $this->getUrlXml(base_url('/berita'), '0.9', 'daily');
        $xml .= $this->getUrlXml(base_url('/galeri'), '0.8', 'weekly');
        $xml .= $this->getUrlXml(base_url('/kontak'), '0.7', 'monthly');
        
        // Berita Dinamis
        foreach ($berita as $b) {
            $slug = $b['slug'] ?? '';
            if ($slug !== '') {
                $xml .= $this->getUrlXml(base_url('/berita/' . $slug), '0.8', 'monthly');
            }
        }
        
        // Galeri Dinamis
        foreach ($galeri as $g) {
            $slug = $g['slug'] ?? '';
            if ($slug !== '') {
                $xml .= $this->getUrlXml(base_url('/galeri/' . $slug), '0.7', 'monthly');
            }
        }
        
        $xml .= '</urlset>';
        
        echo $xml;
        exit;
    }

    private function getUrlXml($url, $priority = '0.5', $changefreq = 'monthly')
    {
        $xml = '<url>';
        $xml .= '<loc>' . htmlspecialchars($url) . '</loc>';
        $xml .= '<changefreq>' . $changefreq . '</changefreq>';
        $xml .= '<priority>' . $priority . '</priority>';
        $xml .= '</url>';
        return $xml;
    }
}
