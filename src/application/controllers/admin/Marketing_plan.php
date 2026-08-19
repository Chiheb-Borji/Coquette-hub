<?php

defined('BASEPATH')
    or exit('No direct script access allowed');


class Marketing_plan extends AdminController
{
    public function __construct()
    {
        parent::__construct();
    }


    private function plansTable()
    {
        return db_prefix()
            . 'marketing_plans';
    }


    private function objectivesTable()
    {
        return db_prefix()
            . 'marketing_objectives';
    }


    private function phasesTable()
    {
        return db_prefix()
            . 'marketing_phases';
    }


    private function sectionsTable()
    {
        return db_prefix()
            . 'marketing_plan_sections';
    }


    private function itemsTable()
    {
        return db_prefix()
            . 'marketing_plan_items';
    }


    private function currentPlan()
    {
        $requested =
            (int)$this->input->get('plan_id');


        if ($requested > 0) {

            $plan = $this->db
                ->where('id', $requested)
                ->get($this->plansTable())
                ->row_array();

            if ($plan) {
                return $plan;
            }
        }


        $plan = $this->db
            ->where('status', 'active')
            ->order_by('id', 'DESC')
            ->get($this->plansTable())
            ->row_array();


        if ($plan) {
            return $plan;
        }


        return $this->db
            ->order_by('id', 'DESC')
            ->get($this->plansTable())
            ->row_array();
    }


    private function redirectWorkspace($planId = 0)
    {
        $url = 'marketing_plan';

        if ($planId > 0) {
            $url .= '?plan_id=' . $planId;
        }

        redirect(admin_url($url));
    }


    private function validWorkingSlot(
        $date,
        $start,
        $end
    ) {

        try {

            $day = (int)(
                new DateTime($date)
            )->format('N');

        } catch (Exception $e) {
            return false;
        }


        /*
         * Sunday closed.
         */
        if ($day === 7) {
            return false;
        }


        if (
            !preg_match(
                '/^\d{2}:\d{2}$/',
                $start
            )
            ||
            !preg_match(
                '/^\d{2}:\d{2}$/',
                $end
            )
        ) {
            return false;
        }


        $opening = '08:30';

        $closing =
            $day === 6
            ? '14:00'
            : '17:30';


        if ($start < $opening) {
            return false;
        }


        if ($end > $closing) {
            return false;
        }


        if ($end <= $start) {
            return false;
        }


        return true;
    }


    /*
     * ==================================================
     * WORKSPACE
     * ==================================================
     */

    public function index()
    {
        $plan = $this->currentPlan();

        $data['title'] =
            'Plan Marketing';

        $data['plans'] = $this->db
            ->order_by('start_date', 'DESC')
            ->order_by('id', 'DESC')
            ->get($this->plansTable())
            ->result_array();

        $data['plan'] = $plan;

        $data['objectives'] = [];
        $data['phases'] = [];
        $data['items'] = [];


        if ($plan) {

            $planId = (int)$plan['id'];


            $data['objectives'] =
                $this->db
                    ->where(
                        'plan_id',
                        $planId
                    )
                    ->order_by(
                        'position',
                        'ASC'
                    )
                    ->get(
                        $this->objectivesTable()
                    )
                    ->result_array();


            $data['phases'] =
                $this->db
                    ->where(
                        'plan_id',
                        $planId
                    )
                    ->order_by(
                        'position',
                        'ASC'
                    )
                    ->get(
                        $this->phasesTable()
                    )
                    ->result_array();


            $data['items'] =
                $this->db
                    ->select(
                        $this->itemsTable()
                        . '.*, '
                        . $this->sectionsTable()
                        . '.name AS section_name'
                    )
                    ->from(
                        $this->itemsTable()
                    )
                    ->join(
                        $this->sectionsTable(),
                        $this->sectionsTable()
                        . '.id='
                        . $this->itemsTable()
                        . '.section_id',
                        'left'
                    )
                    ->where(
                        $this->itemsTable()
                        . '.plan_id',
                        $planId
                    )
                    ->order_by(
                        'plan_date',
                        'DESC'
                    )
                    ->order_by(
                        'start_time',
                        'ASC'
                    )
                    ->limit(500)
                    ->get()
                    ->result_array();
        }


        $data['sections'] =
            $this->db
                ->order_by(
                    'position',
                    'ASC'
                )
                ->order_by(
                    'id',
                    'ASC'
                )
                ->get(
                    $this->sectionsTable()
                )
                ->result_array();


        $data['edit_plan'] =
            $this->editRecord(
                $this->plansTable(),
                'edit_plan'
            );


        $data['edit_objective'] =
            $this->editRecord(
                $this->objectivesTable(),
                'edit_objective'
            );


        $data['edit_phase'] =
            $this->editRecord(
                $this->phasesTable(),
                'edit_phase'
            );


        $data['edit_section'] =
            $this->editRecord(
                $this->sectionsTable(),
                'edit_section'
            );


        $data['edit_item'] =
            $this->editRecord(
                $this->itemsTable(),
                'edit_item'
            );


        $this->load->view(
            'admin/marketing_plan/workspace',
            $data
        );
    }


