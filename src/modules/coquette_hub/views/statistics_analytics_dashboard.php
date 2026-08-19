<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| COQUETTE_DASHBOARD_FULL_ANALYTICS_V1_VIEW
|--------------------------------------------------------------------------
| Version Dashboard de Statistics > Analytics.
|--------------------------------------------------------------------------
*/

$analytics = isset($analytics) && is_array($analytics)
    ? $analytics
    : [];

$analytics += [
    'sources'       => [],
    'landing_pages' => [],
    'pages'         => [],
    'events'        => [],
    'devices'       => [],
    'audience'      => [],
    'geo'           => [],
    'ecommerce'     => [],
];

$analyticsBlocks = [

    [
        'title' =>
            'Acquisition — sources de trafic',

        'help' =>
            'Montre d’où viennent les visiteurs : Google, '
            . 'Instagram, Facebook, accès direct, publicité, etc. '
            . 'Cette section permet de juger les campagnes marketing.',

        'rows' =>
            $analytics['sources'],

        'visual_type' =>
            'donut',

        'label_field' =>
            'source_medium',

        'value_field' =>
            'sessions',

        'center_label' =>
            'sessions',

        'columns' => [
            'source_medium' => 'Source / Medium',
            'active_users'  => 'Utilisateurs',
            'sessions'      => 'Sessions',
            'page_views'    => 'Pages vues',
        ],
    ],

    [
        'title' =>
            'Landing pages — pages d’entrée',

        'help' =>
            'La landing page est la première page visitée. '
            . 'Elle permet de voir quelles pages attirent le trafic.',

        'rows' =>
            $analytics['landing_pages'],

        'visual_type' =>
            'bars',

        'label_field' =>
            'landing_page',

        'value_field' =>
            'sessions',

        'limit' =>
            6,

        'columns' => [
            'landing_page' => 'Landing page',
            'active_users' => 'Utilisateurs',
            'sessions'     => 'Sessions',
            'page_views'   => 'Pages vues',
        ],
    ],

    [
        'title' =>
            'Engagement — pages les plus visitées',

        'help' =>
            'Affiche les pages les plus consultées afin '
            . 'd’identifier les catégories, promotions et recherches '
            . 'qui intéressent le plus les visiteurs.',

        'rows' =>
            $analytics['pages'],

        'visual_type' =>
            'bars',

        'label_field' =>
            'page_path',

        'value_field' =>
            'page_views',

        'limit' =>
            6,

        'columns' => [
            'page_path'    => 'Page',
            'active_users' => 'Utilisateurs',
            'page_views'   => 'Pages vues',
        ],
    ],

    [
        'title' =>
            'Événements GA4',

        'help' =>
            'Actions réalisées par les visiteurs : page_view, '
            . 'search, view_item, add_to_cart, begin_checkout, etc.',

        'rows' =>
            $analytics['events'],

        'visual_type' =>
            'bars',

        'label_field' =>
            'event_name',

        'value_field' =>
            'event_count',

        'limit' =>
            6,

        'columns' => [
            'event_name'  => 'Événement',
            'event_count' => 'Nombre',
        ],
    ],

    [
        'title' =>
            'Appareils',

        'help' =>
            'Répartition du trafic entre mobile, desktop et tablette.',

        'rows' =>
            $analytics['devices'],

        'visual_type' =>
            'donut',

        'label_field' =>
            'device_category',

        'value_field' =>
            'sessions',

        'center_label' =>
            'sessions',

        'columns' => [
            'device_category' => 'Appareil',
            'active_users'    => 'Utilisateurs',
            'sessions'        => 'Sessions',
            'page_views'      => 'Pages vues',
        ],
    ],

    [
        'title' =>
            'Audience — nouveaux vs anciens',

        'help' =>
            'Compare les nouveaux visiteurs aux utilisateurs '
            . 'qui reviennent sur Coquette.tn.',

        'rows' =>
            $analytics['audience'],

        'visual_type' =>
            'donut',

        'label_field' =>
            'audience_type',

        'value_field' =>
            'sessions',

        'center_label' =>
            'sessions',

        'columns' => [
            'audience_type' => 'Type',
            'active_users'  => 'Utilisateurs',
            'sessions'      => 'Sessions',
        ],
    ],

    [
        'title' =>
            'Géographie',

        'help' =>
            'Affiche les principales zones géographiques '
            . 'd’origine du trafic.',

        'rows' =>
            $analytics['geo'],

        'visual_type' =>
            'bars',

        'label_field' =>
            'city',

        'value_field' =>
            'sessions',

        'limit' =>
            6,

        'columns' => [
            'country'      => 'Pays',
            'city'         => 'Ville',
            'active_users' => 'Utilisateurs',
            'sessions'     => 'Sessions',
        ],
    ],
];

