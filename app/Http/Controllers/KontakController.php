<?php

namespace App\Http\Controllers;

use App\Core\Session;

class KontakController extends Controller
{
    public function index()
    {
        return $this->view('pages/kontak/index', $this->siteData([
            'title' => 'Kontak Kami',
            'page' => 'kontak',
        ]));
    }

    public function send()
    {
        set_old_input([
            'nama' => trim((string) $this->request->post('nama', '')),
            'email' => trim((string) $this->request->post('email', '')),
            'subjek' => trim((string) $this->request->post('subjek', '')),
            'pesan' => trim((string) $this->request->post('pesan', '')),
        ]);

        $rules = [
            'nama' => 'required|min:2',
            'email' => 'required|email',
            'subjek' => 'required|min:3',
            'pesan' => 'required|min:10'
        ];

        $validation = \App\Core\Validator::make($this->request->all(), $rules)->validate();
        $hasErrors = false;
        foreach ($validation as $value) {
            if (is_array($value)) {
                $hasErrors = true;
                break;
            }
        }

        if ($hasErrors) {
            Session::flash('errors', $validation);
            Session::flash('error', 'Mohon periksa kembali form kontak Anda.');
            return $this->redirect(base_url('/kontak'));
        }

        \App\Core\Database::getInstance()->table('kontak')->insert([
            'nama' => trim((string) $this->request->post('nama', '')),
            'email' => trim((string) $this->request->post('email', '')),
            'subjek' => trim((string) $this->request->post('subjek', '')),
            'pesan' => trim((string) $this->request->post('pesan', '')),
            'status' => 'baru',
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        Session::flash('success', 'Pesan berhasil dikirim!');
        Session::forget('_old_input');
        return $this->redirect(base_url('/kontak'));
    }
}