    private function editRecord(
        $table,
        $queryName
    ) {

        $id = (int)
            $this->input->get($queryName);


        if ($id <= 0) {
            return null;
        }


        return $this->db
            ->where('id', $id)
            ->get($table)
            ->row_array();
    }


    /*
     * ==================================================
     * PLAN CRUD
     * ==================================================
     */

    public function save_plan()
    {
        $id = (int)
            $this->input->post('id');


        $title = trim(
            (string)
            $this->input->post(
                'title',
                true
            )
        );


        $start = trim(
            (string)
            $this->input->post(
                'start_date',
                true
            )
        );


        $end = trim(
            (string)
            $this->input->post(
                'end_date',
                true
            )
        );


        if (
            $title === ''
            || $start === ''
            || $end === ''
            || $end < $start
        ) {

            set_alert(
                'danger',
                'Nom et dates du plan invalides.'
            );

            $this->redirectWorkspace();
        }


        $data = [

            'title' => $title,

            'description' => trim(
                (string)
                $this->input->post(
                    'description',
                    true
                )
            ),

            'start_date' => $start,

            'end_date' => $end,

            'updated_at' =>
                date('Y-m-d H:i:s'),
        ];


        if ($id > 0) {

            $this->db
                ->where('id', $id)
                ->update(
                    $this->plansTable(),
                    $data
                );

            $planId = $id;

            set_alert(
                'success',
                'Plan marketing modifié.'
            );

        } else {

            $data['status'] =
                'draft';

            $data['created_at'] =
                date('Y-m-d H:i:s');

            $this->db->insert(
                $this->plansTable(),
                $data
            );

            $planId =
                (int)$this->db->insert_id();

            set_alert(
                'success',
                'Nouveau plan marketing créé.'
            );
        }


        $this->redirectWorkspace(
            $planId
        );
    }


    public function activate_plan($id)
    {
        $id = (int)$id;


        $exists = $this->db
            ->where('id', $id)
            ->count_all_results(
                $this->plansTable()
            );


        if (!$exists) {
            $this->redirectWorkspace();
        }


        $this->db->update(
            $this->plansTable(),
            ['status' => 'archived']
        );


        $this->db
            ->where('id', $id)
            ->update(
                $this->plansTable(),
                [
                    'status' => 'active',
                    'updated_at' =>
                        date('Y-m-d H:i:s'),
                ]
            );


        set_alert(
            'success',
            'Plan marketing activé.'
        );


        $this->redirectWorkspace($id);
    }


    public function delete_plan($id)
    {
        $id = (int)$id;


        $linked =
            $this->db
                ->where(
                    'plan_id',
                    $id
                )
                ->count_all_results(
                    $this->itemsTable()
                );


        $linked +=
            $this->db
                ->where(
                    'plan_id',
                    $id
                )
                ->count_all_results(
                    $this->objectivesTable()
                );


        $linked +=
            $this->db
                ->where(
                    'plan_id',
                    $id
                )
                ->count_all_results(
                    $this->phasesTable()
                );


        if ($linked > 0) {

            set_alert(
                'danger',
                'Ce plan contient déjà des données. '
                . 'Archivez-le au lieu de le supprimer.'
            );

        } else {

            $this->db
                ->where('id', $id)
                ->delete(
                    $this->plansTable()
                );

            set_alert(
                'success',
                'Plan supprimé.'
            );
        }


        $this->redirectWorkspace();
    }


