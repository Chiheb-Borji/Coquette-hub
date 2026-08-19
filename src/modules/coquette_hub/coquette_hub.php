<?php

defined('BASEPATH') or exit('No direct script access allowed');


/*
|--------------------------------------------------------------------------
| COQUETTE_HUB_LANGUAGE_FILES_V1
|--------------------------------------------------------------------------
*/

register_language_files(
    'coquette_hub',
    [
        'coquette_hub',
    ]
);


/*
Module Name: Coquette Hub
Description: Interface interne Coquette pour CRM, tâches, tickets et statistiques.
Version: 0.3.0
Requires at least: 3.2.*
*/

define('COQUETTE_HUB_MODULE_NAME', 'coquette_hub');


/*
|--------------------------------------------------------------------------
| Assets
|--------------------------------------------------------------------------
*/

hooks()->add_action('app_admin_assets', 'coquette_hub_register_assets');

function coquette_hub_register_assets()
{
    $CI = &get_instance();

    $CI->app_css->add(
        'coquette-hub-css',
        module_dir_url(
            COQUETTE_HUB_MODULE_NAME,
            'assets/css/coquette-hub.css'
        ),
        'admin',
        ['app-css']
    );

    $CI->app_scripts->add(
        'coquette-hub-js',
        module_dir_url(
            COQUETTE_HUB_MODULE_NAME,
            'assets/js/coquette-hub.js'
        ),
        'admin',
        ['app-js']
    );
}


/*
|--------------------------------------------------------------------------
| Default dashboard -> Coquette Hub
|--------------------------------------------------------------------------
*/

hooks()->add_action(
    'admin_init',
    'coquette_hub_redirect_dashboard',
    1
);

function coquette_hub_redirect_dashboard()
{
    if (!is_staff_logged_in()) {
        return;
    }

    $CI = &get_instance();

    if (
        strtolower($CI->router->fetch_class()) === 'dashboard'
        && strtolower($CI->router->fetch_method()) === 'index'
    ) {
        redirect(admin_url('coquette_hub'));
        exit;
    }
}


/*
|--------------------------------------------------------------------------
| Coquette HUB Navigation
|--------------------------------------------------------------------------
*/

hooks()->add_action(
    'admin_init',
    'coquette_hub_register_navigation',
    90
);

