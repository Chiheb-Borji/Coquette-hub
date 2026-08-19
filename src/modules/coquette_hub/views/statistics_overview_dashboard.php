<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| COQUETTE_DASHBOARD_ANALYTICS_OVERVIEW_V12_PARTIAL
|--------------------------------------------------------------------------
| Statistics > Vue d'ensemble uniquement.
|--------------------------------------------------------------------------
*/

if (!function_exists('cq_money')) {
    function cq_money($value)
    {
        return number_format(
            (float) $value,
            3,
            ',',
            ' '
        );
    }
}

if (!function_exists('cq_number')) {
    function cq_number($value)
    {
        return number_format(
            (float) $value,
            0,
            ',',
            ' '
        );
    }
}

if (!function_exists('cq_date_short')) {
    function cq_date_short($value)
    {
        if (!$value) {
            return '—';
        }

        $ts = strtotime($value);

        return $ts
            ? date('d/m/Y H:i', $ts)
            : $value;
    }
}
?>

<?php
        $orders = (float) ($sales_summary['orders_count'] ?? 0);
        $revenue = (float) ($sales_summary['revenue'] ?? 0);
        $sessions = (float) ($ga_summary['sessions'] ?? 0);
        $views = (float) ($ga_summary['page_views'] ?? 0);

        $conversion = $sessions > 0
            ? ($orders / $sessions) * 100
            : 0;
        ?>

        <div class="cqstats-kpi-grid">

            <div class="cqstats-kpi">
                <span>Commandes</span>
                <strong><?= cq_number($orders); ?></strong>
                <small><?= (int) $period; ?> jours</small>
            </div>

            <div class="cqstats-kpi">
                <span>Chiffre d’affaires</span>
                <strong>
                    <?= cq_money($revenue); ?>
                    <em>TND</em>
                </strong>
                <small>
                    Panier moyen :
                    <?= $orders > 0
                        ? cq_money($revenue / $orders)
                        : '0,000'; ?>
                    TND
                </small>
            </div>

            <div class="cqstats-kpi">
                <span>Sessions</span>
                <strong><?= cq_number($sessions); ?></strong>
                <small>
                    <?= cq_number($views); ?>
                    pages vues
                </small>
            </div>

            <div class="cqstats-kpi">
                <span>Conversion approx.</span>
                <strong>
                    <?= number_format(
                        $conversion,
                        2,
                        ',',
                        ' '
                    ); ?>%
                </strong>
                <small>Commandes ÷ sessions</small>
            </div>

            <div class="cqstats-kpi">
                <span>Produits actifs</span>
                <strong>
                    <?= cq_number(
                        $product_summary['active_products'] ?? 0
                    ); ?>
                </strong>
                <small>
                    <?= cq_number(
                        $product_summary['available_products'] ?? 0
                    ); ?>
                    disponibles
                </small>
            </div>

            <div class="cqstats-kpi cqstats-kpi-alert">
                <span>Ruptures stock</span>
                <strong>
                    <?= cq_number(
                        $product_summary['out_of_stock'] ?? 0
                    ); ?>
                </strong>
                <small>
                    Produits actifs sans stock
                </small>
            </div>

        </div>


        
        <!-- COQUETTE_TRAFFIC_ANALYTICS_V1_DASHBOARD_VIEW -->

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


