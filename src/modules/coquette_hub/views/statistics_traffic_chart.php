<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| COQUETTE_TRAFFIC_ANALYTICS_V1_CHART
|--------------------------------------------------------------------------
*/

$trafficRows = isset($traffic_daily) && is_array($traffic_daily)
    ? $traffic_daily
    : [];

$trafficPeriod = isset($period)
    ? (int) $period
    : 30;

$totalSessions = 0;
$totalOrders = 0;
$totalViews = 0;

$maxSessions = 0;
$maxOrders = 0;

foreach ($trafficRows as $row) {

    $sessions = (float) ($row['sessions'] ?? 0);
    $orders = (float) ($row['orders_count'] ?? 0);
    $views = (float) ($row['page_views'] ?? 0);

    $totalSessions += $sessions;
    $totalOrders += $orders;
    $totalViews += $views;

    $maxSessions = max(
        $maxSessions,
        $sessions
    );

    $maxOrders = max(
        $maxOrders,
        $orders
    );
}

$conversion = $totalSessions > 0
    ? ($totalOrders / $totalSessions) * 100
    : 0;

$width = 1000;
$height = 285;

$padLeft = 28;
$padRight = 28;
$padTop = 28;
$padBottom = 42;

$plotWidth =
    $width - $padLeft - $padRight;

$plotHeight =
    $height - $padTop - $padBottom;

$countRows = count($trafficRows);

$sessionPoints = [];
$orderPoints = [];
$pointMeta = [];

foreach ($trafficRows as $index => $row) {

    $x = $countRows > 1
        ? $padLeft
            + (
                $index
                / ($countRows - 1)
            ) * $plotWidth
        : $padLeft + ($plotWidth / 2);

    $sessionRatio = $maxSessions > 0
        ? (
            (float) ($row['sessions'] ?? 0)
            / $maxSessions
        )
        : 0;

    $orderRatio = $maxOrders > 0
        ? (
            (float) ($row['orders_count'] ?? 0)
            / $maxOrders
        )
        : 0;

    $sessionY =
        $padTop
        + $plotHeight
        - ($sessionRatio * $plotHeight);

    $orderY =
        $padTop
        + $plotHeight
        - ($orderRatio * $plotHeight);

    $sessionPoints[] =
        number_format($x, 2, '.', '')
        . ','
        . number_format($sessionY, 2, '.', '');

    $orderPoints[] =
        number_format($x, 2, '.', '')
        . ','
        . number_format($orderY, 2, '.', '');

    $pointMeta[] = [
        'x' => $x,
        'session_y' => $sessionY,
        'order_y' => $orderY,
        'date' => $row['stat_date'] ?? '',
        'sessions' => $row['sessions'] ?? 0,
        'orders' => $row['orders_count'] ?? 0,
    ];
}

$axisIndexes = [];

if ($countRows > 0) {

    $wanted = min(
        6,
        $countRows
    );

    for ($i = 0; $i < $wanted; $i++) {

        $idx = $wanted > 1
            ? (int) round(
                $i
                * ($countRows - 1)
                / ($wanted - 1)
            )
            : 0;

        $axisIndexes[$idx] = true;
    }
}

?>

