<?php

defined('BASEPATH')
    or exit('No direct script access allowed');

init_head();

?>

<div id="wrapper">

<div class="content">

<div class="cq-team-todo">


    <header class="cqtt-header">

        <div>

            <div class="cqtt-kicker">
                <?= _l('cqhub_work'); ?>
            </div>

            <h1>
                <?= _l('cqhub_team_todo_title'); ?>
            </h1>

            <p>
                <?= _l('cqhub_team_todo_subtitle'); ?>
            </p>

        </div>

    </header>


    <!-- =================================================
         SUMMARY
    ================================================== -->

    <section class="cqtt-summary">


        <article>

            <span>
                <?= _l('cqhub_users_count'); ?>
            </span>

            <strong>
                <?= (int) $team_totals['users']; ?>
            </strong>

        </article>


        <article>

            <span>
                <?= _l('cqhub_total_todo'); ?>
            </span>

            <strong>
                <?= (int) $team_totals['total']; ?>
            </strong>

        </article>


        <article>

            <span>
                <?= _l('cqhub_todo_open'); ?>
            </span>

            <strong class="pink">
                <?= (int) $team_totals['open']; ?>
            </strong>

        </article>


        <article>

            <span>
                <?= _l('cqhub_todo_finished'); ?>
            </span>

            <strong>
                <?= (int) $team_totals['finished']; ?>
            </strong>

        </article>


    </section>


    <!-- =================================================
         FILTERS
    ================================================== -->

    <section class="cqtt-filters">

        <form
        method="get"
        action="<?= admin_url(
            'coquette_hub/team_todo'
        ); ?>"
        >


            <div class="cqtt-filter-field">

                <label for="cqttStaff">
                    <?= _l('cqhub_user'); ?>
                </label>

                <select
                id="cqttStaff"
                name="staff"
                >

                    <option value="0">
                        <?= _l('cqhub_all_users'); ?>
                    </option>

                    <?php foreach (
                        $staff_members as $member
                    ) { ?>

                    <option
                    value="<?= (int) $member['staffid']; ?>"
                    <?= (
                        (int) $selected_staff
                        ===
                        (int) $member['staffid']
                    )
                        ? 'selected'
                        : ''; ?>
                    >

                        <?= html_escape(
                            trim(
                                $member['firstname']
                                . ' '
                                . $member['lastname']
                            )
                        ); ?>

                    </option>

                    <?php } ?>

                </select>

            </div>


            <div class="cqtt-filter-field">

                <label for="cqttStatus">
                    <?= _l('cqhub_status'); ?>
                </label>

                <select
                id="cqttStatus"
                name="status"
                >

                    <option
                    value="all"
                    <?= $todo_status === 'all'
                        ? 'selected'
                        : ''; ?>
                    >
                        <?= _l('cqhub_all'); ?>
                    </option>

                    <option
                    value="open"
                    <?= $todo_status === 'open'
                        ? 'selected'
                        : ''; ?>
                    >
                        <?= _l('cqhub_todo_open'); ?>
                    </option>

                    <option
                    value="finished"
                    <?= $todo_status === 'finished'
                        ? 'selected'
                        : ''; ?>
                    >
                        <?= _l('cqhub_todo_finished'); ?>
                    </option>

                </select>

            </div>


            <div class="cqtt-filter-field cqtt-search">

                <label for="cqttSearch">
                    <?= _l('cqhub_search'); ?>
                </label>

                <input
                id="cqttSearch"
                type="text"
                name="q"
                value="<?= html_escape(
                    $todo_search
                ); ?>"
                placeholder="<?= html_escape(_l('cqhub_search_todo')); ?>"
                >

            </div>


            <div class="cqtt-filter-action">

                <button
                type="submit"
                >
                    <i class="fa-solid fa-filter"></i>
                    <?= _l('cqhub_filter'); ?>
                </button>

            </div>


            <div class="cqtt-filter-reset">

                <a
                href="<?= admin_url(
                    'coquette_hub/team_todo'
                ); ?>"
                >
                    <?= _l('cqhub_reset'); ?>
                </a>

            </div>


        </form>

    </section>


    <!-- =================================================
         USERS
    ================================================== -->

    <section class="cqtt-users">


    <?php foreach (
        $staff_members as $member
    ) {


        $staffId =
            (int) $member['staffid'];


        if (
            (int) $selected_staff > 0
            &&
            (int) $selected_staff
            !==
            $staffId
        ) {

            continue;
        }


        $todos =
            $todos_by_staff[$staffId]
            ?? [];


        $counter =
            $todo_counts[$staffId]
            ?? [
                'total' => 0,
                'open' => 0,
                'finished' => 0,
            ];


        $fullname =
            trim(
                $member['firstname']
                . ' '
                . $member['lastname']
            );


        $initials =
            mb_strtoupper(
                mb_substr(
                    trim(
                        $member['firstname']
                    ),
                    0,
                    1
                )
                .
                mb_substr(
                    trim(
                        $member['lastname']
                    ),
                    0,
                    1
                )
            );


        $openByDefault =
            (
                count($todos) > 0
                ||
                (int) $selected_staff === $staffId
            );

    ?>


        <details
        class="cqtt-user"
        <?= $openByDefault
            ? 'open'
            : ''; ?>
        >


            <summary class="cqtt-user-head">


                <div class="cqtt-user-main">


                    <span class="cqtt-avatar">

                        <?= html_escape(
                            $initials
                        ); ?>

                    </span>


                    <div>

                        <strong>
                            <?= html_escape(
                                $fullname
                            ); ?>
                        </strong>


                        <div class="cqtt-user-meta">

                            <span>
                                <?= html_escape(
                                    $member['role_name']
                                    ?: _l('cqhub_user_fallback')
                                ); ?>
                            </span>


                            <?php if (
                                !(int) $member['active']
                            ) { ?>

                            <span class="inactive">
                                Inactif
                            </span>

                            <?php } ?>

                        </div>

                    </div>


                </div>


                <div class="cqtt-user-counts">

                    <span class="open">

                        <?= (int) $counter['open']; ?>
                        à faire

                    </span>

                    <span>

                        <?= (int) $counter['finished']; ?>
                        terminée<?= (
                            (int) $counter['finished']
                            !== 1
                        )
                            ? 's'
                            : ''; ?>

                    </span>

                </div>


            </summary>


            <div class="cqtt-user-body">


                <?php if (
                    count($todos) === 0
                ) { ?>


                    <div class="cqtt-empty">

                        <i class="fa-regular fa-circle-check"></i>

                        <span>
                            <?= _l('cqhub_todo_no_result'); ?>
                        </span>

                    </div>


                <?php } else { ?>


                    <?php foreach (
                        $todos as $todo
                    ) {


                        $finished =
                            (int) $todo['finished']
                            === 1;

                    ?>


                    <article
                    class="cqtt-todo <?= $finished
                        ? 'done'
                        : 'open'; ?>"
                    >


                        <div class="cqtt-status-icon">

                            <?php if ($finished) { ?>

                            <i class="fa-solid fa-check"></i>

                            <?php } else { ?>

                            <i class="fa-regular fa-circle"></i>

                            <?php } ?>

                        </div>


                        <div class="cqtt-todo-main">


                            <div class="cqtt-description">

                                <?= process_text_content_for_display(
                                    $todo['description']
                                ); ?>

                            </div>


                            <div class="cqtt-date">

                                <?php if ($finished) { ?>

                                    <?= _l('cqhub_finished_on'); ?>
                                    <?= html_escape(
                                        _dt(
                                            $todo['datefinished']
                                        )
                                    ); ?>

                                <?php } else { ?>

                                    <?= _l('cqhub_added_on'); ?>
                                    <?= html_escape(
                                        _dt(
                                            $todo['dateadded']
                                        )
                                    ); ?>

                                <?php } ?>

                            </div>


                        </div>


                        <span class="cqtt-status">

                            <?= $finished
                                ? _l('cqhub_todo_singular_finished')
                                : _l('cqhub_todo_singular_open'); ?>

                        </span>


                    </article>


                    <?php } ?>


                <?php } ?>


            </div>


        </details>


    <?php } ?>


    </section>