    /*
     * ==================================================
     * OBJECTIVES CRUD
     * ==================================================
     */

    public function save_objective()
    {
        $id = (int)
            $this->input->post('id');

        $planId = (int)
            $this->input->post('plan_id');

        $title = trim(
            (string)
            $this->input->post(
                'title',
                true
            )
        );


        if (
            $planId <= 0
            || $title === ''
        ) {

            set_alert(
                'danger',
                'Objectif invalide.'
            );

            $this->redirectWorkspace(
                $planId
            );
        }


        $target =
            $this->input->post(
                'target_value'
            );

        $current =
            $this->input->post(
                'current_value'
            );


        $data = [

            'plan_id' => $planId,

            'title' => $title,

            'description' => trim(
                (string)
                $this->input->post(
                    'description',
                    true
                )
            ),

            'target_value' =>
                $target !== ''
                ? (float)$target
                : null,

            'current_value' =>
                $current !== ''
                ? (float)$current
                : 0,

            'unit' => trim(
                (string)
                $this->input->post(
                    'unit',
                    true
                )
            ),

            'position' => (int)
                $this->input->post(
                    'position'
                ),

            'active' =>
                $this->input->post(
                    'active'
                )
                ? 1
                : 0,

            'updated_at' =>
                date('Y-m-d H:i:s'),
        ];


        if ($id > 0) {

            $this->db
                ->where('id', $id)
                ->update(
                    $this->objectivesTable(),
                    $data
                );

        } else {

            $data['created_at'] =
                date('Y-m-d H:i:s');

            $this->db->insert(
                $this->objectivesTable(),
                $data
            );
        }


        set_alert(
            'success',
            'Objectif enregistré.'
        );


        $this->redirectWorkspace(
            $planId
        );
    }


    public function delete_objective($id)
    {
        $id = (int)$id;


        $row = $this->db
            ->where('id', $id)
            ->get(
                $this->objectivesTable()
            )
            ->row_array();


        if ($row) {

            $this->db
                ->where('id', $id)
                ->delete(
                    $this->objectivesTable()
                );

            set_alert(
                'success',
                'Objectif supprimé.'
            );

            $this->redirectWorkspace(
                (int)$row['plan_id']
            );
        }


        $this->redirectWorkspace();
    }


    /*
     * ==================================================
     * PHASE CRUD
     * ==================================================
     */

    public function save_phase()
    {
        $id = (int)
            $this->input->post('id');

        $planId = (int)
            $this->input->post('plan_id');

        $title = trim(
            (string)
            $this->input->post(
                'title',
                true
            )
        );

        $start = trim(
            (string)
            $this->input->post(
                'start_date',
                true
            )
        );

        $end = trim(
            (string)
            $this->input->post(
                'end_date',
                true
            )
        );


        if (
            $planId <= 0
            || $title === ''
            || $start === ''
            || $end === ''
            || $end < $start
        ) {

            set_alert(
                'danger',
                'Phase marketing invalide.'
            );

            $this->redirectWorkspace(
                $planId
            );
        }


        $status = trim(
            (string)
            $this->input->post(
                'status',
                true
            )
        );


        if (!in_array(
            $status,
            [
                'todo',
                'progress',
                'done'
            ],
            true
        )) {
            $status = 'todo';
        }


        $data = [

            'plan_id' =>
                $planId,

            'title' =>
                $title,

            'focus' => trim(
                (string)
                $this->input->post(
                    'focus',
                    true
                )
            ),

            'start_date' =>
                $start,

            'end_date' =>
                $end,

            'status' =>
                $status,

            'position' =>
                (int)$this->input->post(
                    'position'
                ),

            'updated_at' =>
                date('Y-m-d H:i:s'),
        ];


        if ($id > 0) {

            $this->db
                ->where('id', $id)
                ->update(
                    $this->phasesTable(),
                    $data
                );

        } else {

            $data['created_at'] =
                date('Y-m-d H:i:s');

            $this->db->insert(
                $this->phasesTable(),
                $data
            );
        }


        set_alert(
            'success',
            'Phase enregistrée.'
        );


        $this->redirectWorkspace(
            $planId
        );
    }


