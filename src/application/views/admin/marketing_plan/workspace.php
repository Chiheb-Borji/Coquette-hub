<?php defined('BASEPATH')
    or exit('No direct script access allowed'); ?>

<?php init_head(); ?>

<style>

.mp-head {
    background:linear-gradient(
        90deg,
        #da1c5c,
        #dc2b31
    );
    color:white;
    padding:22px;
    border-radius:8px;
    margin-bottom:20px;
}

.mp-head h2 {
    color:white;
    margin:0;
    font-weight:800;
}

.mp-card {
    border:1px solid #e4e5e7;
    border-radius:7px;
    padding:15px;
    margin-bottom:12px;
    background:#fff;
}

.mp-card h4 {
    margin-top:0;
}

.mp-section-title {
    font-weight:700;
    margin:5px 0 15px;
}

.mp-badge-active {
    background:#28a745;
    color:#fff;
    padding:3px 8px;
    border-radius:12px;
}

.mp-working {
    background:#18192b;
    color:#fff;
    padding:15px;
    border-radius:7px;
    line-height:1.9;
}

</style>


<div id="wrapper">

<div class="content">


<div class="mp-head">

<h2>
<i class="fa fa-calendar"></i>
Plan Marketing Coquette.tn
</h2>

<div style="margin-top:6px;">
Marketing Workspace —
Objectifs, phases, contenu et actions
</div>

</div>


<div class="row">

<div class="col-md-4">

<div class="mp-working">

<strong>
HORAIRES ÉQUIPE MARKETING
</strong>

<br>

Lundi — Vendredi :
08:30 → 17:30

<br>

Samedi :
08:30 → 14:00

<br>

Dimanche :
Fermé

</div>

</div>


<div class="col-md-8">

<div class="panel_s">

<div class="panel-body">

<h4 class="mp-section-title">
Plans Marketing
</h4>

<div class="table-responsive">

<table class="table table-striped">

<thead>
<tr>
<th>Plan</th>
<th>Période</th>
<th>État</th>
<th></th>
</tr>
</thead>

<tbody>

<?php foreach ($plans as $pl) { ?>

<tr>

<td>
<a href="<?= admin_url(
    'marketing_plan?plan_id='
    . (int)$pl['id']
); ?>">
<strong>
<?= html_escape($pl['title']); ?>
</strong>
</a>
</td>

<td>
<?= date(
    'd/m/Y',
    strtotime($pl['start_date'])
); ?>
→
<?= date(
    'd/m/Y',
    strtotime($pl['end_date'])
); ?>
</td>

<td>
<?php if ($pl['status'] === 'active') { ?>
<span class="mp-badge-active">
Actif
</span>
<?php } else { ?>
<?= html_escape($pl['status']); ?>
<?php } ?>
</td>

<td class="text-right">

<a
 class="btn btn-default btn-xs"
 href="<?= admin_url(
    'marketing_plan?plan_id='
    . (int)$pl['id']
    . '&edit_plan='
    . (int)$pl['id']
); ?>"
>
<i class="fa fa-pencil"></i>
</a>

<a
 class="btn btn-success btn-xs"
 href="<?= admin_url(
    'marketing_plan/activate_plan/'
    . (int)$pl['id']
); ?>"
>
<i class="fa fa-check"></i>
</a>

<a
 class="btn btn-danger btn-xs"
 onclick="return confirm('Supprimer ce plan ?');"
 href="<?= admin_url(
    'marketing_plan/delete_plan/'
    . (int)$pl['id']
); ?>"
>
<i class="fa fa-trash"></i>
</a>

</td>

</tr>

<?php } ?>

</tbody>

</table>

</div>


<?php

$pp = $edit_plan ?: [
    'id'=>'',
    'title'=>'',
    'description'=>'',
    'start_date'=>date('Y-m-01'),
    'end_date'=>date('Y-m-t'),
];

?>

<hr>

<?= form_open(
    admin_url('marketing_plan/save_plan')
); ?>

<input
type="hidden"
name="id"
value="<?= html_escape($pp['id']); ?>"
>

<div class="row">

<div class="col-md-4">
<input
name="title"
required
class="form-control"
placeholder="Nom du plan"
value="<?= html_escape($pp['title']); ?>"
>
</div>

