<?php

namespace App\Http\Controllers;

use App\Models\Pengaturan;
use App\Models\ProfilDesa;

class AssistantController
{
    public function faq(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: public, max-age=300');

        $pengaturan = Pengaturan::getAll();
        $profil     = ProfilDesa::first();

        $villageName = trim((string) ($pengaturan['website_nama'] ?? ($profil['nama_desa'] ?? 'Gampong Muenye Pirak')));
        $greeting    = trim((string) ($pengaturan['assistant_greeting'] ?? ''));
        if ($greeting === '') {
            $greeting = "Halo! Saya Si Meung 🦉, asisten virtual {$villageName}. Ada yang ingin Anda tanyakan tentang gampong kami?";
        }

        // Build FAQs from DB settings (keys: assistant_faq_N_q, assistant_faq_N_a, assistant_faq_N_keywords)
        $faqs = [];
        for ($i = 1; $i <= 20; $i++) {
            $q = trim((string) ($pengaturan["assistant_faq_{$i}_q"] ?? ''));
            $a = trim((string) ($pengaturan["assistant_faq_{$i}_a"] ?? ''));
            if ($q === '' || $a === '') {
                break;
            }
            $rawKeywords = trim((string) ($pengaturan["assistant_faq_{$i}_keywords"] ?? ''));
            $keywords    = $rawKeywords !== ''
                ? array_map('trim', explode(',', $rawKeywords))
                : $this->generateKeywords($q);

            $faqs[] = [
                'q'        => $q,
                'a'        => $a,
                'keywords' => $keywords,
            ];
        }

        // Fallback FAQs merged with village data
        if (empty($faqs)) {
            $faqs = $this->buildDefaultFaqs($villageName, $profil, $pengaturan);
        }

        echo json_encode([
            'village_name' => $villageName,
            'greeting'     => $greeting,
            'faqs'         => $faqs,
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    private function buildDefaultFaqs(string $name, $profil, array $pengaturan): array
    {
        $alamat   = trim((string) ($profil['alamat'] ?? ($pengaturan['desa_alamat'] ?? '')));
        $telepon  = trim((string) ($profil['telepon'] ?? ($pengaturan['desa_telepon'] ?? '')));
        $email    = trim((string) ($profil['email'] ?? ($pengaturan['desa_email'] ?? '')));
        $kecamatan = trim((string) ($pengaturan['district_name'] ?? 'Nisam'));
        $kabupaten = trim((string) ($pengaturan['regency_name'] ?? 'Aceh Utara'));

        return [
            [
                'q'        => "Apa itu {$name}?",
                'a'        => "{$name} adalah sebuah desa yang terletak di Kecamatan {$kecamatan}, Kabupaten {$kabupaten}, Provinsi Aceh. Kami berkomitmen memberikan pelayanan terbaik bagi seluruh warga.",
                'keywords' => ['gampong', 'desa', 'tentang', 'apa', 'informasi', 'profil'],
            ],
            [
                'q'        => 'Dimana lokasi kantor gampong?',
                'a'        => $alamat !== ''
                    ? "Kantor {$name} berlokasi di {$alamat}. Kecamatan {$kecamatan}, {$kabupaten}."
                    : "Kantor {$name} berlokasi di Kecamatan {$kecamatan}, {$kabupaten}. Cek halaman Kontak untuk peta lokasi.",
                'keywords' => ['lokasi', 'kantor', 'alamat', 'dimana', 'letak', 'maps', 'tempat'],
            ],
            [
                'q'        => 'Bagaimana cara menghubungi gampong?',
                'a'        => "Anda dapat menghubungi {$name} melalui:\n"
                    . ($telepon !== '' ? "• Telepon: {$telepon}\n" : '')
                    . ($email   !== '' ? "• Email: {$email}\n" : '')
                    . "• Formulir di halaman Kontak\n• Kunjungi langsung kantor pada jam kerja.",
                'keywords' => ['hubungi', 'kontak', 'telepon', 'whatsapp', 'wa', 'email', 'komunikasi'],
            ],
            [
                'q'        => 'Apa saja berita terbaru gampong?',
                'a'        => 'Untuk membaca berita dan informasi terkini, kunjungi halaman Berita kami. Kami rutin memperbarui kegiatan dan pengumuman penting.',
                'keywords' => ['berita', 'informasi', 'pengumuman', 'kabar', 'terkini', 'terbaru', 'kegiatan'],
            ],
            [
                'q'        => 'Bagaimana cara melihat galeri foto?',
                'a'        => 'Galeri foto dan dokumentasi kegiatan gampong dapat dilihat di halaman Galeri. Nikmati koleksi foto-foto keindahan dan kegiatan gampong kami.',
                'keywords' => ['galeri', 'foto', 'gambar', 'dokumentasi', 'lihat'],
            ],
            [
                'q'        => 'Apa visi dan misi gampong?',
                'a'        => "Visi dan misi {$name} dapat dilihat di halaman Profil > Visi & Misi. Kami berkomitmen membangun gampong yang maju, sejahtera, dan berbudaya.",
                'keywords' => ['visi', 'misi', 'tujuan', 'cita', 'harapan', 'program'],
            ],
            [
                'q'        => 'Siapa perangkat gampong?',
                'a'        => 'Informasi tentang struktur organisasi dan perangkat gampong dapat dilihat di halaman Profil > Struktur Organisasi.',
                'keywords' => ['perangkat', 'struktur', 'organisasi', 'aparatur', 'kepala', 'geuchik', 'sekretaris'],
            ],
        ];
    }

    private function generateKeywords(string $question): array
    {
        $stopwords = ['apa', 'adalah', 'bagaimana', 'cara', 'siapa', 'dimana', 'kapan', 'mengapa', 'yang', 'dan', 'atau', 'di', 'ke', 'dari', 'dengan', 'untuk', 'saya', 'kami', 'kamu'];
        $words     = preg_split('/\s+/', strtolower(preg_replace('/[^a-z0-9\s]/iu', '', $question)));
        return array_values(array_filter($words, fn($w) => strlen($w) > 2 && !in_array($w, $stopwords)));
    }
}