<section class="cqtraffic-panel">

    <div class="cqtraffic-head">

        <div>

            <span class="cqstats-section-label">
                TRAFIC & CONVERSION
            </span>

            <h2>
                Google Analytics / Commandes
            </h2>

            <p>
                Comparaison quotidienne du trafic GA4
                et des commandes PrestaShop.
            </p>

        </div>

        <span class="cqtraffic-period">
            <?= $trafficPeriod === 1
                ? 'Aujourd’hui'
                : $trafficPeriod . ' jours'; ?>
        </span>

    </div>


    <div class="cqtraffic-kpis">

        <div>
            <span>Sessions</span>

            <strong>
                <?= number_format(
                    $totalSessions,
                    0,
                    ',',
                    ' '
                ); ?>
            </strong>
        </div>


        <div>
            <span>Commandes</span>

            <strong>
                <?= number_format(
                    $totalOrders,
                    0,
                    ',',
                    ' '
                ); ?>
            </strong>
        </div>


        <div>
            <span>Conversion approx.</span>

            <strong>
                <?= number_format(
                    $conversion,
                    2,
                    ',',
                    ' '
                ); ?>%
            </strong>
        </div>


        <div>
            <span>Pages vues</span>

            <strong>
                <?= number_format(
                    $totalViews,
                    0,
                    ',',
                    ' '
                ); ?>
            </strong>
        </div>

    </div>


    <?php if (!$trafficRows): ?>

        <div class="cqstats-empty">
            Aucune donnée trafic disponible.
        </div>

    <?php else: ?>

        <div class="cqtraffic-chart-wrap">

            <svg
                class="cqtraffic-chart"
                viewBox="0 0 <?= $width; ?> <?= $height; ?>"
                preserveAspectRatio="none"
                role="img"
                aria-label="Sessions Google Analytics et commandes"
            >

                <?php for ($g = 0; $g <= 4; $g++): ?>

                    <?php
                    $gy =
                        $padTop
                        + (
                            $plotHeight
                            * ($g / 4)
                        );
                    ?>

                    <line
                        x1="<?= $padLeft; ?>"
                        y1="<?= number_format($gy, 2, '.', ''); ?>"
                        x2="<?= $width - $padRight; ?>"
                        y2="<?= number_format($gy, 2, '.', ''); ?>"
                        class="cqtraffic-grid-line"
                    />

                <?php endfor; ?>


                <line
                    x1="<?= $padLeft; ?>"
                    y1="<?= $padTop + $plotHeight; ?>"
                    x2="<?= $width - $padRight; ?>"
                    y2="<?= $padTop + $plotHeight; ?>"
                    class="cqtraffic-axis-line"
                />


                <polyline
                    points="<?= html_escape(
                        implode(
                            ' ',
                            $sessionPoints
                        )
                    ); ?>"
                    class="cqtraffic-line cqtraffic-line-session"
                />


                <polyline
                    points="<?= html_escape(
                        implode(
                            ' ',
                            $orderPoints
                        )
                    ); ?>"
                    class="cqtraffic-line cqtraffic-line-order"
                />


                <?php foreach ($pointMeta as $idx => $point): ?>

                    <?php if (
                        isset($axisIndexes[$idx])
                        || $idx === $countRows - 1
                    ): ?>

                        <circle
                            cx="<?= number_format(
                                $point['x'],
                                2,
                                '.',
                                ''
                            ); ?>"
                            cy="<?= number_format(
                                $point['session_y'],
                                2,
                                '.',
                                ''
                            ); ?>"
                            r="4"
                            class="cqtraffic-dot cqtraffic-dot-session"
                        >
                            <title>
                                <?= html_escape(
                                    $point['date']
                                    . ' — Sessions : '
                                    . number_format(
                                        (float) $point['sessions'],
                                        0,
                                        ',',
                                        ' '
                                    )
                                ); ?>
                            </title>
                        </circle>


                        <circle
                            cx="<?= number_format(
                                $point['x'],
                                2,
                                '.',
                                ''
                            ); ?>"
                            cy="<?= number_format(
                                $point['order_y'],
                                2,
                                '.',
                                ''
                            ); ?>"
                            r="4"
                            class="cqtraffic-dot cqtraffic-dot-order"
                        >
                            <title>
                                <?= html_escape(
                                    $point['date']
                                    . ' — Commandes : '
                                    . number_format(
                                        (float) $point['orders'],
                                        0,
                                        ',',
                                        ' '
                                    )
                                ); ?>
                            </title>
                        </circle>

                    <?php endif; ?>

                <?php endforeach; ?>


                <?php foreach (
                    array_keys($axisIndexes)
                    as $idx
                ): ?>

                    <?php
                    $row = $trafficRows[$idx];

                    $x = $countRows > 1
                        ? $padLeft
                            + (
                                $idx
                                / ($countRows - 1)
                            ) * $plotWidth
                        : $padLeft + ($plotWidth / 2);

                    $label = !empty($row['stat_date'])
                        ? date(
                            'd/m',
                            strtotime($row['stat_date'])
                        )
                        : '';
                    ?>

                    <text
                        x="<?= number_format(
                            $x,
                            2,
                            '.',
                            ''
                        ); ?>"
                        y="<?= $height - 13; ?>"
                        text-anchor="middle"
                        class="cqtraffic-axis-label"
                    >
                        <?= html_escape($label); ?>
                    </text>

                <?php endforeach; ?>

            </svg>

        </div>


        <div class="cqtraffic-legend">

            <span>
                <i class="session"></i>
                Sessions Google Analytics
            </span>

            <span>
                <i class="orders"></i>
                Commandes PrestaShop
            </span>

        </div>


        <div class="cqtraffic-note">
            Les deux courbes utilisent leur propre échelle afin
            de comparer facilement leur évolution.
        </div>

    <?php endif; ?>

</section>