<div class="col-md-3">
<input
type="date"
name="start_date"
required
class="form-control"
value="<?= html_escape($pp['start_date']); ?>"
>
</div>

<div class="col-md-3">
<input
type="date"
name="end_date"
required
class="form-control"
value="<?= html_escape($pp['end_date']); ?>"
>
</div>

<div class="col-md-2">
<button class="btn btn-info btn-block">
<i class="fa fa-save"></i>
Enregistrer
</button>
</div>

</div>

<div style="margin-top:10px;">

<textarea
name="description"
class="form-control"
rows="2"
placeholder="Description"
><?= html_escape($pp['description']); ?></textarea>

</div>

<?= form_close(); ?>

</div>

</div>

</div>

</div>


<?php if ($plan) { ?>

<div class="row">


<div class="col-md-12">

<div class="panel_s">

<div class="panel-body">

<h3 style="margin-top:0;">
<?= html_escape($plan['title']); ?>
</h3>

<p class="text-muted">
<?= html_escape($plan['description']); ?>
</p>

</div>

</div>

</div>

</div>


<div class="row">


<div class="col-md-6">

<div class="panel_s">
<div class="panel-body">

<h4 class="mp-section-title">
🎯 Objectifs du plan
</h4>

<?php foreach ($objectives as $o) { ?>

<div class="mp-card">

<strong>
<?= html_escape($o['title']); ?>
</strong>

<p>
<?= html_escape($o['description']); ?>
</p>

<?php if ($o['target_value'] !== null) { ?>

<small>
<?= (float)$o['current_value']; ?>
/
<?= (float)$o['target_value']; ?>
<?= html_escape($o['unit']); ?>
</small>

<?php } ?>

<div class="pull-right">

<a href="<?= admin_url(
    'marketing_plan?plan_id='
    . (int)$plan['id']
    . '&edit_objective='
    . (int)$o['id']
); ?>">
<i class="fa fa-pencil"></i>
</a>

&nbsp;

<a
onclick="return confirm('Supprimer cet objectif ?');"
href="<?= admin_url(
    'marketing_plan/delete_objective/'
    . (int)$o['id']
); ?>"
>
<i class="fa fa-trash text-danger"></i>
</a>

</div>

</div>

<?php } ?>


<?php

$eo = $edit_objective ?: [
    'id'=>'',
    'title'=>'',
    'description'=>'',
    'target_value'=>'',
    'current_value'=>0,
    'unit'=>'',
    'position'=>10,
    'active'=>1,
];

?>

<hr>

<?= form_open(
    admin_url(
        'marketing_plan/save_objective'
    )
); ?>

<input type="hidden"
name="id"
value="<?= html_escape($eo['id']); ?>">

<input type="hidden"
name="plan_id"
value="<?= (int)$plan['id']; ?>">

<div class="form-group">
<input
class="form-control"
required
name="title"
placeholder="Objectif"
value="<?= html_escape($eo['title']); ?>"
>
</div>

<div class="form-group">
<textarea
class="form-control"
name="description"
placeholder="Description"
><?= html_escape($eo['description']); ?></textarea>
</div>

<div class="row">

<div class="col-md-3">
<input
type="number"
step="0.01"
class="form-control"
name="current_value"
placeholder="Actuel"
value="<?= html_escape($eo['current_value']); ?>"
>
</div>

<div class="col-md-3">
<input
type="number"
step="0.01"
class="form-control"
name="target_value"
placeholder="Cible"
value="<?= html_escape($eo['target_value']); ?>"
>
</div>

<div class="col-md-3">
<input
class="form-control"
name="unit"
placeholder="Unité"
value="<?= html_escape($eo['unit']); ?>"
>
</div>

<div class="col-md-3">
<input
type="number"
class="form-control"
name="position"
value="<?= (int)$eo['position']; ?>"
>
</div>

</div>

<div style="margin-top:10px;">

<label>
<input
type="checkbox"
name="active"
value="1"
<?= (int)$eo['active'] ? 'checked' : ''; ?>
>
Actif
</label>

&nbsp;

<button class="btn btn-info btn-sm">
Enregistrer objectif
</button>

</div>

<?= form_close(); ?>

</div>
</div>

</div>


<div class="col-md-6">

<div class="panel_s">
<div class="panel-body">

<h4 class="mp-section-title">
📅 Phases / Semaines
</h4>