</div>

</div>

</div>


<style>

/*
========================================================
COQUETTE HUB - TEAM TODO V1
========================================================
*/

.cq-team-todo {
    max-width: 1500px;
    margin: 0 auto;
    color: #18181b;
}


.cqtt-header {
    display: flex;
    align-items: flex-end;
    justify-content: space-between;

    margin-bottom: 22px;
}


.cqtt-kicker {
    color: #e91e63;

    font-size: 11px;
    font-weight: 800;

    letter-spacing: .11em;

    margin-bottom: 5px;
}


.cqtt-header h1 {
    margin: 0;

    color: #18181b;

    font-size: 30px;
    font-weight: 800;
}


.cqtt-header p {
    margin: 5px 0 0;

    color: #71717a;

    font-size: 13px;
}


/*
SUMMARY
*/

.cqtt-summary {
    display: grid;

    grid-template-columns:
        repeat(4, minmax(0, 1fr));

    gap: 12px;

    margin-bottom: 15px;
}


.cqtt-summary article {
    background: #fff;

    border: 1px solid #e4e4e7;
    border-radius: 14px;

    padding: 15px;
}


.cqtt-summary span {
    display: block;

    color: #71717a;

    font-size: 11px;
    font-weight: 600;

    margin-bottom: 5px;
}


.cqtt-summary strong {
    color: #18181b;

    font-size: 24px;
    font-weight: 800;
}


.cqtt-summary strong.pink {
    color: #e91e63;
}


/*
FILTERS
*/

.cqtt-filters {
    background: #fff;

    border: 1px solid #e4e4e7;
    border-radius: 14px;

    padding: 13px;

    margin-bottom: 15px;
}


.cqtt-filters form {
    display: flex;
    align-items: flex-end;
    flex-wrap: wrap;

    gap: 10px;
}


.cqtt-filter-field {
    width: 220px;
}


.cqtt-filter-field.cqtt-search {
    flex: 1;
    min-width: 240px;
}


