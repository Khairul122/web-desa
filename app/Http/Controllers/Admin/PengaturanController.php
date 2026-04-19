<?php

namespace App\Http\Controllers\Admin;

use App\Core\Database;
use App\Core\Response;
use App\Core\Session;
use App\Core\View;
use App\Http\Controllers\Controller;
use App\Models\Pengaturan;
use App\Models\ProfilDesa;

class PengaturanController extends Controller
{
    public function index(): Response
    {
        $settings = Pengaturan::getAll() ?: [];

        return Response::make(View::renderWithLayout('pages/admin/pengaturan/index', $this->baseData([
            'title' => 'Pengaturan Website',
            'page' => 'admin-pengaturan',
            'settings' => $settings,
        ]), 'layouts/admin'));
    }

    public function update(): Response
    {
        $keys = [
            'website_nama',
            'website_deskripsi',
            'website_keywords',
            'social_facebook',
            'social_instagram',
            'social_youtube',
            'whatsapp_number',
            'office_hours_text',
            'footer_brand_description',
            'footer_badge_text',
            'privacy_policy_label',
            'privacy_policy_url',
            'menu_profile_label',
            'menu_story_label',
            'menu_vision_label',
            'menu_structure_label',
            'contact_hero_title',
            'contact_hero_description',
            'login_visual_title',
            'login_form_description',
            'hero_explore_label',
            'home_services_title',
            'home_services_subtitle',
            'home_vision_title',
            'home_vision_subtitle',
            'home_news_title',
            'home_news_subtitle',
            'home_gallery_title',
            'home_gallery_subtitle',
            'home_stats_title',
            'home_stats_subtitle',
            'news_page_title',
            'news_page_description',
            'gallery_page_description',
            'profile_empty_history_text',
            'profile_info_title',
            'vision_page_description',
            'vision_empty_text',
            'mission_empty_text',
            'structure_page_description',
            'structure_section_title',
            'structure_image_alt',
            'structure_leader_subtitle',
            'structure_leader_description',
            'structure_empty_text',
            'service_item_1_title',
            'service_item_1_desc',
            'service_item_1_icon',
            'service_item_2_title',
            'service_item_2_desc',
            'service_item_2_icon',
            'service_item_3_title',
            'service_item_3_desc',
            'service_item_3_icon',
            'service_item_4_title',
            'service_item_4_desc',
            'service_item_4_icon',
            'important_announcement_text',
            'important_announcement_link',
            'quick_location_url',
            'map_section_title',
            'map_section_description',
            'map_embed_url',
            'popular_service_1_label',
            'popular_service_1_url',
            'popular_service_1_icon',
            'popular_service_2_label',
            'popular_service_2_url',
            'popular_service_2_icon',
            'popular_service_3_label',
            'popular_service_3_url',
            'popular_service_3_icon',
            'popular_service_4_label',
            'popular_service_4_url',
            'popular_service_4_icon',
        ];

        foreach ($keys as $key) {
            $value = trim((string) $this->request->post($key, ''));
            $value = $this->normalizeSettingValue($key, $value);
            $exists = Database::getInstance()->table('pengaturan')->where('nama_setting', $key)->first();
            if ($exists) {
                Database::getInstance()->table('pengaturan')->where('nama_setting', $key)->update([
                    'nilai_setting' => $value,
                ]);
            } else {
                Database::getInstance()->table('pengaturan')->insert([
                    'nama_setting' => $key,
                    'nilai_setting' => $value,
                    'keterangan' => null,
                ]);
            }
        }

        $announcementActive = $this->request->post('important_announcement_active') ? '1' : '0';
        $existsActive = Database::getInstance()->table('pengaturan')->where('nama_setting', 'important_announcement_active')->first();
        if ($existsActive) {
            Database::getInstance()->table('pengaturan')->where('nama_setting', 'important_announcement_active')->update([
                'nilai_setting' => $announcementActive,
            ]);
        } else {
            Database::getInstance()->table('pengaturan')->insert([
                'nama_setting' => 'important_announcement_active',
                'nilai_setting' => $announcementActive,
                'keterangan' => 'Aktifkan banner pengumuman penting',
            ]);
        }

        $existingLogo = Database::getInstance()->table('pengaturan')->where('nama_setting', 'logo_desa')->first();
        $existingLogoPath = (string) ($existingLogo['nilai_setting'] ?? '');

        $logoPath = $this->handleLogoUpload($uploadError, $existingLogoPath);
        if ($uploadError !== null) {
            Session::flash('error', $uploadError);
            return $this->redirect(base_url('/admin/pengaturan'));
        }

        if ($logoPath !== null) {
            if ($existingLogo) {
                Database::getInstance()->table('pengaturan')->where('nama_setting', 'logo_desa')->update([
                    'nilai_setting' => $logoPath,
                ]);
            } else {
                Database::getInstance()->table('pengaturan')->insert([
                    'nama_setting' => 'logo_desa',
                    'nilai_setting' => $logoPath,
                    'keterangan' => 'Logo desa',
                ]);
            }
        }

        Session::flash('success', 'Pengaturan website berhasil diperbarui.');
        return $this->redirect(base_url('/admin/pengaturan'));
    }

