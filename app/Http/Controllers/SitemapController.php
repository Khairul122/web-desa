<?php

namespace App\Http\Controllers;

use App\Models\Berita;
use App\Models\Galeri;
use App\Models\ProfilDesa;

class SitemapController extends Controller
{
    public function index()
    {
        $berita = Berita::all();
        $galeri = Galeri::all();
        $profil = ProfilDesa::all();

        header('Content-Type: application/xml; charset=utf-8');
        
        echo '<?xml version="1.0" encoding="UTF-8"?>';
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        
        // Homepage
        $this->addUrl(base_url(), '1.0', 'daily');
        
        // Static Pages
        $this->addUrl(base_url('/profil'), '0.8', 'monthly');
        $this->addUrl(base_url('/profil/visi-misi'), '0.8', 'monthly');
        $this->addUrl(base_url('/profil/struktur-organisasi'), '0.8', 'monthly');
        $this->addUrl(base_url('/berita'), '0.9', 'daily');
        $this->addUrl(base_url('/galeri'), '0.8', 'weekly');
        $this->addUrl(base_url('/kontak'), '0.7', 'monthly');
        
        // Dynamic Profil
        foreach ($profil as $p) {
            $this->addUrl(base_url('/profil/' . $p->slug), '0.8', 'monthly');
        }
        
        // Dynamic Berita
        foreach ($berita as $b) {
            $this->addUrl(base_url('/berita/' . $b->slug), '0.8', 'monthly');
        }
        
        // Dynamic Galeri
        foreach ($galeri as $g) {
            $this->addUrl(base_url('/galeri/' . $g->slug), '0.7', 'monthly');
        }
        
        echo '</urlset>';
        exit;
    }

    private function addUrl($url, $priority = '0.5', $changefreq = 'monthly')
    {
        echo '<url>';
        echo '<loc>' . htmlspecialchars($url) . '</loc>';
        echo '<changefreq>' . $changefreq . '</changefreq>';
        echo '<priority>' . $priority . '</priority>';
        echo '</url>';
    }
}
