<?php

namespace App\Http\Controllers\Admin;

use App\Core\Database;
use App\Core\Response;
use App\Core\Session;
use App\Core\View;
use App\Http\Controllers\Controller;
use App\Models\Pengaturan;
use App\Models\ProfilDesa;

class KontakController extends Controller
{
    public function index(): Response
    {
        $statusFilter = trim((string) $this->request->get('status', ''));
        $search = trim((string) $this->request->get('q', ''));

        $pdo = Database::getInstance()->getConnection();
        $whereParts = [];
        $bindings = [];

        if ($statusFilter !== '' && in_array($statusFilter, ['baru', 'dibaca', 'dibalas'], true)) {
            $whereParts[] = 'k.status = :status';
            $bindings[':status'] = $statusFilter;
        }

        if ($search !== '') {
            $whereParts[] = '(k.nama LIKE :search OR k.email LIKE :search OR k.subjek LIKE :search OR k.pesan LIKE :search)';
            $bindings[':search'] = '%' . $search . '%';
        }

        $whereSql = empty($whereParts) ? '' : ('WHERE ' . implode(' AND ', $whereParts));

        $stmt = $pdo->prepare("SELECT k.* FROM kontak k {$whereSql} ORDER BY k.created_at DESC");
        foreach ($bindings as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        $kontak = $stmt->fetchAll() ?: [];

        return Response::make(View::renderWithLayout('pages/admin/kontak/index', $this->baseData([
            'title' => 'Manajemen Pesan Kontak',
            'page' => 'admin-kontak',
            'kontak' => $kontak,
            'filters' => [
                'status' => $statusFilter,
                'q' => $search,
            ],
        ]), 'layouts/admin'));
    }

    public function show(): Response
    {
        $id = $this->resolveRouteId();
        $item = Database::getInstance()->table('kontak')->where('id', $id)->first();
        if (!$item) {
            Session::flash('error', 'Pesan tidak ditemukan.');
            return $this->redirect(base_url('/admin/kontak'));
        }

        if ((string) ($item['status'] ?? '') === 'baru') {
            Database::getInstance()->table('kontak')->where('id', $id)->update(['status' => 'dibaca']);
            $item['status'] = 'dibaca';
        }

        return Response::make(View::renderWithLayout('pages/admin/kontak/show', $this->baseData([
            'title' => 'Detail Pesan Kontak',
            'page' => 'admin-kontak',
            'kontak_item' => $item,
        ]), 'layouts/admin'));
    }

    public function updateStatus(): Response
    {
        $id = $this->resolveRouteId();
        $status = trim((string) $this->request->post('status', ''));

        if (!in_array($status, ['baru', 'dibaca', 'dibalas'], true)) {
            Session::flash('error', 'Status pesan tidak valid.');
            return $this->redirect(base_url('/admin/kontak'));
        }

        $item = Database::getInstance()->table('kontak')->where('id', $id)->first();
        if (!$item) {
            Session::flash('error', 'Pesan tidak ditemukan.');
            return $this->redirect(base_url('/admin/kontak'));
        }

        Database::getInstance()->table('kontak')->where('id', $id)->update(['status' => $status]);
        Session::flash('success', 'Status pesan berhasil diperbarui.');
        return $this->redirect(base_url('/admin/kontak/show/' . $id));
    }

    public function destroy(): Response
    {
        $id = $this->resolveRouteId();
        $item = Database::getInstance()->table('kontak')->where('id', $id)->first();
        if (!$item) {
            Session::flash('error', 'Pesan tidak ditemukan.');
            return $this->redirect(base_url('/admin/kontak'));
        }

        Database::getInstance()->table('kontak')->where('id', $id)->delete();
        Session::flash('success', 'Pesan kontak berhasil dihapus.');
        return $this->redirect(base_url('/admin/kontak'));
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