    private function normalizeSettingValue(string $key, string $value): string
    {
        if (preg_match('/^popular_service_\d+_icon$/', $key) === 1) {
            return $this->normalizePopularServiceIconKey($value);
        }

        if ($key === 'map_embed_url') {
            return $this->normalizeMapEmbedValue($value);
        }

        return $value;
    }

    private function normalizeMapEmbedValue(string $value): string
    {
        $raw = trim($value);
        if ($raw === '') {
            return '';
        }

        if (preg_match('/<iframe[^>]*src=["\']([^"\']+)["\']/i', $raw, $matches) === 1) {
            $raw = trim((string) ($matches[1] ?? ''));
        }

        if (filter_var($raw, FILTER_VALIDATE_URL) === false) {
            return '';
        }

        return $raw;
    }

    private function normalizePopularServiceIconKey(string $value): string
    {
        $normalized = strtolower(trim($value));
        $allowed = [
            'beranda',
            'informasi',
            'berita',
            'galeri',
            'kontak',
            'telepon',
            'whatsapp',
            'lokasi',
            'dokumen',
            'layanan',
            'pengumuman',
            'pengaturan',
        ];

        if (in_array($normalized, $allowed, true)) {
            return $normalized;
        }

        if (str_starts_with($normalized, 'bi ')) {
            return $normalized;
        }

        return 'layanan';
    }

    private function handleLogoUpload(?string &$error = null, string $oldLogoPath = ''): ?string
    {
        $error = null;

        if (!$this->request->hasFile('logo_desa')) {
            return null;
        }

        $file = $this->request->file('logo_desa');
        if (!is_array($file)) {
            $error = 'Data upload logo tidak valid.';
            return null;
        }

        $uploadErrorCode = (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE);
        if ($uploadErrorCode !== UPLOAD_ERR_OK) {
            if ($uploadErrorCode === UPLOAD_ERR_NO_FILE) {
                return null;
            }
            $error = 'Upload logo gagal: ' . $this->uploadErrorMessage($uploadErrorCode);
            return null;
        }

        $tmpPath = (string) ($file['tmp_name'] ?? '');
        $original = (string) ($file['name'] ?? 'logo');
        $size = (int) ($file['size'] ?? 0);

        if ($size <= 0) {
            $error = 'File logo kosong atau tidak valid.';
            return null;
        }

        if ($size > 5 * 1024 * 1024) {
            $error = 'Ukuran logo maksimal 5MB.';
            return null;
        }

        $ext = strtolower((string) pathinfo($original, PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'], true)) {
            $error = 'Format logo tidak didukung. Gunakan JPG, JPEG, PNG, WEBP, atau GIF.';
            return null;
        }

        $imageInfo = @getimagesize($tmpPath);
        if ($imageInfo === false) {
            $error = 'File logo bukan gambar yang valid.';
            return null;
        }

        $dir = public_path('uploads/logo');
        if (!$this->ensureUploadDirectory($dir)) {
            $error = 'Folder upload logo tidak tersedia atau tidak bisa ditulis di server.';
            return null;
        }

        $fileName = 'logo-desa-' . date('YmdHis') . '-' . substr(bin2hex(random_bytes(6)), 0, 8) . '.' . $ext;
        $path = public_path('uploads/logo/' . $fileName);
        if (!$this->moveUploadedFileSafely($tmpPath, $path)) {
            $error = 'Server gagal menyimpan file logo ke folder uploads/logo.';
            return null;
        }

        if ($oldLogoPath !== '') {
            $oldFile = public_path('uploads/' . ltrim($oldLogoPath, '/'));
            if (is_file($oldFile) && realpath((string) dirname($oldFile)) === realpath($dir)) {
                @unlink($oldFile);
            }
        }

        return 'logo/' . $fileName;
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
