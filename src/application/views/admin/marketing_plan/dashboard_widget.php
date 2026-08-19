<?php defined('BASEPATH')
    or exit('No direct script access allowed'); ?>

<?php

$days = [
    1=>'Lundi',
    2=>'Mardi',
    3=>'Mercredi',
    4=>'Jeudi',
    5=>'Vendredi',
    6=>'Samedi',
    7=>'Dimanche',
];

$statusLabels = [
    'todo'=>'À faire',
    'progress'=>'En cours',
    'waiting'=>'En attente',
    'done'=>'Terminé',
];

$priorityLabels = [
    'normal'=>'Normale',
    'high'=>'Importante',
    'urgent'=>'Urgente',
];

$today = date('Y-m-d');

$weekStart =
    date(
        'Y-m-d',
        strtotime('monday this week')
    );

$weekEnd =
    date(
        'Y-m-d',
        strtotime('saturday this week')
    );

$monthStart =
    date('Y-m-01');

$monthEnd =
    date('Y-m-t');

$threeEnd =
    date(
        'Y-m-d',
        strtotime('+3 months')
    );


function cmpItemsBetween(
    $items,
    $start,
    $end
) {

    return array_values(
        array_filter(
            $items,
            function($item) use (
                $start,
                $end
            ) {

                return
                    $item['plan_date'] >= $start
                    &&
                    $item['plan_date'] <= $end;
            }
        )
    );
}


function cmpGroupDate($items)
{
    $out = [];

    foreach ($items as $item) {

        $date = $item['plan_date'];

        if (!isset($out[$date])) {
            $out[$date] = [];
        }

        $out[$date][] = $item;
    }

    return $out;
}


$todayItems =
    cmpItemsBetween(
        $cmp_items,
        $today,
        $today
    );

$weekItems =
    cmpItemsBetween(
        $cmp_items,
        $weekStart,
        $weekEnd
    );

$monthItems =
    cmpItemsBetween(
        $cmp_items,
        $monthStart,
        $monthEnd
    );

$threeItems =
    cmpItemsBetween(
        $cmp_items,
        $today,
        $threeEnd
    );

$weekGrouped =
    cmpGroupDate($weekItems);

$monthGrouped =
    cmpGroupDate($monthItems);

$threeGrouped =
    cmpGroupDate($threeItems);

?>


<style>

/*
========================================================
COQUETTE HUB STYLE
Marketing widget only
========================================================
*/

#cmp-v3 {
    --hub-pink: #e91e63;
    --hub-pink-soft: #fff2f6;
    --hub-pink-border: #f6c8d7;

    --hub-text: #111827;
    --hub-muted: #6b7280;
    --hub-border: #e4e5e7;
    --hub-bg: #ffffff;
    --hub-page: #f7f7f9;

    background: transparent;
    color: var(--hub-text);

    border-radius: 0;
    overflow: visible;

    margin: 24px 0 30px;
    box-shadow: none;
}


/*
--------------------------------------------------------
HEADER
--------------------------------------------------------
*/

.cmp3-head {

    background: transparent;

    padding: 0 0 20px;

    display: flex;
    justify-content: space-between;
    align-items: flex-start;

    gap: 20px;

    flex-wrap: wrap;

    border-bottom: 0;
}


.cmp3-head > div:first-child:before {

    content: "MARKETING";

    display: block;

    color: var(--hub-pink);

    font-size: 12px;

    font-weight: 800;

    letter-spacing: .09em;

    margin-bottom: 5px;
}


.cmp3-head h2 {

    color: var(--hub-text);

    margin: 0;

    font-size: 30px;

    font-weight: 800;

    line-height: 1.15;

    text-transform: none;
}


.cmp3-head h2 i {

    display: none;
}


.cmp3-head > div:first-child > div {

    color: var(--hub-muted);

    font-size: 15px;

    margin-top: 7px;
}


.cmp3-head a,
.cmp3-head button {

    background: #fff;

    color: var(--hub-pink);

    border: 1px solid var(--hub-pink-border);

    padding: 9px 14px;

    border-radius: 10px;

    font-weight: 700;

    transition: .15s ease;
}


.cmp3-head a:hover,
.cmp3-head button:hover {

    background: var(--hub-pink-soft);

    color: var(--hub-pink);

    text-decoration: none;
}


/*
--------------------------------------------------------
BODY
--------------------------------------------------------
*/

.cmp3-body {

    padding: 0;
}


/*
--------------------------------------------------------
OBJECTIVES
--------------------------------------------------------
*/

.cmp3-objectives {

    display: grid;

    grid-template-columns:
        repeat(
            auto-fit,
            minmax(240px, 1fr)
        );

    gap: 18px;

    margin-bottom: 18px;
}


.cmp3-objective {

    position: relative;

    background: #fff;

    border: 1px solid var(--hub-border);

    padding: 20px;

    border-radius: 14px;

    min-height: 135px;

    box-shadow: none;
}


.cmp3-objective:before {

    content: "OBJECTIF";

    display: block;

    color: var(--hub-pink);

    font-size: 11px;

    font-weight: 800;

    letter-spacing: .08em;

    margin-bottom: 10px;
}