<?php foreach ($phases as $ph) { ?>

<div class="mp-card">

<strong>
<?= html_escape($ph['title']); ?>
</strong>

<br>

<small>
<?= date('d/m', strtotime($ph['start_date'])); ?>
→
<?= date('d/m', strtotime($ph['end_date'])); ?>
</small>

<p style="margin-top:7px;">
<?= html_escape($ph['focus']); ?>
</p>

<div class="pull-right">

<a href="<?= admin_url(
    'marketing_plan?plan_id='
    . (int)$plan['id']
    . '&edit_phase='
    . (int)$ph['id']
); ?>">
<i class="fa fa-pencil"></i>
</a>

&nbsp;

<a
onclick="return confirm('Supprimer cette phase ?');"
href="<?= admin_url(
    'marketing_plan/delete_phase/'
    . (int)$ph['id']
); ?>"
>
<i class="fa fa-trash text-danger"></i>
</a>

</div>

</div>

<?php } ?>


<?php

$ep = $edit_phase ?: [
    'id'=>'',
    'title'=>'',
    'focus'=>'',
    'start_date'=>$plan['start_date'],
    'end_date'=>$plan['end_date'],
    'status'=>'todo',
    'position'=>10,
];

?>

<hr>

<?= form_open(
    admin_url(
        'marketing_plan/save_phase'
    )
); ?>

<input type="hidden"
name="id"
value="<?= html_escape($ep['id']); ?>">

<input type="hidden"
name="plan_id"
value="<?= (int)$plan['id']; ?>">

<div class="form-group">
<input
class="form-control"
required
name="title"
placeholder="Nom de phase"
value="<?= html_escape($ep['title']); ?>"
>
</div>

<div class="form-group">
<textarea
class="form-control"
name="focus"
placeholder="Focus"
><?= html_escape($ep['focus']); ?></textarea>
</div>

<div class="row">

<div class="col-md-3">
<input
type="date"
required
class="form-control"
name="start_date"
value="<?= html_escape($ep['start_date']); ?>"
>
</div>

<div class="col-md-3">
<input
type="date"
required
class="form-control"
name="end_date"
value="<?= html_escape($ep['end_date']); ?>"
>
</div>

<div class="col-md-3">
<select name="status" class="form-control">

<?php foreach ([
    'todo'=>'À faire',
    'progress'=>'En cours',
    'done'=>'Terminé'
] as $k=>$v) { ?>

<option
value="<?= $k; ?>"
<?= $ep['status']===$k ? 'selected' : ''; ?>
>
<?= $v; ?>
</option>

<?php } ?>

</select>
</div>

<div class="col-md-3">
<input
type="number"
class="form-control"
name="position"
value="<?= (int)$ep['position']; ?>"
>
</div>

</div>

<div style="margin-top:10px;">
<button class="btn btn-info btn-sm">
Enregistrer phase
</button>
</div>

<?= form_close(); ?>

</div>
</div>

</div>


</div>


<div class="row">


<div class="col-md-5">

<div class="panel_s">
<div class="panel-body">

<h4 class="mp-section-title">
🧩 Sections du planning
</h4>

<table class="table table-striped">

<thead>
<tr>
<th>Section</th>
<th>Objectif</th>
<th></th>
</tr>
</thead>

<tbody>

<?php foreach ($sections as $s) { ?>

<tr>

<td>
<i class="<?= html_escape($s['icon']); ?>"></i>
<?= html_escape($s['name']); ?>

<?php if (!(int)$s['active']) { ?>
<small class="text-muted">
(inactive)
</small>
<?php } ?>

</td>

<td>
<?= (int)$s['daily_target']; ?>/jour
</td>

<td class="text-right">

<a href="<?= admin_url(
    'marketing_plan?plan_id='
    . (int)$plan['id']
    . '&edit_section='
    . (int)$s['id']
); ?>">
<i class="fa fa-pencil"></i>
</a>

&nbsp;

<a href="<?= admin_url(
    'marketing_plan/toggle_section/'
    . (int)$s['id']
); ?>">
<i class="fa fa-power-off"></i>
</a>

&nbsp;

<a
onclick="return confirm('Supprimer cette section ?');"
href="<?= admin_url(
    'marketing_plan/delete_section/'
    . (int)$s['id']
); ?>"
>
<i class="fa fa-trash text-danger"></i>
</a>

</td>

</tr>

