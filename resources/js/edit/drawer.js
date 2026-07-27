import { initGeneralDrawer } from './general';

import { initManufacturingDrawer } from './manufacturing';

let drawer = null;
let overlay = null;
let body = null;

function initDrawer() {

    drawer = document.getElementById('app-drawer');
    overlay = document.getElementById('app-drawer-overlay');
    body = document.getElementById('drawer-body');

    if (!drawer) {
        return;
    }

    overlay.addEventListener('click', closeDrawer);

    document.addEventListener('submit', async function (e) {

        const form = e.target;

        if (!form.matches('.drawer-form')) {
            return;
        }

        e.preventDefault();

        await submitDrawer(form);

    });

}

async function openDrawer(config = {}) {

    if (!drawer) {
        initDrawer();
    }

    document.getElementById('drawer-title').textContent =
        config.title ?? 'Edit';

    document.getElementById('drawer-description').textContent =
        config.description ?? '';

    body.innerHTML = `
        <div class="flex justify-center py-10">
            <svg class="animate-spin h-6 w-6 text-gray-400"
                 xmlns="http://www.w3.org/2000/svg"
                 fill="none"
                 viewBox="0 0 24 24">

                <circle
                    class="opacity-25"
                    cx="12"
                    cy="12"
                    r="10"
                    stroke="currentColor"
                    stroke-width="4">
                </circle>

                <path
                    class="opacity-75"
                    fill="currentColor"
                    d="M4 12a8 8 0 018-8v8z">
                </path>

            </svg>
        </div>
    `;

    overlay.classList.remove('hidden');

    requestAnimationFrame(() => {

        overlay.classList.remove('opacity-0');

        drawer.classList.remove('translate-x-full');

    });

    try {

        const response = await fetch(config.url, {

            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }

        });

        body.innerHTML = await response.text();

        initGeneralDrawer();

        initManufacturingDrawer();

    } catch (e) {

        body.innerHTML = `
            <div class="text-red-600 text-sm">
                Failed to load content.
            </div>
        `;

    }

}

function closeDrawer() {

    drawer.classList.add('translate-x-full');

    overlay.classList.add('opacity-0');

    setTimeout(() => {

        overlay.classList.add('hidden');

        body.innerHTML = '';

    }, 300);

}

async function submitDrawer(form) {

    const formData = new FormData(form);

    const method =
        form.method ?? 'POST';

    const action =
        form.action;

    try {

        const response = await fetch(action, {

            method: method,

            headers: {

                'X-CSRF-TOKEN':
                    document.querySelector('meta[name="csrf-token"]').content,

                'Accept': 'application/json'

            },

            body: formData

        });

        const data = await response.json();

        if (!response.ok) {

            if (data.errors) {

                throw new Error(

                    Object.values(data.errors)
                        .flat()
                        .join('\n')

                );

            }

            throw new Error(data.message ?? 'Save failed');

        }

        dispatchAlert('success', data.message);

closeDrawer();

setTimeout(() => {

    location.reload();

}, 1000);

        if (typeof window.drawerSuccess === 'function') {

            window.drawerSuccess(data);

        }

    }
    catch (e) {

        dispatchAlert('error', e.message);

    }

}

document.addEventListener('DOMContentLoaded', initDrawer);

window.openDrawer = openDrawer;
window.closeDrawer = closeDrawer;