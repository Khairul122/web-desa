<?php

namespace App\Http\Controllers\Admin;

use App\Core\Database;
use App\Core\Response;
use App\Core\Session;
use App\Core\View;
use App\Http\Controllers\Controller;
use App\Models\Pengaturan;
use App\Models\ProfilDesa;

class StatistikController extends Controller
{
    public function index(): Response
    {
        $items = Database::getInstance()->table('statistik_desa')->orderBy('urutan', 'ASC')->orderBy('id', 'DESC')->get() ?: [];

        return Response::make(View::renderWithLayout('pages/admin/statistik/index', $this->baseData([
            'title' => 'Manajemen Statistik Gampong',
            'page' => 'admin-statistik',
            'statistik' => $items,
        ]), 'layouts/admin'));
    }

    public function create(): Response
    {
        return Response::make(View::renderWithLayout('pages/admin/statistik/form', $this->baseData([
            'title' => 'Tambah Statistik Gampong',
            'page' => 'admin-statistik',
            'mode' => 'create',
            'statistik_item' => [],
        ]), 'layouts/admin'));
    }

    public function store(): Response
    {
        $nama = trim((string) $this->request->post('nama_statistik', ''));
        $nilai = trim((string) $this->request->post('nilai_statistik', ''));
        $icon = statistik_icon_key((string) $this->request->post('icon', 'warga'));
        $urutan = (int) $this->request->post('urutan', 0);

        if ($nama === '' || $nilai === '') {
            Session::flash('error', 'Nama statistik dan nilai wajib diisi.');
            return $this->redirect(base_url('/admin/statistik/create'));
        }

        Database::getInstance()->table('statistik_desa')->insert([
            'nama_statistik' => $nama,
            'nilai_statistik' => $nilai,
            'icon' => $icon,
            'urutan' => $urutan,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        Session::flash('success', 'Statistik berhasil ditambahkan.');
        return $this->redirect(base_url('/admin/statistik'));
    }

    public function edit(): Response
    {
        $id = $this->resolveRouteId();
        $item = Database::getInstance()->table('statistik_desa')->where('id', $id)->first();
        if (!$item) {
            Session::flash('error', 'Data statistik tidak ditemukan.');
            return $this->redirect(base_url('/admin/statistik'));
        }

        return Response::make(View::renderWithLayout('pages/admin/statistik/form', $this->baseData([
            'title' => 'Edit Statistik Gampong',
            'page' => 'admin-statistik',
            'mode' => 'edit',
            'statistik_item' => $item,
        ]), 'layouts/admin'));
    }

    public function update(): Response
    {
        $id = $this->resolveRouteId();
        $item = Database::getInstance()->table('statistik_desa')->where('id', $id)->first();
        if (!$item) {
            Session::flash('error', 'Data statistik tidak ditemukan.');
            return $this->redirect(base_url('/admin/statistik'));
        }

        $nama = trim((string) $this->request->post('nama_statistik', ''));
        $nilai = trim((string) $this->request->post('nilai_statistik', ''));
        $icon = statistik_icon_key((string) $this->request->post('icon', 'warga'));
        $urutan = (int) $this->request->post('urutan', 0);

        if ($nama === '' || $nilai === '') {
            Session::flash('error', 'Nama statistik dan nilai wajib diisi.');
            return $this->redirect(base_url('/admin/statistik/edit/' . $id));
        }

        Database::getInstance()->table('statistik_desa')->where('id', $id)->update([
            'nama_statistik' => $nama,
            'nilai_statistik' => $nilai,
            'icon' => $icon,
            'urutan' => $urutan,
        ]);

        Session::flash('success', 'Statistik berhasil diperbarui.');
        return $this->redirect(base_url('/admin/statistik'));
    }

    public function destroy(): Response
    {
        $id = $this->resolveRouteId();
        $item = Database::getInstance()->table('statistik_desa')->where('id', $id)->first();
        if (!$item) {
            Session::flash('error', 'Data statistik tidak ditemukan.');
            return $this->redirect(base_url('/admin/statistik'));
        }

        Database::getInstance()->table('statistik_desa')->where('id', $id)->delete();
        Session::flash('success', 'Statistik berhasil dihapus.');
        return $this->redirect(base_url('/admin/statistik'));
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
