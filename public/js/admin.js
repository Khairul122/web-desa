document.addEventListener('DOMContentLoaded', () => {
    const sidebar = document.getElementById('adminSidebar');
    const backdrop = document.getElementById('adminSidebarBackdrop');
    const toggle = document.getElementById('sidebarToggle');
    const closeBtn = document.getElementById('sidebarClose');

    const openSidebar = () => {
        if (!sidebar) return;
        sidebar.classList.add('is-open');
        if (backdrop) backdrop.classList.add('show');
        document.body.style.overflow = 'hidden';
    };

    const closeSidebar = () => {
        if (!sidebar) return;
        sidebar.classList.remove('is-open');
        if (backdrop) backdrop.classList.remove('show');
        document.body.style.overflow = '';
    };

    if (toggle) {
        toggle.addEventListener('click', openSidebar);
    }

    if (closeBtn) {
        closeBtn.addEventListener('click', closeSidebar);
    }

    if (backdrop) {
        backdrop.addEventListener('click', closeSidebar);
    }

    window.addEventListener('resize', () => {
        if (window.innerWidth >= 992) {
            closeSidebar();
        }
    });

    const counters = document.querySelectorAll('[data-counter]');

    counters.forEach((counter) => {
        const target = Number(counter.getAttribute('data-counter') || 0);
        const duration = 700;
        const start = performance.now();

        const tick = (now) => {
            const progress = Math.min((now - start) / duration, 1);
            const eased = 1 - Math.pow(1 - progress, 3);
            counter.textContent = Math.floor(target * eased).toLocaleString('id-ID');

            if (progress < 1) {
                requestAnimationFrame(tick);
            } else {
                counter.textContent = target.toLocaleString('id-ID');
            }
        };

        requestAnimationFrame(tick);
    });

    const flashModalEl = document.getElementById('adminFlashModal');
    if (flashModalEl && typeof bootstrap !== 'undefined') {
        const flashModal = new bootstrap.Modal(flashModalEl);
        flashModal.show();
    }

    const confirmModalEl = document.getElementById('adminConfirmModal');
    const confirmSubmitBtn = document.getElementById('adminConfirmSubmit');
    const confirmTitleEl = document.getElementById('adminConfirmTitle');
    const confirmMessageEl = document.getElementById('adminConfirmMessage');
    let pendingForm = null;

    if (confirmModalEl && confirmSubmitBtn && typeof bootstrap !== 'undefined') {
        const confirmModal = new bootstrap.Modal(confirmModalEl);

        document.querySelectorAll('form.js-confirm-submit').forEach((form) => {
            form.addEventListener('submit', (event) => {
                if (form.dataset.confirmed === '1') {
                    form.dataset.confirmed = '0';
                    return;
                }

                event.preventDefault();

                pendingForm = form;
                const title = form.getAttribute('data-confirm-title') || 'Konfirmasi';
                const message = form.getAttribute('data-confirm-message') || 'Apakah Anda yakin ingin melanjutkan?';

                if (confirmTitleEl) {
                    confirmTitleEl.textContent = title;
                }

                if (confirmMessageEl) {
                    confirmMessageEl.textContent = message;
                }

                confirmModal.show();
            });
        });

        confirmSubmitBtn.addEventListener('click', () => {
            if (!pendingForm) {
                confirmModal.hide();
                return;
            }

            const form = pendingForm;
            pendingForm = null;
            confirmModal.hide();
            form.dataset.confirmed = '1';

            if (typeof form.requestSubmit === 'function') {
                form.requestSubmit();
            } else {
                form.submit();
            }
        });

        confirmModalEl.addEventListener('hidden.bs.modal', () => {
            pendingForm = null;
        });
    }

    const richTextAreas = document.querySelectorAll('.js-richtext');
    if (richTextAreas.length > 0 && typeof Quill !== 'undefined') {
        const quillInstances = [];
        const editorUploadUrl = document.body?.dataset?.editorUploadUrl || '';
        const csrfToken = document.body?.dataset?.csrfToken || '';

        const uploadEditorImage = async (file, moduleName) => {
            if (!editorUploadUrl || !csrfToken) {
                throw new Error('Konfigurasi upload editor belum tersedia.');
            }

            const formData = new FormData();
            formData.append('_token', csrfToken);
            formData.append('module', moduleName || 'general');
            formData.append('image', file);

            const response = await fetch(editorUploadUrl, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            let payload = {};
            try {
                payload = await response.json();
            } catch (error) {
                payload = {};
            }

            if (!response.ok || payload.success !== true || !payload?.data?.url) {
                const msg = payload?.message || 'Upload gambar gagal.';
                throw new Error(msg);
            }

            return payload.data.url;
        };

        richTextAreas.forEach((textarea, index) => {
            const editorId = textarea.id || `editor-${index}`;
            const wrapper = document.createElement('div');
            wrapper.className = 'admin-richtext-wrapper';

            const editorElement = document.createElement('div');
            editorElement.id = `${editorId}-quill`;
            editorElement.className = 'admin-richtext-editor';

            const height = Number(textarea.getAttribute('data-editor-height') || 0) || 360;
            editorElement.style.minHeight = `${height}px`;
            const isRequired = textarea.hasAttribute('required');

            if (isRequired) {
                textarea.removeAttribute('required');
                textarea.dataset.richRequired = '1';
            }

            textarea.style.display = 'none';
            textarea.insertAdjacentElement('afterend', wrapper);
            wrapper.appendChild(editorElement);

            const quill = new Quill(editorElement, {
                theme: 'snow',
                modules: {
                    toolbar: [
                        [{ header: [2, 3, false] }],
                        ['bold', 'italic', 'underline'],
                        [{ list: 'ordered' }, { list: 'bullet' }],
                        [{ align: [] }],
                        ['link', 'image'],
                        ['clean']
                    ]
                }
            });

            const toolbar = quill.getModule('toolbar');
            if (toolbar) {
                toolbar.addHandler('image', () => {
                    const input = document.createElement('input');
                    input.type = 'file';
                    input.accept = 'image/png,image/jpeg,image/jpg,image/webp,image/gif';
                    input.click();

                    input.addEventListener('change', async () => {
                        const file = input.files && input.files[0] ? input.files[0] : null;
                        if (!file) {
                            return;
                        }

                        const moduleName = textarea.getAttribute('data-editor-module') || 'general';

                        try {
                            const range = quill.getSelection(true);
                            const cursorPosition = range ? range.index : quill.getLength();
                            const imageUrl = await uploadEditorImage(file, moduleName);
                            quill.insertEmbed(cursorPosition, 'image', imageUrl, 'user');
                            quill.setSelection(cursorPosition + 1, 0, 'silent');
                        } catch (error) {
                            window.alert(error?.message || 'Upload gambar gagal diproses.');
                        }
                    });
                });
            }

            const initialContent = (textarea.value || '').trim();
            if (initialContent !== '') {
                quill.clipboard.dangerouslyPasteHTML(initialContent);
            }

            quillInstances.push({ textarea, quill, editorElement });
        });

        document.querySelectorAll('form').forEach((form) => {
            form.addEventListener('submit', (event) => {
                let hasInvalidEditor = false;

                quillInstances.forEach(({ textarea, quill, editorElement }) => {
                    if (form.contains(textarea)) {
                        const html = quill.root.innerHTML;
                        const plainText = quill.getText().trim();
                        const hasMedia = quill.root.querySelector('img,video,iframe') !== null;
                        const hasContent = plainText !== '' || hasMedia;
                        const isRequired = textarea.dataset.richRequired === '1';

                        textarea.value = hasContent ? html : '';

                        if (isRequired && !hasContent) {
                            hasInvalidEditor = true;
                            editorElement.classList.add('is-invalid');
                        } else {
                            editorElement.classList.remove('is-invalid');
                        }
                    }
                });

                if (hasInvalidEditor) {
                    event.preventDefault();
                    const firstInvalid = form.querySelector('.admin-richtext-editor.is-invalid');
                    if (firstInvalid) {
                        firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                }
            });
        });
    }
});