<?php } ?>

</tbody>

</table>


<?php

$es = $edit_section ?: [
    'id'=>'',
    'name'=>'',
    'icon'=>'fa fa-folder',
    'daily_target'=>1,
    'position'=>10,
    'active'=>1,
];

?>

<hr>

<?= form_open(
    admin_url(
        'marketing_plan/save_section'
    )
); ?>

<input type="hidden"
name="id"
value="<?= html_escape($es['id']); ?>">

<div class="form-group">
<input
required
class="form-control"
name="name"
placeholder="Nom section"
value="<?= html_escape($es['name']); ?>"
>
</div>

<div class="row">

<div class="col-md-4">
<input
class="form-control"
name="icon"
placeholder="fa fa-folder"
value="<?= html_escape($es['icon']); ?>"
>
</div>

<div class="col-md-3">
<input
type="number"
min="0"
max="50"
class="form-control"
name="daily_target"
value="<?= (int)$es['daily_target']; ?>"
>
</div>

<div class="col-md-3">
<input
type="number"
class="form-control"
name="position"
value="<?= (int)$es['position']; ?>"
>
</div>

<div class="col-md-2">
<label>
<input
type="checkbox"
name="active"
value="1"
<?= (int)$es['active'] ? 'checked' : ''; ?>
>
Actif
</label>
</div>

</div>

<div style="margin-top:10px;">
<button class="btn btn-info btn-sm">
Enregistrer section
</button>
</div>

<?= form_close(); ?>

</div>
</div>

</div>


<div class="col-md-7">

<div class="panel_s">

<div class="panel-body">

<h4 class="mp-section-title">
➕ Ajouter au planning
</h4>


<?php

$ei = $edit_item ?: [
    'id'=>'',
    'phase_id'=>'',
    'section_id'=>'',
    'item_type'=>'content',
    'plan_date'=>date('Y-m-d'),
    'start_time'=>'08:30:00',
    'end_time'=>'09:30:00',
    'title'=>'',
    'responsible'=>'',
    'priority'=>'normal',
    'status'=>'todo',
    'notes'=>'',
];

?>


<?= form_open(
    admin_url(
        'marketing_plan/save_item'
    )
); ?>

<input type="hidden"
name="id"
value="<?= html_escape($ei['id']); ?>">

<input type="hidden"
name="plan_id"
value="<?= (int)$plan['id']; ?>">


<div class="row">

<div class="col-md-4">

<div class="form-group">

<label>Date</label>

<input
type="date"
name="plan_date"
required
min="<?= html_escape($plan['start_date']); ?>"
max="<?= html_escape($plan['end_date']); ?>"
class="form-control"
value="<?= html_escape($ei['plan_date']); ?>"
>

</div>

</div>


<div class="col-md-4">

<div class="form-group">

<label>Début</label>

<input
type="time"
name="start_time"
required
step="900"
class="form-control"
value="<?= substr(
    (string)$ei['start_time'],
    0,
    5
); ?>"
>

</div>

</div>


<div class="col-md-4">

<div class="form-group">

<label>Fin</label>

<input
type="time"
name="end_time"
required
step="900"
class="form-control"
value="<?= substr(
    (string)$ei['end_time'],
    0,
    5
); ?>"
>

</div>

</div>

</div>


<div class="row">


<div class="col-md-4">

<div class="form-group">

<label>Section</label>

<select
required
name="section_id"
class="form-control"
>

<option value="">
Choisir
</option>

<?php foreach ($sections as $s) { ?>

<?php
if (
    !(int)$s['active']
    && (int)$s['id']
       !== (int)$ei['section_id']
) {
    continue;
}
?>

<option
value="<?= (int)$s['id']; ?>"
<?= (int)$ei['section_id']===(int)$s['id']
    ? 'selected'
    : ''; ?>
>
<?= html_escape($s['name']); ?>
</option>

<?php } ?>

</select>

</div>

</div>


<div class="col-md-4">

<div class="form-group">

<label>Phase</label>

<select
name="phase_id"
class="form-control"
>

<option value="">
Aucune
</option>

<?php foreach ($phases as $ph) { ?>

<option
value="<?= (int)$ph['id']; ?>"
<?= (int)$ei['phase_id']===(int)$ph['id']
    ? 'selected'
    : ''; ?>
>
<?= html_escape($ph['title']); ?>
</option>

<?php } ?>

