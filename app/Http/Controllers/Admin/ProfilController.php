<?php

namespace App\Http\Controllers\Admin;

use App\Core\Database;
use App\Core\Response;
use App\Core\Session;
use App\Core\View;
use App\Http\Controllers\Controller;
use App\Models\Pengaturan;
use App\Models\ProfilDesa;

class ProfilController extends Controller
{
    public function index(): Response
    {
        $profil = ProfilDesa::first() ?: [];
        $id = (int) ($profil['id'] ?? 1);

        if (empty($profil)) {
            Database::getInstance()->table('profil_desa')->insert([
                'nama_desa' => 'Desa',
                'alamat' => '',
                'telepon' => '',
                'email' => '',
                'sejarah' => '',
                'visi_misi' => '',
                'struktur_organisasi' => '',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            $profil = ProfilDesa::first() ?: [];
            $id = (int) ($profil['id'] ?? 1);
        }

        return Response::make(View::renderWithLayout('pages/admin/profil/index', $this->baseData([
            'title' => 'Profil Desa',
            'page' => 'admin-profil',
            'profil_data' => $profil,
            'profil_id' => $id,
        ]), 'layouts/admin'));
    }

    public function update(): Response
    {
        $id = $this->resolveRouteId();
        if ($id <= 0) {
            Session::flash('error', 'Profil desa tidak valid.');
            return $this->redirect(base_url('/admin/profil'));
        }

        $namaDesa = trim((string) $this->request->post('nama_desa', ''));
        $alamat = trim((string) $this->request->post('alamat', ''));
        $telepon = trim((string) $this->request->post('telepon', ''));
        $email = trim((string) $this->request->post('email', ''));
        $sejarah = $this->normalizeEditorHtml((string) $this->request->post('sejarah', ''));
        $visiMisi = $this->normalizeEditorHtml((string) $this->request->post('visi_misi', ''));
        $struktur = $this->normalizeEditorHtml((string) $this->request->post('struktur_organisasi', ''));

        if ($namaDesa === '') {
            Session::flash('error', 'Nama desa wajib diisi.');
            return $this->redirect(base_url('/admin/profil'));
        }

        Database::getInstance()->table('profil_desa')->where('id', $id)->update([
            'nama_desa' => $namaDesa,
            'alamat' => $alamat,
            'telepon' => $telepon,
            'email' => $email,
            'sejarah' => $sejarah,
            'visi_misi' => $visiMisi,
            'struktur_organisasi' => $struktur,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        Session::flash('success', 'Profil desa berhasil diperbarui.');
        return $this->redirect(base_url('/admin/profil'));
    }

    private function resolveRouteId(): int
    {
        $id = (int) $this->request->getParam('id', 0);
        if ($id > 0) {
            return $id;
        }

        $uri = (string) $this->request->getUri();
        if (preg_match('#/(\d+)$#', $uri, $matches) === 1) {
            return (int) $matches[1];
        }

        return 0;
    }

    private function baseData(array $extra = []): array
    {
        $profil = ProfilDesa::first() ?: [];
        $pengaturan = Pengaturan::getAll() ?: [];
        $websiteNama = trim((string) ($pengaturan['website_nama'] ?? '')) ?: 'Website Desa';
        $desaNama = trim((string) ($profil['nama_desa'] ?? '')) ?: 'Desa';

        return array_merge([
            'website_nama' => $websiteNama,
            'website_deskripsi' => (string) ($pengaturan['website_deskripsi'] ?? ''),
            'desa_nama' => $desaNama,
            'user_name' => Session::get('user_name', 'Administrator'),
            'user_role' => Session::get('user_role', 'admin'),
        ], $extra);
    }
}
