<?php

namespace App\Http\Controllers;

use App\Core\Controller as BaseController;
use App\Core\Database;
use App\Core\Validator;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\CSRF;
use App\Models\Pengaturan;
use App\Models\ProfilDesa;

abstract class Controller extends BaseController
{
    protected $db;
    protected $request;
    protected $response;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->request = Request::capture();
        $this->response = new Response();
    }

    protected function model(string $model): object
    {
        $modelClass = 'App\\Models\\' . $model;
        return new $modelClass();
    }

    protected function view(string $view, array $data = []): Response
    {
        return Response::view($view, $data);
    }

    protected function redirect(string $url): Response
    {
        return Response::redirect($url);
    }

    protected function redirectBack(): Response
    {
        return Response::back();
    }

    protected function with(string $key, $value): Response
    {
        return $this->response->with($key, $value);
    }

    protected function withSuccess(string $message): Response
    {
        return $this->response->withSuccess($message);
    }

    protected function withError(string $message): Response
    {
        return $this->response->withError($message);
    }

    protected function json(array $data, int $statusCode = 200): Response
    {
        return Response::json($data, $statusCode);
    }

    protected function validate(array $rules): array
    {
        return \App\Core\Validator::make($this->request->all(), $rules)->validate();
    }

    protected function abort(int $code = 404, string $message = ''): void
    {
        http_response_code($code);
        include APP_PATH . '/Views/errors/' . $code . '.php';
        exit;
    }

    protected function siteData(array $extra = []): array
    {
        $profil = ProfilDesa::first() ?: [];
        $pengaturan = Pengaturan::getAll() ?: [];

        $desaNama = trim((string) ($profil['nama_desa'] ?? '')) ?: 'Desa';
        $websiteNama = trim((string) ($pengaturan['website_nama'] ?? '')) ?: 'Website Desa';

        return array_merge([
            'profil' => $profil,
            'pengaturan' => $pengaturan,
            'desa_nama' => $desaNama,
            'website_nama' => $websiteNama,
            'website_deskripsi' => (string) ($pengaturan['website_deskripsi'] ?? ''),
            'desa_alamat' => (string) ($profil['alamat'] ?? ''),
            'desa_telepon' => (string) ($profil['telepon'] ?? ''),
            'desa_email' => (string) ($profil['email'] ?? ''),
            'alamatDesa' => (string) ($profil['alamat'] ?? ''),
            'teleponDesa' => (string) ($profil['telepon'] ?? ''),
            'emailDesa' => (string) ($profil['email'] ?? ''),
            'social_facebook' => (string) ($pengaturan['social_facebook'] ?? ''),
            'social_instagram' => (string) ($pengaturan['social_instagram'] ?? ''),
            'social_youtube' => (string) ($pengaturan['social_youtube'] ?? ''),
            'whatsapp_number' => (string) ($pengaturan['whatsapp_number'] ?? ''),
            'logoDesa' => (string) ($pengaturan['logo_desa'] ?? ''),
        ], $extra);
    }

    protected function uploadErrorMessage(int $errorCode): string
    {
        return match ($errorCode) {
            UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'Ukuran file melebihi batas yang diizinkan server.',
            UPLOAD_ERR_PARTIAL => 'File terunggah sebagian. Silakan coba lagi.',
            UPLOAD_ERR_NO_FILE => 'Tidak ada file yang dipilih.',
            UPLOAD_ERR_NO_TMP_DIR => 'Folder sementara upload tidak tersedia di server.',
            UPLOAD_ERR_CANT_WRITE => 'Server gagal menulis file ke penyimpanan.',
            UPLOAD_ERR_EXTENSION => 'Upload dibatalkan oleh ekstensi PHP di server.',
            default => 'Upload gagal karena kesalahan tidak dikenal.',
        };
    }

    protected function ensureUploadDirectory(string $directory): bool
    {
        if (!is_dir($directory) && !mkdir($directory, 0755, true) && !is_dir($directory)) {
            return false;
        }

        if (!is_writable($directory)) {
            @chmod($directory, 0755);
        }

        return is_writable($directory);
    }

    protected function moveUploadedFileSafely(string $tmpPath, string $destination): bool
    {
        if ($tmpPath === '' || $destination === '') {
            return false;
        }

        if (move_uploaded_file($tmpPath, $destination)) {
            return true;
        }

        if (@rename($tmpPath, $destination)) {
            return true;
        }

        if (@copy($tmpPath, $destination)) {
            @unlink($tmpPath);
            return true;
        }

        return false;
    }

    protected function normalizeEditorHtml(string $html): string
    {
        $content = trim($html);
        if ($content === '') {
            return '';
        }

        $base = parse_url((string) BASE_URL);
        $baseHost = strtolower((string) ($base['host'] ?? ''));
        $basePort = (int) ($base['port'] ?? 0);

        return (string) preg_replace_callback(
            '/\b(src|href)\s*=\s*(["\'])(https?:\/\/[^"\']+)\2/i',
            static function (array $matches) use ($baseHost, $basePort): string {
                $attr = (string) ($matches[1] ?? 'src');
                $quote = (string) ($matches[2] ?? '"');
                $url = (string) ($matches[3] ?? '');

                $parts = parse_url($url);
                if (!is_array($parts)) {
                    return $matches[0];
                }

                $urlHost = strtolower((string) ($parts['host'] ?? ''));
                $urlPort = (int) ($parts['port'] ?? 0);

                if ($baseHost === '' || $urlHost === '' || $urlHost !== $baseHost) {
                    return $matches[0];
                }

                if ($basePort > 0 && $urlPort > 0 && $urlPort !== $basePort) {
                    return $matches[0];
                }

                $path = (string) ($parts['path'] ?? '/');
                if ($path === '') {
                    $path = '/';
                }

                if ($path[0] !== '/') {
                    $path = '/' . $path;
                }

                $normalized = $path;
                if (isset($parts['query']) && $parts['query'] !== '') {
                    $normalized .= '?' . $parts['query'];
                }

                if (isset($parts['fragment']) && $parts['fragment'] !== '') {
                    $normalized .= '#' . $parts['fragment'];
                }

                return $attr . '=' . $quote . $normalized . $quote;
            },
            $content
        );
    }
}