.cmp3-objective h4 {

    margin: 0 0 9px;

    color: var(--hub-text);

    font-size: 18px;

    line-height: 1.25;

    font-weight: 750;
}


.cmp3-objective > div {

    color: var(--hub-muted) !important;

    line-height: 1.55;
}


.cmp3-progress {

    height: 6px;

    background: #f3f4f6;

    border-radius: 20px;

    overflow: hidden;

    margin-top: 14px;
}


.cmp3-progress span {

    display: block;

    height: 100%;

    background: var(--hub-pink);
}


/*
--------------------------------------------------------
CURRENT PHASE
--------------------------------------------------------
*/

.cmp3-focus {

    position: relative;

    background: #fff;

    border: 1px solid var(--hub-border);

    border-left: 5px solid var(--hub-pink);

    padding: 18px 20px;

    margin-bottom: 18px;

    border-radius: 12px;

    color: var(--hub-text);

    box-shadow: none;
}


.cmp3-focus strong {

    display: block;

    color: var(--hub-text);

    font-size: 18px;

    margin-bottom: 5px;
}


.cmp3-focus br {

    display: none;
}


/*
--------------------------------------------------------
TABS
--------------------------------------------------------
*/

.cmp3-tabs {

    display: flex;

    gap: 9px;

    flex-wrap: wrap;

    margin: 20px 0 18px;
}


.cmp3-tab {

    background: #fff;

    color: #374151;

    border: 1px solid var(--hub-border);

    padding: 9px 15px;

    border-radius: 10px;

    cursor: pointer;

    font-weight: 650;

    transition: .15s ease;
}


.cmp3-tab:hover {

    background: #fafafa;

    border-color: #d7d7dc;
}


.cmp3-tab.active {

    background: var(--hub-pink-soft);

    border-color: var(--hub-pink-border);

    color: var(--hub-pink);
}


/*
--------------------------------------------------------
TASK CARDS
--------------------------------------------------------
*/

.cmp3-timeline {

    display: grid;

    gap: 10px;
}


.cmp3-task {

    background: #fff;

    border: 1px solid var(--hub-border);

    border-left: 4px solid var(--hub-pink);

    padding: 16px 18px;

    border-radius: 12px;

    color: var(--hub-text);

    box-shadow: none;
}


.cmp3-task:hover {

    border-color: #dedee4;
}


.cmp3-task.urgent {

    border-left-color: #dc2626;
}


.cmp3-task strong {

    color: var(--hub-text);

    font-size: 15px;
}


.cmp3-time {

    font-weight: 750;

    color: var(--hub-pink);

    margin-bottom: 4px;
}


.cmp3-meta {

    color: var(--hub-muted);

    font-size: 12px;

    margin-top: 5px;
}


/*
--------------------------------------------------------
WEEK TABLE
--------------------------------------------------------
*/

.cmp3-week {

    overflow: auto;

    border: 1px solid var(--hub-border);

    border-radius: 14px;

    background: #fff;
}


.cmp3-week-table {

    width: 100%;

    min-width: 950px;

    border-collapse: collapse;
}


.cmp3-week-table th {

    background: #fafafa;

    padding: 14px 12px;

    border-bottom: 1px solid var(--hub-border);

    border-right: 1px solid var(--hub-border);

    color: var(--hub-text);

    font-weight: 750;

    text-align: left;
}


.cmp3-week-table th:last-child {

    border-right: 0;
}


.cmp3-week-table td {

    background: #fff;

    color: var(--hub-text);

    padding: 10px;

    vertical-align: top;

    border-right: 1px solid var(--hub-border);
}


.cmp3-week-table td:last-child {

    border-right: 0;
}


.cmp3-day {

    min-width: 145px;
}


/*
--------------------------------------------------------
MONTH / 3 MONTHS
--------------------------------------------------------
*/

.cmp3-list-date {

    background: #fafafa;

    color: var(--hub-text);

    border: 1px solid var(--hub-border);

    padding: 10px 14px;

    margin-top: 14px;

    font-weight: 750;

    border-radius: 10px 10px 0 0;
}


.cmp3-list-date + .cmp3-task {

    border-radius: 0;
}


.cmp3-empty {

    color: var(--hub-muted);

    background: #fff;

    border: 1px solid var(--hub-border);

    border-radius: 12px;

    text-align: center;

    padding: 25px;
}


.cmp3-hours {

    color: var(--hub-muted);

    font-size: 11px;

    margin-top: 4px;
}


/*
--------------------------------------------------------
WALL MODE
--------------------------------------------------------
*/

#cmp-v3.cmp3-wall {

    position: fixed;

    inset: 0;

    z-index: 999999;

    margin: 0;

    padding: 30px;

    background: #f7f7f9;

    overflow: auto;

    border-radius: 0;
}


#cmp-v3.cmp3-wall .cmp3-head h2 {

    font-size: 34px;
}


/*
--------------------------------------------------------
RESPONSIVE
--------------------------------------------------------
*/

