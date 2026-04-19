<?php
$alertType = isset($alertType) ? (string) $alertType : 'light';
$alertMessage = isset($alertMessage) ? trim((string) $alertMessage) : '';
$alertIcon = isset($alertIcon) ? trim((string) $alertIcon) : '';
$alertClass = isset($alertClass) ? trim((string) $alertClass) : '';
$alertList = isset($alertList) && is_array($alertList) ? $alertList : [];
$alertDataAos = isset($alertDataAos) ? trim((string) $alertDataAos) : '';
$alertDataReveal = isset($alertDataReveal) ? trim((string) $alertDataReveal) : $alertDataAos;

if ($alertMessage === '' && empty($alertList)) {
    return;
}

$alertTypeClass = preg_replace('/[^a-z]/', '', strtolower($alertType));
if ($alertTypeClass === '') {
    $alertTypeClass = 'light';
}

$classes = trim('alert alert-' . $alertTypeClass . ' ' . $alertClass);
?>
<div class="<?= htmlspecialchars($classes) ?>" role="alert"<?= $alertDataReveal !== '' ? ' data-reveal="' . htmlspecialchars($alertDataReveal) . '"' : '' ?>>
    <?php if ($alertMessage !== ''): ?>
        <p class="mb-0">
            <?php if ($alertIcon !== ''): ?>
                <i class="<?= htmlspecialchars($alertIcon) ?> me-2"></i>
            <?php endif; ?>
            <?= htmlspecialchars($alertMessage) ?>
        </p>
    <?php endif; ?>

    <?php if (!empty($alertList)): ?>
        <ul class="mb-0 ps-3<?= $alertMessage !== '' ? ' mt-2' : '' ?>">
            <?php foreach ($alertList as $item): ?>
                <li><?= htmlspecialchars((string) $item) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>
