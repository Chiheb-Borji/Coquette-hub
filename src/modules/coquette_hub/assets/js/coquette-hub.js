(function () {
    'use strict';

    function initCoquetteHub() {

        document.documentElement.classList.add('coquette-hub');

        if (document.body) {
            document.body.classList.add('coquette-hub-ui');
        }

        var logoUrl =
            '/modules/coquette_hub/assets/images/logo-hub-full.png';


        /*
        |--------------------------------------------------------------------------
        | HEADER LOGO
        |--------------------------------------------------------------------------
        */

        var logoContainer = document.querySelector('#header #logo');

        if (logoContainer) {

            var logoLink = logoContainer.querySelector('a');

            if (!logoLink) {
                logoLink = document.createElement('a');
                logoLink.href = '/admin/coquette_hub';
                logoLink.className = 'logo';
                logoContainer.appendChild(logoLink);
            }

            logoLink.href = '/admin/coquette_hub';

            logoLink.innerHTML =
                '<img class="coquette-hub-header-logo" ' +
                'src="' + logoUrl + '" ' +
                'alt="Coquette.tn HUB">';

            logoContainer.style.display = 'flex';
        }


        /*
        |--------------------------------------------------------------------------
        | SIDEBAR BRAND
        |--------------------------------------------------------------------------
        */

        var sidebar = document.querySelector('#menu.sidebar');

        if (
            sidebar
            && !sidebar.querySelector('.coquette-hub-sidebar-brand')
        ) {

            var brand = document.createElement('a');

            brand.className = 'coquette-hub-sidebar-brand';
            brand.href = '/admin/coquette_hub';

            brand.innerHTML =
                '<img src="' + logoUrl + '" ' +
                'alt="Coquette.tn HUB">';

            sidebar.insertBefore(
                brand,
                sidebar.firstChild
            );
        }


        /*
        |--------------------------------------------------------------------------
        | REMOVE DUPLICATE TOP SETTINGS
        |--------------------------------------------------------------------------
        */

        document
            .querySelectorAll('#header .navbar-right a')
            .forEach(function (link) {

                var href = link.getAttribute('href') || '';

                if (
                    href.indexOf('/admin/settings') !== -1
                ) {
                    var item = link.closest('li');

                    if (item) {
                        item.remove();
                    }
                }
            });


        /*
        |--------------------------------------------------------------------------
        | REMOVE LEGACY SETUP
        |--------------------------------------------------------------------------
        */

        var setup = document.getElementById('setup-menu-item');

        if (setup) {
            setup.remove();
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Perfex peut charger ce fichier APRES DOMContentLoaded.
    |--------------------------------------------------------------------------
    */

    if (document.readyState === 'loading') {

        document.addEventListener(
            'DOMContentLoaded',
            initCoquetteHub
        );

    } else {

        initCoquetteHub();

    }

})();


/* =========================================================
   COQUETTE HUB BRAND TEXT v1
   ========================================================= */

(function () {
    'use strict';

    function installHubBrandText() {

        var headerLogo = document.querySelector('#header #logo a');

        if (
            headerLogo &&
            !headerLogo.querySelector('.coquette-hub-brand-title')
        ) {
            var title = document.createElement('span');

            title.className = 'coquette-hub-brand-title';
            title.textContent = 'COQUETTE.TN HUB';

            headerLogo.appendChild(title);
        }


        var sidebarBrand =
            document.querySelector('.coquette-hub-sidebar-brand');

        if (
            sidebarBrand &&
            !sidebarBrand.querySelector('.coquette-hub-sidebar-title')
        ) {
            var sidebarTitle = document.createElement('span');

            sidebarTitle.className =
                'coquette-hub-sidebar-title';

            sidebarTitle.textContent =
                'COQUETTE.TN HUB';

            sidebarBrand.appendChild(sidebarTitle);
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener(
            'DOMContentLoaded',
            installHubBrandText
        );
    } else {
        installHubBrandText();
    }

})();


/*
========================================================
COQUETTE_HUB_LANGUAGE_SWITCH_FR_EN_V1
========================================================
*/

(function () {

    function initCoquetteLanguageSwitch()
    {
        /*
         * Prevent duplicate buttons.
         */
        if (
            document.querySelector(
                '.coquette-hub-language-switch'
            )
        ) {
            return;
        }


        var headerRight =
            document.querySelector(
                '#header .navbar-right'
            );


        if (!headerRight) {
            return;
        }


        /*
         * Detect current language.
         *
         * Perfex may expose app_language.
         * Otherwise use HTML lang.
         */
        var currentLanguage = '';


        if (
            typeof window.app_language === 'string'
        ) {

            currentLanguage =
                window.app_language
                    .toLowerCase();
        }


        if (!currentLanguage) {

            currentLanguage =
                (
                    document.documentElement
                        .getAttribute('lang')
                    || ''
                )
                .toLowerCase();
        }


        var isFrench =
            currentLanguage === 'french'
            ||
            currentLanguage === 'fr'
            ||
            currentLanguage.indexOf('fr-') === 0
            ||
            currentLanguage.indexOf('fr_') === 0;


        var activeLanguage =
            isFrench
                ? 'fr'
                : 'en';


        /*
         * HUB may someday run under a path prefix,
         * so derive /admin dynamically.
         */
        var pathname =
            window.location.pathname;


        var adminPosition =
            pathname.indexOf('/admin');


        var adminBase =
            adminPosition >= 0
                ? pathname.substring(
                    0,
                    adminPosition
                ) + '/admin'
                : '/admin';


        /*
         * Wrapper.
         */
        var wrapper =
            document.createElement('li');


        wrapper.className =
            'coquette-hub-language-switch';


        wrapper.setAttribute(
            'aria-label',
            'Language'
        );


        /*
         * FR
         */
        var fr =
            document.createElement('a');


        fr.href =
            adminBase
            + '/staff/change_language/french';


        fr.textContent =
            'FR';


        fr.className =
            'coquette-hub-lang-btn'
            +
            (
                activeLanguage === 'fr'
                    ? ' active'
                    : ''
            );


        fr.setAttribute(
            'title',
            'Français'
        );


        /*
         * Separator
         */
        var separator =
            document.createElement('span');


        separator.className =
            'coquette-hub-lang-separator';


        separator.textContent =
            '|';


        /*
         * EN
         */
        var en =
            document.createElement('a');


        en.href =
            adminBase
            + '/staff/change_language/english';


        en.textContent =
            'EN';


        en.className =
            'coquette-hub-lang-btn'
            +
            (
                activeLanguage === 'en'
                    ? ' active'
                    : ''
            );


        en.setAttribute(
            'title',
            'English'
        );


        wrapper.appendChild(fr);
        wrapper.appendChild(separator);
        wrapper.appendChild(en);


        /*
         * Put it before the user/profile controls.
         */
        headerRight.insertBefore(
            wrapper,
            headerRight.firstChild
        );
    }


    if (
        document.readyState === 'loading'
    ) {

        document.addEventListener(
            'DOMContentLoaded',
            initCoquetteLanguageSwitch
        );

    } else {

        initCoquetteLanguageSwitch();
    }

})();



/*
========================================================
COQUETTE_HUB_REMOVE_HEADER_WORD_V1
========================================================
*/

(function () {

    function removeHubHeaderWord()
    {
        document
            .querySelectorAll(
                '#header .coquette-hub-brand-title'
            )
            .forEach(function (element) {

                element.remove();

            });
    }


    if (
        document.readyState === 'loading'
    ) {

        document.addEventListener(
            'DOMContentLoaded',
            removeHubHeaderWord
        );

    } else {

        removeHubHeaderWord();
    }


    /*
     * Run once again after Perfex finishes
     * constructing the header.
     */
    window.setTimeout(
        removeHubHeaderWord,
        250
    );

})();