@media (max-width: 767px) {

    .cmp3-head {

        flex-direction: column;
    }


    .cmp3-head > div:last-child {

        width: 100%;
    }


    .cmp3-head a,
    .cmp3-head button {

        display: inline-block;

        margin-bottom: 5px;
    }


    .cmp3-head h2 {

        font-size: 25px;
    }


    .cmp3-objectives {

        grid-template-columns: 1fr;
    }
}



/*
========================================================
COQUETTE_MARKETING_TAB_VISIBILITY_FIX
Only the active temporal view is displayed.
========================================================
*/

.cmp3-view {
    display: none !important;
}

.cmp3-view.active {
    display: block !important;
}



/*
========================================================
COQUETTE_MARKETING_MONTH_CALENDAR_V2
HUB-style monthly calendar
========================================================
*/

.cmp3-month-scroll {
    overflow-x: auto;
    width: 100%;
    padding-bottom: 6px;
}

.cmp3-month-toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;

    background: #fff;

    border: 1px solid var(--hub-border);
    border-radius: 12px;

    padding: 14px 17px;
    margin-bottom: 12px;
}

.cmp3-month-toolbar strong {
    color: var(--hub-text);
    font-size: 18px;
}

.cmp3-month-toolbar span {
    color: var(--hub-muted);
    font-size: 13px;
}

.cmp3-month-calendar {
    display: grid;

    grid-template-columns:
        repeat(7, minmax(155px, 1fr));

    gap: 8px;

    min-width: 1120px;
}

.cmp3-month-header {
    background: #fafafa;

    border: 1px solid var(--hub-border);
    border-radius: 10px;

    padding: 9px;

    text-align: center;

    color: var(--hub-text);

    font-size: 12px;
    font-weight: 800;
}

.cmp3-month-cell {
    min-height: 185px;

    background: #fff;

    border: 1px solid var(--hub-border);
    border-radius: 12px;

    padding: 10px;

    overflow: hidden;
}

.cmp3-month-cell-empty {
    background: #fafafa;
    opacity: .55;
}

.cmp3-month-cell-sunday {
    background: #f5f5f6;
}

.cmp3-month-cell-today {
    border: 2px solid var(--hub-pink);
}

.cmp3-month-day {
    display: flex;
    align-items: center;
    justify-content: space-between;

    margin-bottom: 9px;

    color: var(--hub-text);

    font-weight: 800;
}

.cmp3-month-day-number {
    display: flex;
    align-items: center;
    justify-content: center;

    width: 29px;
    height: 29px;

    border-radius: 8px;

    background: #fafafa;
}

.cmp3-month-cell-today
.cmp3-month-day-number {
    background: var(--hub-pink-soft);
    color: var(--hub-pink);
}

.cmp3-month-count {
    color: var(--hub-muted);

    font-size: 10px;
    font-weight: 600;
}

.cmp3-month-closed {
    display: inline-block;

    background: #e5e7eb;
    color: #6b7280;

    border-radius: 20px;

    padding: 4px 8px;

    font-size: 10px;
    font-weight: 700;
}

.cmp3-month-events {
    display: flex;
    flex-direction: column;

    gap: 6px;
}

.cmp3-month-event {
    background: var(--hub-pink-soft);

    border-left: 3px solid var(--hub-pink);

    border-radius: 7px;

    padding: 6px 7px;
}

.cmp3-month-event-time {
    color: var(--hub-pink);

    font-size: 10px;
    font-weight: 800;
}

.cmp3-month-event-title {
    color: var(--hub-text);

    font-size: 11px;
    font-weight: 700;

    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;

    margin-top: 2px;
}

.cmp3-month-event-section {
    color: var(--hub-muted);

    font-size: 9px;

    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;

    margin-top: 1px;
}

.cmp3-month-more {
    display: inline-block;

    color: var(--hub-pink);

    font-size: 10px;
    font-weight: 800;

    margin-top: 3px;
}



/*
========================================================
COQUETTE_MARKETING_DAY_TOAST_V1
Calendar daily detail viewer
========================================================
*/

.cmp3-day-consult {
    width: 100%;

    display: flex;
    align-items: center;
    justify-content: center;

    gap: 5px;

    margin: 0 0 8px;

    padding: 5px 8px;

    border: 1px solid var(--hub-pink-border);
    border-radius: 7px;

    background: #fff;

    color: var(--hub-pink);

    font-size: 10px;
    font-weight: 750;

    cursor: pointer;

    transition:
        background .15s ease,
        border-color .15s ease;
}


.cmp3-day-consult:hover {
    background: var(--hub-pink-soft);
    border-color: var(--hub-pink);
}


.cmp3-day-toast {
    display: none;

    position: fixed;

    right: 24px;
    bottom: 24px;

    z-index: 1000000;

    width: 430px;
    max-width: calc(100vw - 40px);

    max-height: 72vh;

    background: #fff;

    border: 1px solid var(--hub-border);
    border-radius: 16px;

    box-shadow:
        0 18px 55px
        rgba(17,24,39,.18);

    overflow: hidden;
}


.cmp3-day-toast.active {
    display: block;
}


