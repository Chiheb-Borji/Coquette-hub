<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Coquette_hub extends AdminController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $data = [];

        $staffId = get_staff_user_id();

        $staff = $this->db
            ->where('staffid', $staffId)
            ->get(db_prefix() . 'staff')
            ->row();

        $data['staff'] = $staff;

        if (is_admin()) {
            $data['hub_role'] = 'Admin Total';
        } elseif ($staff && (int) $staff->role > 0) {
            $role = $this->db
                ->where('roleid', $staff->role)
                ->get(db_prefix() . 'roles')
                ->row();

            if ($role && $role->name === 'Coquette Super Admin') {
                $data['hub_role'] = 'Super Admin';
            } elseif ($role && $role->name === 'Coquette User') {
                $data['hub_role'] = 'User';
            } else {
                $data['hub_role'] = $role ? $role->name : 'User';
            }
        } else {
            $data['hub_role'] = 'User';
        }


        /*
        |--------------------------------------------------------------------------
        | Global counters
        |--------------------------------------------------------------------------
        */

        $data['tasks_count'] = total_rows(db_prefix() . 'tasks');

        $data['tickets_count'] = total_rows(db_prefix() . 'tickets');


        $data['clients_count'] = total_rows(db_prefix() . 'clients');


        /*
        |--------------------------------------------------------------------------
        | Kanban counters
        |
        | Perfex task states:
        | 1 = Not Started      -> Todo
        | 4 = In Progress      -> In Progress
        | 3 = Testing          -> Test
        | 5 = Complete         -> Done
        |--------------------------------------------------------------------------
        */

        $data['kanban'] = [
            'todo' => total_rows(
                db_prefix() . 'tasks',
                ['status' => 1]
            ),

            'progress' => total_rows(
                db_prefix() . 'tasks',
                ['status' => 4]
            ),

            'test' => total_rows(
                db_prefix() . 'tasks',
                ['status' => 3]
            ),

            'done' => total_rows(
                db_prefix() . 'tasks',
                ['status' => 5]
            ),
        ];


        /*
        |--------------------------------------------------------------------------
        | Recent tasks
        |--------------------------------------------------------------------------
        */

        $this->db->select('id,name,status,duedate,priority');
        $this->db->from(db_prefix() . 'tasks');
        $this->db->order_by('id', 'DESC');
        $this->db->limit(5);

        $data['recent_tasks'] = $this->db->get()->result_array();


        /*
        |--------------------------------------------------------------------------
        | Recent tickets
        |--------------------------------------------------------------------------
        */

        $this->db->select('ticketid,subject,status,date,lastreply');
        $this->db->from(db_prefix() . 'tickets');
        $this->db->order_by('ticketid', 'DESC');
        $this->db->limit(5);

        $data['recent_tickets'] = $this->db->get()->result_array();





        /*
        |--------------------------------------------------------------------------
        | COQUETTE_DASHBOARD_ANALYTICS_OVERVIEW_V1
        | Vue d'ensemble Statistics dans Dashboard HUB
        |--------------------------------------------------------------------------
        */

        $period = (int) $this->input->get(
            'period',
            true
        );

        if (
            !in_array(
                $period,
                [1, 7, 30, 90, 365],
                true
            )
        ) {
            $period = 30;
        }


        $this->load->model(
            'coquette_statistics_model'
        );

        $dashboardStats =
            $this->coquette_statistics_model;


        $data['period'] =
            $period;


        $data['sales_summary'] =
            $dashboardStats->salesSummary(
                $period
            );


        $data['sales_daily'] =
            $dashboardStats->salesDaily(
                $period
            );


        $data['ga_summary'] =
            $dashboardStats->gaSummary(
                $period
            );



        /*
         * COQUETTE_TRAFFIC_ANALYTICS_V1_DASHBOARD
         */
        $data['traffic_daily'] =
            $dashboardStats->trafficVsOrders(
                $period
            );


        $data['product_summary'] =
            $dashboardStats->productSummary();


        $data['top_products'] =
            $dashboardStats->topProducts(
                $period,
                10
            );


        $data['sync_status'] =
            $dashboardStats->syncStatus();



        /*
         * COQUETTE_DASHBOARD_FULL_ANALYTICS_V1
         * Statistics > Analytics dans Dashboard
         */
        $data['analytics'] =
            $dashboardStats->analytics();




        /*
        |--------------------------------------------------------------------------
        | COQUETTE_HUB_MARKETING_DASHBOARD_V1
        | Active marketing plan on custom HUB dashboard
        |--------------------------------------------------------------------------
        */

        $data['marketing_widget'] = '';

        $marketingPlan = $this->db
            ->where('status', 'active')
            ->order_by('id', 'DESC')
            ->get(
                db_prefix() . 'marketing_plans'
            )
            ->row_array();


        if ($marketingPlan) {

            $marketingPlanId =
                (int) $marketingPlan['id'];


            $marketingObjectives =
                $this->db
                    ->where(
                        'plan_id',
                        $marketingPlanId
                    )
                    ->where('active', 1)
                    ->order_by(
                        'position',
                        'ASC'
                    )
                    ->get(
                        db_prefix()
                        . 'marketing_objectives'
                    )
                    ->result_array();


            $marketingPhases =
                $this->db
                    ->where(
                        'plan_id',
                        $marketingPlanId
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


            $rangeEnd =
                date(
                    'Y-m-d',
                    strtotime('+3 months')
                );


            $marketingItems =
                $this->db

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
                        $marketingPlanId
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


            $data['marketing_widget'] =
                $this->load->view(
                    'admin/marketing_plan/dashboard_widget',
                    [
                        'cmp_plan' =>
                            $marketingPlan,

                        'cmp_objectives' =>
                            $marketingObjectives,

                        'cmp_phases' =>
                            $marketingPhases,

                        'cmp_items' =>
                            $marketingItems,
                    ],
                    true
                );
        }


        $data['title'] = 'Coquette.tn HUB';

        $this->load->view(
            'coquette_hub/dashboard',
            $data
        );
    }


    /*
    |--------------------------------------------------------------------------
    | COQUETTE HUB - STATISTICS NATIVE V1
    |--------------------------------------------------------------------------
    */

    public function statistics()
    {

        /*
         * COQUETTE_PRODUCTS_PERMISSION_STATISTICS_V1
         */
        $data['can_view_products'] =
            $this->can_view_products_section();


        $requestedSection =
            trim(
                (string)
                $this->input->get(
                    'section',
                    true
                )
            );


        /*
         * Do not merely hide the tab:
         * block direct requests too.
         */
        if (
            $requestedSection === 'products'
            &&
            !$data['can_view_products']
        ) {
            access_denied('Produits');
        }


        /*
        | COQUETTE_STATS_DEBUG_4B2
        | Temporary isolated runtime logging
        */

        ini_set('log_errors', '1');
        ini_set(
            'error_log',
            '/var/log/coquette-statistics-debug.log'
        );

        error_reporting(E_ALL);

        register_shutdown_function(function () {
            $error = error_get_last();

            if ($error !== null) {
                error_log(
                    '[STATISTICS SHUTDOWN] '
                    . json_encode(
                        $error,
                        JSON_UNESCAPED_SLASHES
                        | JSON_UNESCAPED_UNICODE
                    )
                );
            }
        });

        error_log(
            '[STATISTICS START] '
            . date('Y-m-d H:i:s')
            . ' uri='
            . ($_SERVER['REQUEST_URI'] ?? '-')
        );

        if (!is_staff_logged_in()) {
            redirect(admin_url('authentication'));
            exit;
        }

        $staffId = get_staff_user_id();

        $staff = $this->db
            ->where('staffid', $staffId)
            ->where('active', 1)
            ->get(db_prefix() . 'staff')
            ->row();

        if (!$staff) {
            access_denied('Statistics');
        }

        /* COQUETTE_STATS_TRACE_4B3 */
        error_log('[CQSTEP 01] staff OK');


        $section = strtolower(
            trim(
                (string) $this->input->get(
                    'section',
                    true
                )
            )
        );

        $allowedSections = [
            'overview',
            'analytics',
            'sales',
            'products',
            'stock',
        ];

        if (!in_array(
            $section,
            $allowedSections,
            true
        )) {
            $section = 'overview';
        }


        $period = (int) $this->input->get(
            'period',
            true
        );

        if (!in_array(
            $period,
            [1, 7, 30, 90, 365],
            true
        )) {
            $period = 30;
        }


        $limit = (int) $this->input->get(
            'limit',
            true
        );

        if (!in_array(
            $limit,
            [25, 50, 100],
            true
        )) {
            $limit = 50;
        }


        $q = trim(
            (string) $this->input->get(
                'q',
                true
            )
        );


        error_log('[CQSTEP 02] avant load model');

        $this->load->model(
            'coquette_statistics_model'
        );

        error_log('[CQSTEP 03] model charge');


        $stats =
            $this->coquette_statistics_model;

        error_log(
            '[CQSTEP 04] objet model='
            . (
                is_object($stats)
                    ? get_class($stats)
                    : gettype($stats)
            )
        );


        $data = [];

        $data['title'] =
            'Statistics - Coquette.tn HUB';

        $data['staff'] =
            $staff;

        $data['section'] =
            $section;

        $data['period'] =
            $period;

        $data['limit'] =
            $limit;

        $data['q'] =
            $q;


        error_log('[CQSTEP 05] avant salesSummary');

        $data['sales_summary'] =
            $stats->salesSummary($period);

        error_log('[CQSTEP 06] salesSummary OK');

        $data['sales_daily'] =
            $stats->salesDaily($period);

        error_log(
            '[CQSTEP 07] salesDaily OK count='
            . count($data['sales_daily'])
        );

        $data['ga_summary'] =
            $stats->gaSummary($period);

        error_log('[CQSTEP 08] gaSummary OK');

        $data['ga_daily'] =
            $stats->gaDaily($period);

        error_log(
            '[CQSTEP 09] gaDaily OK count='
            . count($data['ga_daily'])
        );


        /*
         * COQUETTE_TRAFFIC_ANALYTICS_V1_STATISTICS
         */
        $data['traffic_daily'] =
            $stats->trafficVsOrders(
                $period
            );


        $data['product_summary'] =
            $stats->productSummary();

        error_log('[CQSTEP 10] productSummary OK');

        $data['top_products'] =
            $stats->topProducts($period, 10);

        error_log(
            '[CQSTEP 11] topProducts OK count='
            . count($data['top_products'])
        );

        $data['sync_status'] =
            $stats->syncStatus();

        error_log('[CQSTEP 12] syncStatus OK');


        /*
        |--------------------------------------------------------------------------
        | Analytics
        |--------------------------------------------------------------------------
        */

        $data['analytics'] = [];

        if ($section === 'analytics') {
            $data['analytics'] =
                $stats->analytics();
        }


        /*
        |--------------------------------------------------------------------------
        | Products
        |--------------------------------------------------------------------------
        */

        $data['filters'] = [
            'q' => $q,

            'stock' => trim(
                (string) $this->input->get(
                    'stock',
                    true
                )
            ),

            'active' => trim(
                (string) $this->input->get(
                    'active',
                    true
                )
            ),

            'brand' => trim(
                (string) $this->input->get(
                    'brand',
                    true
                )
            ),

            'category' => trim(
                (string) $this->input->get(
                    'category',
                    true
                )
            ),
        ];


        if (!in_array(
            $data['filters']['stock'],
            ['all', 'in', 'out'],
            true
        )) {
            $data['filters']['stock'] = 'all';
        }


        if (!in_array(
            $data['filters']['active'],
            ['all', '1', '0'],
            true
        )) {
            $data['filters']['active'] = 'all';
        }


        $data['products'] = [];
        $data['products_total'] = 0;
        $data['brands'] = [];
        $data['categories'] = [];
        $data['recent_changes'] = [];


        if ($section === 'products') {

            $data['products'] =
                $stats->products(
                    $data['filters'],
                    $limit
                );

            $data['products_total'] =
                $stats->productCount(
                    $data['filters']
                );

            $data['brands'] =
                $stats->brands();

            $data['categories'] =
                $stats->categories();

            $data['recent_changes'] =
                $stats->recentChanges(
                    15,
                    $q
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Stock
        |--------------------------------------------------------------------------
        */

        $data['out_of_stock'] = [];

        if ($section === 'stock') {
            $data['out_of_stock'] =
                $stats->outOfStock(
                    $limit,
                    $q
                );
        }


        error_log(
            '[CQSTEP 90] avant load view statistics'
        );

        /*
         * COQUETTE_PRODUCTS_PERMISSION_FINAL_V1
         *
         * Recalculate the permission immediately before
         * rendering because $data is populated/rebuilt
         * throughout statistics().
         *
         * Admin Total          => YES
         * Coquette Super Admin => YES
         * Coquette User        => NO
         */
        $data['can_view_products'] =
            $this->can_view_products_section();


        $this->load->view(
            'coquette_hub/statistics',
            $data
        );

        error_log(
            '[CQSTEP 99] VIEW TERMINEE'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | COQUETTE_PRODUCTS_AUDIT_V2_ENDPOINT
    |--------------------------------------------------------------------------
    */

    public function product_audit_detail($productId = 0)
    {

        /*
         * COQUETTE_PRODUCTS_PERMISSION_AUDIT_V1
         *
         * Prevent Coquette User from obtaining
         * product audit data by calling the
         * endpoint directly.
         */
        if (
            !$this->can_view_products_section()
        ) {
            show_error(
                'Accès interdit',
                403
            );

            return;
        }


        if (!is_staff_logged_in()) {

            $this->output
                ->set_status_header(401)
                ->set_content_type(
                    'application/json',
                    'utf-8'
                )
                ->set_output(
                    json_encode([
                        'ok' => false,
                        'error' => 'Non authentifié',
                    ])
                );

            return;
        }


        $productId = (int) $productId;

        if ($productId <= 0) {

            $this->output
                ->set_status_header(400)
                ->set_content_type(
                    'application/json',
                    'utf-8'
                )
                ->set_output(
                    json_encode([
                        'ok' => false,
                        'error' => 'Produit invalide',
                    ])
                );

            return;
        }


        $limit = (int) $this->input->get(
            'limit',
            true
        );

        if ($limit <= 0) {
            $limit = 100;
        }

        $limit = min(
            250,
            max(1, $limit)
        );


        $offset = max(
            0,
            (int) $this->input->get(
                'offset',
                true
            )
        );


        $this->load->model(
            'coquette_statistics_model'
        );

        $stats =
            $this->coquette_statistics_model;


        $product =
            $stats->productAuditProduct(
                $productId
            );


        if (!$product) {

            $this->output
                ->set_status_header(404)
                ->set_content_type(
                    'application/json',
                    'utf-8'
                )
                ->set_output(
                    json_encode([
                        'ok' => false,
                        'error' => 'Produit introuvable',
                    ])
                );

            return;
        }


        $total =
            $stats->productAuditCount(
                $productId
            );


        $rows =
            $stats->productAuditRows(
                $productId,
                $limit,
                $offset
            );


        $payload = [
            'ok' => true,

            'product' => $product,

            'pagination' => [
                'total' => $total,
                'limit' => $limit,
                'offset' => $offset,
                'returned' => count($rows),
                'has_more' =>
                    ($offset + count($rows)) < $total,
            ],

            'audits' => $rows,
        ];


        $this->output
            ->set_content_type(
                'application/json',
                'utf-8'
            )
            ->set_output(
                json_encode(
                    $payload,
                    JSON_UNESCAPED_UNICODE
                    | JSON_UNESCAPED_SLASHES
                    | JSON_INVALID_UTF8_SUBSTITUTE
                )
            );
    }


    /*
    ========================================================
    COQUETTE_PRODUCTS_PERMISSION_V1

    Product statistics access:
      - Perfex Admin Total: YES
      - Coquette Super Admin: YES
      - Coquette User: NO
    ========================================================
    */

    private function can_view_products_section()
    {
        /*
         * Native Perfex administrator always has access.
         */
        if (is_admin()) {
            return true;
        }


        $staffId =
            (int) get_staff_user_id();


        if ($staffId <= 0) {
            return false;
        }


        $staff =
            $this->db
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
            $this->db
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


        return
            (string) $role->name
            ===
            'Coquette Super Admin';
    }



    /*
    ========================================================
    COQUETTE_HUB_TEAM_TODO_CONTROLLER_V1
    ========================================================
    */

    private function can_view_team_todos()
    {
        /*
         * Native Perfex Admin Total.
         */
        if (is_admin()) {
            return true;
        }


        $staffId =
            (int) get_staff_user_id();


        if ($staffId <= 0) {
            return false;
        }


        $staff =
            $this->db
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
            $this->db
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


        /*
         * Current HUB supervisor role:
         * Coquette Super Admin.
         *
         * "Supervisor" is also accepted if a dedicated
         * role with that name is created later.
         */
        return in_array(
            (string) $role->name,
            [
                'Coquette Super Admin',
                'Supervisor',
            ],
            true
        );
    }


    public function team_todo()
    {
        /*
         * Backend security.
         */
        if (
            !$this->can_view_team_todos()
        ) {

            access_denied(
                'Team ToDo'
            );

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | Filters
        |--------------------------------------------------------------------------
        */

        $selectedStaff =
            (int) $this->input->get(
                'staff',
                true
            );


        $status =
            trim(
                (string)
                $this->input->get(
                    'status',
                    true
                )
            );


        if (
            !in_array(
                $status,
                [
                    'all',
                    'open',
                    'finished',
                ],
                true
            )
        ) {

            $status = 'all';
        }


        $search =
            trim(
                (string)
                $this->input->get(
                    'q',
                    true
                )
            );


        /*
        |--------------------------------------------------------------------------
        | Staff
        |--------------------------------------------------------------------------
        */

        $staffTable =
            db_prefix() . 'staff';


        $rolesTable =
            db_prefix() . 'roles';


        $this->db
            ->select(
                's.staffid, '
                . 's.firstname, '
                . 's.lastname, '
                . 's.active, '
                . 's.role, '
                . 'r.name AS role_name'
            )
            ->from(
                $staffTable . ' s'
            )
            ->join(
                $rolesTable . ' r',
                'r.roleid = s.role',
                'left'
            )
            ->where(
                's.is_not_staff',
                0
            )
            ->order_by(
                's.active',
                'DESC'
            )
            ->order_by(
                's.firstname',
                'ASC'
            )
            ->order_by(
                's.lastname',
                'ASC'
            );


        $staffMembers =
            $this->db
                ->get()
                ->result_array();


        /*
         * Validate optional user filter.
         */
        if ($selectedStaff > 0) {

            $validStaff = false;


            foreach (
                $staffMembers as $member
            ) {

                if (
                    (int) $member['staffid']
                    ===
                    $selectedStaff
                ) {

                    $validStaff = true;

                    break;
                }
            }


            if (!$validStaff) {
                $selectedStaff = 0;
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Overall counters
        |--------------------------------------------------------------------------
        */

        $counts = [];


        foreach (
            $staffMembers as $member
        ) {

            $id =
                (int) $member['staffid'];


            $counts[$id] = [
                'total'    => 0,
                'open'     => 0,
                'finished' => 0,
            ];
        }


        $countRows =
            $this->db
                ->select(
                    'staffid, '
                    . 'COUNT(*) AS total, '
                    . 'SUM(finished = 0) AS open_count, '
                    . 'SUM(finished = 1) AS finished_count',
                    false
                )
                ->from(
                    db_prefix() . 'todos'
                )
                ->group_by(
                    'staffid'
                )
                ->get()
                ->result_array();


        foreach (
            $countRows as $row
        ) {

            $id =
                (int) $row['staffid'];


            if (
                !isset(
                    $counts[$id]
                )
            ) {
                continue;
            }


            $counts[$id] = [

                'total' =>
                    (int) $row['total'],

                'open' =>
                    (int) $row['open_count'],

                'finished' =>
                    (int) $row['finished_count'],
            ];
        }


        /*
        |--------------------------------------------------------------------------
        | Filtered ToDos
        |--------------------------------------------------------------------------
        */

        $this->db
            ->select(
                't.todoid, '
                . 't.description, '
                . 't.staffid, '
                . 't.dateadded, '
                . 't.finished, '
                . 't.datefinished, '
                . 't.item_order'
            )
            ->from(
                db_prefix() . 'todos t'
            )
            ->join(
                $staffTable . ' s',
                's.staffid = t.staffid',
                'left'
            );


        if ($selectedStaff > 0) {

            $this->db->where(
                't.staffid',
                $selectedStaff
            );
        }


        if ($status === 'open') {

            $this->db->where(
                't.finished',
                0
            );

        } elseif (
            $status === 'finished'
        ) {

            $this->db->where(
                't.finished',
                1
            );
        }


        if ($search !== '') {

            $this->db
                ->group_start()
                ->like(
                    't.description',
                    $search
                )
                ->or_like(
                    's.firstname',
                    $search
                )
                ->or_like(
                    's.lastname',
                    $search
                )
                ->group_end();
        }


        $this->db
            ->order_by(
                't.finished',
                'ASC'
            )
            ->order_by(
                't.item_order',
                'ASC'
            )
            ->order_by(
                't.dateadded',
                'DESC'
            );


        $todoRows =
            $this->db
                ->get()
                ->result_array();


        /*
        |--------------------------------------------------------------------------
        | Group by staff
        |--------------------------------------------------------------------------
        */

        $todosByStaff = [];


        foreach (
            $staffMembers as $member
        ) {

            $todosByStaff[
                (int) $member['staffid']
            ] = [];
        }


        foreach (
            $todoRows as $todo
        ) {

            $id =
                (int) $todo['staffid'];


            if (
                !isset(
                    $todosByStaff[$id]
                )
            ) {

                $todosByStaff[$id] = [];
            }


            $todosByStaff[$id][] =
                $todo;
        }


        /*
        |--------------------------------------------------------------------------
        | Team totals
        |--------------------------------------------------------------------------
        */

        $teamTotals = [
            'users'    => count(
                $staffMembers
            ),
            'total'    => 0,
            'open'     => 0,
            'finished' => 0,
        ];


        foreach (
            $counts as $counter
        ) {

            $teamTotals['total'] +=
                (int) $counter['total'];

            $teamTotals['open'] +=
                (int) $counter['open'];

            $teamTotals['finished'] +=
                (int) $counter['finished'];
        }


        /*
        |--------------------------------------------------------------------------
        | View
        |--------------------------------------------------------------------------
        */

        $data = [

            'title' =>
                'Team ToDo',

            'staff_members' =>
                $staffMembers,

            'todos_by_staff' =>
                $todosByStaff,

            'todo_counts' =>
                $counts,

            'team_totals' =>
                $teamTotals,

            'selected_staff' =>
                $selectedStaff,

            'todo_status' =>
                $status,

            'todo_search' =>
                $search,
        ];


        $this->load->view(
            'coquette_hub/team_todo',
            $data
        );
    }


}
