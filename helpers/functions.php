<?php

use App\Core\Router;
use App\Core\Response;
use App\Core\View;
use App\Core\Session;
use App\Core\CSRF;
use App\Core\Request;

function base_url(string $path = ''): string
{
    return BASE_URL . ($path ? '/' . ltrim($path, '/') : '');
}

function public_path(string $path = ''): string
{
    return dirname(__DIR__) . ($path ? '/' . ltrim($path, '/') : '');
}

function asset(string $path): string
{
    return PUBLIC_PATH . '/' . ltrim($path, '/');
}

function css(string $path): string
{
    return '<link rel="stylesheet" href="' . CSS_PATH . '/' . $path . '">';
}

function js(string $path): string
{
    return '<script src="' . JS_PATH . '/' . $path . '"></script>';
}

function img(string $path, array $attributes = []): string
{
    $attr = '';
    foreach ($attributes as $key => $value) {
        $attr .= " $key=\"$value\"";
    }
    return '<img src="' . IMAGES_PATH . '/' . ltrim($path, '/') . '"' . $attr . '>';
}

function upload_url(string $path): string
{
    $normalized = ltrim(str_replace('\\', '/', $path), '/');
    return UPLOADS_PATH . '/' . $normalized;
}

function resolve_upload_url(string $path, string $fallback = ''): string
{
    $normalized = ltrim(str_replace('\\', '/', trim($path)), '/');
    if ($normalized === '') {
        return $fallback;
    }

    $root = dirname(__DIR__);
    $candidates = array_values(array_unique([
        $root . '/uploads/' . $normalized,
        $root . '/public/uploads/' . $normalized,
    ]));

    foreach ($candidates as $candidate) {
        if (is_file($candidate)) {
            if (str_contains(str_replace('\\', '/', $candidate), '/public/uploads/')) {
                return base_url('/public/uploads/' . $normalized);
            }

            return base_url('/uploads/' . $normalized);
        }
    }

    return $fallback;
}

function route(string $name, array $params = []): string
{
    return app(Router::class)->url($name, $params);
}

function redirect(string $url): Response
{
    return Response::redirect($url);
}

function back(): Response
{
    return Response::back();
}

function view(string $view, array $data = []): string
{
    return View::make($view, $data);
}