.cmp3-day-toast-head {
    display: flex;
    align-items: center;
    justify-content: space-between;

    gap: 12px;

    padding: 15px 16px;

    border-bottom:
        1px solid var(--hub-border);

    background: #fff;
}


.cmp3-day-toast-heading {
    min-width: 0;
}


.cmp3-day-toast-kicker {
    color: var(--hub-pink);

    font-size: 10px;
    font-weight: 800;

    letter-spacing: .08em;

    text-transform: uppercase;

    margin-bottom: 3px;
}


.cmp3-day-toast-title {
    color: var(--hub-text);

    font-size: 16px;
    font-weight: 800;

    line-height: 1.2;
}


.cmp3-day-toast-close {
    width: 32px;
    height: 32px;

    flex: 0 0 32px;

    display: flex;
    align-items: center;
    justify-content: center;

    border: 1px solid var(--hub-border);
    border-radius: 9px;

    background: #fff;

    color: var(--hub-muted);

    cursor: pointer;
}


.cmp3-day-toast-close:hover {
    background: #f7f7f9;
    color: var(--hub-text);
}


.cmp3-day-toast-body {
    padding: 12px;

    max-height: calc(72vh - 67px);

    overflow-y: auto;

    background: #fafafa;
}


.cmp3-day-toast-item {
    background: #fff;

    border:
        1px solid var(--hub-border);

    border-left:
        4px solid var(--hub-pink);

    border-radius: 10px;

    padding: 11px 12px;

    margin-bottom: 8px;
}


.cmp3-day-toast-item:last-child {
    margin-bottom: 0;
}


.cmp3-day-toast-time {
    color: var(--hub-pink);

    font-size: 12px;
    font-weight: 800;

    margin-bottom: 3px;
}


.cmp3-day-toast-item-title {
    color: var(--hub-text);

    font-size: 13px;
    font-weight: 750;

    line-height: 1.35;
}


.cmp3-day-toast-meta {
    display: flex;
    flex-wrap: wrap;

    gap: 5px;

    margin-top: 7px;
}


.cmp3-day-toast-chip {
    display: inline-flex;

    padding: 3px 7px;

    border-radius: 20px;

    background: #f4f4f6;

    color: var(--hub-muted);

    font-size: 10px;
    font-weight: 600;
}


.cmp3-day-toast-empty {
    padding: 25px 15px;

    text-align: center;

    color: var(--hub-muted);

    font-size: 13px;
}


@media (max-width: 600px) {

    .cmp3-day-toast {
        left: 12px;
        right: 12px;
        bottom: 12px;

        width: auto;
        max-width: none;

        max-height: 78vh;
    }

}



/*
========================================================
COQUETTE_MARKETING_MONTH_COMPACT_V1
Compact monthly dashboard
========================================================
*/

.cmp3-month-calendar {
    gap: 6px;
}


.cmp3-month-header {
    padding: 7px 6px;
}


.cmp3-month-cell {
    min-height: 118px;
    padding: 7px;
    border-radius: 10px;
}


.cmp3-month-day {
    margin-bottom: 5px;
}


.cmp3-month-day-number {
    width: 25px;
    height: 25px;
    font-size: 11px;
}


.cmp3-month-count {
    font-size: 9px;
}


.cmp3-day-consult {
    padding: 4px 6px;
    margin-bottom: 5px;

    font-size: 9px;

    min-height: 26px;
}


.cmp3-month-events {
    gap: 4px;
}


.cmp3-month-event {
    padding: 5px 6px;

    border-radius: 6px;
}


.cmp3-month-event-time {
    font-size: 9px;
}


.cmp3-month-event-title {
    font-size: 10px;
}


.cmp3-month-event-section {
    font-size: 8px;
}


.cmp3-month-more {
    margin-top: 2px;

    font-size: 10px;

    background: var(--hub-pink-soft);

    border-radius: 20px;

    padding: 3px 7px;
}


.cmp3-month-closed {
    padding: 3px 7px;

    font-size: 9px;
}

</style>



<div id="cmp-v3">


<div class="cmp3-head">

<div>

<h2>
<i class="fa fa-calendar"></i>
<?= _l('cqhub_marketing_title'); ?>
</h2>

<div>
<?= html_escape($cmp_plan['title']); ?>
</div>

</div>


<div>

<a href="<?= admin_url(
    'marketing_plan'
); ?>">
<i class="fa fa-cog"></i>
<?= _l('cqhub_manage'); ?>
</a>

<button
type="button"
onclick="cmp3Wall(this)"
>
<i class="fa fa-television"></i>
<?= _l('cqhub_screen_mode'); ?>
</button>

</div>

</div>


<div class="cmp3-body">


<div class="cmp3-objectives">

