<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Panel Admin') ?></title>
    <meta name="description" content="<?= htmlspecialchars((string) ($website_deskripsi ?? 'Panel admin website.')) ?>">
    <?php $logoUrl = !empty($logoDesa ?? '') ? resolve_upload_url((string) $logoDesa) : ''; ?>
    <?php if ($logoUrl !== ''): ?>
    <link rel="shortcut icon" href="<?= htmlspecialchars($logoUrl) ?>" type="image/x-icon">
    <?php endif; ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.snow.css">
    <?php
    $cssFiles = [
        '/public/css/base.css',
        '/public/css/admin.css',
    ];
    $styleVersion = 0;
    foreach ($cssFiles as $cssFile) {
        $mtime = @filemtime(dirname(__DIR__, 3) . $cssFile) ?: 0;
        if ($mtime > $styleVersion) {
            $styleVersion = $mtime;
        }
    }
    if ($styleVersion === 0) {
        $styleVersion = time();
    }
    ?>
    <?php foreach ($cssFiles as $cssFile): ?>
        <link rel="stylesheet" href="<?= CSS_PATH . '/' . basename($cssFile) ?>?v=<?= $styleVersion ?>">
    <?php endforeach; ?>
</head>
<body
    data-page="<?= htmlspecialchars($page ?? '') ?>"
    data-editor-upload-url="<?= htmlspecialchars(base_url('/admin/editor/upload-image')) ?>"
    data-csrf-token="<?= htmlspecialchars(csrf_token()) ?>"
    class="admin-body"
>
    <div class="admin-shell">
        <?php include APP_PATH . '/Views/includes/admin/sidebar.php'; ?>

        <div class="admin-main">
            <?php include APP_PATH . '/Views/includes/admin/navbar.php'; ?>

            <main class="admin-content">
                <?= $content ?? '' ?>
            </main>
        </div>
    </div>

    <?php include APP_PATH . '/Views/includes/admin/dialogs.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.min.js"></script>
    <?php $adminJsVersion = @filemtime(dirname(__DIR__, 3) . '/public/js/admin.js') ?: time(); ?>
    <script src="<?= JS_PATH ?>/admin.js?v=<?= $adminJsVersion ?>"></script>
</body>
</html>
