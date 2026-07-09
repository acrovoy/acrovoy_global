class ConfirmModal {

    constructor() {

        this.modal = document.getElementById('confirm-modal');
        this.overlay = document.getElementById('confirm-modal-overlay');
        this.window = document.getElementById('confirm-modal-window');
        this.isOpen = false;

        this.icon = document.getElementById('confirm-modal-icon');

        this.iconDanger = document.getElementById('confirm-icon-danger');
        this.iconWarning = document.getElementById('confirm-icon-warning');
        this.iconSuccess = document.getElementById('confirm-icon-success');
        this.iconInfo = document.getElementById('confirm-icon-info');

        this.closeButton = document.getElementById('confirm-modal-close');

        this.title = document.getElementById('confirm-modal-title');
        this.message = document.getElementById('confirm-modal-message');
        this.description = document.getElementById('confirm-modal-description');

        this.cancelButton = document.getElementById('confirm-modal-cancel');
        this.confirmButton = document.getElementById('confirm-modal-confirm');
        this.confirmButton.focus();

        this.onConfirm = null;

        this.bindEvents();
    }

    bindEvents() {

        this.overlay.addEventListener('click', () => {
            this.close();
        });

        this.cancelButton.addEventListener('click', () => {
            this.close();
        });

        this.confirmButton.addEventListener('click', () => {

            if (typeof this.onConfirm === 'function') {
                this.onConfirm();
            }

            this.close();

        });

        this.closeButton.addEventListener('click', () => {
            this.close();
        });

        document.addEventListener('keydown', (event) => {

    if (this.modal.classList.contains('hidden')) {
        return;
    }


    // ESC - закрыть
    if (event.key === 'Escape') {

        this.close();

        return;
    }


    // ENTER - подтвердить
    if (event.key === 'Enter') {

        this.confirmButton.click();

    }

});

    }

    

    setType(type = 'danger') {

    this.iconDanger.classList.add('hidden');
    this.iconWarning.classList.add('hidden');
    this.iconSuccess.classList.add('hidden');
    this.iconInfo.classList.add('hidden');

    this.icon.className =
        'flex h-11 w-11 items-center justify-center rounded-xl';

    this.confirmButton.className =
        'inline-flex items-center rounded-lg bg-gray-900 px-5 py-2.5 text-sm font-medium text-white transition hover:bg-gray-700';

    switch (type) {

        case 'danger':

            this.icon.classList.add(
                'bg-red-100',
                'text-red-600'
            );

            this.confirmButton.className =
                'inline-flex items-center rounded-lg bg-red-600 px-5 py-2.5 text-sm font-medium text-white transition hover:bg-red-700';

            this.iconDanger.classList.remove('hidden');

            break;

        case 'warning':

            this.icon.classList.add(
                'bg-amber-100',
                'text-amber-600'
            );

            this.iconWarning.classList.remove('hidden');

            break;

        case 'success':

            this.icon.classList.add(
                'bg-green-100',
                'text-green-600'
            );

            this.iconSuccess.classList.remove('hidden');

            break;

        case 'info':

            this.icon.classList.add(
                'bg-blue-100',
                'text-blue-600'
            );

            this.iconInfo.classList.remove('hidden');

            break;

    }

}




    open(options = {}) {

        this.title.textContent =
            options.title ?? 'Confirmation';

        this.message.textContent =
            options.message ?? '';

        this.description.textContent =
            options.description ?? '';

        this.cancelButton.textContent =
            options.cancelText ?? 'Cancel';

        this.confirmButton.textContent =
            options.confirmText ?? 'Confirm';

        this.onConfirm =
            options.onConfirm ?? null;

        this.setType(
            options.type ?? 'danger'
        );




        this.modal.classList.remove('hidden');

document.body.classList.add('overflow-hidden');

requestAnimationFrame(() => {

    this.modal.classList.remove('opacity-0');

    this.window.classList.remove(
        'opacity-0',
        'scale-95'
    );

    this.window.classList.add(
        'opacity-100',
        'scale-100'
    );

});

setTimeout(() => {

    this.confirmButton.focus();

}, 100);

this.isOpen = true;

    }

    close() {


    this.modal.classList.add('opacity-0');

    this.window.classList.remove(
        'opacity-100',
        'scale-100'
    );

    this.window.classList.add(
        'opacity-0',
        'scale-95'
    );


    setTimeout(() => {

        this.modal.classList.add('hidden');

        document.body.classList.remove(
            'overflow-hidden'
        );

    }, 200);


    this.onConfirm = null;

    this.isOpen = false;

}

}

document.addEventListener('DOMContentLoaded', () => {

    const confirmModal = new ConfirmModal();

    window.confirmModal = confirmModal;

});