function coquette_hub_register_navigation()
{
    $CI = &get_instance();


    /*
    |--------------------------------------------------------------------------
    | CRM
    |--------------------------------------------------------------------------
    */

    $CI->app_menu->add_sidebar_menu_item('hub-crm', [
        'collapse' => true,
        'name'     => _l('cqhub_crm'),
        'href'     => '#',
        'icon'     => 'fa-solid fa-address-book',
        'position' => 10,
    ]);

    if (
        staff_can('view', 'customers')
        || staff_can('create', 'customers')
    ) {
        $CI->app_menu->add_sidebar_children_item(
            'hub-crm',
            [
                'slug'     => 'hub-clients',
                'name'     => _l('cqhub_clients'),
                'href'     => admin_url('clients'),
                'icon'     => 'fa-solid fa-users',
                'position' => 10,
            ]
        );

        $CI->app_menu->add_sidebar_children_item(
            'hub-crm',
            [
                'slug'     => 'hub-contacts',
                'name'     => _l('cqhub_contacts'),
                'href'     => admin_url('clients/all_contacts'),
                'icon'     => 'fa-regular fa-address-card',
                'position' => 20,
            ]
        );
    }


    /*
    |--------------------------------------------------------------------------
    | WORK
    |--------------------------------------------------------------------------
    */

    $CI->app_menu->add_sidebar_menu_item('hub-work', [
        'collapse' => true,
        'name'     => _l('cqhub_work'),
        'href'     => '#',
        'icon'     => 'fa-solid fa-briefcase',
        'position' => 20,
    ]);

    if (
        staff_can('view', 'tasks')
        || staff_can('create', 'tasks')
        || staff_can('edit', 'tasks')
    ) {
        $CI->app_menu->add_sidebar_children_item(
            'hub-work',
            [
                'slug'     => 'hub-tasks',
                'name'     => _l('cqhub_tasks'),
                'href'     => admin_url('tasks'),
                'icon'     => 'fa-solid fa-list-check',
                'position' => 10,
            ]
        );
    }

    if (is_staff_member() || is_admin()) {
        $CI->app_menu->add_sidebar_children_item(
            'hub-work',
            [
                'slug'     => 'hub-tickets',
                'name'     => _l('cqhub_tickets'),
                'href'     => admin_url('tickets'),
                'icon'     => 'fa-solid fa-ticket',
                'position' => 20,
            ]
        );
    }



    /*
    |--------------------------------------------------------------------------
    | COQUETTE_HUB_TEAM_TODO_MENU_V1
    |--------------------------------------------------------------------------
    */

    if (
        coquette_hub_can_view_team_todo()
    ) {

        $CI->app_menu->add_sidebar_children_item(
            'hub-work',
            [
                'slug'     => 'hub-team-todo',
                'name'     => _l('cqhub_team_todo'),
                'href'     => admin_url(
                    'coquette_hub/team_todo'
                ),
                'icon'     => 'fa-solid fa-clipboard-check',
                'position' => 15,
            ]
        );
    }


    /*
    |--------------------------------------------------------------------------
    | MARKETING PLAN
    |--------------------------------------------------------------------------
    */

    if (is_staff_member() || is_admin()) {
        $CI->app_menu->add_sidebar_children_item(
            'hub-work',
            [
                'slug'     => 'hub-marketing-plan',
                'name'     => _l('cqhub_marketing_plan'),
                'href'     => admin_url('marketing_plan'),
                'icon'     => 'fa-solid fa-calendar-days',
                'position' => 30,
            ]
        );
    }


    /*
    |--------------------------------------------------------------------------
    | ANALYTICS
    |--------------------------------------------------------------------------
    */

    $CI->app_menu->add_sidebar_menu_item('hub-analytics', [
        'collapse' => true,
        'name'     => _l('cqhub_analytics_menu'),
        'href'     => '#',
        'icon'     => 'fa-solid fa-chart-line',
        'position' => 30,
    ]);

    $CI->app_menu->add_sidebar_children_item(
        'hub-analytics',
        [
            'slug'     => 'hub-statistics',
            'name'     => _l('cqhub_statistics'),
            'href'     => admin_url('coquette_hub/statistics'),
            'icon'     => 'fa-solid fa-chart-pie',
            'position' => 10,
        ]
    );


    /*
    |--------------------------------------------------------------------------
    | SYSTEM — Admin Total only
    |--------------------------------------------------------------------------
    */

    if (is_admin()) {
        $CI->app_menu->add_sidebar_menu_item('hub-system', [
            'collapse' => true,
            'name'     => _l('cqhub_system'),
            'href'     => '#',
            'icon'     => 'fa-solid fa-gear',
            'position' => 40,
        ]);

        /*
        |--------------------------------------------------------------------------
        | COQUETTE_HUB_SYSTEM_USERS_V1
        |--------------------------------------------------------------------------
        */

        $CI->app_menu->add_sidebar_children_item(
            'hub-system',
            [
                'slug'     => 'hub-users',
                'name'     => _l('cqhub_users'),
                'href'     => admin_url('staff'),
                'icon'     => 'fa-solid fa-users',
                'position' => 10,
            ]
        );

        $CI->app_menu->add_sidebar_children_item(
            'hub-system',
            [
                'slug'     => 'hub-roles',
                'name'     => _l('cqhub_roles_permissions'),
                'href'     => admin_url('roles'),
                'icon'     => 'fa-solid fa-user-shield',
                'position' => 20,
            ]
        );

        $CI->app_menu->add_sidebar_children_item(
            'hub-system',
            [
                'slug'     => 'hub-settings',
                'name'     => _l('cqhub_settings'),
                'href'     => admin_url('settings'),
                'icon'     => 'fa-solid fa-sliders',
                'position' => 30,
            ]
        );
    }
}




/*
|--------------------------------------------------------------------------
| COQUETTE_HUB_TEAM_TODO_PERMISSION_V1
|--------------------------------------------------------------------------
|
| Admin Total + Supervisor only.
|
*/

function coquette_hub_can_view_team_todo()
{
    if (is_admin()) {
        return true;
    }


    $CI = &get_instance();


    $staffId =
        (int) get_staff_user_id();


    if ($staffId <= 0) {
        return false;
    }


    $staff =
        $CI->db
            ->select('role')
            ->where(
                'staffid',
                $staffId
            )
            ->get(
                db_prefix() . 'staff'
            )
            ->row();


    if (
        !$staff
        ||
        (int) $staff->role <= 0
    ) {
        return false;
    }


    $role =
        $CI->db
            ->select('name')
            ->where(
                'roleid',
                (int) $staff->role
            )
            ->get(
                db_prefix() . 'roles'
            )
            ->row();


    if (!$role) {
        return false;
    }


    return in_array(
        (string) $role->name,
        [
            'Coquette Super Admin',
            'Supervisor',
        ],
        true
    );
}


