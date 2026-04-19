<?php

namespace App\Http\Controllers\Admin;

use App\Core\Database;
use App\Core\Response;
use App\Core\Session;
use App\Core\View;
use App\Http\Controllers\Controller;
use App\Models\Pengaturan;
use App\Models\ProfilDesa;

class AccountController extends Controller
{
    public function index(): Response
    {
        $userId = (int) Session::get('user_id', 0);
        $user = Database::getInstance()->table('users')->where('id', $userId)->first() ?: [];

        return Response::make(View::renderWithLayout('pages/admin/account/index', $this->baseData([
            'title' => 'Profil Admin',
            'page' => 'admin-account',
            'account' => $user,
        ]), 'layouts/admin'));
    }

    public function update(): Response
    {
        $userId = (int) Session::get('user_id', 0);
        $user = Database::getInstance()->table('users')->where('id', $userId)->first();
        if (!$user) {
            Session::flash('error', 'Akun tidak ditemukan.');
            return $this->redirect(base_url('/login'));
        }

        $nama = trim((string) $this->request->post('nama_lengkap', ''));
        $email = trim((string) $this->request->post('email', ''));
        $username = trim((string) $this->request->post('username', ''));
        $password = (string) $this->request->post('password', '');

        if ($nama === '' || $username === '') {
            Session::flash('error', 'Nama lengkap dan username wajib diisi.');
            return $this->redirect(base_url('/admin/profile'));
        }

        $duplicate = Database::getInstance()->table('users')->where('username', $username)->first();
        if ($duplicate && (int) ($duplicate['id'] ?? 0) !== $userId) {
            Session::flash('error', 'Username sudah dipakai pengguna lain.');
            return $this->redirect(base_url('/admin/profile'));
        }

        $payload = [
            'nama_lengkap' => $nama,
            'email' => $email === '' ? null : $email,
            'username' => $username,
        ];

        if ($password !== '') {
            $payload['password'] = password_hash($password, PASSWORD_BCRYPT);
        }

        Database::getInstance()->table('users')->where('id', $userId)->update($payload);
        Session::set('user_name', $nama);
        Session::set('user_username', $username);
        Session::flash('success', 'Profil akun berhasil diperbarui.');

        return $this->redirect(base_url('/admin/profile'));
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