<?php foreach ($cmp_objectives as $o) { ?>

<?php

$progress = 0;

if (
    $o['target_value'] !== null
    &&
    (float)$o['target_value'] > 0
) {

    $progress = min(
        100,
        round(
            (
                (float)$o['current_value']
                /
                (float)$o['target_value']
            ) * 100
        )
    );
}

?>

<div class="cmp3-objective">

<h4>
<?= html_escape($o['title']); ?>
</h4>

<div style="color:#bbb;">
<?= html_escape($o['description']); ?>
</div>

<?php if ($o['target_value'] !== null) { ?>

<div class="cmp3-progress">
<span style="width:<?= $progress; ?>%"></span>
</div>

<div class="cmp3-meta">
<?= $progress; ?>%
—
<?= (float)$o['current_value']; ?>
/
<?= (float)$o['target_value']; ?>
<?= html_escape($o['unit']); ?>
</div>

<?php } ?>

</div>

<?php } ?>

</div>


<?php

$currentPhase = null;

foreach ($cmp_phases as $phase) {

    if (
        $today >= $phase['start_date']
        &&
        $today <= $phase['end_date']
    ) {

        $currentPhase = $phase;
        break;
    }
}

?>


<?php if ($currentPhase) { ?>

<div class="cmp3-focus">

<strong>
<?= html_escape($currentPhase['title']); ?>
</strong>

<br>

Focus :
<?= html_escape($currentPhase['focus']); ?>

</div>

<?php } ?>


<div class="cmp3-tabs">

<button
class="cmp3-tab active"
data-cmp3="today"
>
<?= _l('cqhub_today'); ?>
</button>

<button
class="cmp3-tab"
data-cmp3="week"
>
<?= _l('cqhub_this_week'); ?>
</button>

<button
class="cmp3-tab"
data-cmp3="month"
>
<?= _l('cqhub_this_month'); ?>
</button>

<button
class="cmp3-tab"
data-cmp3="quarter"
>
<?= _l('cqhub_three_months'); ?>
</button>

</div>


<div
class="cmp3-view active"
data-cmp3-view="today"
>

<?php if ((int)date('N') === 7) { ?>

<div class="cmp3-empty">
Dimanche — hors planning.
</div>

<?php } elseif (!$todayItems) { ?>

<div class="cmp3-empty">
<?= _l('cqhub_no_marketing_today'); ?>
</div>

<?php } else { ?>

<div class="cmp3-timeline">

<?php foreach ($todayItems as $item) { ?>

<div class="
cmp3-task
<?= $item['priority']==='urgent'
    ? 'urgent'
    : ''; ?>
">

<div class="cmp3-time">

<?= substr($item['start_time'],0,5); ?>
→
<?= substr($item['end_time'],0,5); ?>

</div>

<strong>
<?= html_escape($item['title']); ?>
</strong>

<div class="cmp3-meta">

<?= html_escape(
    $item['section_name']
); ?>

<?php if ($item['responsible']) { ?>
•
<?= html_escape(
    $item['responsible']
); ?>
<?php } ?>

•
<?= html_escape(
    $statusLabels[
        $item['status']
    ] ?? $item['status']
); ?>

</div>

</div>

<?php } ?>

</div>

<?php } ?>

</div>


<div
class="cmp3-view"
data-cmp3-view="week"
>

<div class="cmp3-week">

<table class="cmp3-week-table">

<thead>

<tr>

<?php

for ($i=0; $i<6; $i++) {

    $date = date(
        'Y-m-d',
        strtotime(
            $weekStart . " +{$i} day"
        )
    );

    $n = (int)date(
        'N',
        strtotime($date)
    );

?>

<th>

<?= $days[$n]; ?>

<br>

<small>
<?= date(
    'd/m',
    strtotime($date)
); ?>
</small>

<div class="cmp3-hours">

<?= $n === 6
    ? '08:30 → 14:00'
    : '08:30 → 17:30'; ?>

</div>

</th>

<?php } ?>

</tr>

</thead>


<tbody>

<tr>

<?php

for ($i=0; $i<6; $i++) {

    $date = date(
        'Y-m-d',
        strtotime(
            $weekStart . " +{$i} day"
        )
    );

    $rows =
        $weekGrouped[$date]
        ?? [];

?>

<td class="cmp3-day">

<?php if (!$rows) { ?>

<div class="cmp3-empty">
—
</div>

<?php } ?>


<?php foreach ($rows as $item) { ?>

<div
class="cmp3-task"
style="margin-bottom:7px;"
>

<div class="cmp3-time">

<?= substr(
    $item['start_time'],
    0,
    5
); ?>

</div>

<strong>
<?= html_escape(
    $item['title']
); ?>
</strong>

<div class="cmp3-meta">

<?= html_escape(
    $item['section_name']
); ?>

<?php if ($item['responsible']) { ?>
•
<?= html_escape(
    $item['responsible']
); ?>
<?php } ?>

</div>

</div>

<?php } ?>

</td>

<?php } ?>

</tr>

</tbody>

</table>

</div>

</div>


<div
class="cmp3-view"
data-cmp3-view="month"
>

<?php

$cmpMonthDate =
    new DateTime($monthStart);

$cmpMonthYear =
    (int)$cmpMonthDate->format('Y');

$cmpMonthNumber =
    (int)$cmpMonthDate->format('m');

$cmpMonthDays =
    (int)$cmpMonthDate->format('t');

