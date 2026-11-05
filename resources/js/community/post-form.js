document.addEventListener('DOMContentLoaded', function () {

    const form = document.getElementById('postForm');

    if (!form) return;

    // =========================================================
    // LANGUAGE TABS
    // =========================================================

    const tabs = form.querySelectorAll('.language-tab');
    const panels = form.querySelectorAll('.language-panel');

    function activateLanguage(locale) {

        tabs.forEach(tab => {
            const active = tab.dataset.locale === locale;

            tab.classList.toggle('border-gray-900', active);
            tab.classList.toggle('bg-gray-900', active);
            tab.classList.toggle('text-white', active);

            tab.classList.toggle('border-gray-200', !active);
            tab.classList.toggle('bg-white', !active);
            tab.classList.toggle('text-gray-600', !active);
        });

        panels.forEach(panel => {
            panel.classList.toggle(
                'hidden',
                panel.dataset.localePanel !== locale
            );
        });
    }

    tabs.forEach(tab => {
        tab.addEventListener('click', function () {
            activateLanguage(this.dataset.locale);
        });
    });


    // =========================================================
    // RICH TEXT EDITOR
    // =========================================================

    function getEditor(locale) {
        return document.getElementById('post-editor-' + locale);
    }

    function getEditorInput(locale) {
        return document.getElementById('post-content-' + locale);
    }

    function getEditorCounter(locale) {
        return document.getElementById('post-editor-counter-' + locale);
    }

    function getEditorText(editor) {
        if (!editor) return '';

        return (editor.innerText || '')
            .replace(/\u00a0/g, ' ')
            .trim();
    }

    function syncEditor(locale) {

        const editor = getEditor(locale);
        const input = getEditorInput(locale);
        const counter = getEditorCounter(locale);

        if (!editor || !input) return;

        input.value = editor.innerHTML;

        const count = getEditorText(editor).length;

        if (counter) {

            const countElement = counter.querySelector('span');

            if (countElement) {
                countElement.textContent = count.toLocaleString();
            }

            counter.classList.toggle('text-red-500', count > 20000);
            counter.classList.toggle('text-gray-400', count <= 20000);
        }
    }


    // Initialize editors.

    form.querySelectorAll('[data-post-editor-area]').forEach(editor => {

        const locale = editor.dataset.editorLocale;

        syncEditor(locale);

        editor.addEventListener('input', function () {
            syncEditor(this.dataset.editorLocale);
        });

        editor.addEventListener('blur', function () {
            syncEditor(this.dataset.editorLocale);
        });

        editor.addEventListener('paste', function () {

            const currentLocale = this.dataset.editorLocale;

            setTimeout(() => {
                syncEditor(currentLocale);
            }, 0);
        });
    });


    // Toolbar actions.

    form.querySelectorAll('[data-editor-command]').forEach(button => {

        button.addEventListener('mousedown', function (event) {
            event.preventDefault();
        });

        button.addEventListener('click', function () {

            const locale = this.dataset.editorLocale;
            const command = this.dataset.editorCommand;
            const editor = getEditor(locale);

            if (!editor) return;

            editor.focus();

            if (command === 'createLink') {

                const selection = window.getSelection();

                if (!selection || selection.isCollapsed) {
                    alert('Please select the text you want to turn into a link.');
                    return;
                }

                const url = prompt('Enter the URL:');

                if (!url) return;

                let normalizedUrl;

                try {
                    normalizedUrl = new URL(url, window.location.origin);
                } catch (error) {
                    alert('Please enter a valid URL.');
                    return;
                }

                if (!['http:', 'https:', 'mailto:'].includes(normalizedUrl.protocol)) {
                    alert('Only HTTP, HTTPS and email links are allowed.');
                    return;
                }

                document.execCommand('createLink', false, normalizedUrl.href);

                editor.querySelectorAll('a').forEach(link => {

                    if (link.href === normalizedUrl.href) {
                        link.setAttribute('target', '_blank');
                        link.setAttribute('rel', 'noopener noreferrer');
                    }

                });

            } else if (command === 'formatBlock') {

                document.execCommand(
                    'formatBlock',
                    false,
                    this.dataset.editorValue
                );

            } else {

                document.execCommand(command, false, null);
            }

            syncEditor(locale);
        });
    });


        // =========================================================
    // SLUG GENERATION — GLOBAL POST SLUG
    // =========================================================

    const slugInput = form.querySelector('[data-slug-target]');
    const slugSource = form.querySelector('[data-slug-source]');

    function generateSlug(value) {
        return value
            .toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/^-+|-+$/g, '');
    }

    if (slugInput && slugSource) {

        let manuallyEdited = slugInput.value.trim() !== '';

        slugInput.addEventListener('input', function () {
            manuallyEdited = true;
        });

        slugSource.addEventListener('input', function () {

            if (manuallyEdited) return;

            slugInput.value = generateSlug(this.value);
        });

        // Generate initial slug if the English title already exists.
        if (!manuallyEdited && slugSource.value.trim() !== '') {
            slugInput.value = generateSlug(slugSource.value);
        }
    }


            // =========================================================
    // FORM VALIDATION
    // =========================================================

    form.addEventListener('submit', function (event) {

        let hasCompleteTranslation = false;
        let firstIncompleteLocale = null;

        const localePanels = form.querySelectorAll('[data-locale-panel]');

        const slug = form.querySelector('[name="slug"]');

        localePanels.forEach(panel => {

            const locale = panel.dataset.localePanel;

            const fields = Array.from(
                panel.querySelectorAll('[data-translation-field]')
            );

            const title = panel.querySelector('[name$="[title]"]');
            const excerpt = panel.querySelector('[name$="[excerpt]"]');

            const contentInput = getEditorInput(locale);
            const editor = getEditor(locale);

            if (editor && contentInput) {
                syncEditor(locale);
            }

            // Only actual publication content determines
            // whether this language version has been started.
            const hasStarted =
                (title?.value.trim() !== '') ||
                (excerpt?.value.trim() !== '') ||
                (contentInput?.value.trim() !== '');

            // Completely empty language version is optional.
            if (!hasStarted) {

                fields.forEach(field => {
                    field.disabled = true;
                    field.required = false;
                });

                return;
            }

            // Keep all fields enabled for started translations.
            fields.forEach(field => {
                field.disabled = false;
                field.required = false;
            });

            const isComplete =
                title?.value.trim() !== '' &&
                contentInput?.value.trim() !== '' &&
                getEditorText(editor).length > 0;

            if (isComplete) {
                hasCompleteTranslation = true;
            } else {
                firstIncompleteLocale = firstIncompleteLocale || locale;
            }

        });

        // Do not silently discard partially completed translations.
        if (firstIncompleteLocale) {

            event.preventDefault();

            activateLanguage(firstIncompleteLocale);

            const panel = form.querySelector(
                '[data-locale-panel="' + firstIncompleteLocale + '"]'
            );

            const missingTitle = panel?.querySelector(
                '[name$="[title]"]'
            );

            if (!missingTitle?.value.trim()) {
                missingTitle?.focus();
            } else {
                getEditor(firstIncompleteLocale)?.focus();
            }

            alert(
                'Please complete the title and main content for every language version you started.'
            );

            return;
        }

        // At least one complete translation is required.
        if (!hasCompleteTranslation) {

            event.preventDefault();

            activateLanguage('en');

            const englishTitle = form.querySelector(
                '[data-locale-panel="en"] [name$="[title]"]'
            );

            if (!englishTitle?.value.trim()) {
                englishTitle?.focus();
            } else {
                getEditor('en')?.focus();
            }

            alert(
                'Please complete at least one language version with a title and main content.'
            );

            return;
        }

        // One global slug is required.
        if (!slug || !slug.value.trim()) {

            event.preventDefault();

            slug?.focus();

            alert('Please enter the URL slug for this publication.');

            return;
        }

        // Final synchronization of all enabled editors.
        localePanels.forEach(panel => {

            const locale = panel.dataset.localePanel;
            const editor = getEditor(locale);
            const input = getEditorInput(locale);

            if (editor && input && !input.disabled) {
                syncEditor(locale);
            }

        });

    });


    // =========================================================
    // IMAGE PREVIEW
    // =========================================================

    const mediaInput = document.getElementById('media');
    const mediaPreview = document.getElementById('mediaPreview');

    if (mediaInput && mediaPreview) {

        mediaInput.addEventListener('change', function () {

            mediaPreview.innerHTML = '';

            const files = Array.from(this.files);

            if (files.length > 10) {
                alert('You can upload a maximum of 10 images at once.');
                this.value = '';
                return;
            }

            files.forEach(file => {

                if (!['image/jpeg', 'image/png', 'image/webp'].includes(file.type)) {
                    return;
                }

                if (file.size > 10 * 1024 * 1024) {
                    alert(file.name + ' exceeds the 10 MB limit.');
                    return;
                }

                const wrapper = document.createElement('div');

                wrapper.className =
                    'relative aspect-[4/3] overflow-hidden rounded-xl border border-gray-200 bg-gray-100';

                const image = document.createElement('img');

                image.className = 'h-full w-full object-cover';
                image.alt = file.name;

                const url = URL.createObjectURL(file);

                image.src = url;
                image.onload = () => URL.revokeObjectURL(url);

                wrapper.appendChild(image);
                mediaPreview.appendChild(wrapper);
            });

        });
    }

});