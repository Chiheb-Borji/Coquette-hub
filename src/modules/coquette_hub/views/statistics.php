<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php
function cq_money($value)
{
    return number_format(
        (float) $value,
        3,
        ',',
        ' '
    );
}

function cq_number($value)
{
    return number_format(
        (float) $value,
        0,
        ',',
        ' '
    );
}

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

$tabs = [
    'overview' => [
        'label' => 'Vue d’ensemble',
        'icon'  => 'fa-chart-line',
    ],
    'analytics' => [
        'label' => _l('cqhub_analytics'),
        'icon'  => 'fa-chart-pie',
    ],
    'sales' => [
        'label' => _l('cqhub_sales'),
        'icon'  => 'fa-receipt',
    ],
    'products' => [
        'label' => _l('cqhub_products'),
        'icon'  => 'fa-box',
    ],
    'stock' => [
        'label' => _l('cqhub_stockouts'),
        'icon'  => 'fa-triangle-exclamation',
    ],
];
?>

<?php init_head(); ?>

<div id="wrapper">

<div class="content cqstats-page">

    <!-- HEADER -->

    <div class="cqstats-header">

        <div>
            <div class="cqstats-eyebrow">
                COQUETTE.TN HUB · ANALYTICS
            </div>

            <h1><?= _l('cqhub_statistics_title'); ?></h1>

            <p>
                <?= _l('cqhub_statistics_subtitle'); ?>
            </p>
        </div>

        <div class="cqstats-sync-pill">
            <span></span>
            <?= _l('cqhub_hub_data'); ?>
        </div>

    </div>


    <!-- NAV -->

    
<?php

/*
========================================================
COQUETTE_HIDE_PRODUCTS_TAB_V1
========================================================
*/

if (
    empty($can_view_products)
) {
    unset(
        $tabs['products']
    );
}

?>

