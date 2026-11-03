document.addEventListener('DOMContentLoaded', function () {

    // =========================================================
    // PUBLICATION ACTIONS DROPDOWN
    // =========================================================

    const actionMenus = document.querySelectorAll('.post-actions-menu');

    if (!actionMenus.length) return;


    // =========================================================
    // CLOSE MENUS
    // =========================================================

    function closeAllPostMenus(except = null) {

        actionMenus.forEach(menu => {

            if (menu !== except) {
                menu.removeAttribute('open');
            }

        });

    }


    // =========================================================
    // INITIALIZE MENUS
    // =========================================================

    actionMenus.forEach(menu => {

        const summary = menu.querySelector('summary');
        const dropdown = menu.querySelector('[data-actions-dropdown]');

        if (!summary || !dropdown) return;


        // =====================================================
        // POSITION DROPDOWN
        // =====================================================

        function positionDropdown() {

            const rect = summary.getBoundingClientRect();

            const dropdownWidth = dropdown.offsetWidth;
            const dropdownHeight = dropdown.offsetHeight;

            const viewportWidth = window.innerWidth;
            const viewportHeight = window.innerHeight;

            const spaceBelow = viewportHeight - rect.bottom;
            const spaceAbove = rect.top;

            const openAbove =
                spaceBelow < dropdownHeight + 12 &&
                spaceAbove > spaceBelow;

            let left = rect.right - dropdownWidth;

            let top = openAbove
                ? rect.top - dropdownHeight - 6
                : rect.bottom + 6;


            // Keep dropdown inside viewport.

            left = Math.max(
                8,
                Math.min(left, viewportWidth - dropdownWidth - 8)
            );

            top = Math.max(
                8,
                Math.min(top, viewportHeight - dropdownHeight - 8)
            );


            dropdown.style.left = `${left}px`;
            dropdown.style.top = `${top}px`;

        }


        // =====================================================
        // TOGGLE MENU
        // =====================================================

        summary.addEventListener('click', function (event) {

            event.preventDefault();

            const isOpen = menu.hasAttribute('open');

            closeAllPostMenus();

            if (!isOpen) {

                menu.setAttribute('open', '');

                positionDropdown();

            }

        });


        // =====================================================
        // CLOSE AFTER ACTION
        // =====================================================

        menu.querySelectorAll('[data-close-post-actions]').forEach(element => {

            element.addEventListener('click', function () {

                menu.removeAttribute('open');

            });

        });


        // =====================================================
        // RESIZE
        // =====================================================

        window.addEventListener('resize', function () {

            if (menu.hasAttribute('open')) {
                positionDropdown();
            }

        });


        // =====================================================
        // SCROLL
        // =====================================================

        window.addEventListener('scroll', function () {

            if (menu.hasAttribute('open')) {
                positionDropdown();
            }

        }, true);


    });


    // =========================================================
    // CLICK OUTSIDE
    // =========================================================

    document.addEventListener('click', function (event) {

        if (!event.target.closest('.post-actions-menu')) {
            closeAllPostMenus();
        }

    });


    // =========================================================
    // ESCAPE KEY
    // =========================================================

    document.addEventListener('keydown', function (event) {

        if (event.key === 'Escape') {

            closeAllPostMenus();

        }

    });

});