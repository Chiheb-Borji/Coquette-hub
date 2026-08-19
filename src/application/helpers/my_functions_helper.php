<?php

defined('BASEPATH')
    or exit('No direct script access allowed');



/* COQUETTE_MARKETING_PLAN_V3 */


hooks()->add_action(
    'admin_init',
    'coquette_marketing_v3_menu'
);


/*
 * COQUETTE HUB CUSTOM DASHBOARD
 *
 * Marketing is now rendered directly inside
 * modules/coquette_hub/views/dashboard.php.
 *
 * Keep the old function below for compatibility,
 * but do not render it through the generic
 * Perfex dashboard hook.
 */

/*
hooks()->add_action(
    'after_dashboard_top_container',
    'coquette_marketing_v3_dashboard'
);
*/


function coquette_marketing_v3_menu()
{
    if (!is_staff_logged_in()) {
        return;
    }


    $CI = &get_instance();


    $CI->app_menu->add_sidebar_menu_item(
        'coquette-marketing-plan',
        [

            'name' =>
                'Plan Marketing',

            'href' =>
                admin_url(
                    'marketing_plan'
                ),

            'position' =>
                6,

            'icon' =>
                'fa fa-calendar',
        ]
    );
}


function coquette_marketing_v3_dashboard()
{
    if (!is_staff_logged_in()) {
        return;
    }


    $CI = &get_instance();


    $plan = $CI->db

        ->where(
            'status',
            'active'
        )

        ->order_by(
            'id',
            'DESC'
        )

        ->get(
            db_prefix()
            . 'marketing_plans'
        )

        ->row_array();


    if (!$plan) {
        return;
    }


    $planId =
        (int)$plan['id'];


    $objectives = $CI->db

        ->where(
            'plan_id',
            $planId
        )

        ->where(
            'active',
            1
        )

        ->order_by(
            'position',
            'ASC'
        )

        ->get(
            db_prefix()
            . 'marketing_objectives'
        )

        ->result_array();


    $phases = $CI->db

        ->where(
            'plan_id',
            $planId
        )

        ->order_by(
            'position',
            'ASC'
        )

        ->get(
            db_prefix()
            . 'marketing_phases'
        )

        ->result_array();


    $rangeStart = min(
        date('Y-m-01'),
        date(
            'Y-m-d',
            strtotime(
                'monday this week'
            )
        )
    );


    $rangeEnd = date(
        'Y-m-d',
        strtotime('+3 months')
    );


    $items = $CI->db

        ->select(
            db_prefix()
            . 'marketing_plan_items.*, '
            . db_prefix()
            . 'marketing_plan_sections.name '
            . 'AS section_name'
        )

        ->from(
            db_prefix()
            . 'marketing_plan_items'
        )

        ->join(
            db_prefix()
            . 'marketing_plan_sections',

            db_prefix()
            . 'marketing_plan_sections.id='
            . db_prefix()
            . 'marketing_plan_items.section_id',

            'left'
        )

        ->where(
            db_prefix()
            . 'marketing_plan_items.plan_id',
            $planId
        )

        ->where(
            'plan_date >=',
            $rangeStart
        )

        ->where(
            'plan_date <=',
            $rangeEnd
        )

        ->order_by(
            'plan_date',
            'ASC'
        )

        ->order_by(
            'start_time',
            'ASC'
        )

        ->get()

        ->result_array();


    $CI->load->view(
        'admin/marketing_plan/dashboard_widget',
        [

            'cmp_plan' =>
                $plan,

            'cmp_objectives' =>
                $objectives,

            'cmp_phases' =>
                $phases,

            'cmp_items' =>
                $items,
        ]
    );
}


/* END COQUETTE_MARKETING_PLAN_V3 */

