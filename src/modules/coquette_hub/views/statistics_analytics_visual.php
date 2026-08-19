<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| COQUETTE_ANALYTICS_VISUAL_MIGRATION_V1_COMPONENT
|--------------------------------------------------------------------------
*/

$visualType = $visual_type ?? '';
$visualRows = isset($rows) && is_array($rows)
    ? $rows
    : [];

$labelField = $label_field ?? '';
$valueField = $value_field ?? '';
$centerLabel = $center_label ?? '';
$visualLimit = isset($limit)
    ? max(1, (int) $limit)
    : 6;

if (!$visualRows || !$labelField || !$valueField) {
    return;
}


/*
|--------------------------------------------------------------------------
| DONUT
|--------------------------------------------------------------------------
*/

if ($visualType === 'donut') {

    $total = 0;

    foreach ($visualRows as $row) {
        $total += (float) ($row[$valueField] ?? 0);
    }

    if ($total <= 0) {
        return;
    }

    $palette = [
        '#DA1C5C',
        '#7C3AED',
        '#2563EB',
        '#F59E0B',
        '#10B981',
        '#F97316',
    ];

    $prepared = [];
    $other = 0;

    foreach ($visualRows as $idx => $row) {

        $value = (float) ($row[$valueField] ?? 0);

        if ($value <= 0) {
            continue;
        }

        if ($idx < 5) {
            $prepared[] = [
                'label' => (string) ($row[$labelField] ?? '—'),
                'value' => $value,
            ];
        } else {
            $other += $value;
        }
    }

    if ($other > 0) {
        $prepared[] = [
            'label' => 'Autres',
            'value' => $other,
        ];
    }

    $cursor = 0;
    $segments = [];

    foreach ($prepared as $idx => $item) {

        $pct = ($item['value'] / $total) * 100;

        $start = $cursor;
        $end = $cursor + $pct;

        $segments[] =
            $palette[$idx % count($palette)]
            . ' '
            . number_format($start, 3, '.', '')
            . '% '
            . number_format($end, 3, '.', '')
            . '%';

        $prepared[$idx]['pct'] = $pct;
        $prepared[$idx]['color'] =
            $palette[$idx % count($palette)];

        $cursor = $end;
    }
    ?>

    <div class="cqav-donut-layout">

        <div
            class="cqav-donut"
            style="background:conic-gradient(
                <?= html_escape(implode(',', $segments)); ?>
            );"
        >
            <div class="cqav-donut-hole">

                <strong>
                    <?= number_format(
                        $total,
                        0,
                        ',',
                        ' '
                    ); ?>
                </strong>

                <span>
                    <?= html_escape(
                        $centerLabel ?: $valueField
                    ); ?>
                </span>

            </div>
        </div>


        <div class="cqav-donut-legend">

            <?php foreach ($prepared as $item): ?>

                <div class="cqav-donut-row">

                    <div class="cqav-donut-name">

                        <i
                            style="background:
                            <?= html_escape($item['color']); ?>"
                        ></i>

                        <span>
                            <?= html_escape($item['label']); ?>
                        </span>

                    </div>

                    <strong>
                        <?= number_format(
                            $item['pct'],
                            1,
                            ',',
                            ' '
                        ); ?>%
                    </strong>

                </div>

            <?php endforeach; ?>

        </div>

    </div>

<?php
    return;
}


/*
|--------------------------------------------------------------------------
| BARRES
|--------------------------------------------------------------------------
*/

if ($visualType === 'bars') {

    $slice = array_slice(
        $visualRows,
        0,
        $visualLimit
    );

    $maxValue = 0;

    foreach ($slice as $row) {
        $maxValue = max(
            $maxValue,
            (float) ($row[$valueField] ?? 0)
        );
    }

    if ($maxValue <= 0) {
        return;
    }
    ?>

    <div class="cqav-bars">

        <?php foreach ($slice as $row): ?>

            <?php
            $label = (string) (
                $row[$labelField] ?? '—'
            );

            $value = (float) (
                $row[$valueField] ?? 0
            );

            $pct = ($value / $maxValue) * 100;
            ?>

            <div class="cqav-bar-row">

                <div class="cqav-bar-label">
                    <?= html_escape($label); ?>
                </div>

                <div class="cqav-bar-track">
                    <i
                        style="width:
                        <?= number_format(
                            $pct,
                            2,
                            '.',
                            ''
                        ); ?>%"
                    ></i>
                </div>

                <strong>
                    <?= number_format(
                        $value,
                        0,
                        ',',
                        ' '
                    ); ?>
                </strong>

            </div>

        <?php endforeach; ?>

    </div>

<?php
}
