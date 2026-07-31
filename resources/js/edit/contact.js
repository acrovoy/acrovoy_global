export function initContactDrawer() {

    const addButton = document.getElementById('add-contact-btn');
    const cancelButton = document.getElementById('cancel-add-contact');
    const form = document.getElementById('contact-create-form');
    const contactForm = document.getElementById('contact-form');
    const saveButton = document.getElementById('save-contact');
    const contactType = document.getElementById('contact-type');
    const showInProfile = document.getElementById('show-in-profile');

    if (!addButton || !form || !contactForm) {
        return;
    }

     if (!contactType || !showInProfile) {
        return;
    }

    const showInProfilelabel = showInProfile.closest('label');
    
    function updateShowInProfileState() {

        const disabled = contactType.value === 'website';

        showInProfile.disabled = disabled;

        if (disabled) {
            showInProfile.checked = false;
        }

        showInProfilelabel.classList.toggle('opacity-50', disabled);
        showInProfilelabel.classList.toggle('cursor-not-allowed', disabled);
    }

    contactType.addEventListener('change', updateShowInProfileState);

    updateShowInProfileState();

    const createAction = contactForm.action;

    const defaultsInput = document.getElementById('contact-defaults');

    const defaults = defaultsInput
        ? JSON.parse(defaultsInput.value)
        : {};

    const type = document.getElementById('contact-type');
    const value = document.getElementById('contact-value');
    const label = document.getElementById('contact-label');

    /*
    |--------------------------------------------------------------------------
    | Add Contact
    |--------------------------------------------------------------------------
    */

    addButton.addEventListener('click', () => {

        contactForm.reset();

        updateShowInProfileState();

        contactForm.action = createAction;

        document.getElementById('contact-form-title').textContent =
    'Add Contact';

document.getElementById('contact-form-description').textContent =
    'Add a new way for customers to contact this company.';
    
        contactForm.querySelector('[name="_method"]')?.remove();

        value.placeholder = 'Select contact type first';

        document.getElementById('save-contact').textContent =
            'Add Contact';

        form.classList.remove('hidden');
        addButton.classList.add('hidden');
        

    });

    cancelButton?.addEventListener('click', () => {

    contactForm.reset();
    
    updateShowInProfileState();

document.getElementById('contact-form-title').textContent =
    'Add Contact';

document.getElementById('contact-form-description').textContent =
    'Add a new way for customers to contact this company.';


    contactForm.action = createAction;

    contactForm.querySelector('[name="_method"]')?.remove();

    saveButton.textContent = 'Add Contact';

    form.classList.add('hidden');
    addButton.classList.remove('hidden');

});



    /*
    |--------------------------------------------------------------------------
    | Edit Contact
    |--------------------------------------------------------------------------
    */

    document.querySelectorAll('.edit-contact').forEach(button => {

        button.addEventListener('click', () => {

            form.classList.remove('hidden');
            addButton.classList.add('hidden');

            document.getElementById('contact-form-title').textContent =
    'Edit Contact';

document.getElementById('contact-form-description').textContent =
    'Update this contact information.';

            document.getElementById('drawer-body')?.scrollTo({
        top: 0,
        behavior: 'smooth',
    });

            contactForm.action = button.dataset.url;

            contactForm.querySelector('[name="_method"]')?.remove();

            contactForm.insertAdjacentHTML(
                'beforeend',
                '<input type="hidden" name="_method" value="PUT">'
            );

            type.value = button.dataset.type;
            updateShowInProfileState();
            value.value = button.dataset.value;
            label.value = button.dataset.label;

            const item = defaults[type.value];

            if (item) {
                value.placeholder = item.placeholder;
            }

            contactForm.querySelector('[name="is_primary"]').checked =
                button.dataset.primary === '1';

            contactForm.querySelector('[name="is_public"]').checked =
                button.dataset.public === '1';

            document.getElementById('save-contact').textContent =
                'Save Changes';

        });

    });

    /*
    |--------------------------------------------------------------------------
    | Contact Type Changed
    |--------------------------------------------------------------------------
    */

    if (type && value && label) {

        type.addEventListener('change', () => {

            const item = defaults[type.value];

            if (!item) {
                return;
            }

            value.placeholder = item.placeholder;

            if (!label.value) {
                label.value = item.label;
            }

        });

    }

    /*
    |--------------------------------------------------------------------------
    | Delete Contact
    |--------------------------------------------------------------------------
    */

    document.querySelectorAll('.delete-contact').forEach(button => {

        button.addEventListener('click', () => {

            window.confirmModal.open({

                title: 'Delete Contact',
                description: 'This action cannot be undone.',
                message: 'Are you sure you want to permanently delete this contact?',
                confirmText: 'Delete',
                type: 'danger',

                onConfirm: async () => {

                    const response = await fetch(button.dataset.url, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document
                                .querySelector('meta[name="csrf-token"]')
                                .content,
                            'Accept': 'application/json',
                        },
                    });

                    const data = await response.json();

                    if (!response.ok) {
                        dispatchAlert('error', data.message ?? 'Delete failed');
                        return;
                    }

                    dispatchAlert('success', data.message);

                    button.closest('.rounded-2xl')?.remove();

                }

            });

        });

    });

}