function old(string $key, $default = ''): string
{
    $value = Session::get('_old_input')[$key] ?? $default;
    Session::forget('_old_input');
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function old_input(string $key = null, $default = null)
{
    $old = Session::get('_old_input', []);
    if ($key === null) {
        return $old;
    }
    return $old[$key] ?? $default;
}

function set_old_input(array $input): void
{
    Session::set('_old_input', $input);
}

function has_old_input(string $key = null): bool
{
    $old = Session::get('_old_input', []);
    if (empty($old)) {
        return false;
    }
    if ($key === null) {
        return true;
    }
    return isset($old[$key]);
}

function flash(string $key = null, $value = null)
{
    if ($key === null) {
        return Session::all()['_flash'] ?? [];
    }
    if ($value === null) {
        return Session::flash($key);
    }
    Session::flash($key, $value);
    return null;
}

function has_flash(string $key): bool
{
    return Session::has('_flash') && isset($_SESSION['_flash'][$key]);
}

function success(string $key = 'success', $default = null)
{
    return Session::flash($key) ?? $default;
}

function error(string $key = 'error', $default = null)
{
    return Session::flash($key) ?? $default;
}

function csrf_token(): string
{
    return CSRF::getToken();
}

function csrf_field(): string
{
    return CSRF::tokenField();
}

function method_field(string $method): string
{
    return '<input type="hidden" name="_method" value="' . strtoupper($method) . '">';
}

function abort(int $code = 404, string $message = ''): void
{
    http_response_code($code);
    if ($message) {
        echo $message;
    } else {
        include APP_PATH . '/Views/errors/' . $code . '.php';
    }
    exit;
}

function abort_if(bool $condition, int $code = 404, string $message = ''): void
{
    if ($condition) {
        abort($code, $message);
    }
}

function abort_unless(bool $condition, int $code = 404, string $message = ''): void
{
    if (!$condition) {
        abort($code, $message);
    }
}

function dd(...$vars): void
{
    echo '<pre>';
    foreach ($vars as $var) {
        print_r($var);
    }
    echo '</pre>';
    exit;
}

function dump(...$vars): void
{
    echo '<pre>';
    foreach ($vars as $var) {
        var_dump($var);
    }
    echo '</pre>';
}

function config(string $key, $default = null)
{
    static $configs = [];
    
    $parts = explode('.', $key);
    $file = array_shift($parts);
    
    if (!isset($configs[$file])) {
        $configFile = CONFIG_PATH . '/' . $file . '.php';
        if (file_exists($configFile)) {
            $configs[$file] = require $configFile;
        } else {
            return $default;
        }
    }
    
    $value = $configs[$file];
    
    foreach ($parts as $part) {
        if (!isset($value[$part])) {
            return $default;
        }
        $value = $value[$part];
    }
    
    return $value;
}

function app(string $abstract = null)
{
    static $container = [];
    
    if ($abstract === null) {
        return $container;
    }
    
    if (!isset($container[$abstract])) {
        if (class_exists($abstract)) {
            $container[$abstract] = new $abstract();
        } else {
            return null;
        }
    }
    
    return $container[$abstract];
}

function bind(string $abstract, $concrete): void
{
    $container[$abstract] = $concrete;
}

function singleton(string $abstract, $concrete): void
{
    app($abstract);
}

function sanitize(string $string, string $type = 'html'): string
{
    switch ($type) {
        case 'html':
            return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
        case 'email':
            return filter_var($string, FILTER_SANITIZE_EMAIL);
        case 'url':
            return filter_var($string, FILTER_SANITIZE_URL);
        case 'number_int':
            return (int) $string;
        case 'number_float':
            return (float) $string;
        default:
            return $string;
    }
}

function escape(string $string): string
{
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

function e(string $string): string
{
    return escape($string);
}

function slug(string $text): string
{
    $text = strtolower($text);
    $text = preg_replace('/[^\w\s-]/', '', $text);
    $text = preg_replace('/[\s_-]+/', '-', $text);
    $text = preg_replace('/^-+|-+$/', '', $text);
    return $text;
}

function limit(string $text, int $limit, string $append = '...'): string
{
    if (strlen($text) <= $limit) {
        return $text;
    }
    return substr($text, 0, $limit) . $append;
}

function truncate(string $text, int $limit, string $append = '...'): string
{
    return limit($text, $limit, $append);
}

function date_id($date, string $format = 'd F Y'): string
{
    if (!($date instanceof DateTime)) {
        $date = new DateTime($date);
    }
    return $date->format($format);
}

function date_format_id($date, string $format = 'd F Y'): string
{
    return date_id($date, $format);
}

function datetime_id($date, string $format = 'd F Y H:i'): string
{
    if (!($date instanceof DateTime)) {
        $date = new DateTime($date);
    }
    return $date->format($format);
}

function hari_id($date): string
{
    $nama_hari = [
        'Sunday' => 'Minggu',
        'Monday' => 'Senin',
        'Tuesday' => 'Selasa',
        'Wednesday' => 'Rabu',
        'Thursday' => 'Kamis',
        'Friday' => 'Jumat',
        'Saturday' => 'Sabtu'
    ];
    
    if (!($date instanceof DateTime)) {
        $date = new DateTime($date);
    }
    
    return $nama_hari[$date->format('l')] ?? '';
}

function bulan_id($month): string
{
    $nama_bulan = [
        1 => 'Januari',
        2 => 'Februari',
        3 => 'Maret',
        4 => 'April',
        5 => 'Mei',
        6 => 'Juni',
        7 => 'Juli',
        8 => 'Agustus',
        9 => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember'
    ];
    
    if (is_numeric($month)) {
        return $nama_bulan[(int) $month] ?? '';
    }
    
    $bulan = (int) date('m', strtotime($month));
    return $nama_bulan[$bulan] ?? '';
}

function indonesia_date($date, string $format = 'd F Y'): string
{
    return date_id($date, $format);
}

function format_rupiah(int|float $number): string
{
    return 'Rp ' . number_format($number, 0, ',', '.');
}

function format_number(int|float $number): string
{
    return number_format($number, 0, ',', '.');
}

function format_bytes(int $bytes, int $precision = 2): string
{
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= pow(1024, $pow);
    return round($bytes, $precision) . ' ' . $units[$pow];
}

if (!function_exists('str_contains')) {
    function str_contains(string $haystack, string $needle): bool
    {
        return strpos($haystack, $needle) !== false;
    }
}

if (!function_exists('str_starts_with')) {
    function str_starts_with(string $haystack, string $needle): bool
    {
        return strncmp($haystack, $needle, strlen($needle)) === 0;
    }
}

if (!function_exists('str_ends_with')) {
    function str_ends_with(string $haystack, string $needle): bool
    {
        return substr($haystack, -strlen($needle)) === $needle;
    }
}

function str_random(int $length = 16): string
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function array_get(array $array, string $key, $default = null)
{
    $keys = explode('.', $key);
    $value = $array;
    
    foreach ($keys as $k) {
        if (!is_array($value) || !array_key_exists($k, $value)) {
            return $default;
        }
        $value = $value[$k];
    }
    
    return $value;
}

function array_set(array &$array, string $key, $value): array
{
    $keys = explode('.', $key);
    $current = &$array;
    
    foreach ($keys as $i => $k) {
        if ($i === count($keys) - 1) {
            $current[$k] = $value;
        } else {
            if (!isset($current[$k]) || !is_array($current[$k])) {
                $current[$k] = [];
            }
            $current = &$current[$k];
        }
    }
    
    return $array;
}

function array_forget(array &$array, string $key): void
{
    $keys = explode('.', $key);
    $current = &$array;
    
    foreach ($keys as $i => $k) {
        if ($i === count($keys) - 1) {
            unset($current[$k]);
        } else {
            if (!isset($current[$k]) || !is_array($current[$k])) {
                return;
            }
            $current = &$current[$k];
        }
    }
}

function array_has(array $array, string $key): bool
{
    return array_get($array, $key, '__NOT_FOUND__') !== '__NOT_FOUND__';
}

function array_only(array $array, array $keys): array
{
    return array_intersect_key($array, array_flip($keys));
}

function array_except(array $array, array $keys): array
{
    return array_diff_key($array, array_flip($keys));
}

function array_first(array $array, callable $callback = null, $default = null)
{
    if ($callback === null) {
        return $array[0] ?? $default;
    }
    
    foreach ($array as $key => $value) {
        if ($callback($value, $key)) {
            return $value;
        }
    }
    
    return $default;
}

function is_active(string $path, string $activeClass = 'active', array $classes = []): string
{
    $currentPath = (string) Request::capture()->getPath();
    $currentPath = '/' . trim($currentPath, '/');
    if ($currentPath === '//') {
        $currentPath = '/';
    }

    $targetPath = '/' . trim($path, '/');
    if ($targetPath === '//') {
        $targetPath = '/';
    }

    if ($targetPath === '/' && $currentPath === '/') {
        return $activeClass;
    }

    if ($currentPath === $targetPath || str_starts_with($currentPath, $targetPath . '/')) {
        return $activeClass;
    }

    return implode(' ', $classes);
}

function menu_active(string $path): string
{
    return is_active($path);
}

function assets(string $path): string
{
    return PUBLIC_PATH . '/' . ltrim($path, '/');
}

function upload_path(string $path = ''): string
{
    return dirname(__DIR__) . '/public/uploads' . ($path ? '/' . $path : '');
}

function statistik_icon_definitions(): array
{
    return [
        'beranda' => ['label' => 'Beranda', 'class' => 'bi bi-house-door'],
        'warga' => ['label' => 'Warga', 'class' => 'bi bi-people'],
        'wilayah' => ['label' => 'Wilayah', 'class' => 'bi bi-map'],
        'keluarga' => ['label' => 'Keluarga', 'class' => 'bi bi-house'],
        'pendidikan' => ['label' => 'Pendidikan', 'class' => 'bi bi-mortarboard'],
        'pertanian' => ['label' => 'Pertanian', 'class' => 'bi bi-flower1'],
        'kesehatan' => ['label' => 'Kesehatan', 'class' => 'bi bi-heart-pulse'],
        'umkm' => ['label' => 'UMKM', 'class' => 'bi bi-shop'],
        'ibadah' => ['label' => 'Rumah Ibadah', 'class' => 'bi bi-building'],
        'fasilitas' => ['label' => 'Fasilitas Umum', 'class' => 'bi bi-bank'],
    ];
}

function statistik_icon_key(string $raw): string
{
    $value = strtolower(trim($raw));
    $defs = statistik_icon_definitions();
    if (isset($defs[$value])) {
        return $value;
    }

    $legacyMap = [
        'fas fa-home' => 'beranda',
        'fas fa-users' => 'warga',
        'fas fa-map' => 'wilayah',
        'fas fa-house-user' => 'keluarga',
        'fas fa-school' => 'pendidikan',
        'fas fa-seedling' => 'pertanian',
        'fas fa-briefcase-medical' => 'kesehatan',
        'fas fa-store' => 'umkm',
        'fas fa-mosque' => 'ibadah',
        'fas fa-landmark' => 'fasilitas',
    ];

    return $legacyMap[$value] ?? 'warga';
}

function statistik_icon_class(string $raw): string
{
    $key = statistik_icon_key($raw);
    $defs = statistik_icon_definitions();
    return (string) ($defs[$key]['class'] ?? 'bi bi-people');
}

function statistik_icon_label(string $raw): string
{
    $key = statistik_icon_key($raw);
    $defs = statistik_icon_definitions();
    return (string) ($defs[$key]['label'] ?? 'Warga');
}
