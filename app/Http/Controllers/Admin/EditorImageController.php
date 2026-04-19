<?php

namespace App\Http\Controllers\Admin;

use App\Core\Database;
use App\Core\Response;
use App\Core\Session;
use App\Http\Controllers\Controller;

class EditorImageController extends Controller
{
    public function upload(): Response
    {
        $requestToken = trim((string) $this->request->post('_token', ''));
        $sessionToken = (string) csrf_token();
        if ($requestToken === '' || !hash_equals($sessionToken, $requestToken)) {
            return $this->json([
                'success' => false,
                'message' => 'Token keamanan tidak valid.',
            ], 419);
        }

        if (!$this->request->hasFile('image')) {
            return $this->json([
                'success' => false,
                'message' => 'File gambar tidak ditemukan.',
            ], 422);
        }

        $file = $this->request->file('image');
        if (!is_array($file)) {
            return $this->json([
                'success' => false,
                'message' => 'Data upload gambar tidak valid.',
            ], 422);
        }

        $uploadErrorCode = (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE);
        if ($uploadErrorCode !== UPLOAD_ERR_OK) {
            return $this->json([
                'success' => false,
                'message' => 'Upload gambar gagal: ' . $this->uploadErrorMessage($uploadErrorCode),
            ], 422);
        }

        $tmpPath = (string) ($file['tmp_name'] ?? '');
        $originalName = (string) ($file['name'] ?? 'image');
        $extension = strtolower((string) pathinfo($originalName, PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

        if (!in_array($extension, $allowedExtensions, true)) {
            return $this->json([
                'success' => false,
                'message' => 'Format gambar tidak didukung.',
            ], 422);
        }

        $maxFileSize = 5 * 1024 * 1024;
        $fileSize = (int) ($file['size'] ?? 0);
        if ($fileSize <= 0 || $fileSize > $maxFileSize) {
            return $this->json([
                'success' => false,
                'message' => 'Ukuran gambar maksimal 5MB.',
            ], 422);
        }

        $imageInfo = @getimagesize($tmpPath);
        if ($imageInfo === false) {
            return $this->json([
                'success' => false,
                'message' => 'File yang diunggah bukan gambar valid.',
            ], 422);
        }

        $year = date('Y');
        $month = date('m');
        $relativeDir = 'uploads/editor/' . $year . '/' . $month;
        $uploadDir = public_path($relativeDir);

        if (!$this->ensureUploadDirectory($uploadDir)) {
            return $this->json([
                'success' => false,
                'message' => 'Folder upload tidak tersedia atau tidak bisa ditulis.',
            ], 500);
        }

        $safeBaseName = preg_replace('/[^a-z0-9\-_]+/i', '-', pathinfo($originalName, PATHINFO_FILENAME));
        $safeBaseName = trim((string) $safeBaseName, '-');
        if ($safeBaseName === '') {
            $safeBaseName = 'editor-image';
        }

        $fileName = $safeBaseName . '-' . date('YmdHis') . '-' . substr(bin2hex(random_bytes(8)), 0, 8) . '.' . $extension;
        $absolutePath = $uploadDir . DIRECTORY_SEPARATOR . $fileName;

        if (!$this->moveUploadedFileSafely($tmpPath, $absolutePath)) {
            return $this->json([
                'success' => false,
                'message' => 'Gagal menyimpan file ke folder upload.',
            ], 500);
        }

        $relativePath = $relativeDir . '/' . $fileName;
        $fileUrl = $this->buildRelativeUploadUrl($relativePath);
        $module = trim((string) $this->request->post('module', 'general'));
        if ($module === '') {
            $module = 'general';
        }

        $id = Database::getInstance()->table('editor_images')->insert([
            'module' => $module,
            'ref_table' => null,
            'ref_id' => null,
            'file_name' => $fileName,
            'file_path' => $relativePath,
            'file_url' => $fileUrl,
            'mime_type' => (string) ($imageInfo['mime'] ?? ''),
            'file_size' => $fileSize,
            'uploaded_by' => (int) Session::get('user_id', 0) ?: null,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        return $this->json([
            'success' => true,
            'message' => 'Gambar berhasil diunggah.',
            'data' => [
                'id' => $id,
                'url' => $fileUrl,
                'path' => $relativePath,
                'name' => $fileName,
            ],
        ]);
    }

    private function buildRelativeUploadUrl(string $relativePath): string
    {
        $basePath = (string) parse_url((string) BASE_URL, PHP_URL_PATH);
        $basePath = trim($basePath, '/');
        $path = trim($relativePath, '/');

        if ($basePath === '') {
            return '/' . $path;
        }

        return '/' . $basePath . '/' . $path;
    }
}