.cqtt-filter-field label {
    display: block;

    color: #52525b;

    font-size: 10px;
    font-weight: 700;

    margin-bottom: 5px;
}


.cqtt-filter-field select,
.cqtt-filter-field input {
    width: 100%;
    height: 38px;

    border: 1px solid #e4e4e7;
    border-radius: 9px;

    background: #fff;

    padding: 0 10px;

    color: #27272a;

    outline: none;
}


.cqtt-filter-field select:focus,
.cqtt-filter-field input:focus {
    border-color: #e91e63;
}


.cqtt-filter-action button {
    height: 38px;

    border: 0;
    border-radius: 9px;

    background: #e91e63;

    color: #fff;

    padding: 0 15px;

    font-size: 12px;
    font-weight: 700;
}


.cqtt-filter-reset {
    height: 38px;

    display: flex;
    align-items: center;
}


.cqtt-filter-reset a {
    color: #71717a;

    font-size: 11px;
}


/*
USERS
*/

.cqtt-users {
    display: flex;
    flex-direction: column;

    gap: 10px;
}


.cqtt-user {
    background: #fff;

    border: 1px solid #e4e4e7;
    border-radius: 14px;

    overflow: hidden;
}


.cqtt-user-head {
    display: flex;
    align-items: center;
    justify-content: space-between;

    gap: 15px;

    padding: 13px 15px;

    cursor: pointer;

    list-style: none;
}


.cqtt-user-head::-webkit-details-marker {
    display: none;
}


.cqtt-user-main {
    display: flex;
    align-items: center;

    gap: 10px;
}


.cqtt-avatar {
    width: 38px;
    height: 38px;

    display: flex;
    align-items: center;
    justify-content: center;

    flex: 0 0 38px;

    border-radius: 11px;

    background: #fff1f6;

    color: #e91e63;

    font-size: 12px;
    font-weight: 800;
}


.cqtt-user-main strong {
    display: block;

    color: #18181b;

    font-size: 13px;
}


.cqtt-user-meta {
    display: flex;
    gap: 6px;

    margin-top: 3px;

    color: #71717a;

    font-size: 10px;
}


.cqtt-user-meta .inactive {
    color: #dc2626;
}


.cqtt-user-counts {
    display: flex;
    align-items: center;

    gap: 7px;
}


.cqtt-user-counts span {
    display: inline-flex;

    border-radius: 999px;

    background: #f4f4f5;

    color: #52525b;

    padding: 4px 8px;

    font-size: 10px;
    font-weight: 700;
}


.cqtt-user-counts span.open {
    background: #fff1f6;
    color: #e91e63;
}


.cqtt-user-body {
    border-top: 1px solid #f1f1f3;

    background: #fafafa;

    padding: 9px;
}


/*
TODO ITEM
*/

.cqtt-todo {
    display: flex;
    align-items: flex-start;

    gap: 10px;

    background: #fff;

    border: 1px solid #e4e4e7;
    border-radius: 10px;

    padding: 10px 11px;

    margin-bottom: 6px;
}


.cqtt-todo:last-child {
    margin-bottom: 0;
}


.cqtt-todo.open {
    border-left: 3px solid #e91e63;
}


.cqtt-todo.done {
    opacity: .72;
}


.cqtt-status-icon {
    width: 22px;

    flex: 0 0 22px;

    color: #e91e63;

    padding-top: 1px;
}


.cqtt-todo.done
.cqtt-status-icon {
    color: #16a34a;
}


.cqtt-todo-main {
    flex: 1;
    min-width: 0;
}


.cqtt-description {
    color: #27272a;

    font-size: 12px;
    font-weight: 600;

    line-height: 1.45;
}


.cqtt-todo.done
.cqtt-description {
    text-decoration: line-through;

    color: #71717a;
}


.cqtt-date {
    margin-top: 4px;

    color: #a1a1aa;

    font-size: 9px;
}


.cqtt-status {
    flex: 0 0 auto;

    border-radius: 999px;

    background: #fff1f6;

    color: #e91e63;

    padding: 4px 7px;

    font-size: 9px;
    font-weight: 700;
}


.cqtt-todo.done
.cqtt-status {
    background: #f0fdf4;
    color: #15803d;
}


.cqtt-empty {
    display: flex;
    align-items: center;
    justify-content: center;

    gap: 7px;

    padding: 24px;

    color: #a1a1aa;

    font-size: 11px;
}


@media (max-width: 900px) {

    .cqtt-summary {
        grid-template-columns:
            repeat(2, minmax(0, 1fr));
    }

}


@media (max-width: 650px) {

    .cqtt-summary {
        grid-template-columns: 1fr;
    }


    .cqtt-filter-field,
    .cqtt-filter-field.cqtt-search {

        width: 100%;
        min-width: 100%;
    }


    .cqtt-filter-action {
        width: 100%;
    }


    .cqtt-filter-action button {
        width: 100%;
    }


    .cqtt-user-head {
        align-items: flex-start;
        flex-direction: column;
    }


    .cqtt-user-counts {
        margin-left: 48px;
    }


    .cqtt-status {
        display: none;
    }

}

</style>


<?php init_tail(); ?>
