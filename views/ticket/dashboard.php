<?php

use miloschuman\highcharts\Highcharts;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;
use yii\helpers\Html;

$this->title = 'Tickets';

?>

<div class="ticket-index">
	
	<!-- Button bar. -->
    <?= $this->render('/_linkbar.php') ?>
	<div class="row text-center">
		<hr/>
		<h1>Metrics Dashboard</h1>
	</div>
	
	<!-- Refresh content after here. -->
    <?php Pjax::begin([
        'id' => 'tkt_dashboard',
        'timeout' => 5000,
        'enablePushState' => false,
        'enableReplaceState' => false,
    ]); ?>

	<div class="row well">
		
		<?php $form = ActiveForm::begin(['options' => ['id' => 'someForm']]);?>

			<div class="col-xs-2">
			</div>
			
			<div class="col-xs-4 col-md-3"">
				<?= $form->field($model, 'from_date')->textInput(['value' => $model->from_date, 'type' => 'date']) ?>
			</div>
			
			<div class="col-xs-4 col-md-3">
				<?= $form->field($model, 'to_date')->textInput(['value' => $model->to_date, 'type' => 'date']) ?>
			</div>
			
			<div class="col-xs-2">
				<div class="form-group">
					<?= Html::submitButton('Submit', ['class' => 'btn btn-primary']) ?>
				</div>
			</div>
			
		<?php ActiveForm::end(); ?>
		
	</div>
	
	<div class="row text-center">
		<?php
		if($model->to_date && $model->from_date) {
			echo Html::a('Download Data', [
					'/ticket/ticket/download',
					'from_date'=>$model->from_date, 'to_date'=>$model->to_date
				], [
					'class' => 'btn btn-md btn-success',
					'target'=>'_blank',
					'data-pjax' => 0
				]
			);
		}
		?>
	</div>
	
	<div class="row">
		
		<!-- If dates valid: -->
		<?php if ($model->from_date && $model->to_date) : ?>
		
			<?php 
			 $sql = "
				SELECT 
					z.workflow 'Process' 
				  , y1.val 'Location'
				  , y2.val 'Line'
				  , y3.val 'Machine'
				  , y4.val 'Station'
				  , y5.val 'Unit'
				  , Count(ticket_id_pk) cnt
				FROM  ticket_module.ticket 		x
				LEFT JOIN ticket_module.ticket_data 	y1 ON y1.ticket_id_fk = ticket_id_pk AND y1.category = 'Location'
				LEFT JOIN ticket_module.ticket_data 	y2 ON y2.ticket_id_fk = ticket_id_pk AND y2.category = 'Line' 
				LEFT JOIN ticket_module.ticket_data 	y3 ON y3.ticket_id_fk = ticket_id_pk AND y3.category = 'Machine'
				LEFT JOIN ticket_module.ticket_data 	y4 ON y4.ticket_id_fk = ticket_id_pk AND y4.category IN ('Section','Station')
				LEFT JOIN ticket_module.ticket_data 	y5 ON y5.ticket_id_fk = ticket_id_pk AND y5.category = 'Unit'
				LEFT JOIN ticket_module.workflow 		z  ON z.workflow = x.process
				WHERE 
					x.ticket_status = 'Closed'
				AND z.workflow IS NOT NULL
				AND x.created_at >= :from_date
				AND x.created_at <= :to_date
				GROUP BY 
					z.workflow 
				  , y1.val 
				  , y2.val 
				  , y3.val 
				  , y4.val 
				  , y5.val 
				ORDER BY 
					cnt DESC
				LIMIT 10
				;
			";
			$params = [':from_date' => $model->from_date . '00:00:00', ':to_date' => $model->to_date. '23:59:59'];
			$data = Yii::$app->db_ticket->createCommand($sql)->bindValues($params)->queryAll();
			?>
			
			<?php if(count($data)>0) : ?>
				<h2>Top 5 Reoccurring Offenders</h2>
		
				<?php
					$from = strtotime($model->from_date);
					$to = strtotime($model->to_date);
					$days = floor(($from-$to)/60/60/24);
				?>
				<div class="bs-callout bs-callout-info">
					
					<?= "<h4>Tickets <u>Closed</u> Over the last $days day(s);</h4>" ?>
					<?= '<p>' . date('d-M-Y', $from) . ' - ' . date('d-M-Y', $to).'</p>' ?>
				</div>
				<table class='table table-striped table-bordered'>
					<tr>
						<th>Process</th>
						<th>Area</th>
						<th>Loc|line</th>
						<th>Station|Sect</th>
						<th>Machine</th>
						<th>Unit</th>
						<th>Occurrences</th>
					</tr>
					<?php
					foreach ($data as $row) {
						echo '<tr>';
						echo '<td>'.$row['Process'].'</td>';
						echo '<td>'.$row['Location'].'</td>';
						echo '<td>'.$row['Line'].'</td>';
						echo '<td>'.$row['Machine'].'</td>';
						echo '<td>'.$row['Station'].'</td>';
						echo '<td>'.$row['Unit'].'</td>';
						echo '<td>'.$row['cnt'].'</td>';
						echo '</tr>';
					}
					?>
				</table>
			<?php endif; ?>
		
		<?php endif; ?>
	
	</div>
	
	<?php Pjax::end(); ?>
	
</div>