$cmpFirstWeekday =
    (int)$cmpMonthDate->format('N');

$cmpTodayDate =
    date('Y-m-d');


$cmpFrenchMonths = [

    1  => 'Janvier',
    2  => 'Février',
    3  => 'Mars',
    4  => 'Avril',
    5  => 'Mai',
    6  => 'Juin',
    7  => 'Juillet',
    8  => 'Août',
    9  => 'Septembre',
    10 => 'Octobre',
    11 => 'Novembre',
    12 => 'Décembre',
];


$cmpDayHeaders = [

    'Lun',
    'Mar',
    'Mer',
    'Jeu',
    'Ven',
    'Sam',
    'Dim',
];


$cmpMonthLabel =
    $cmpFrenchMonths[
        $cmpMonthNumber
    ]
    . ' '
    . $cmpMonthYear;


$cmpTotalCells =
    ($cmpFirstWeekday - 1)
    + $cmpMonthDays;


$cmpTrailingCells =
    (7 - ($cmpTotalCells % 7))
    % 7;

?>


<div class="cmp3-month-toolbar">

    <strong>
        <?= html_escape(
            $cmpMonthLabel
        ); ?>
    </strong>

    <span>
        Lundi → Samedi ·
        <?= _l('cqhub_sunday_closed'); ?>
    </span>

</div>


<div class="cmp3-month-scroll">

<div class="cmp3-month-calendar">


<?php foreach (
    $cmpDayHeaders as $header
) { ?>

<div class="cmp3-month-header">
    <?= html_escape($header); ?>
</div>

<?php } ?>


<?php

for (
    $blank = 1;
    $blank < $cmpFirstWeekday;
    $blank++
) {

?>

<div
class="
cmp3-month-cell
cmp3-month-cell-empty
"
></div>

<?php } ?>


<?php

for (
    $day = 1;
    $day <= $cmpMonthDays;
    $day++
) {

    $cellDate =
        sprintf(
            '%04d-%02d-%02d',
            $cmpMonthYear,
            $cmpMonthNumber,
            $day
        );


    $weekday =
        (int)date(
            'N',
            strtotime($cellDate)
        );


    $isSunday =
        $weekday === 7;


    $isToday =
        $cellDate ===
        $cmpTodayDate;


    $rows =
        $monthGrouped[$cellDate]
        ?? [];


    $visibleRows =
        array_slice(
            $rows,
            0,
            1
        );


    $hiddenCount =
        max(
            0,
            count($rows) - 1
        );


    $cellClasses =
        'cmp3-month-cell';


    if ($isSunday) {

        $cellClasses .=
            ' cmp3-month-cell-sunday';
    }


    if ($isToday) {

        $cellClasses .=
            ' cmp3-month-cell-today';
    }

?>


<div
class="<?= $cellClasses; ?>"
>


<div class="cmp3-month-day">

    <span class="cmp3-month-day-number">
        <?= (int)$day; ?>
    </span>


    <?php if (
        !$isSunday
        && count($rows) > 0
    ) { ?>

    <span class="cmp3-month-count">

        <?= count($rows); ?>
        élément<?= count($rows) > 1
            ? 's'
            : ''; ?>

    </span>

    <?php } ?>

</div>



<?php

/*
 * Build complete day payload.
 * The calendar itself displays max 3 items,
 * while this payload contains ALL items.
 */

$cmpDayPayload = [];


foreach ($rows as $cmpDayRow) {

    $cmpDayPayload[] = [

        'start' =>
            substr(
                (string)(
                    $cmpDayRow['start_time']
                    ?? ''
                ),
                0,
                5
            ),

        'end' =>
            substr(
                (string)(
                    $cmpDayRow['end_time']
                    ?? ''
                ),
                0,
                5
            ),

        'title' =>
            (string)(
                $cmpDayRow['title']
                ?? ''
            ),

        'section' =>
            (string)(
                $cmpDayRow['section_name']
                ?? ''
            ),

        'status' =>
            (string)(
                $cmpDayRow['status']
                ?? ''
            ),

        'priority' =>
            (string)(
                $cmpDayRow['priority']
                ?? ''
            ),

        'responsible' =>
            (string)(
                $cmpDayRow['responsible']
                ?? ''
            ),

    ];
}


$cmpDayJson =
    json_encode(
        $cmpDayPayload,
        JSON_UNESCAPED_UNICODE
        |
        JSON_UNESCAPED_SLASHES
    );


$cmpDayLabel =
    ($days[$weekday] ?? '')
    . ' '
    . date(
        'd/m/Y',
        strtotime($cellDate)
    );

?>


<?php if (!$isSunday) { ?>

<button
type="button"
class="cmp3-day-consult"
data-cmp3-day-label="<?= html_escape(
    $cmpDayLabel
); ?>"
data-cmp3-day-items="<?= html_escape(
    $cmpDayJson
); ?>"
>

<i class="fa fa-eye"></i>

<?= _l('cqhub_consult'); ?>

<?php if (count($rows) > 0) { ?>

<span>
(<?= count($rows); ?>)
</span>

<?php } ?>