<div class="cqstats-tabs">

        <?php foreach ($tabs as $key => $tab): ?>

            <a
                href="<?= admin_url(
                    'coquette_hub/statistics?section='
                    . $key
                    . '&period='
                    . (int) $period
                ); ?>"
                class="<?= $section === $key ? 'active' : ''; ?>"
            >
                <i class="fa-solid <?= html_escape($tab['icon']); ?>"></i>

                <?= html_escape($tab['label']); ?>
            </a>

        <?php endforeach; ?>

    </div>


    <?php if (in_array($section, ['overview', 'sales'], true)): ?>

        <form
            method="get"
            action="<?= admin_url('coquette_hub/statistics'); ?>"
            class="cqstats-filters"
        >
            <input
                type="hidden"
                name="section"
                value="<?= html_escape($section); ?>"
            >

            <div>
                <label><?= _l('cqhub_period'); ?></label>

                <select name="period">
                    <?php foreach ([1,7,30,90,365] as $p): ?>

                        <option
                            value="<?= $p; ?>"
                            <?= (int) $period === $p
                                ? 'selected'
                                : ''; ?>
                        >
                            <?= $p === 1
                                ? 'Aujourd’hui'
                                : $p . ' jours'; ?>
                        </option>

                    <?php endforeach; ?>
                </select>
            </div>

            <button
                class="btn btn-primary"
                type="submit"
            >
                <?= _l('cqhub_filter'); ?>
            </button>
        </form>

    <?php endif; ?>


    <!-- ======================================================
         OVERVIEW
         ====================================================== -->

    <?php if ($section === 'overview'): ?>

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
                <span><?= _l('cqhub_orders'); ?></span>
                <strong><?= cq_number($orders); ?></strong>
                <small><?= (int) $period; ?> jours</small>
            </div>

            <div class="cqstats-kpi">
                <span><?= _l('cqhub_revenue'); ?></span>
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
                <span><?= _l('cqhub_sessions'); ?></span>
                <strong><?= cq_number($sessions); ?></strong>
                <small>
                    <?= cq_number($views); ?>
                    pages vues
                </small>
            </div>

            <div class="cqstats-kpi">
                <span><?= _l('cqhub_conversion_approx'); ?></span>
                <strong>
                    <?= number_format(
                        $conversion,
                        2,
                        ',',
                        ' '
                    ); ?>%
                </strong>
                <small><?= _l('cqhub_orders_sessions'); ?></small>
            </div>

            <div class="cqstats-kpi">
                <span><?= _l('cqhub_active_products'); ?></span>
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
                    <?= _l('cqhub_active_products_without_stock'); ?>
                </small>
            </div>

        </div>


        
        <!-- COQUETTE_TRAFFIC_ANALYTICS_V1_STATS_OVERVIEW -->

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
                        <h2><?= _l('cqhub_recent_sales'); ?></h2>
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
                            <span><?= _l('cqhub_stockouts'); ?></span>
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
                        <span><?= _l('cqhub_prestashop_sales'); ?></span>
                        <strong>
                            <?= cq_date_short(
                                $sync_status['sales']['value'] ?? null
                            ); ?>
                        </strong>
                    </div>

                    <div>
                        <span><?= _l('cqhub_google_analytics'); ?></span>
                        <strong>
                            <?= cq_date_short(
                                $sync_status['ga']['value'] ?? null
                            ); ?>
                        </strong>
                    </div>

                    <div>
                        <span><?= _l('cqhub_products'); ?></span>
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

    <?php elseif ($section === 'sales'): ?>

        <?php
        $orders = (float) ($sales_summary['orders_count'] ?? 0);
        $revenue = (float) ($sales_summary['revenue'] ?? 0);
        ?>

        <div class="cqstats-section-title">
            <div>
                <span class="cqstats-section-label">
                    PERFORMANCE COMMERCIALE
                </span>
                <h2><?= _l('cqhub_sales'); ?></h2>
                <p>
                    Activité commerciale sur
                    <?= (int) $period; ?> jours.
                </p>
            </div>
        </div>

        <div class="cqstats-kpi-grid cqstats-kpi-grid-4">

            <div class="cqstats-kpi">
                <span>CA total</span>
                <strong>
                    <?= cq_money($revenue); ?>
                    <em>TND</em>
                </strong>
            </div>

            <div class="cqstats-kpi">
                <span><?= _l('cqhub_orders'); ?></span>
                <strong><?= cq_number($orders); ?></strong>
            </div>

            <div class="cqstats-kpi">
                <span>Panier moyen</span>
                <strong>
                    <?= $orders > 0
                        ? cq_money($revenue / $orders)
                        : '0,000'; ?>
                    <em>TND</em>
                </strong>
            </div>

            <div class="cqstats-kpi">
                <span>Jours avec ventes</span>
                <strong>
                    <?= cq_number(count($sales_daily)); ?>
                </strong>
            </div>

        </div>

        <div class="cqstats-sales-card-grid">

            <?php
            $maxRevenue = 0;

            foreach ($sales_daily as $row) {
                $maxRevenue = max(
                    $maxRevenue,
                    (float) $row['revenue']
                );
            }

            $reversedSales = array_reverse($sales_daily);
            ?>

            <?php foreach ($reversedSales as $row): ?>

                <?php
                $dayRevenue = (float) $row['revenue'];
                $dayOrders = (int) $row['orders_count'];

                $pct = $maxRevenue > 0
                    ? ($dayRevenue / $maxRevenue) * 100
                    : 0;
                ?>

                <article class="cqstats-sale-card">

                    <div class="cqstats-sale-date">
                        <strong>
                            <?= html_escape(
                                date(
                                    'd/m/Y',
                                    strtotime($row['sales_date'])
                                )
                            ); ?>
                        </strong>

                        <span>
                            <?= html_escape(
                                date(
                                    'D',
                                    strtotime($row['sales_date'])
                                )
                            ); ?>
                        </span>
                    </div>

                    <div class="cqstats-sale-revenue">
                        <?= cq_money($dayRevenue); ?>
                        <small>TND</small>
                    </div>

                    <div class="cqstats-sale-meta">
                        <span>
                            <?= cq_number($dayOrders); ?>
                            commandes
                        </span>

                        <span>
                            <?= $dayOrders > 0
                                ? cq_money(
                                    $dayRevenue / $dayOrders
                                )
                                : '0,000'; ?>
                            TND panier
                        </span>
                    </div>

                    <div class="cqstats-sale-progress">
                        <i
                            style="width:<?= number_format(
                                $pct,
                                2,
                                '.',
                                ''
                            ); ?>%"
                        ></i>
                    </div>

                </article>

            <?php endforeach; ?>

        </div>


    <!-- ======================================================
         PRODUCTS
         ====================================================== -->

    <?php elseif (
    $section === 'products'
    &&
    !empty($can_view_products)
): ?>

        <div class="cqstats-section-title">
            <div>
                <span class="cqstats-section-label">
                    CATALOGUE
                </span>
                <h2><?= _l('cqhub_products'); ?></h2>
                <p>
                    Recherche et contrôle du catalogue synchronisé.
                </p>
            </div>

            <div class="cqstats-result-count">
                <?= cq_number($products_total); ?>
                résultat(s)
            </div>
        </div>

        <div class="cqstats-kpi-grid cqstats-kpi-grid-4">

            <div class="cqstats-kpi">
                <span>Total produits</span>
                <strong>
                    <?= cq_number(
                        $product_summary['total_products'] ?? 0
                    ); ?>
                </strong>
            </div>

            <div class="cqstats-kpi">
                <span>Actifs</span>
                <strong>
                    <?= cq_number(
                        $product_summary['active_products'] ?? 0
                    ); ?>
                </strong>
            </div>

            <div class="cqstats-kpi">
                <span>Disponibles</span>
                <strong>
                    <?= cq_number(
                        $product_summary['available_products'] ?? 0
                    ); ?>
                </strong>
            </div>

            <div class="cqstats-kpi cqstats-kpi-alert">
                <span><?= _l('cqhub_stockouts'); ?></span>
                <strong>
                    <?= cq_number(
                        $product_summary['out_of_stock'] ?? 0
                    ); ?>
                </strong>
            </div>

        </div>


        <form
            method="get"
            action="<?= admin_url('coquette_hub/statistics'); ?>"
            class="cqstats-product-filters"
        >

            <input
                type="hidden"
                name="section"
                value="products"
            >

            <div class="cqstats-search">
                <i class="fa-solid fa-magnifying-glass"></i>

                <input
                    type="text"
                    name="q"
                    value="<?= html_escape($filters['q']); ?>"
                    placeholder="Nom, référence, marque, catégorie..."
                >
            </div>

            <select name="stock">
                <option value="all">Tous les stocks</option>
                <option
                    value="in"
                    <?= $filters['stock'] === 'in'
                        ? 'selected'
                        : ''; ?>
                >
                    En stock
                </option>
                <option
                    value="out"
                    <?= $filters['stock'] === 'out'
                        ? 'selected'
                        : ''; ?>
                >
                    En rupture
                </option>
            </select>

            <select name="active">
                <option value="all">Tous les statuts</option>
                <option
                    value="1"
                    <?= $filters['active'] === '1'
                        ? 'selected'
                        : ''; ?>
                >
                    Actifs
                </option>
                <option
                    value="0"
                    <?= $filters['active'] === '0'
                        ? 'selected'
                        : ''; ?>
                >
                    Inactifs
                </option>
            </select>

            <select name="brand">
                <option value="">Toutes les marques</option>

                <?php foreach ($brands as $b): ?>

                    <option
                        value="<?= html_escape(
                            $b['manufacturer_name']
                        ); ?>"
                        <?= $filters['brand']
                            === $b['manufacturer_name']
                            ? 'selected'
                            : ''; ?>
                    >
                        <?= html_escape(
                            $b['manufacturer_name']
                        ); ?>
                    </option>

                <?php endforeach; ?>
            </select>

            <select name="category">
                <option value="">Toutes les catégories</option>

                <?php foreach ($categories as $c): ?>

                    <option
                        value="<?= html_escape(
                            $c['default_category']
                        ); ?>"
                        <?= $filters['category']
                            === $c['default_category']
                            ? 'selected'
                            : ''; ?>
                    >
                        <?= html_escape(
                            $c['default_category']
                        ); ?>
                    </option>

                <?php endforeach; ?>
            </select>

            <select name="limit">
                <?php foreach ([25,50,100] as $l): ?>
                    <option
                        value="<?= $l; ?>"
                        <?= (int) $limit === $l
                            ? 'selected'
                            : ''; ?>
                    >
                        <?= $l; ?> résultats
                    </option>
                <?php endforeach; ?>
            </select>

            <button class="btn btn-primary">
                <?= _l('cqhub_filter'); ?>
            </button>

        </form>


        <div class="cqstats-product-grid">

            <?php if (!$products): ?>

                <div class="cqstats-empty cqstats-full">
                    Aucun produit correspondant.
                </div>

            <?php endif; ?>

            <?php foreach ($products as $p): ?>

                <article class="cqstats-product-card">

                    <div class="cqstats-product-card-head">

                        <div class="cqstats-product-id">
                            #<?= (int) $p['product_id']; ?>
                        </div>

                        <?php if ((int) $p['active'] === 1): ?>

                            <span class="cqstats-badge success">
                                Actif
                            </span>

                        <?php else: ?>

                            <span class="cqstats-badge muted">
                                Inactif
                            </span>

                        <?php endif; ?>

                    </div>

                    <h3>
                        <?= html_escape($p['product_name']); ?>
                    </h3>

                    <div class="cqstats-product-ref">
                        <?= $p['reference']
                            ? html_escape($p['reference'])
                            : 'Sans référence'; ?>
                    </div>

                    <div class="cqstats-product-tags">

                        <?php if ($p['manufacturer_name']): ?>
                            <span>
                                <?= html_escape(
                                    $p['manufacturer_name']
                                ); ?>
                            </span>
                        <?php endif; ?>

                        <?php if ($p['default_category']): ?>
                            <span>
                                <?= html_escape(
                                    $p['default_category']
                                ); ?>
                            </span>
                        <?php endif; ?>

                    </div>

                    <div class="cqstats-product-bottom">

                        <div>
                            <span>Stock</span>

                            <strong
                                class="<?= (int) $p['quantity'] <= 0
                                    ? 'danger'
                                    : ''; ?>"
                            >
                                <?= (int) $p['quantity']; ?>
                            </strong>
                        </div>

                        <div>
                            <span>Prix</span>
                            <strong>
                                <?= cq_money($p['price']); ?>
                                TND
                            </strong>
                        </div>

                        <div>
                            <span>Modifié</span>
                            <strong>
                                <?= $p['date_upd']
                                    ? html_escape(
                                        date(
                                            'd/m/Y',
                                            strtotime(
                                                $p['date_upd']
                                            )
                                        )
                                    )
                                    : '—'; ?>
                            </strong>
                        </div>

                    </div>

                    <!-- COQUETTE_PRODUCTS_AUDIT_V2_VIEW -->

                    <div class="cqstats-product-actions">

                        <button
                            type="button"
                            class="cqstats-product-audit-btn"
                            data-product-id="<?= (int) $p['product_id']; ?>"
                        >
                            <i class="fa-solid fa-clock-rotate-left"></i>

                            Historique complet
                        </button>

                    </div>

                </article>

            <?php endforeach; ?>

        </div>


        <section class="cqstats-panel cqstats-product-activity">

            <div class="cqstats-panel-head">
                <div>
                    <span class="cqstats-section-label">
                        ACTIVITÉ PRODUITS
                    </span>
                    <h2>Dernières modifications</h2>
                </div>
            </div>

            <div class="cqstats-activity-list">

                <?php foreach ($recent_changes as $change): ?>

                    <div class="cqstats-activity-row">

                        <div class="cqstats-activity-dot"></div>

                        <div>
                            <strong>
                                <?= html_escape(
                                    $change['product_name']
                                    ?: 'Produit #'
                                       . $change['product_id']
                                ); ?>
                            </strong>

                            <span>
                                <?= html_escape(
                                    $change['employee_name']
                                    ?: 'Système'
                                ); ?>

                                ·

                                <?= html_escape(
                                    $change['change_type']
                                    ?: 'Modification'
                                ); ?>
                            </span>
                        </div>

                        <time>
                            <?= cq_date_short(
                                $change['change_date']
                            ); ?>
                        </time>

                    </div>

                <?php endforeach; ?>

            </div>

        </section>


    <!-- ======================================================
         STOCK
         ====================================================== -->

    <?php elseif ($section === 'stock'): ?>

        <div class="cqstats-section-title">
            <div>
                <span class="cqstats-section-label">
                    STOCK
                </span>
                <h2><?= _l('cqhub_out_of_stock_products'); ?></h2>
                <p>
                    <?= _l('cqhub_out_of_stock_description'); ?>
                    ou égale à zéro.
                </p>
            </div>

            <div class="cqstats-result-count danger">
                <?= cq_number(
                    $product_summary['out_of_stock'] ?? 0
                ); ?>
                ruptures
            </div>
        </div>

        <form
            method="get"
            action="<?= admin_url('coquette_hub/statistics'); ?>"
            class="cqstats-product-filters"
        >
            <input
                type="hidden"
                name="section"
                value="stock"
            >

            <div class="cqstats-search">
                <i class="fa-solid fa-magnifying-glass"></i>

                <input
                    type="text"
                    name="q"
                    value="<?= html_escape($q); ?>"
                    placeholder="Rechercher une rupture..."
                >
            </div>

            <select name="limit">
                <?php foreach ([25,50,100] as $l): ?>
                    <option
                        value="<?= $l; ?>"
                        <?= (int) $limit === $l
                            ? 'selected'
                            : ''; ?>
                    >
                        <?= $l; ?> résultats
                    </option>
                <?php endforeach; ?>
            </select>

            <button class="btn btn-primary">
                <?= _l('cqhub_filter'); ?>
            </button>
        </form>

        <div class="cqstats-product-grid">

            <?php foreach ($out_of_stock as $p): ?>

                <article class="cqstats-product-card cqstats-stockout-card">

                    <div class="cqstats-product-card-head">

                        <div class="cqstats-product-id">
                            #<?= (int) $p['product_id']; ?>
                        </div>

                        <span class="cqstats-badge danger">
                            Rupture
                        </span>

                    </div>

                    <h3>
                        <?= html_escape($p['product_name']); ?>
                    </h3>

                    <div class="cqstats-product-ref">
                        <?= $p['reference']
                            ? html_escape($p['reference'])
                            : 'Sans référence'; ?>
                    </div>

                    <div class="cqstats-product-tags">
                        <span>
                            <?= html_escape(
                                $p['manufacturer_name']
                                ?: 'Sans marque'
                            ); ?>
                        </span>

                        <span>
                            <?= html_escape(
                                $p['default_category']
                                ?: 'Sans catégorie'
                            ); ?>
                        </span>
                    </div>

                    <div class="cqstats-product-bottom">

                        <div>
                            <span>Stock</span>
                            <strong class="danger">
                                <?= (int) $p['quantity']; ?>
                            </strong>
                        </div>

                        <div>
                            <span>Prix</span>
                            <strong>
                                <?= cq_money($p['price']); ?>
                                TND
                            </strong>
                        </div>

                        <div>
                            <span>Modifié</span>
                            <strong>
                                <?= $p['date_upd']
                                    ? html_escape(
                                        date(
                                            'd/m/Y',
                                            strtotime(
                                                $p['date_upd']
                                            )
                                        )
                                    )
                                    : '—'; ?>
                            </strong>
                        </div>

                    </div>

                </article>

            <?php endforeach; ?>

        </div>


    <!-- ======================================================
         ANALYTICS
         ====================================================== -->

    <?php elseif ($section === 'analytics'): ?>

        <div class="cqstats-section-title">
            <div>
                <span class="cqstats-section-label">
                    GOOGLE ANALYTICS 4
                </span>

                <h2><?= _l('cqhub_analytics'); ?></h2>

                <p>
                    Les vues détaillées utilisent la période
                    GA4 de 30 jours synchronisée.
                </p>
            </div>
        </div>

        <div class="cqstats-kpi-grid cqstats-kpi-grid-4">

            <div class="cqstats-kpi">
                <span><?= _l('cqhub_sessions'); ?></span>
                <strong>
                    <?= cq_number(
                        $ga_summary['sessions'] ?? 0
                    ); ?>
                </strong>
            </div>

            <div class="cqstats-kpi">
                <span>Pages vues</span>
                <strong>
                    <?= cq_number(
                        $ga_summary['page_views'] ?? 0
                    ); ?>
                </strong>
            </div>

            <div class="cqstats-kpi">
                <span><?= _l('cqhub_engaged_sessions'); ?></span>
                <strong>
                    <?= cq_number(
                        $ga_summary[
                            'engaged_sessions'
                        ] ?? 0
                    ); ?>
                </strong>
            </div>

            <div class="cqstats-kpi">
                <span>Taux engagement</span>
                <strong>
                    <?= number_format(
                        (
                            (float) (
                                $ga_summary[
                                    'engagement_rate'
                                ] ?? 0
                            )
                        ) * 100,
                        1,
                        ',',
                        ' '
                    ); ?>%
                </strong>
            </div>

        </div>


        
        <!-- COQUETTE_TRAFFIC_ANALYTICS_V1_STATS_ANALYTICS -->

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