</select>

</div>

</div>


<div class="col-md-4">

<div class="form-group">

<label>Type</label>

<select
name="item_type"
class="form-control"
>

<option
value="content"
<?= $ei['item_type']==='content'
    ? 'selected'
    : ''; ?>
>
Contenu
</option>

<option
value="action"
<?= $ei['item_type']==='action'
    ? 'selected'
    : ''; ?>
>
Action Marketing
</option>

</select>

</div>

</div>


</div>


<div class="form-group">

<label>Tâche / contenu</label>

<input
required
class="form-control"
name="title"
placeholder="Ex: Filmer Reel Pack BAC 2027"
value="<?= html_escape($ei['title']); ?>"
>

</div>


<div class="row">

<div class="col-md-4">

<input
class="form-control"
name="responsible"
placeholder="Responsable"
value="<?= html_escape($ei['responsible']); ?>"
>

</div>


<div class="col-md-4">

<select
name="priority"
class="form-control"
>

<?php foreach ([
    'normal'=>'Normale',
    'high'=>'Importante',
    'urgent'=>'Urgente'
] as $k=>$v) { ?>

<option
value="<?= $k; ?>"
<?= $ei['priority']===$k
    ? 'selected'
    : ''; ?>
>
<?= $v; ?>
</option>

<?php } ?>

</select>

</div>


<div class="col-md-4">

<select
name="status"
class="form-control"
>

<?php foreach ([
    'todo'=>'À faire',
    'progress'=>'En cours',
    'waiting'=>'En attente',
    'done'=>'Terminé'
] as $k=>$v) { ?>

<option
value="<?= $k; ?>"
<?= $ei['status']===$k
    ? 'selected'
    : ''; ?>
>
<?= $v; ?>
</option>

<?php } ?>

</select>

</div>

</div>


<div style="margin-top:10px;">

<textarea
name="notes"
class="form-control"
rows="2"
placeholder="Notes"
><?= html_escape($ei['notes']); ?></textarea>

</div>


<div style="margin-top:10px;">

<button class="btn btn-info">
<i class="fa fa-save"></i>
Enregistrer au planning
</button>

</div>

<?= form_close(); ?>

</div>

</div>

</div>


</div>


<div class="row">

<div class="col-md-12">

<div class="panel_s">

<div class="panel-body">

<h4 class="mp-section-title">
📋 Planning actuel
</h4>

<div class="table-responsive">

<table class="table table-striped">

<thead>

<tr>
<th>Date</th>
<th>Horaire</th>
<th>Section</th>
<th>Tâche</th>
<th>Responsable</th>
<th>Priorité</th>
<th>Statut</th>
<th></th>
</tr>

</thead>

<tbody>

<?php foreach ($items as $it) { ?>

<tr>

<td>
<?= date(
    'd/m/Y',
    strtotime($it['plan_date'])
); ?>
</td>

<td>
<?= substr($it['start_time'],0,5); ?>
→
<?= substr($it['end_time'],0,5); ?>
</td>

<td>
<?= html_escape($it['section_name']); ?>
</td>

<td>
<strong>
<?= html_escape($it['title']); ?>
</strong>
</td>

<td>
<?= html_escape($it['responsible']); ?>
</td>

<td>
<?= html_escape($it['priority']); ?>
</td>

<td>
<?= html_escape($it['status']); ?>
</td>

<td class="text-right">

<a
class="btn btn-default btn-xs"
href="<?= admin_url(
    'marketing_plan?plan_id='
    . (int)$plan['id']
    . '&edit_item='
    . (int)$it['id']
); ?>"
>
<i class="fa fa-pencil"></i>
</a>

<a
class="btn btn-danger btn-xs"
onclick="return confirm('Supprimer cette tâche ?');"
href="<?= admin_url(
    'marketing_plan/delete_item/'
    . (int)$it['id']
); ?>"
>
<i class="fa fa-trash"></i>
</a>

</td>

</tr>

<?php } ?>

<?php if (!$items) { ?>

<tr>
<td
colspan="8"
class="text-center text-muted"
>
Aucune tâche planifiée.
</td>
</tr>

<?php } ?>

</tbody>

</table>

</div>

</div>

</div>

</div>

</div>


<?php } ?>


</div>
</div>

<?php init_tail(); ?>

</body>
</html>