/*
|--------------------------------------------------------------------------
| Keep only Hub navigation
|--------------------------------------------------------------------------
*/

hooks()->add_filter(
    'sidebar_menu_items',
    'coquette_hub_filter_sidebar',
    10000
);

function coquette_hub_filter_sidebar($items)
{
    $allowed = [
        'dashboard',
        'hub-crm',
        'hub-work',
        'hub-analytics',
        'hub-system',
    ];

    foreach (array_keys($items) as $slug) {
        if (!in_array($slug, $allowed, true)) {
            unset($items[$slug]);
        }
    }

    if (isset($items['dashboard'])) {
        $items['dashboard']['name'] = _l('cqhub_dashboard');
        $items['dashboard']['href'] = admin_url('coquette_hub');
        $items['dashboard']['icon'] = 'fa-solid fa-house';
        $items['dashboard']['position'] = 1;
    }

    return $items;
}


/*
|--------------------------------------------------------------------------
| Hide legacy Perfex Setup menu
|--------------------------------------------------------------------------
*/

hooks()->add_filter(
    'setup_menu_items',
    'coquette_hub_hide_legacy_setup',
    10000
);

function coquette_hub_hide_legacy_setup($items)
{
    return [];
}


/*
|--------------------------------------------------------------------------
| COQUETTE_HUB_LOGIN_PREMIUM_V1
|--------------------------------------------------------------------------
*/

hooks()->add_action(
    'app_admin_authentication_head',
    'coquette_hub_login_premium_style'
);