<?php
        $analyticsBlocks = [

            // COQUETTE_ANALYTICS_VISUAL_MIGRATION_V1_CONFIG

            [
                'title' =>
                    'Acquisition — sources de trafic',

                'help' =>
                    'Montre d’où viennent les visiteurs : Google, '
                    . 'Instagram, Facebook, accès direct, publicité, etc. '
                    . 'C’est la section la plus importante pour juger '
                    . 'les campagnes marketing.',

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
                    'source_medium' =>
                        'Source / Medium',

                    'active_users' =>
                        'Utilisateurs',

                    'sessions' =>
                        'Sessions',

                    'page_views' =>
                        'Pages vues',
                ],
            ],


            [
                'title' =>
                    'Landing pages — pages d’entrée',

                'help' =>
                    'Une landing page est la première page vue par '
                    . 'un visiteur. Si une page attire beaucoup de '
                    . 'trafic mais génère peu de commandes, il faut '
                    . 'vérifier l’offre, le stock, le prix ou '
                    . 'l’ergonomie.',

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
                    'landing_page' =>
                        'Landing page',

                    'active_users' =>
                        'Utilisateurs',

                    'sessions' =>
                        'Sessions',

                    'page_views' =>
                        'Pages vues',
                ],
            ],


            [
                'title' =>
                    'Engagement — pages les plus visitées',

                'help' =>
                    'Montre les pages les plus consultées. '
                    . 'Utile pour savoir quelles catégories, '
                    . 'promotions ou recherches intéressent '
                    . 'les visiteurs.',

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
                    'page_path' =>
                        'Page',

                    'active_users' =>
                        'Utilisateurs',

                    'page_views' =>
                        'Pages vues',
                ],
            ],


            [
                'title' =>
                    'Événements GA4',

                'help' =>
                    'Les événements indiquent les actions des '
                    . 'visiteurs : page_view, user_engagement, '
                    . 'search, view_item, add_to_cart, '
                    . 'begin_checkout, purchase si configuré.',

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
                    'event_name' =>
                        'Événement',

                    'event_count' =>
                        'Nombre',
                ],
            ],


            [
                'title' =>
                    'Appareils',

                'help' =>
                    'Permet de savoir si les visiteurs utilisent '
                    . 'surtout mobile, desktop ou tablette. '
                    . 'Si mobile domine, chaque page doit être '
                    . 'pensée mobile-first.',

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
                    'device_category' =>
                        'Appareil',

                    'active_users' =>
                        'Utilisateurs',

                    'sessions' =>
                        'Sessions',

                    'page_views' =>
                        'Pages vues',
                ],
            ],


            [
                'title' =>
                    'Audience — nouveaux vs anciens',

                'help' =>
                    'Montre si le trafic vient surtout de nouveaux '
                    . 'visiteurs ou de personnes qui reviennent. '
                    . 'Beaucoup de visiteurs qui reviennent peut '
                    . 'montrer une bonne notoriété ou une intention '
                    . 'd’achat différée.',

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
                    'audience_type' =>
                        'Type',

                    'active_users' =>
                        'Utilisateurs',

                    'sessions' =>
                        'Sessions',
                ],
            ],


            [
                'title' =>
                    'Géographie',

                'help' =>
                    'Montre les pays et villes d’origine du trafic. '
                    . 'Utile pour confirmer que les campagnes '
                    . 'touchent bien la Tunisie et les zones '
                    . 'commerciales importantes.',

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
                    'country' =>
                        'Pays',

                    'city' =>
                        'Ville',

                    'active_users' =>
                        'Utilisateurs',

                    'sessions' =>
                        'Sessions',
                ],
            ],
        ];
        ?>

        <?php foreach ($analyticsBlocks as $block): ?>

            <section class="cqstats-panel cqstats-table-panel cqstats-analytics-visual-panel">
                <!-- COQUETTE_ANALYTICS_DESIGN_COMPACT_V2 -->

                <div class="cqstats-panel-head">
                    <div>
                        <span class="cqstats-section-label">
                            GA4
                        </span>
                        <h2>
                            <?= html_escape($block['title']); ?>
                        </h2>
                    </div>
                </div>


                <!-- COQUETTE_ANALYTICS_VISUAL_MIGRATION_V1_RENDER -->

                <?php if (!empty($block['help'])): ?>

                    <details class="cqav-help">

                        <summary>
                            <i class="fa-solid fa-circle-info"></i>
                            À quoi ça sert ?
                        </summary>

                        <div>
                            <?= html_escape($block['help']); ?>
                        </div>

                    </details>

                <?php endif; ?>


                <?php if (!empty($block['rows'])): ?>

                    <div class="cqav-visual">

                        <?php
                        $this->load->view(
                            'coquette_hub/statistics_analytics_visual',
                            [
                                'visual_type' =>
                                    $block['visual_type'] ?? '',

                                'rows' =>
                                    $block['rows'],

                                'label_field' =>
                                    $block['label_field'] ?? '',

                                'value_field' =>
                                    $block['value_field'] ?? '',

                                'center_label' =>
                                    $block['center_label'] ?? '',

                                'limit' =>
                                    $block['limit'] ?? 6,
                            ]
                        );
                        ?>

                    </div>

                <?php endif; ?>


                <?php if (!$block['rows']): ?>

                    <div class="cqstats-empty">
                        Aucune donnée disponible.
                    </div>

                <?php else: ?>

                    <div class="cqstats-table-wrap">

                        <table class="cqstats-table">

                            <thead>
                                <tr>
                                    <?php foreach (
                                        $block['columns'] as $label
                                    ): ?>
                                        <th>
                                            <?= html_escape($label); ?>
                                        </th>
                                    <?php endforeach; ?>
                                </tr>
                            </thead>

                            <tbody>

                            <?php foreach (
                                $block['rows'] as $row
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
                                    <?= html_escape(
                                        $eventName
                                    ); ?>
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

                        <thead>
                            <tr>
                                <?php foreach (
                                    array_keys(
                                        $analytics['ecommerce'][0]
                                    ) as $column
                                ): ?>
                                    <th>
                                        <?= html_escape($column); ?>
                                    </th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>

                        <tbody>

                        <?php foreach (
                            $analytics['ecommerce'] as $row
                        ): ?>

                            <tr>

                            <?php foreach ($row as $value): ?>

                                <td>
                                    <?= html_escape($value); ?>
                                </td>

                            <?php endforeach; ?>

                            </tr>

                        <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>

            <?php endif; ?>

        </section>

    <?php endif; ?>

</div>

</div>



<!-- =========================================================
     COQUETTE PRODUCTS AUDIT V2 MODAL
     ========================================================= -->

<div
    id="cqProductAuditModal"
    class="cqpa-modal"
    aria-hidden="true"
>

    <div
        class="cqpa-backdrop"
        data-cqpa-close
    ></div>


    <div
        class="cqpa-dialog"
        role="dialog"
        aria-modal="true"
        aria-labelledby="cqpaTitle"
    >

        <button
            type="button"
            class="cqpa-close"
            data-cqpa-close
            aria-label="Fermer"
        >
            <i class="fa-solid fa-xmark"></i>
        </button>


        <div class="cqpa-header">

            <div>

                <div class="cqpa-eyebrow">
                    AUDIT PRODUIT
                </div>

                <h2 id="cqpaTitle">
                    Historique produit
                </h2>

                <p id="cqpaSubtitle">
                    Chargement...
                </p>

            </div>


            <div
                id="cqpaCount"
                class="cqpa-count"
            >
                —
            </div>

        </div>


        <div
            id="cqpaProductSummary"
            class="cqpa-product-summary"
        ></div>


        <div class="cqpa-body">

            <div
                id="cqpaLoading"
                class="cqpa-loading"
            >
                <i class="fa-solid fa-circle-notch fa-spin"></i>
                Chargement de l'historique...
            </div>


            <div
                id="cqpaEmpty"
                class="cqpa-empty"
                hidden
            >
                Aucun événement d'audit enregistré pour ce produit.
            </div>


            <div
                id="cqpaTimeline"
                class="cqpa-timeline"
            ></div>


            <button
                type="button"
                id="cqpaLoadMore"
                class="cqpa-load-more"
                hidden
            >
                Charger plus d'historique
            </button>

        </div>

    </div>

</div>


<script>
(function () {

    const modal =
        document.getElementById(
            'cqProductAuditModal'
        );

    if (!modal) {
        return;
    }


    const endpointBase =
        <?= json_encode(
            admin_url(
                'coquette_hub/product_audit_detail/'
            )
        ); ?>;


    const timeline =
        document.getElementById(
            'cqpaTimeline'
        );

    const loading =
        document.getElementById(
            'cqpaLoading'
        );

    const empty =
        document.getElementById(
            'cqpaEmpty'
        );

    const loadMore =
        document.getElementById(
            'cqpaLoadMore'
        );

    const title =
        document.getElementById(
            'cqpaTitle'
        );

    const subtitle =
        document.getElementById(
            'cqpaSubtitle'
        );

    const count =
        document.getElementById(
            'cqpaCount'
        );

    const summary =
        document.getElementById(
            'cqpaProductSummary'
        );


    let currentProductId = 0;
    let currentOffset = 0;
    let totalRows = 0;
    let loadingMore = false;


    function esc(value) {

        return String(
            value ?? ''
        ).replace(
            /[&<>"']/g,
            function (char) {

                return {
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#039;'
                }[char];

            }
        );
    }


    function money(value) {

        const number =
            Number(value || 0);

        return new Intl.NumberFormat(
            'fr-TN',
            {
                minimumFractionDigits: 3,
                maximumFractionDigits: 3
            }
        ).format(number)
        + ' TND';
    }


    function dateTime(value) {

        if (!value) {
            return '—';
        }

        const normalized =
            String(value)
            .replace(
                ' ',
                'T'
            );

        const date =
            new Date(normalized);

        if (
            Number.isNaN(
                date.getTime()
            )
        ) {
            return esc(value);
        }

        return new Intl.DateTimeFormat(
            'fr-FR',
            {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            }
        ).format(date);
    }


    function eventIcon(row) {

        const type =
            String(
                row.event_type || ''
            ).toLowerCase();

        const field =
            String(
                row.field_changed || ''
            ).toLowerCase();


        if (
            type.includes('image')
            || field.includes('image')
        ) {
            return 'fa-image';
        }

        if (
            type.includes('stock')
            || field.includes('stock')
        ) {
            return 'fa-boxes-stacked';
        }

        if (
            type.includes('specific_price')
            || field.includes('price')
        ) {
            return 'fa-tag';
        }

        if (
            field.includes('description')
        ) {
            return 'fa-align-left';
        }

        if (
            field.includes('meta')
            || field.includes('link_rewrite')
        ) {
            return 'fa-magnifying-glass';
        }

        if (
            field === 'active'
        ) {
            return 'fa-toggle-on';
        }

        if (
            field.includes('category')
        ) {
            return 'fa-folder-tree';
        }

        if (
            type.includes('create')
            || type.includes('add')
        ) {
            return 'fa-circle-plus';
        }

        return 'fa-pen';
    }


    function eventClass(row) {

        const type =
            String(
                row.event_type || ''
            ).toLowerCase();

        const nature =
            String(
                row.change_nature || ''
            ).toLowerCase();


        if (
            type.includes('delete')
        ) {
            return 'danger';
        }

        if (
            type.includes('add')
            || type.includes('create')
        ) {
            return 'success';
        }

        if (
            nature.includes('stock')
        ) {
            return 'stock';
        }

        if (
            nature.includes('image')
        ) {
            return 'image';
        }

        return '';
    }


    function valueBlock(
        label,
        value,
        cls
    ) {

        const clean =
            value === null
            || value === undefined
            || String(value) === ''
                ? '—'
                : String(value);


        return `
            <div class="cqpa-value ${cls || ''}">

                <span>
                    ${esc(label)}
                </span>

                <pre>${esc(clean)}</pre>

            </div>
        `;
    }


    function renderProduct(product) {

        title.textContent =
            product.product_name
            || (
                'Produit #'
                + product.product_id
            );


        subtitle.textContent =
            '#'
            + product.product_id
            + (
                product.reference
                    ? ' · ' + product.reference
                    : ' · Sans référence'
            );


        const createdBy =
            product.created_by
            || 'Non renseigné';

        const modifiedBy =
            product.modified_by
            || 'Non renseigné';


        summary.innerHTML = `

            <div>
                <span>Stock actuel</span>
                <strong class="${
                    Number(product.quantity) <= 0
                        ? 'danger'
                        : ''
                }">
                    ${esc(product.quantity)}
                </strong>
            </div>

            <div>
                <span>Prix actuel</span>
                <strong>
                    ${money(product.price)}
                </strong>
            </div>

            <div>
                <span>Statut</span>
                <strong>
                    ${
                        Number(product.active) === 1
                            ? 'Actif'
                            : 'Inactif'
                    }
                </strong>
            </div>

            <div>
                <span>Création catalogue</span>
                <strong>
                    ${esc(createdBy)}
                </strong>
                <small>
                    ${dateTime(
                        product.created_log_date
                        || product.date_add
                    )}
                </small>
            </div>

            <div>
                <span>Dernière modification</span>
                <strong>
                    ${esc(modifiedBy)}
                </strong>
                <small>
                    ${dateTime(
                        product.modified_log_date
                        || product.date_upd
                    )}
                </small>
            </div>

        `;
    }


    function renderEvent(row) {

        const employee =
            row.employee_name
            || 'Système';

        const email =
            row.employee_email
            || '';

        const label =
            row.field_label
            || row.field_changed
            || row.change_nature
            || 'Modification';


        const meta = [];

        if (email) {
            meta.push(
                esc(email)
            );
        }

        if (row.ip_address) {
            meta.push(
                'IP ' + esc(row.ip_address)
            );
        }


        const element =
            document.createElement(
                'article'
            );

        element.className =
            'cqpa-event '
            + eventClass(row);


        element.innerHTML = `

            <div class="cqpa-event-line">

                <div class="cqpa-event-icon">

                    <i class="fa-solid ${eventIcon(row)}"></i>

                </div>


                <div class="cqpa-event-main">

                    <div class="cqpa-event-head">

                        <div>

                            <span class="cqpa-event-nature">
                                ${esc(
                                    row.change_nature
                                    || row.entity_type
                                    || 'Modification'
                                )}
                            </span>

                            <h3>
                                ${esc(label)}
                            </h3>

                        </div>

                        <time>
                            ${dateTime(row.date_add)}
                        </time>

                    </div>


                    <div class="cqpa-actor">

                        <i class="fa-solid ${
                            employee === 'Système'
                                ? 'fa-gear'
                                : 'fa-user'
                        }"></i>

                        <strong>
                            ${esc(employee)}
                        </strong>

                        ${
                            meta.length
                                ? '<span>'
                                  + meta.join(' · ')
                                  + '</span>'
                                : ''
                        }

                    </div>


                    <div class="cqpa-change-grid">

                        ${valueBlock(
                            'Ancienne valeur',
                            row.old_value,
                            'old'
                        )}

                        <div class="cqpa-arrow">
                            <i class="fa-solid fa-arrow-right"></i>
                        </div>

                        ${valueBlock(
                            'Nouvelle valeur',
                            row.new_value,
                            'new'
                        )}

                    </div>


                    <div class="cqpa-event-footer">

                        <span>
                            Event:
                            ${esc(row.event_type || '—')}
                        </span>

                        <span>
                            Champ:
                            ${esc(row.field_changed || '—')}
                        </span>

                        ${
                            row.request_uri
                                ? `
                                <details>
                                    <summary>
                                        Requête
                                    </summary>

                                    <code>
                                        ${esc(row.request_uri)}
                                    </code>
                                </details>
                                `
                                : ''
                        }

                    </div>

                </div>

            </div>

        `;


        timeline.appendChild(
            element
        );
    }


    async function fetchHistory(
        append
    ) {

        if (
            !currentProductId
            || loadingMore
        ) {
            return;
        }


        loadingMore = true;

        if (!append) {

            timeline.innerHTML = '';

            currentOffset = 0;

            empty.hidden = true;

            loading.hidden = false;

            loadMore.hidden = true;

        } else {

            loadMore.disabled = true;

            loadMore.textContent =
                'Chargement...';

        }


        try {

            const url =
                endpointBase
                + currentProductId
                + '?limit=100&offset='
                + currentOffset;


            const response =
                await fetch(
                    url,
                    {
                        credentials:
                            'same-origin',
                        headers: {
                            'X-Requested-With':
                                'XMLHttpRequest'
                        }
                    }
                );


            if (!response.ok) {

                throw new Error(
                    'HTTP '
                    + response.status
                );
            }


            const data =
                await response.json();


            if (!data.ok) {

                throw new Error(
                    data.error
                    || 'Erreur API'
                );
            }


            renderProduct(
                data.product
            );


            totalRows =
                Number(
                    data.pagination.total
                    || 0
                );


            count.textContent =
                totalRows
                + (
                    totalRows > 1
                        ? ' événements'
                        : ' événement'
                );


            data.audits.forEach(
                renderEvent
            );


            currentOffset +=
                data.audits.length;


            loading.hidden = true;


            empty.hidden =
                totalRows !== 0;


            loadMore.hidden =
                !data.pagination.has_more;


            loadMore.disabled = false;

            loadMore.textContent =
                'Charger plus d’historique';


        } catch (error) {

            loading.hidden = true;

            empty.hidden = false;

            empty.textContent =
                'Impossible de charger l’historique : '
                + error.message;

            console.error(
                'Coquette Product Audit:',
                error
            );

        } finally {

            loadingMore = false;

        }

    }


    function openModal(productId) {

        currentProductId =
            Number(productId || 0);

        modal.classList.add(
            'is-open'
        );

        modal.setAttribute(
            'aria-hidden',
            'false'
        );

        document.body.classList.add(
            'cqpa-lock'
        );

        title.textContent =
            'Historique produit';

        subtitle.textContent =
            'Chargement...';

        count.textContent =
            '—';

        summary.innerHTML =
            '';

        fetchHistory(false);
    }


    function closeModal() {

        modal.classList.remove(
            'is-open'
        );

        modal.setAttribute(
            'aria-hidden',
            'true'
        );

        document.body.classList.remove(
            'cqpa-lock'
        );

    }


    document.addEventListener(
        'click',
        function (event) {

            const auditButton =
                event.target.closest(
                    '.cqstats-product-audit-btn'
                );

            if (auditButton) {

                openModal(
                    auditButton.dataset.productId
                );

                return;
            }


            if (
                event.target.closest(
                    '[data-cqpa-close]'
                )
            ) {
                closeModal();
            }

        }
    );


    loadMore.addEventListener(
        'click',
        function () {
            fetchHistory(true);
        }
    );


    document.addEventListener(
        'keydown',
        function (event) {

            if (
                event.key === 'Escape'
                && modal.classList.contains(
                    'is-open'
                )
            ) {
                closeModal();
            }

        }
    );

})();
</script>



<?php init_tail(); ?>