</button>

<?php } ?>


<?php if ($isSunday) { ?>

<span class="cmp3-month-closed">
    <?= _l('cqhub_closed'); ?>
</span>

<?php } else { ?>


<div class="cmp3-month-events">


<?php foreach (
    $visibleRows as $item
) { ?>


<div class="cmp3-month-event">

    <div class="cmp3-month-event-time">

        <?= substr(
            (string)$item['start_time'],
            0,
            5
        ); ?>

    </div>


    <div
    class="cmp3-month-event-title"
    title="<?= html_escape(
        $item['title']
    ); ?>"
    >

        <?= html_escape(
            $item['title']
        ); ?>

    </div>


    <div class="cmp3-month-event-section">

        <?= html_escape(
            $item['section_name']
            ?? ''
        ); ?>

    </div>

</div>


<?php } ?>


<?php if ($hiddenCount > 0) { ?>

<div class="cmp3-month-more">

    +<?= (int)$hiddenCount; ?>
    autre<?= $hiddenCount > 1
        ? 's'
        : ''; ?>

</div>

<?php } ?>


</div>


<?php } ?>


</div>


<?php } ?>


<?php

for (
    $blank = 0;
    $blank < $cmpTrailingCells;
    $blank++
) {

?>

<div
class="
cmp3-month-cell
cmp3-month-cell-empty
"
></div>

<?php } ?>


</div>

</div>

</div>




<!--
========================================================
COQUETTE MARKETING DAY TOAST
========================================================
-->

<div
class="cmp3-day-toast"
aria-hidden="true"
>

<div class="cmp3-day-toast-head">

    <div class="cmp3-day-toast-heading">

        <div class="cmp3-day-toast-kicker">
            <?= _l('cqhub_day_schedule'); ?>
        </div>

        <div class="cmp3-day-toast-title">
            Journée
        </div>

    </div>


    <button
    type="button"
    class="cmp3-day-toast-close"
    aria-label="Fermer"
    title="Fermer"
    >
        <i class="fa fa-times"></i>
    </button>

</div>


<div
class="cmp3-day-toast-body"
aria-live="polite"
>
</div>

</div>


<div
class="cmp3-view"
data-cmp3-view="quarter"
>

<?php if (!$threeGrouped) { ?>

<div class="cmp3-empty">
Aucun élément prévu sur les 3 prochains mois.
</div>

<?php } ?>


<?php foreach (
    $threeGrouped as
    $date => $rows
) { ?>

<?php

$n = (int)date(
    'N',
    strtotime($date)
);

if ($n === 7) {
    continue;
}

?>

<div class="cmp3-list-date">

<?= $days[$n]; ?>
<?= date(
    'd/m/Y',
    strtotime($date)
); ?>

</div>

<?php foreach ($rows as $item) { ?>

<div class="cmp3-task">

<strong>
<?= html_escape($item['title']); ?>
</strong>

<div class="cmp3-meta">

<?= substr($item['start_time'],0,5); ?>
•
<?= html_escape($item['section_name']); ?>

<?php if ($item['responsible']) { ?>
•
<?= html_escape($item['responsible']); ?>
<?php } ?>

</div>

</div>

<?php } ?>

<?php } ?>

</div>


</div>

</div>


<script>

/*
========================================================
COQUETTE MARKETING TAB CONTROLLER V2
Scoped per marketing widget
========================================================
*/

(function () {

    var widgets =
        document.querySelectorAll(
            '#cmp-v3'
        );


    widgets.forEach(function (root) {

        var tabs =
            root.querySelectorAll(
                '[data-cmp3]'
            );

        var views =
            root.querySelectorAll(
                '[data-cmp3-view]'
            );


        function activateView(scope) {

            tabs.forEach(
                function (tab) {

                    tab.classList.remove(
                        'active'
                    );

                    if (
                        tab.getAttribute(
                            'data-cmp3'
                        ) === scope
                    ) {

                        tab.classList.add(
                            'active'
                        );
                    }
                }
            );


            views.forEach(
                function (view) {

                    view.classList.remove(
                        'active'
                    );

                    if (
                        view.getAttribute(
                            'data-cmp3-view'
                        ) === scope
                    ) {

                        view.classList.add(
                            'active'
                        );
                    }
                }
            );


            try {

                sessionStorage.setItem(
                    'coquetteMarketingView',
                    scope
                );

            } catch (e) {
            }
        }


        tabs.forEach(
            function (tab) {

                tab.addEventListener(
                    'click',
                    function (event) {

                        event.preventDefault();

                        var scope =
                            tab.getAttribute(
                                'data-cmp3'
                            );

                        activateView(
                            scope
                        );
                    }
                );
            }
        );


        var initialView =
            'today';


        try {

            var saved =
                sessionStorage.getItem(
                    'coquetteMarketingView'
                );


            if (
                [
                    'today',
                    'week',
                    'month',
                    'quarter'
                ].indexOf(saved) !== -1
            ) {

                initialView =
                    saved;
            }

        } catch (e) {
        }


        activateView(
            initialView
        );

    });

})();


