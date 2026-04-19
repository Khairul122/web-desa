<?php

use App\Core\Router;
use App\Core\AuthMiddleware;
use App\Core\GuestMiddleware;
use App\Core\CSRFCheckMiddleware;
use App\Core\ThrottleMiddleware;
use App\Core\AdminMiddleware;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\BeritaController;
use App\Http\Controllers\GaleriController;
use App\Http\Controllers\KontakController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AssistantController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\BeritaController as AdminBeritaController;
use App\Http\Controllers\Admin\GaleriController as AdminGaleriController;
use App\Http\Controllers\Admin\CarouselController as AdminCarouselController;
use App\Http\Controllers\Admin\StatistikController as AdminStatistikController;
use App\Http\Controllers\Admin\KontakController as AdminKontakController;
use App\Http\Controllers\Admin\PengaturanController as AdminPengaturanController;
use App\Http\Controllers\Admin\ProfilController as AdminProfilController;
use App\Http\Controllers\Admin\UsersController as AdminUsersController;
use App\Http\Controllers\Admin\AccountController as AdminAccountController;
use App\Http\Controllers\Admin\EditorImageController as AdminEditorImageController;

$router = new Router();

$router->get('/', [HomeController::class, 'index']);

$router->group(['middleware' => []], function (Router $router) {
    $router->get('/profil', [ProfilController::class, 'index']);
    $router->get('/profil/visi-misi', [ProfilController::class, 'visiMisi']);
    $router->get('/profil/struktur-organisasi', [ProfilController::class, 'strukturOrganisasi']);
    $router->get('/profil/{slug}', [ProfilController::class, 'show']);

    $router->get('/berita', [BeritaController::class, 'index']);
    $router->get('/berita/{slug}', [BeritaController::class, 'show']);

    $router->get('/galeri', [GaleriController::class, 'index']);
    $router->get('/galeri/{slug}', [GaleriController::class, 'show']);

    $router->get('/kontak', [KontakController::class, 'index']);
    $router->post('/kontak', [KontakController::class, 'send'], [CSRFCheckMiddleware::class]);

    $router->get('/api/assistant', [AssistantController::class, 'faq']);

    $router->get('/login', [AuthController::class, 'showLogin'], [GuestMiddleware::class]);
    $router->post('/login', [AuthController::class, 'login'], [GuestMiddleware::class, CSRFCheckMiddleware::class, ThrottleMiddleware::class]);
    $router->post('/logout', [AuthController::class, 'logout'], [AuthMiddleware::class, CSRFCheckMiddleware::class]);

    $router->get('/admin', [DashboardController::class, 'index'], [AuthMiddleware::class, AdminMiddleware::class]);
    $router->get('/admin/dashboard', [DashboardController::class, 'index'], [AuthMiddleware::class, AdminMiddleware::class]);

    $router->get('/admin/berita', [AdminBeritaController::class, 'index'], [AuthMiddleware::class, AdminMiddleware::class]);
    $router->get('/admin/berita/create', [AdminBeritaController::class, 'create'], [AuthMiddleware::class, AdminMiddleware::class]);
    $router->post('/admin/berita', [AdminBeritaController::class, 'store'], [AuthMiddleware::class, AdminMiddleware::class, CSRFCheckMiddleware::class]);
    $router->get('/admin/berita/edit/{id}', [AdminBeritaController::class, 'edit'], [AuthMiddleware::class, AdminMiddleware::class]);
    $router->post('/admin/berita/update/{id}', [AdminBeritaController::class, 'update'], [AuthMiddleware::class, AdminMiddleware::class, CSRFCheckMiddleware::class]);
    $router->post('/admin/berita/delete/{id}', [AdminBeritaController::class, 'destroy'], [AuthMiddleware::class, AdminMiddleware::class, CSRFCheckMiddleware::class]);

    $router->get('/admin/galeri', [AdminGaleriController::class, 'index'], [AuthMiddleware::class, AdminMiddleware::class]);
    $router->get('/admin/galeri/edit/{id}', [AdminGaleriController::class, 'edit'], [AuthMiddleware::class, AdminMiddleware::class]);
    $router->post('/admin/galeri', [AdminGaleriController::class, 'store'], [AuthMiddleware::class, AdminMiddleware::class, CSRFCheckMiddleware::class]);
    $router->post('/admin/galeri/update/{id}', [AdminGaleriController::class, 'update'], [AuthMiddleware::class, AdminMiddleware::class, CSRFCheckMiddleware::class]);
    $router->post('/admin/galeri/delete/{id}', [AdminGaleriController::class, 'destroy'], [AuthMiddleware::class, AdminMiddleware::class, CSRFCheckMiddleware::class]);

    $router->get('/admin/carousel', [AdminCarouselController::class, 'index'], [AuthMiddleware::class, AdminMiddleware::class]);
    $router->get('/admin/carousel/create', [AdminCarouselController::class, 'create'], [AuthMiddleware::class, AdminMiddleware::class]);
    $router->post('/admin/carousel', [AdminCarouselController::class, 'store'], [AuthMiddleware::class, AdminMiddleware::class, CSRFCheckMiddleware::class]);
    $router->get('/admin/carousel/edit/{id}', [AdminCarouselController::class, 'edit'], [AuthMiddleware::class, AdminMiddleware::class]);
    $router->post('/admin/carousel/update/{id}', [AdminCarouselController::class, 'update'], [AuthMiddleware::class, AdminMiddleware::class, CSRFCheckMiddleware::class]);
    $router->post('/admin/carousel/delete/{id}', [AdminCarouselController::class, 'destroy'], [AuthMiddleware::class, AdminMiddleware::class, CSRFCheckMiddleware::class]);

    $router->get('/admin/statistik', [AdminStatistikController::class, 'index'], [AuthMiddleware::class, AdminMiddleware::class]);
    $router->get('/admin/statistik/create', [AdminStatistikController::class, 'create'], [AuthMiddleware::class, AdminMiddleware::class]);
    $router->post('/admin/statistik', [AdminStatistikController::class, 'store'], [AuthMiddleware::class, AdminMiddleware::class, CSRFCheckMiddleware::class]);
    $router->get('/admin/statistik/edit/{id}', [AdminStatistikController::class, 'edit'], [AuthMiddleware::class, AdminMiddleware::class]);
    $router->post('/admin/statistik/update/{id}', [AdminStatistikController::class, 'update'], [AuthMiddleware::class, AdminMiddleware::class, CSRFCheckMiddleware::class]);
    $router->post('/admin/statistik/delete/{id}', [AdminStatistikController::class, 'destroy'], [AuthMiddleware::class, AdminMiddleware::class, CSRFCheckMiddleware::class]);

    $router->get('/admin/kontak', [AdminKontakController::class, 'index'], [AuthMiddleware::class, AdminMiddleware::class]);
    $router->get('/admin/kontak/show/{id}', [AdminKontakController::class, 'show'], [AuthMiddleware::class, AdminMiddleware::class]);
    $router->post('/admin/kontak/status/{id}', [AdminKontakController::class, 'updateStatus'], [AuthMiddleware::class, AdminMiddleware::class, CSRFCheckMiddleware::class]);
    $router->post('/admin/kontak/delete/{id}', [AdminKontakController::class, 'destroy'], [AuthMiddleware::class, AdminMiddleware::class, CSRFCheckMiddleware::class]);

    $router->get('/admin/pengaturan', [AdminPengaturanController::class, 'index'], [AuthMiddleware::class, AdminMiddleware::class]);
    $router->post('/admin/pengaturan', [AdminPengaturanController::class, 'update'], [AuthMiddleware::class, AdminMiddleware::class, CSRFCheckMiddleware::class]);

    $router->get('/admin/profil', [AdminProfilController::class, 'index'], [AuthMiddleware::class, AdminMiddleware::class]);
    $router->post('/admin/profil/update/{id}', [AdminProfilController::class, 'update'], [AuthMiddleware::class, AdminMiddleware::class, CSRFCheckMiddleware::class]);

    $router->get('/admin/users', [AdminUsersController::class, 'index'], [AuthMiddleware::class, AdminMiddleware::class]);
    $router->get('/admin/users/create', [AdminUsersController::class, 'create'], [AuthMiddleware::class, AdminMiddleware::class]);
    $router->post('/admin/users', [AdminUsersController::class, 'store'], [AuthMiddleware::class, AdminMiddleware::class, CSRFCheckMiddleware::class]);
    $router->get('/admin/users/edit/{id}', [AdminUsersController::class, 'edit'], [AuthMiddleware::class, AdminMiddleware::class]);
    $router->post('/admin/users/update/{id}', [AdminUsersController::class, 'update'], [AuthMiddleware::class, AdminMiddleware::class, CSRFCheckMiddleware::class]);
    $router->post('/admin/users/delete/{id}', [AdminUsersController::class, 'destroy'], [AuthMiddleware::class, AdminMiddleware::class, CSRFCheckMiddleware::class]);

    $router->get('/admin/profile', [AdminAccountController::class, 'index'], [AuthMiddleware::class, AdminMiddleware::class]);
    $router->post('/admin/profile', [AdminAccountController::class, 'update'], [AuthMiddleware::class, AdminMiddleware::class, CSRFCheckMiddleware::class]);
    $router->post('/admin/editor/upload-image', [AdminEditorImageController::class, 'upload'], [AuthMiddleware::class, AdminMiddleware::class]);
});

return $router;
