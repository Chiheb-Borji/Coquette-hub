<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php init_head(); ?>

<div id="wrapper">

    <div class="content coquette-dashboard">

        <div class="coquette-dashboard-header">

            <div>
                <div class="coquette-eyebrow">
                    COQUETTE.TN HUB
                </div>

                <h1>
                    <?= _l('cqhub_hello'); ?> <?= html_escape($staff->firstname ?? ''); ?> 👋
                </h1>

                <p>
                    <?= _l('cqhub_dashboard_intro'); ?>
                </p>
            </div>

            <div class="coquette-user-role">
                <?= html_escape($hub_role); ?>
            </div>

        </div>


        <div class="coquette-stats-grid">

            <a
                class="coquette-stat-card"
                href="<?= admin_url('tasks'); ?>"
            >
                <div class="coquette-stat-icon">
                    <i class="fa-solid fa-list-check"></i>
                </div>

                <div>
                    <span><?= _l('cqhub_tasks'); ?></span>
                    <strong><?= (int) $tasks_count; ?></strong>
                </div>
            </a>


            <a
                class="coquette-stat-card"
                href="<?= admin_url('tickets'); ?>"
            >
                <div class="coquette-stat-icon">
                    <i class="fa-solid fa-ticket"></i>
                </div>

                <div>
                    <span><?= _l('cqhub_tickets'); ?></span>
                    <strong><?= (int) $tickets_count; ?></strong>
                </div>
            </a>



            <a
                class="coquette-stat-card"
                href="<?= admin_url('clients'); ?>"
            >
                <div class="coquette-stat-icon">
                    <i class="fa-solid fa-users"></i>
                </div>

                <div>
                    <span><?= _l('cqhub_clients'); ?></span>
                    <strong><?= (int) $clients_count; ?></strong>
                </div>
            </a>

        </div>


        <div class="coquette-section">

            <div class="coquette-section-heading">

                <div>
                    <h2><?= _l('cqhub_tasks'); ?></h2>
                    <p><?= _l('cqhub_current_workflow'); ?></p>
                </div>

                <a href="<?= admin_url('tasks'); ?>">
                    <?= _l('cqhub_view_all_tasks'); ?>
                    <i class="fa-solid fa-arrow-right"></i>
                </a>

            </div>


            <div class="coquette-kanban-summary">

                <div class="coquette-kanban-column">
                    <span class="coquette-kanban-label">
                        <?= _l('cqhub_todo'); ?>
                    </span>

                    <strong><?= (int) $kanban['todo']; ?></strong>
                </div>


                <div class="coquette-kanban-column">
                    <span class="coquette-kanban-label">
                        <?= _l('cqhub_in_progress'); ?>
                    </span>

                    <strong><?= (int) $kanban['progress']; ?></strong>
                </div>


                <div class="coquette-kanban-column">
                    <span class="coquette-kanban-label">
                        Test
                    </span>

                    <strong><?= (int) $kanban['test']; ?></strong>
                </div>


                <div class="coquette-kanban-column">
                    <span class="coquette-kanban-label">
                        Terminé
                    </span>

                    <strong><?= (int) $kanban['done']; ?></strong>
                </div>

            </div>

        </div>



        <!--
        ======================================================
        COQUETTE_MARKETING_DASHBOARD_DISPLAY_V1
        Current active marketing plan
        ======================================================
        -->

        <?php if (
            !empty($marketing_widget)
        ) { ?>

            <div
                class="coquette-marketing-dashboard"
                style="margin-top:24px;"
            >

                <?= $marketing_widget; ?>

            </div>

        <?php } ?>



        <div class="coquette-dashboard-grid">



            <!-- COQUETTE_DASHBOARD_FULL_ANALYTICS_V1_RENDER -->

<section class="coquette-dashboard-overview">

    <?php

    $this->load->view(
        'coquette_hub/statistics_analytics_dashboard',
        [
            'period' =>
                $period ?? 30,

            'ga_summary' =>
                $ga_summary ?? [],

            'traffic_daily' =>
                $traffic_daily ?? [],

            'analytics' =>
                $analytics ?? [],
        ]
    );

    ?>

</section>

        </div>

    </div>

</div>

<?php init_tail(); ?>
