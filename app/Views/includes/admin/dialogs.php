<?php
$flashType = null;
$flashMessage = null;

if (has_flash('success')) {
    $flashType = 'success';
    $flashMessage = (string) success();
} elseif (has_flash('error')) {
    $flashType = 'error';
    $flashMessage = (string) error();
}
?>

<?php if ($flashType !== null && $flashMessage !== null && $flashMessage !== ''): ?>
    <div
        id="adminFlashModal"
        class="modal fade"
        tabindex="-1"
        aria-hidden="true"
        data-flash-type="<?= htmlspecialchars($flashType) ?>"
        data-flash-message="<?= htmlspecialchars($flashMessage) ?>"
    >
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content admin-flash-modal">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title">
                        <?= $flashType === 'success' ? 'Berhasil' : 'Gagal' ?>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-2">
                    <div class="admin-flash-modal__icon <?= $flashType === 'success' ? 'is-success' : 'is-error' ?>">
                        <i class="bi <?= $flashType === 'success' ? 'bi-check2-circle' : 'bi-x-octagon' ?>"></i>
                    </div>
                    <p class="admin-flash-modal__message mb-0"><?= htmlspecialchars($flashMessage) ?></p>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn admin-btn-primary w-100" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<div id="adminConfirmModal" class="modal fade" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content admin-flash-modal">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title" id="adminConfirmTitle">Konfirmasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-2">
                <div class="admin-flash-modal__icon is-confirm">
                    <i class="bi bi-question-circle"></i>
                </div>
                <p class="admin-flash-modal__message mb-0" id="adminConfirmMessage">Apakah Anda yakin ingin melanjutkan?</p>
            </div>
            <div class="modal-footer border-0 pt-0 d-flex gap-2">
                <button type="button" class="btn admin-btn-light w-100" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn admin-btn-primary w-100" id="adminConfirmSubmit">Lanjutkan</button>
            </div>
        </div>
    </div>
</div>
