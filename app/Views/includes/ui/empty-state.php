<?php
$emptyMessage = isset($emptyMessage) ? trim((string) $emptyMessage) : '';
$emptyIcon = isset($emptyIcon) ? trim((string) $emptyIcon) : 'bi bi-info-circle';
$emptyClass = isset($emptyClass) ? trim((string) $emptyClass) : 'empty-state';
$emptyDataAos = isset($emptyDataAos) ? trim((string) $emptyDataAos) : '';
$emptyDataReveal = isset($emptyDataReveal) ? trim((string) $emptyDataReveal) : $emptyDataAos;

if ($emptyMessage === '') {
    return;
}
?>
<div class="<?= htmlspecialchars($emptyClass) ?>"<?= $emptyDataReveal !== '' ? ' data-reveal="' . htmlspecialchars($emptyDataReveal) . '"' : '' ?>>
    <?php if ($emptyIcon !== ''): ?>
        <i class="<?= htmlspecialchars($emptyIcon) ?>"></i>
    <?php endif; ?>
    <p><?= htmlspecialchars($emptyMessage) ?></p>
</div>