/*
========================================================
WALL MODE
========================================================
*/

function cmp3Wall(button)
{
    var board = null;


    if (
        button
        &&
        typeof button.closest === 'function'
    ) {

        board =
            button.closest(
                '#cmp-v3'
            );
    }


    if (!board) {

        board =
            document.querySelector(
                '#cmp-v3'
            );
    }


    if (!board) {
        return;
    }


    var entering =
        !board.classList.contains(
            'cmp3-wall'
        );


    board.classList.toggle(
        'cmp3-wall'
    );


    if (
        entering
        &&
        board.requestFullscreen
    ) {

        board
            .requestFullscreen()
            .catch(function () {});

    } else if (
        !entering
        &&
        document.fullscreenElement
    ) {

        document
            .exitFullscreen()
            .catch(function () {});
    }
}



/*
========================================================
CALENDAR DAY TOAST CONTROLLER
========================================================
*/

(function () {

    document
        .querySelectorAll('#cmp-v3')
        .forEach(function (root) {

            var toast =
                root.querySelector(
                    '.cmp3-day-toast'
                );


            if (!toast) {
                return;
            }


            var title =
                toast.querySelector(
                    '.cmp3-day-toast-title'
                );


            var body =
                toast.querySelector(
                    '.cmp3-day-toast-body'
                );


            var closeButton =
                toast.querySelector(
                    '.cmp3-day-toast-close'
                );


            function closeToast() {

                toast.classList.remove(
                    'active'
                );

                toast.setAttribute(
                    'aria-hidden',
                    'true'
                );
            }


            function addChip(
                container,
                value
            ) {

                if (!value) {
                    return;
                }


                var chip =
                    document.createElement(
                        'span'
                    );


                chip.className =
                    'cmp3-day-toast-chip';


                chip.textContent =
                    value;


                container.appendChild(
                    chip
                );
            }


            function showToast(button) {

                var label =
                    button.getAttribute(
                        'data-cmp3-day-label'
                    )
                    || 'Journée';


                var raw =
                    button.getAttribute(
                        'data-cmp3-day-items'
                    )
                    || '[]';


                var items = [];


                try {

                    items =
                        JSON.parse(raw);

                } catch (e) {

                    items = [];
                }


                title.textContent =
                    label;


                body.innerHTML =
                    '';


                if (!items.length) {

                    var empty =
                        document.createElement(
                            'div'
                        );


                    empty.className =
                        'cmp3-day-toast-empty';


                    empty.textContent =
                        'Aucun contenu marketing '
                        + 'planifié pour cette journée.';


                    body.appendChild(
                        empty
                    );

                } else {

                    items.forEach(
                        function (item) {

                            var card =
                                document.createElement(
                                    'div'
                                );


                            card.className =
                                'cmp3-day-toast-item';


                            var time =
                                document.createElement(
                                    'div'
                                );


                            time.className =
                                'cmp3-day-toast-time';


                            var timeValue =
                                item.start || '';


                            if (item.end) {

                                timeValue +=
                                    ' → '
                                    + item.end;
                            }


                            time.textContent =
                                timeValue;


                            card.appendChild(
                                time
                            );


                            var itemTitle =
                                document.createElement(
                                    'div'
                                );


                            itemTitle.className =
                                'cmp3-day-toast-item-title';


                            itemTitle.textContent =
                                item.title
                                || 'Sans titre';


                            card.appendChild(
                                itemTitle
                            );


                            var meta =
                                document.createElement(
                                    'div'
                                );


                            meta.className =
                                'cmp3-day-toast-meta';


                            addChip(
                                meta,
                                item.section
                            );


                            if (item.status) {

                                addChip(
                                    meta,
                                    'Statut : '
                                    + item.status
                                );
                            }


                            if (item.priority) {

                                addChip(
                                    meta,
                                    'Priorité : '
                                    + item.priority
                                );
                            }


                            if (item.responsible) {

                                addChip(
                                    meta,
                                    'Responsable : '
                                    + item.responsible
                                );
                            }


                            card.appendChild(
                                meta
                            );


                            body.appendChild(
                                card
                            );
                        }
                    );
                }


                toast.classList.add(
                    'active'
                );


                toast.setAttribute(
                    'aria-hidden',
                    'false'
                );
            }


            root
                .querySelectorAll(
                    '.cmp3-day-consult'
                )
                .forEach(
                    function (button) {

                        button.addEventListener(
                            'click',
                            function () {

                                showToast(
                                    button
                                );
                            }
                        );
                    }
                );


            if (closeButton) {

                closeButton.addEventListener(
                    'click',
                    closeToast
                );
            }


            /*
             * When user changes Today / Week /
             * Month / Quarter, close open toast.
             */

            root
                .querySelectorAll(
                    '[data-cmp3]'
                )
                .forEach(
                    function (tab) {

                        tab.addEventListener(
                            'click',
                            closeToast
                        );
                    }
                );


            document.addEventListener(
                'keydown',
                function (event) {

                    if (
                        event.key === 'Escape'
                    ) {

                        closeToast();
                    }
                }
            );

        });

})();

</script>