<div class="cqstats-two-columns">

            <section class="cqstats-panel">

                <div class="cqstats-panel-head">
                    <div>
                        <span class="cqstats-section-label">
                            PERFORMANCE
                        </span>
                        <h2>Ventes récentes</h2>
                    </div>

                    <a href="<?= admin_url(
                        'coquette_hub/statistics?section=sales&period='
                        . (int) $period
                    ); ?>">
                        Voir les ventes
                    </a>
                </div>

                <div class="cqstats-sales-bars">

                    <?php
                    $maxRevenue = 0;

                    foreach ($sales_daily as $row) {
                        $maxRevenue = max(
                            $maxRevenue,
                            (float) $row['revenue']
                        );
                    }

                    $recentSales = array_slice(
                        array_reverse($sales_daily),
                        0,
                        10
                    );
                    ?>

                    <?php foreach ($recentSales as $row): ?>

                        <?php
                        $pct = $maxRevenue > 0
                            ? ((float) $row['revenue'] / $maxRevenue) * 100
                            : 0;
                        ?>

                        <div class="cqstats-bar-row">

                            <span>
                                <?= html_escape(
                                    date(
                                        'd/m',
                                        strtotime($row['sales_date'])
                                    )
                                ); ?>
                            </span>

                            <div>
                                <i
                                    style="width:<?= number_format(
                                        $pct,
                                        2,
                                        '.',
                                        ''
                                    ); ?>%"
                                ></i>
                            </div>

                            <strong>
                                <?= cq_money($row['revenue']); ?>
                            </strong>

                        </div>

                    <?php endforeach; ?>

                </div>

            </section>


            <section class="cqstats-panel">

                <div class="cqstats-panel-head">
                    <div>
                        <span class="cqstats-section-label">
                            STOCK
                        </span>
                        <h2>Disponibilité produits</h2>
                    </div>

                    <a href="<?= admin_url(
                        'coquette_hub/statistics?section=stock'
                    ); ?>">
                        Voir ruptures
                    </a>
                </div>

                <?php
                $active = max(
                    1,
                    (float) ($product_summary['active_products'] ?? 0)
                );

                $out = (float) (
                    $product_summary['out_of_stock'] ?? 0
                );

                $outPct = ($out / $active) * 100;
                ?>

                <div class="cqstats-stock-layout">

                    <div
                        class="cqstats-donut"
                        style="
                            --cq-out:
                            <?= number_format(
                                min(100, $outPct),
                                2,
                                '.',
                                ''
                            ); ?>deg;
                        "
                    >
                        <div>
                            <strong>
                                <?= number_format(
                                    $outPct,
                                    0,
                                    ',',
                                    ' '
                                ); ?>%
                            </strong>
                            <span>rupture</span>
                        </div>
                    </div>

                    <div class="cqstats-stock-numbers">

                        <div>
                            <span>Disponibles</span>
                            <strong>
                                <?= cq_number(
                                    $product_summary[
                                        'available_products'
                                    ] ?? 0
                                ); ?>
                            </strong>
                        </div>

                        <div>
                            <span>Ruptures</span>
                            <strong>
                                <?= cq_number($out); ?>
                            </strong>
                        </div>

                        <div>
                            <span>Total actifs</span>
                            <strong>
                                <?= cq_number($active); ?>
                            </strong>
                        </div>

                    </div>

                </div>

            </section>

        </div>


        <div class="cqstats-two-columns">

            <section class="cqstats-panel">

                <div class="cqstats-panel-head">
                    <div>
                        <span class="cqstats-section-label">
                            TOP PRODUITS
                        </span>
                        <h2>Meilleures ventes</h2>
                    </div>
                </div>

                <?php if (!$top_products): ?>

                    <div class="cqstats-empty">
                        Aucune donnée disponible.
                    </div>

                <?php else: ?>

                    <div class="cqstats-ranking">

                        <?php foreach ($top_products as $i => $p): ?>

                            <div class="cqstats-rank-row">

                                <span class="cqstats-rank-number">
                                    <?= $i + 1; ?>
                                </span>

                                <div>
                                    <strong>
                                        <?= html_escape(
                                            $p['product_name']
                                        ); ?>
                                    </strong>

                                    <small>
                                        <?= cq_number(
                                            $p['quantity_sold']
                                        ); ?>
                                        vendu(s)
                                    </small>
                                </div>

                                <b>
                                    <?= cq_money($p['revenue']); ?>
                                    TND
                                </b>

                            </div>

                        <?php endforeach; ?>

                    </div>

                <?php endif; ?>

            </section>


            <section class="cqstats-panel">

                <div class="cqstats-panel-head">
                    <div>
                        <span class="cqstats-section-label">
                            SYNCHRONISATION
                        </span>
                        <h2>État des données</h2>
                    </div>
                </div>

                <div class="cqstats-sync-list">

                    <div>
                        <span>Ventes PrestaShop</span>
                        <strong>
                            <?= cq_date_short(
                                $sync_status['sales']['value'] ?? null
                            ); ?>
                        </strong>
                    </div>

                    <div>
                        <span>Google Analytics</span>
                        <strong>
                            <?= cq_date_short(
                                $sync_status['ga']['value'] ?? null
                            ); ?>
                        </strong>
                    </div>

                    <div>
                        <span>Produits</span>
                        <strong>
                            <?= cq_date_short(
                                $sync_status['products']['value'] ?? null
                            ); ?>
                        </strong>
                    </div>

                    <div>
                        <span>Historique produits</span>
                        <strong>
                            <?= cq_date_short(
                                $sync_status['changes']['value'] ?? null
                            ); ?>
                        </strong>
                    </div>

                </div>

                <div class="cqstats-notice">
                    Pour le moment, 106 utilise le snapshot migré
                    depuis le dashboard 105.
                </div>

            </section>

        </div>


    <!-- ======================================================
         SALES
         ====================================================== -->