function coquette_hub_login_premium_style()
{
    ?>
    <style id="coquette-hub-login-premium">

    /*
    |--------------------------------------------------------------------------
    | GLOBAL
    |--------------------------------------------------------------------------
    */

    body.login_admin {
        min-height: 100vh;

        background:
            radial-gradient(
                circle at 15% 20%,
                rgba(218, 28, 92, .08),
                transparent 32%
            ),
            radial-gradient(
                circle at 85% 75%,
                rgba(218, 28, 92, .05),
                transparent 28%
            ),
            #F7F8FA !important;

        color: #18181B;
    }


    /*
    |--------------------------------------------------------------------------
    | WRAPPER
    |--------------------------------------------------------------------------
    */

    body.login_admin .authentication-form-wrapper {
        width: 100% !important;
        max-width: 465px !important;

        padding-top: 55px !important;
        padding-bottom: 50px !important;
    }


    /*
    |--------------------------------------------------------------------------
    | LOGO
    |--------------------------------------------------------------------------
    */

    body.login_admin .company-logo {
        margin: 0 auto 18px !important;
        text-align: center;
    }

    body.login_admin .company-logo img {
        display: inline-block !important;

        width: 145px !important;
        height: 145px !important;

        max-width: 145px !important;
        max-height: 145px !important;

        object-fit: contain !important;

        margin: 0 auto !important;
    }


    /*
    |--------------------------------------------------------------------------
    | TITLE
    |--------------------------------------------------------------------------
    */

    body.login_admin
    .authentication-form-wrapper
    > .text-center {
        margin-bottom: 24px !important;
    }

    body.login_admin
    .authentication-form-wrapper
    > .text-center::before {
        content: "COQUETTE.TN HUB";

        display: block;

        margin-bottom: 8px;

        color: #DA1C5C;

        font-size: 12px;
        font-weight: 800;

        letter-spacing: .16em;
    }

    body.login_admin
    .authentication-form-wrapper
    > .text-center h1 {
        font-size: 27px !important;
        line-height: 1.15 !important;

        font-weight: 800 !important;

        color: #18181B !important;

        margin-bottom: 7px !important;
    }

    body.login_admin
    .authentication-form-wrapper
    > .text-center h1::after {
        content: " — Espace employés";
    }

    body.login_admin
    .authentication-form-wrapper
    > .text-center p {
        font-size: 14px !important;
        line-height: 1.55;

        color: #71717A !important;
    }


    /*
    |--------------------------------------------------------------------------
    | LOGIN CARD
    |--------------------------------------------------------------------------
    */

    body.login_admin
    .authentication-form-wrapper
    > div.tw-bg-white {
        position: relative;

        margin-left: 0 !important;
        margin-right: 0 !important;

        padding: 32px 34px 30px !important;

        background: rgba(255,255,255,.97) !important;

        border: 1px solid #E6E6EA !important;
        border-radius: 18px !important;

        box-shadow:
            0 18px 55px rgba(24,24,27,.08),
            0 2px 6px rgba(24,24,27,.03) !important;

        overflow: hidden;
    }

    body.login_admin
    .authentication-form-wrapper
    > div.tw-bg-white::before {
        content: "";

        position: absolute;
        top: 0;
        left: 0;
        right: 0;

        height: 4px;

        background: #DA1C5C;
    }


    /*
    |--------------------------------------------------------------------------
    | LABELS
    |--------------------------------------------------------------------------
    */

    body.login_admin .control-label {
        color: #3F3F46 !important;

        font-size: 13px !important;
        font-weight: 700 !important;
    }


    /*
    |--------------------------------------------------------------------------
    | INPUTS
    |--------------------------------------------------------------------------
    */

    body.login_admin .form-control {
        height: 46px !important;

        padding: 10px 13px !important;

        border: 1px solid #D4D4D8 !important;
        border-radius: 10px !important;

        background: #FAFAFA !important;

        font-size: 14px !important;

        box-shadow: none !important;

        transition:
            border-color .15s ease,
            box-shadow .15s ease,
            background .15s ease;
    }

    body.login_admin .form-control:hover {
        background: #FFFFFF !important;
        border-color: #A1A1AA !important;
    }

    body.login_admin .form-control:focus {
        background: #FFFFFF !important;

        border-color: #DA1C5C !important;

        box-shadow:
            0 0 0 3px rgba(218, 28, 92, .10)
            !important;
    }


    /*
    |--------------------------------------------------------------------------
    | SPACING
    |--------------------------------------------------------------------------
    */

    body.login_admin .form-group.tw-mt-8 {
        margin-top: 24px !important;
    }


    /*
    |--------------------------------------------------------------------------
    | FORGOT PASSWORD
    |--------------------------------------------------------------------------
    */

    body.login_admin a.text-muted {
        color: #71717A !important;

        font-size: 12px;
        font-weight: 600;

        text-decoration: none !important;
    }

    body.login_admin a.text-muted:hover {
        color: #DA1C5C !important;
    }


    /*
    |--------------------------------------------------------------------------
    | CHECKBOX
    |--------------------------------------------------------------------------
    */

    body.login_admin .checkbox label {
        color: #52525B;

        font-size: 13px;
    }


    /*
    |--------------------------------------------------------------------------
    | BUTTON
    |--------------------------------------------------------------------------
    */

    body.login_admin .btn-primary {
        height: 47px;

        border: 0 !important;
        border-radius: 10px !important;

        background: #DA1C5C !important;

        color: #FFFFFF !important;

        font-size: 14px !important;
        font-weight: 800 !important;

        letter-spacing: .01em;

        box-shadow:
            0 8px 20px rgba(218, 28, 92, .18);

        transition:
            transform .15s ease,
            box-shadow .15s ease,
            background .15s ease;
    }

    body.login_admin .btn-primary:hover,
    body.login_admin .btn-primary:focus {
        background: #C51651 !important;

        transform: translateY(-1px);

        box-shadow:
            0 10px 24px rgba(218, 28, 92, .23);
    }


    /*
    |--------------------------------------------------------------------------
    | SECURITY FOOTER
    |--------------------------------------------------------------------------
    */

    body.login_admin
    .authentication-form-wrapper
    > div.tw-bg-white::after {
        content: "🔒 Accès sécurisé réservé aux employés Coquette.tn";

        display: block;

        margin-top: 23px;
        padding-top: 18px;

        border-top: 1px solid #F0F0F2;

        color: #A1A1AA;

        font-size: 11px;
        font-weight: 500;

        text-align: center;
    }


    /*
    |--------------------------------------------------------------------------
    | MOBILE
    |--------------------------------------------------------------------------
    */

    @media (max-width: 600px) {

        body.login_admin .authentication-form-wrapper {
            max-width: 100% !important;

            padding:
                28px 16px 35px !important;
        }

        body.login_admin .company-logo {
            margin-bottom: 12px !important;
        }

        body.login_admin .company-logo img {
            width: 105px !important;
            height: 105px !important;

            max-width: 105px !important;
            max-height: 105px !important;
        }

        body.login_admin
        .authentication-form-wrapper
        > .text-center h1 {
            font-size: 22px !important;
        }

        body.login_admin
        .authentication-form-wrapper
        > div.tw-bg-white {
            padding: 26px 20px !important;

            border-radius: 15px !important;
        }

    }

    </style>
    <?php
}
