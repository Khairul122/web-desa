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

        if (ob_get_length()) ob_clean();
        header('Content-Type: application/xml; charset=utf-8');
        
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        
        // Homepage
        $xml .= $this->getUrlXml(base_url(), '1.0', 'daily');
        
        // Static Pages
        $xml .= $this->getUrlXml(base_url('/profil'), '0.8', 'monthly');
        $xml .= $this->getUrlXml(base_url('/profil/visi-misi'), '0.8', 'monthly');
        $xml .= $this->getUrlXml(base_url('/profil/struktur-organisasi'), '0.8', 'monthly');
        $xml .= $this->getUrlXml(base_url('/berita'), '0.9', 'daily');
        $xml .= $this->getUrlXml(base_url('/galeri'), '0.8', 'weekly');
        $xml .= $this->getUrlXml(base_url('/kontak'), '0.7', 'monthly');
        
        // Dynamic Profil
        foreach ($profil as $p) {
            $xml .= $this->getUrlXml(base_url('/profil/' . $p->slug), '0.8', 'monthly');
        }
        
        // Dynamic Berita
        foreach ($berita as $b) {
            $xml .= $this->getUrlXml(base_url('/berita/' . $b->slug), '0.8', 'monthly');
        }
        
        // Dynamic Galeri
        foreach ($galeri as $g) {
            $xml .= $this->getUrlXml(base_url('/galeri/' . $g->slug), '0.7', 'monthly');
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
