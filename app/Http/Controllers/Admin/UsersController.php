<?php

namespace App\Http\Controllers\Admin;

use App\Core\Database;
use App\Core\Response;
use App\Core\Session;
use App\Core\View;
use App\Http\Controllers\Controller;
use App\Models\Pengaturan;
use App\Models\ProfilDesa;

class UsersController extends Controller
{
    public function index(): Response
    {
        $list = Database::getInstance()->table('users')->orderBy('created_at', 'DESC')->get() ?: [];

        return Response::make(View::renderWithLayout('pages/admin/users/index', $this->baseData([
            'title' => 'Manajemen Pengguna',
            'page' => 'admin-users',
            'users' => $list,
        ]), 'layouts/admin'));
    }

    public function create(): Response
    {
        return Response::make(View::renderWithLayout('pages/admin/users/form', $this->baseData([
            'title' => 'Tambah Pengguna',
            'page' => 'admin-users',
            'mode' => 'create',
            'user_item' => [],
        ]), 'layouts/admin'));
    }

    public function store(): Response
    {
        $username = trim((string) $this->request->post('username', ''));
        $nama = trim((string) $this->request->post('nama_lengkap', ''));
        $email = trim((string) $this->request->post('email', ''));
        $role = trim((string) $this->request->post('role', 'author'));
        $password = (string) $this->request->post('password', '');
        $isActive = $this->request->post('is_active') ? 1 : 0;

        if ($username === '' || $nama === '' || $password === '') {
            Session::flash('error', 'Username, nama lengkap, dan password wajib diisi.');
            return $this->redirect(base_url('/admin/users/create'));
        }

        if (!in_array($role, ['admin', 'editor', 'author'], true)) {
            $role = 'author';
        }

        $exists = Database::getInstance()->table('users')->where('username', $username)->first();
        if ($exists) {
            Session::flash('error', 'Username sudah digunakan.');
            return $this->redirect(base_url('/admin/users/create'));
        }

        Database::getInstance()->table('users')->insert([
            'username' => $username,
            'password' => password_hash($password, PASSWORD_BCRYPT),
            'email' => $email === '' ? null : $email,
            'nama_lengkap' => $nama,
            'role' => $role,
            'created_at' => date('Y-m-d H:i:s'),
            'is_active' => $isActive,
        ]);

        Session::flash('success', 'Pengguna berhasil ditambahkan.');
        return $this->redirect(base_url('/admin/users'));
    }

    public function edit(): Response
    {
        $id = $this->resolveRouteId();
        $item = Database::getInstance()->table('users')->where('id', $id)->first();
        if (!$item) {
            Session::flash('error', 'Pengguna tidak ditemukan.');
            return $this->redirect(base_url('/admin/users'));
        }

        return Response::make(View::renderWithLayout('pages/admin/users/form', $this->baseData([
            'title' => 'Edit Pengguna',
            'page' => 'admin-users',
            'mode' => 'edit',
            'user_item' => $item,
        ]), 'layouts/admin'));
    }

    public function update(): Response
    {
        $id = $this->resolveRouteId();
        $item = Database::getInstance()->table('users')->where('id', $id)->first();
        if (!$item) {
            Session::flash('error', 'Pengguna tidak ditemukan.');
            return $this->redirect(base_url('/admin/users'));
        }

        $username = trim((string) $this->request->post('username', ''));
        $nama = trim((string) $this->request->post('nama_lengkap', ''));
        $email = trim((string) $this->request->post('email', ''));
        $role = trim((string) $this->request->post('role', 'author'));
        $password = (string) $this->request->post('password', '');
        $isActive = $this->request->post('is_active') ? 1 : 0;

        if ($username === '' || $nama === '') {
            Session::flash('error', 'Username dan nama lengkap wajib diisi.');
            return $this->redirect(base_url('/admin/users/edit/' . $id));
        }

        if (!in_array($role, ['admin', 'editor', 'author'], true)) {
            $role = 'author';
        }

        $duplicate = Database::getInstance()->table('users')->where('username', $username)->first();
        if ($duplicate && (int) ($duplicate['id'] ?? 0) !== $id) {
            Session::flash('error', 'Username sudah digunakan pengguna lain.');
            return $this->redirect(base_url('/admin/users/edit/' . $id));
        }

        $payload = [
            'username' => $username,
            'email' => $email === '' ? null : $email,
            'nama_lengkap' => $nama,
            'role' => $role,
            'is_active' => $isActive,
        ];

        if ($password !== '') {
            $payload['password'] = password_hash($password, PASSWORD_BCRYPT);
        }

        Database::getInstance()->table('users')->where('id', $id)->update($payload);
        Session::flash('success', 'Pengguna berhasil diperbarui.');
        return $this->redirect(base_url('/admin/users'));
    }

    public function destroy(): Response
    {
        $id = $this->resolveRouteId();
        $currentUserId = (int) Session::get('user_id', 0);
        if ($id === $currentUserId) {
            Session::flash('error', 'Akun yang sedang digunakan tidak dapat dihapus.');
            return $this->redirect(base_url('/admin/users'));
        }

        $item = Database::getInstance()->table('users')->where('id', $id)->first();
        if (!$item) {
            Session::flash('error', 'Pengguna tidak ditemukan.');
            return $this->redirect(base_url('/admin/users'));
        }

        Database::getInstance()->table('users')->where('id', $id)->delete();
        Session::flash('success', 'Pengguna berhasil dihapus.');
        return $this->redirect(base_url('/admin/users'));
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
