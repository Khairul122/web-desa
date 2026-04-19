<?php

namespace App\Http\Controllers;

class ProfilController extends Controller
{
    public function index()
    {
        $data = $this->resolveProfilData();

        return $this->view('pages/profil/index', $this->siteData([
            'title' => 'Profil',
            'page' => 'profil',
            'profil_data' => $data['profil'],
            'sejarah' => $data['sejarah'],
            'visi' => $data['visi'],
            'misi_list' => $data['misi_list'],
            'struktur_organisasi' => $data['struktur_organisasi'],
        ]));
    }

    public function visiMisi()
    {
        $data = $this->resolveProfilData();

        return $this->view('pages/profil/visi-misi', $this->siteData([
            'title' => 'Visi & Misi',
            'page' => 'profil-visi-misi',
            'profil_data' => $data['profil'],
            'visi' => $data['visi'],
            'misi_list' => $data['misi_list'],
        ]));
    }

    public function strukturOrganisasi()
    {
        $data = $this->resolveProfilData();

        return $this->view('pages/profil/struktur-organisasi', $this->siteData([
            'title' => 'Struktur Organisasi',
            'page' => 'profil-struktur-organisasi',
            'profil_data' => $data['profil'],
            'struktur_organisasi' => $data['struktur_organisasi'],
        ]));
    }

    public function show(string $slug)
    {
        return $this->redirect(base_url('/profil'));
    }

    private function resolveProfilData(): array
    {
        $profil = \App\Models\ProfilDesa::first() ?: [];
        $sejarah = trim((string) ($profil['sejarah'] ?? ''));
        $visiMisiRaw = trim((string) ($profil['visi_misi'] ?? ''));
        $struktur = trim((string) ($profil['struktur_organisasi'] ?? ''));

        $visi = '';
        $misiList = [];

        if ($visiMisiRaw !== '') {
            if (preg_match('/visi\s*[:\-]\s*(.+?)(?=\bmisi\b\s*[:\-]|$)/is', $visiMisiRaw, $matchVisi)) {
                $visi = trim(strip_tags((string) ($matchVisi[1] ?? '')));
            }

            if (preg_match('/misi\s*[:\-]\s*(.+)$/is', $visiMisiRaw, $matchMisi)) {
                $rawMisi = preg_split('/\r\n|\r|\n|\d+[\.)]\s*/', trim((string) ($matchMisi[1] ?? '')));
                foreach ((array) $rawMisi as $item) {
                    $item = trim(strip_tags((string) $item), " \t\n\r\0\x0B-");
                    if ($item !== '') {
                        $misiList[] = $item;
                    }
                }
            }
        }

        if ($visi === '' && !empty($misiList)) {
            $visi = (string) $misiList[0];
        }

        return [
            'profil' => $profil,
            'sejarah' => $sejarah,
            'visi' => $visi,
            'misi_list' => $misiList,
            'struktur_organisasi' => $struktur,
        ];
    }
}
