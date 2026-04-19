<?php

namespace App\Http\Controllers;

use App\Core\Session;
use App\Models\User;
class AuthController extends Controller
{
    public function showLogin()
    {
        $siteData = $this->siteData();
        return $this->view('pages/auth/login', array_merge($siteData, [
            'title' => 'Login - ' . (string) ($siteData['website_nama'] ?? 'Website'),
            'page' => 'auth-login',
        ]));
    }

    public function login()
    {
        $identifier = trim((string) $this->request->post('identifier', ''));
        $password = (string) $this->request->post('password', '');
        $remember = $this->request->post('remember');

        set_old_input([
            'identifier' => $identifier,
            'remember' => $remember ? '1' : '',
        ]);

        $errors = [];

        if ($identifier === '') {
            $errors['identifier'][] = 'Username atau email wajib diisi';
        }

        if ($password === '') {
            $errors['password'][] = 'Password wajib diisi';
        }

        if (!empty($errors)) {
            Session::flash('errors', $errors);
            Session::flash('error', 'Mohon lengkapi data login terlebih dahulu.');
            return $this->redirect(base_url('/login'));
        }

        $user = User::findByIdentifier($identifier);

        if (!$user || $user['is_active'] !== 1 || !password_verify($password, $user['password'])) {
            Session::flash('error', 'Kredensial tidak valid. Silakan periksa kembali.');
            return $this->redirect(base_url('/login'));
        }

        Session::regenerate(true);
        Session::set('user_id', $user['id']);
        Session::set('user_role', $user['role']);
        Session::set('user_name', $user['nama_lengkap']);
        Session::set('user_username', $user['username']);

        if ($remember) {
            Session::set('remember_login', true);
        } else {
            Session::forget('remember_login');
        }

        Session::forget('_old_input');

        $redirectUrl = Session::pull('redirect_url', '');

        if (is_string($redirectUrl) && str_starts_with($redirectUrl, '/')) {
            return $this->redirect(base_url(ltrim($redirectUrl, '/')));
        }

        if (is_string($redirectUrl) && str_starts_with($redirectUrl, BASE_URL)) {
            return $this->redirect($redirectUrl);
        }

        if (($user['role'] ?? '') === 'admin') {
            return $this->redirect(base_url('/admin/dashboard'));
        }

        return $this->redirect(base_url());
    }

    public function logout()
    {
        Session::invalidate();
        Session::flash('success', 'Anda berhasil logout.');

        return $this->redirect(base_url('/login'));
    }
}