$sessions = (float) (
    $ga_summary['sessions'] ?? 0
);

$pageViews = (float) (
    $ga_summary['page_views'] ?? 0
);

$engaged = (float) (
    $ga_summary['engaged_sessions'] ?? 0
);

$engagementRate = (float) (
    $ga_summary['engagement_rate'] ?? 0
);
?>


<div class="cqstats-page cq-dashboard-analytics">

    <div class="cqstats-section-title">

        <div>

            <span class="cqstats-section-label">
                GOOGLE ANALYTICS 4
            </span>

            <h2>Analytics</h2>

            <p>
                Trafic, acquisition, comportement,
                appareils et audience Coquette.tn.
            </p>

        </div>

        <a
            href="<?= admin_url(
                'coquette_hub/statistics?section=analytics'
            ); ?>"
            class="cqhub-all-stats"
        >
            Ouvrir Analytics complet

            <i class="fa-solid fa-arrow-right"></i>
        </a>

    </div>


    <div class="cqstats-kpi-grid cqstats-kpi-grid-4">

        <div class="cqstats-kpi">

            <span>Sessions</span>

            <strong>
                <?= number_format(
                    $sessions,
                    0,
                    ',',
                    ' '
                ); ?>
            </strong>

        </div>


        <div class="cqstats-kpi">

            <span>Pages vues</span>

            <strong>
                <?= number_format(
                    $pageViews,
                    0,
                    ',',
                    ' '
                ); ?>
            </strong>

        </div>


        <div class="cqstats-kpi">

            <span>Sessions engagées</span>

            <strong>
                <?= number_format(
                    $engaged,
                    0,
                    ',',
                    ' '
                ); ?>
            </strong>

        </div>


        <div class="cqstats-kpi">

            <span>Taux engagement</span>

            <strong>
                <?= number_format(
                    $engagementRate * 100,
                    1,
                    ',',
                    ' '
                ); ?>%
            </strong>

        </div>

    </div>


    <?php
    $this->load->view(
        'coquette_hub/statistics_traffic_chart',
        [
            'traffic_daily' =>
                $traffic_daily ?? [],

            'period' =>
                $period ?? 30,
        ]
    );
    ?>


    <?php foreach ($analyticsBlocks as $block): ?>

        <section
            class="
                cqstats-panel
                cqstats-table-panel
                cqstats-analytics-visual-panel
            "
        >

            <div class="cqstats-panel-head">

                <div>

                    <span class="cqstats-section-label">
                        GA4
                    </span>

                    <h2>
                        <?= html_escape(
                            $block['title']
                        ); ?>
                    </h2>

                </div>

            </div>


            <?php if (!empty($block['help'])): ?>

                <details class="cqav-help">

                    <summary>
                        <i class="fa-solid fa-circle-info"></i>
                        À quoi ça sert ?
                    </summary>

                    <div>
                        <?= html_escape(
                            $block['help']
                        ); ?>
                    </div>

                </details>

            <?php endif; ?>


            <?php if (!$block['rows']): ?>

                <div class="cqstats-empty">
                    Aucune donnée disponible.
                </div>

            <?php else: ?>


                <div class="cqav-visual">

                    <?php
                    $this->load->view(
                        'coquette_hub/statistics_analytics_visual',
                        [
                            'visual_type' =>
                                $block['visual_type'],

                            'rows' =>
                                $block['rows'],

                            'label_field' =>
                                $block['label_field'],

                            'value_field' =>
                                $block['value_field'],

                            'center_label' =>
                                $block['center_label'] ?? '',

                            'limit' =>
                                $block['limit'] ?? 6,
                        ]
                    );
                    ?>

                </div>


                <div class="cqstats-table-wrap">

                    <table class="cqstats-table">

                        <thead>

                        <tr>

                            <?php foreach (
                                $block['columns']
                                as $label
                            ): ?>

                                <th>
                                    <?= html_escape($label); ?>
                                </th>

                            <?php endforeach; ?>

                        </tr>

                        </thead>


                        <tbody>

                        <?php foreach (
                            $block['rows']
                            as $row
                        ): ?>

                            <tr>

                            <?php foreach (
                                $block['columns']
                                as $field => $label
                            ): ?>

                                <td>
                                    <?= html_escape(
                                        $row[$field] ?? '—'
                                    ); ?>
                                </td>

                            <?php endforeach; ?>

                            </tr>

                        <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>

            <?php endif; ?>

        </section>

    <?php endforeach; ?>


    <section class="cqstats-panel cqstats-table-panel">

        <div class="cqstats-panel-head">

            <div>

                <span class="cqstats-section-label">
                    GA4
                </span>

                <h2>E-commerce GA4</h2>

            </div>

        </div>



        <?php

        $ecommerceFunnelLabels = [
            'view_item' =>
                'Produits consultés',

            'add_to_cart' =>
                'Ajouts au panier',

            'view_cart' =>
                'Paniers consultés',

            'begin_checkout' =>
                'Débuts de checkout',

            'purchase' =>
                'Achats',
        ];

        $ecommerceFunnelCounts = array_fill_keys(
            array_keys($ecommerceFunnelLabels),
            0
        );

        foreach (
            ($analytics['ecommerce_funnel'] ?? [])
            as $eventRow
        ) {
            $eventName =
                $eventRow['event_name'] ?? '';

            if (
                array_key_exists(
                    $eventName,
                    $ecommerceFunnelCounts
                )
            ) {
                $ecommerceFunnelCounts[$eventName] =
                    (int) (
                        $eventRow['event_count']
                        ?? 0
                    );
            }
        }

        ?>

        <div class="cqstats-table-wrap">

            <table class="cqstats-table">

                <thead>
                    <tr>
                        <th>Étape</th>
                        <th>Événement GA4</th>
                        <th>30 derniers jours</th>
                    </tr>
                </thead>

                <tbody>

                <?php foreach (
                    $ecommerceFunnelLabels
                    as $eventName => $label
                ): ?>

                    <tr>

                        <td>
                            <?= html_escape($label); ?>
                        </td>

                        <td>
                            <code>
                                <?= html_escape($eventName); ?>
                            </code>
                        </td>

                        <td>
                            <strong>
                                <?= number_format(
                                    $ecommerceFunnelCounts[
                                        $eventName
                                    ],
                                    0,
                                    ',',
                                    ' '
                                ); ?>
                            </strong>
                        </td>

                    </tr>

                <?php endforeach; ?>

                </tbody>

            </table>

        </div>

        <?php if (!$analytics['ecommerce']): ?>

            <div class="cqstats-empty">
                Aucun achat ou revenu GA4 disponible pour le moment.
            </div>

        <?php else: ?>

            <div class="cqstats-table-wrap">

                <table class="cqstats-table">

                    <tbody>

                    <?php foreach (
                        $analytics['ecommerce']
                        as $key => $value
                    ): ?>

                        <tr>
                            <th>
                                <?= html_escape($key); ?>
                            </th>

                            <td>
                                <?= html_escape($value); ?>
                            </td>
                        </tr>

                    <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        <?php endif; ?>

    </section>

</div>
