<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\web\View;

/* @var $this yii\web\View */

$this->title = 'Ticket Dashboard';
//$this->params['breadcrumbs'][] = ['label' => 'Ticket System', 'url' => ['/ticket']];
//$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ticket-dashboard">
	
	<?= $this->render('/_linkbar.php') ?>
	
	<div class='row'>
		<div class="jumbotron">
			<h2><?= Html::encode($this->title) ?></h2>
			<p>Accept and Close Active Tickets.</p>
		</div>
	</div>
	
	<div class='row'>
		<div class="col-lg-12" >
			<?php
			echo Html::a(
				'<span class="glyphicon glyphicon-pencil" aria-hidden=true></span> Maintenance',
				'/ticket/ticket', [
					'class' => 'btn btn-md btn-primary',
					'style' => 'width:125px'
			]);
			echo Html::a(
				'<span class="glyphicon glyphicon-tasks" aria-hidden=true></span> Plant IT',
				'/ticket/dashboard', [
					'class' => 'btn btn-md btn-success',
					'style' => 'width:125px'
			]);
			?>
		</div>
	</div>
	
	<div class="col-lg-12" >
		
		<table class='table' align='center'>
			<tr>
				<th>Ticket#</th>
				<th>Line</th>
				<th>Machine</th>
				<th>Section</th>
				<th>Status</th>
				<th>Creator</th>
				<th>Issue</th>
				<th>Created At</th>
				<th>Maint.Status</th>
				<th>Cancel</th>
			</tr>
			<tr>
				<td>#601</td>
				<td>Line-1</td>
				<td>AutoHydration</td>
				<td>Sec#4</td>
				<td><button type="button" class="btn-xs btn-danger">Down</button></td>
				<td>martinj7</td>
				<td>Capper down.</td>
				<td>2017-04-18</td>
				<td> <button type="button" class="btn-xs btn-warning">Close</button></td>
				<td><button type="button" class="btn-xs btn-danger">Cancel</button></td>
			</tr>
			  <tr>
				<td>#602</td>
				<td>Line-3</td>
				<td>APLE</td>
				<td>N/A</td>
				<td><button type="button" class="btn-xs btn-primary">Up</button></td>
				<td>martinj7</td>
				<td>Capper down.</td>
				<td>2017-04-18</td>
				<td><button type="button" class="btn-xs btn-primary">Take</button></td>
				<td><button type="button" class="btn-xs btn-danger">Cancel</button></td>
			</tr>
		</table>

	</div> <!--End column -->

</div> <!--End ticket-dashboard -->