    public function delete_phase($id)
    {
        $id = (int)$id;


        $used = $this->db
            ->where(
                'phase_id',
                $id
            )
            ->count_all_results(
                $this->itemsTable()
            );


        $row = $this->db
            ->where('id', $id)
            ->get(
                $this->phasesTable()
            )
            ->row_array();


        if (!$row) {
            $this->redirectWorkspace();
        }


        if ($used > 0) {

            set_alert(
                'danger',
                'Cette phase contient déjà des tâches.'
            );

        } else {

            $this->db
                ->where('id', $id)
                ->delete(
                    $this->phasesTable()
                );

            set_alert(
                'success',
                'Phase supprimée.'
            );
        }


        $this->redirectWorkspace(
            (int)$row['plan_id']
        );
    }


    /*
     * ==================================================
     * SECTIONS CRUD
     * ==================================================
     */

    public function save_section()
    {
        $id = (int)
            $this->input->post('id');


        $name = trim(
            (string)
            $this->input->post(
                'name',
                true
            )
        );


        if ($name === '') {

            set_alert(
                'danger',
                'Le nom de section est obligatoire.'
            );

            $this->redirectWorkspace();
        }


        $target = (int)
            $this->input->post(
                'daily_target'
            );


        if ($target < 0) {
            $target = 0;
        }

        if ($target > 50) {
            $target = 50;
        }


        $data = [

            'name' => $name,

            'icon' => trim(
                (string)
                $this->input->post(
                    'icon',
                    true
                )
            ) ?: 'fa fa-folder',

            'daily_target' =>
                $target,

            'position' =>
                (int)$this->input->post(
                    'position'
                ),

            'active' =>
                $this->input->post(
                    'active'
                )
                ? 1
                : 0,

            'updated_at' =>
                date('Y-m-d H:i:s'),
        ];


        if ($id > 0) {

            $this->db
                ->where('id', $id)
                ->update(
                    $this->sectionsTable(),
                    $data
                );

        } else {

            $data['created_at'] =
                date('Y-m-d H:i:s');

            $this->db->insert(
                $this->sectionsTable(),
                $data
            );
        }


        set_alert(
            'success',
            'Section enregistrée.'
        );


        $this->redirectWorkspace();
    }


    public function toggle_section($id)
    {
        $id = (int)$id;


        $section = $this->db
            ->where('id', $id)
            ->get(
                $this->sectionsTable()
            )
            ->row_array();


        if ($section) {

            $active =
                (int)$section['active']
                ? 0
                : 1;


            $this->db
                ->where('id', $id)
                ->update(
                    $this->sectionsTable(),
                    [
                        'active' =>
                            $active,

                        'updated_at' =>
                            date(
                                'Y-m-d H:i:s'
                            ),
                    ]
                );


            set_alert(
                'success',
                $active
                ? 'Section activée.'
                : 'Section désactivée.'
            );
        }


        $this->redirectWorkspace();
    }


    public function delete_section($id)
    {
        $id = (int)$id;


        $used = $this->db
            ->where(
                'section_id',
                $id
            )
            ->count_all_results(
                $this->itemsTable()
            );


        if ($used > 0) {

            set_alert(
                'danger',
                'Cette section est déjà utilisée. '
                . 'Désactivez-la au lieu de la supprimer.'
            );

        } else {

            $this->db
                ->where('id', $id)
                ->delete(
                    $this->sectionsTable()
                );

            set_alert(
                'success',
                'Section supprimée.'
            );
        }


        $this->redirectWorkspace();
    }


    /*
     * ==================================================
     * ITEMS / TASKS CRUD
     * ==================================================
     */

