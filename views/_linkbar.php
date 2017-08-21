<?php
/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\helpers\Markdown;
use yii\helpers\Url;

?>
<div class='ticket-linkbar'>
	<div class='row'>
		<div class='col-xs-12'>
			<div class='text-center'>
				<?php
					echo Html::a(
						'<b>Ticket System</b>',
						'/ticket/ticket/index', [
						//'/ticket', [
							'class' => 'btn btn-md btn-info',
							'style' => 'width:125px'
					]);
					//echo Html::a(
					//	'<span class="glyphicon glyphicon-pencil" aria-hidden=true></span> New Ticket',
					//	'/ticket/ticket/index', [
					//		'class' => 'btn btn-md btn-primary',
					//		'style' => 'width:125px'
					//]);
					echo Html::a(
						'<span class="glyphicon glyphicon-stats" aria-hidden=true></span> Dashboard',
						'/ticket/ticket/dashboard', [
							'class' => 'btn btn-md btn-primary',
							'style' => 'width:125px'
					]);
					/*
					echo Html::a(
						'<span class="glyphicon glyphicon-search" aria-hidden=true></span> Search',
						'/ticket/search', [
							'class' => 'btn btn-md btn-primary',
							'style' => 'width:125px'
					]);
					*/
					echo Html::a(
						'<span class="glyphicon glyphicon-cog" aria-hidden=true></span> Settings',
						'/ticket/workflow', [
							'class' => 'btn btn-md btn-primary',
							'style' => 'width:125px'
					]);
					/*
					echo Html::a(
						'<span class="glyphicon glyphicon-info-sign" aria-hidden=true></span> Info',
						'/ticket/default', [
							'class' => 'btn btn-md btn-primary',
							'style' => 'width:125px'
					]);
					*/
				?>
			</div>
		</div> <!--End column-->
	</div> <!--End row-->
</div>