    public function save_item()
    {
        $id = (int)
            $this->input->post('id');

        $planId = (int)
            $this->input->post('plan_id');

        $phaseId = (int)
            $this->input->post('phase_id');

        $sectionId = (int)
            $this->input->post('section_id');


        $date = trim(
            (string)
            $this->input->post(
                'plan_date',
                true
            )
        );


        $start = trim(
            (string)
            $this->input->post(
                'start_time',
                true
            )
        );


        $end = trim(
            (string)
            $this->input->post(
                'end_time',
                true
            )
        );


        $title = trim(
            (string)
            $this->input->post(
                'title',
                true
            )
        );


        if (
            $planId <= 0
            || $sectionId <= 0
            || $title === ''
        ) {

            set_alert(
                'danger',
                'Plan, section et tâche sont obligatoires.'
            );

            $this->redirectWorkspace(
                $planId
            );
        }


        $plan = $this->db
            ->where('id', $planId)
            ->get(
                $this->plansTable()
            )
            ->row_array();


        if (
            !$plan
            || $date < $plan['start_date']
            || $date > $plan['end_date']
        ) {

            set_alert(
                'danger',
                'La date doit être comprise '
                . 'dans la période du plan.'
            );

            $this->redirectWorkspace(
                $planId
            );
        }


        if (!$this->validWorkingSlot(
            $date,
            $start,
            $end
        )) {

            set_alert(
                'danger',
                'Horaire invalide. '
                . 'Lun-Ven : 08:30-17:30, '
                . 'Samedi : 08:30-14:00, '
                . 'Dimanche fermé.'
            );

            $this->redirectWorkspace(
                $planId
            );
        }


        $type = trim(
            (string)
            $this->input->post(
                'item_type',
                true
            )
        );


        if (!in_array(
            $type,
            ['content','action'],
            true
        )) {
            $type = 'content';
        }


        $priority = trim(
            (string)
            $this->input->post(
                'priority',
                true
            )
        );


        if (!in_array(
            $priority,
            ['normal','high','urgent'],
            true
        )) {
            $priority = 'normal';
        }


        $status = trim(
            (string)
            $this->input->post(
                'status',
                true
            )
        );


        if (!in_array(
            $status,
            [
                'todo',
                'progress',
                'waiting',
                'done'
            ],
            true
        )) {
            $status = 'todo';
        }


        $data = [

            'plan_id' =>
                $planId,

            'phase_id' =>
                $phaseId > 0
                ? $phaseId
                : null,

            'section_id' =>
                $sectionId,

            'item_type' =>
                $type,

            'plan_date' =>
                $date,

            'start_time' =>
                $start . ':00',

            'end_time' =>
                $end . ':00',

            'title' =>
                $title,

            'responsible' => trim(
                (string)
                $this->input->post(
                    'responsible',
                    true
                )
            ),

            'priority' =>
                $priority,

            'status' =>
                $status,

            'notes' => trim(
                (string)
                $this->input->post(
                    'notes',
                    true
                )
            ),

            'updated_at' =>
                date('Y-m-d H:i:s'),
        ];


        if ($id > 0) {

            $this->db
                ->where('id', $id)
                ->update(
                    $this->itemsTable(),
                    $data
                );

            set_alert(
                'success',
                'Tâche marketing modifiée.'
            );

        } else {

            $data['created_at'] =
                date('Y-m-d H:i:s');

            $this->db->insert(
                $this->itemsTable(),
                $data
            );

            set_alert(
                'success',
                'Tâche ajoutée au planning.'
            );
        }


        $this->redirectWorkspace(
            $planId
        );
    }


    public function delete_item($id)
    {
        $id = (int)$id;


        $row = $this->db
            ->where('id', $id)
            ->get(
                $this->itemsTable()
            )
            ->row_array();


        if ($row) {

            $this->db
                ->where('id', $id)
                ->delete(
                    $this->itemsTable()
                );

            set_alert(
                'success',
                'Tâche supprimée.'
            );

            $this->redirectWorkspace(
                (int)$row['plan_id']
            );
        }


        $this->redirectWorkspace();
    }
}
