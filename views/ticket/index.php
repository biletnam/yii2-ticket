<?php


use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\widgets\Pjax;
use mdm\admin\components\Helper;
use frontend\modules\ticket\models\TicketData;

/* @var $this yii\web\View */
/* @var $searchModel frontend\modules\ticket\models\TicketSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

function DateDiffInterval($sDate1, $sDate2, $alt_behavior=false)
{
	//Default values.
	$tag = 'span';
	$class = 'null';
	
	//If param1 is empty return empty string.
	if(!($sDate1)) return '';
    
	//If the default alt_behavior (false) and param1 is empty: 
	if ($alt_behavior === false && $sDate2 === null) {
		//return empty string.
		return '';
	} else {
		//Otherwise if empty use current time.
		if( $sDate2 === null ) $sDate2 = 'now';
	}
	
	//If alt behavior true and date2 is null the current time is used.
	$diff = strtotime($sDate2) - strtotime($sDate1);
	
    $h = str_pad(($diff/60/60%24), 2, '0', STR_PAD_LEFT); // Hours
    $m = str_pad(($diff/60%60), 2, '0', STR_PAD_LEFT); 	// Minutes
	
	//Conditional formatting.
	if($h > 1) { //if greater than x hours.
		$tag = 'b';
		$class = 'text-danger font-weight-bold';
	} elseif($m > 20) {
		$tag = 'b';
		$class = 'text-warning font-weight-bold';
	}
	
	return "<$tag class='$class'>$h:$m</$tag>";
} //DateDiffInterval

$this->title = 'Tickets';

$this->registerJs($this->render('_script_index.js'));
?>

<div class="ticket-index">
    
    <!-- Button bar. -->
    <?= $this->render('/_linkbar.php') ?>
   
    <!--<span class="glyphicon glyphicon-refresh glyphicon-refresh-animate">-->
    <div class='row>'>
		<code>
			TODO
			<ol>
				<li>Note taker.</li>
			</ol>
		</code>
	</div>

        <hr style="height:2px; border:none; color:#706464; background-color:#605959;"/>
		<!--<div class="col-xs-1"></div>-->
		
        <div class="col-xs-3 text-left">
            <?= Html::button('<span class="glyphicon glyphicon-plus"></span>Create', [
                'value' => Url::to(['ticket/create']),
                'class' => 'showModalButton btn btn-md btn-success',
                //'style' => 'width: 10px;',
                'title' => 'New Ticket',
            ]) ?>
        </div>
        
        <div class="col-xs-6 text-center">
            <?= '<span style="color: #337ab7; font-size: 25px;" class="text-center">'. Date('Y-m-d H:m:s') . '</span>' ?>
        </div>
        
        <div class="col-xs-3 text-right">
			<button class='btn btn-md btn-default'><b class="refresh_timer" style='color:red'></b></button>
            <?= Html::button('<span class="glyphicon glyphicon-refresh"></span><b>Refresh</b>', [
                'id' => 'refreshBtn',
				'value' => Url::to(['ticket/index']),
                'class' => 'btn btn-md btn-default',
                //'style' => 'width: 10px;',
                'title' => 'Refresh Tickets',
            ]) ?>
			<?= Html::button('<span class="glyphicon glyphicon-pause"></span><b>Pause</b>', [
                'id' => 'pauseBtn',
				'value' => Url::to(['ticket/index']),
                'class' => 'btn btn-md btn-default',
                //'style' => 'width: 10px;',
                'title' => 'Refresh Tickets',
				'onclick' => 'event.preventDefault();',
            ]) ?>
        </div>
        <!--<div class="col-xs-1"></div>-->
        <div class='clearfix'></div>
        <hr style="height:2px; border:none; color:#706464; background-color:#605959;"/>
    
    </div>
    
	 <!-- Refresh content after here. -->
    <?php Pjax::begin([
        'id' => 'tkt_dashboard',
        'timeout' => 5000,
        'enablePushState' => false,
        'enableReplaceState' => false,
    ]); ?>
	
	<div class='row>'>
		
		<!--<div class="col-xs-1"></div>-->
		
		<div class="col-xs-12">
		<?php
		//print_r($searchModel);
		//print_r($dataProvider->model);
		?>
		<?= GridView::widget([
			'dataProvider' => $dataProvider,
			'filterModel' => $searchModel,
			//'emptyCell'=>'-',
			'formatter' => ['class' => 'yii\i18n\Formatter','nullDisplay' => ''],  //Hide "not set" fields.
			'tableOptions' => ['class' => 'table'],
			'rowOptions' => function($model, $key, $index, $grid){
				$user = (Yii::$app->user->isGuest) ? ''
					: Yii::$app->user->identity->username;
				if( $model->taken_by == $user){
					return ['class' => 'info'];
				} else if ($model->canceled_at) {
					return ['class' => 'bg-warning text-muted'];
				}
			},
			'columns' => [
				//['class' => 'yii\grid\SerialColumn'],
				[
					'attribute' => 'ticket_id_pk',
					'headerOptions' => ['width' => '100'],
					'label' => 'Ticket #',
					'format'=> 'ntext',
				],
				[
					'attribute'=> 'process',
					'headerOptions' => ['width' => '150'],
				],
				[
					'label' => 'Location',
					'format' => 'raw',
					'attribute'=>'param1',
					'value' => 'val1',
				],
				[
					'label' => 'Line',
					'format' => 'raw',
					'attribute'=>'param2',
					'value' => 'val2',
				],
				[
					'label' => 'Machine',
					'format' => 'raw',
					'attribute'=>'param3',
					'value' => 'val3',
				],
				[
					'label' => 'Sec|Station',
					'format' => 'raw',
					'attribute'=>'param4',
					'value' => 'val4',
				],
				[
					'label' => 'Unit',
					'format' => 'raw',
					'attribute'=>'param5',
					'value' => 'val5',
				],
				[
					'label' => 'Wait (Hr:Min)',
					'format'=> 'html',
					 'value' => function ($model) {
						return DateDiffInterval($model->created_at, $model->taken_at, true);
					 }
				],
				[
					'label' => 'Work (Hr:Min)',
					'format'=> 'html',
					 'value' => function ($model) {
						if (!$model->taken_at) {
							return '';
						} else {
							return DateDiffInterval($model->taken_at, $model->closed_at, true);
						}
					 }
				],
				[
					'label' => 'Total(Hr:Min)',
					'format'=> 'html',
					 'value' => function ($model) {
						return DateDiffInterval($model->created_at, $model->closed_at);
					 }
				],
				[
					'class' => 'yii\grid\DataColumn',
					'headerOptions' => ['width' => '100'],
					'contentOptions' => ['class' => 'text-center'],
					'filter'=> array('Up'=>'Up','Down'=>'Down', 'N/A' =>'N/A'),
					'attribute' => 'machine_status',
					'label' => 'Machine Status',
					'format'=> 'raw',
					'value' => function ($model) {
						switch (strtolower($model->machine_status)) {
							case 'up':
								$rs = Html::button('<span class="glyphicon glyphicon-arrow-up"><span>',[
									'class' => 'btn btn-default btn-sm',
									'data-toggle' => 'tooltip',
									'title'=> "Machine Running",
								]);
								break;
							case 'down':
								$rs = Html::button('<span class="glyphicon glyphicon-arrow-down"><span>', [
									'class' => 'btn btn-danger btn-sm',
									'data-toggle' => 'tooltip',
									'title'=> "Machine Down.",
								]);
								break;
							default :
								$rs = "";
								break;
						}
						return $rs;
					},
				],
				[
					'attribute' => 'as_reported',
					'headerOptions' => ['width' => '250'],
					'label' => 'As Reported',
					'format'=> 'ntext',
				],
				
	//			[
	//                'name' => 'Obj',
	//                'value' => 'ticketData.Obj',
	//            ],   
				//'as_determined: ntext',
				//'applied_fix: ntext',
				//[
				//    'class' => 'yii\grid\DataColumn',
				//    'attribute' => 'test',
				//    'label' => 'test',
				//    'format'=> 'html',
				//    'value' => function($model){
				//        $tmp_r = array('<div><b>as_reported</b>'.$model->as_reported.'</div>',$model->as_determined,$model->applied_fix);
				//        return implode('<br/>', $tmp_r);
				//    },
				//],
				[
					'attribute' => 'ticket_status',
					'headerOptions' => ['width' => '100'],
					'filter'=>array(
							'Open'=>'Open',
							'In Repair'=>'In Repair',
							'Closed' =>'Closed',
							'Canceled' =>'Canceled'),
					'label' => 'Ticket Status',
					'format'=> 'ntext',
				],            
				//'taken_by',
				//'taken_at',
				//'closed_by',
				//'closed_at',
				//'canceled_by',
				//'canceled_at',
				//'created_by',
				//'created_at',
				[
					'class' => 'yii\grid\ActionColumn',
					'template' => Helper::filterActionColumn('{take} {cancel} {view} {update}'),
					'filterOptions' => ['id'=>'spawn_reset_btn'],
					'header' => Html::button(Html::a('<span class="glyphicon glyphicon-remove"></span>Clear Filter', ['ticket/index']), [
									'class' => 'btn btn-sm btn-default',
									'style' => 'width: 100px;',
									'onclick' => 'window.location=""',
								]),
					'buttons' => [
						'take' => function ($url, $model) {
							if ($model->ticket_status == $model::TKT_STATUS_OPEN){
								return Html::button('<span class="glyphicon glyphicon-tag"></span>Take',
									['value' => Url::to(['ticket/take', 'id'=>$model->ticket_id_pk]),
									 'class' => 'showModalButton btn btn-sm btn-danger',
									 'style' => 'width: 75px;',
									 'title'=> 'Assign Ticket',
									 'data-toggle' => 'tooltip',
									 'data-selector' => 'true',
									 'data-title' => 'Click to accept ticket.',
									]);
							} elseif ($model->ticket_status == $model::TKT_STATUS_CLOSED) {
								return Html::button('<span class="glyphicon glyphicon-edit"></span>Open',
									['value' => Url::to(['ticket/open', 'id'=>$model->ticket_id_pk]),
									 'class' => 'showModalButton btn btn-sm btn-info',
									 'style' => 'width: 75px;',
									 'title' => 'ReOpen Ticket',
									 'data-toggle'   => 'tooltip',
									 'data-selector' => 'true',
									 'data-title'    => 'Click to re-open the ticket.',
									 ]);
							} else{
								return Html::button('<span class="glyphicon glyphicon-tag"></span>Close',
									['value' => Url::to(['ticket/close', 'id'=>$model->ticket_id_pk]),
									 'class' => 'showModalButton btn btn-sm btn-primary',
									 'style' => 'width: 75px;',
									 'title' => 'Close Ticket',
									 'data-toggle'   => 'tooltip',
									 'data-selector' => 'true',
									 'data-title'    => 'Click to close ticket.',
									 ]);
							}
						},
						'cancel' => function ($url, $model) {
							if ($model->ticket_status == $model::TKT_STATUS_CLOSED) {
								return '';
							} elseif ($model->ticket_status == $model::TKT_STATUS_CANCELED) {
								return '';
							}
							return Html::button('<span class="glyphicon glyphicon-ban-circle"></span>',
									['value' => Url::to(['ticket/cancel', 'id'=>$model->ticket_id_pk]),
									 'class' => 'showModalButton btn btn-sm btn-default',
									 //'style' => 'width: 75px;',
									 'title '=> 'Cancel Ticket',
									 'data-toggle'   => 'tooltip',
									 'data-selector' => 'true',
									 'data-title'    => 'Click to cancel ticket.',
									 ]);
						},
						'view' => function ($url, $model) {
							return Html::button('<span class="glyphicon glyphicon-eye-open"></span>',
									['value' => Url::to(['ticket/view', 'id'=>$model->ticket_id_pk]),
									 'class' => 'showModalButton btn btn-sm btn-default',
									 //'style' => 'width: 75px;',
									 'title' => "Ticket View",
									 'data-toggle'   => 'tooltip',
									 'data-selector' => 'true',
									 'data-title'    => 'Click to view record details.',
									 ]);
						},
						'update' => function ($url, $model) {
							if ($model->ticket_status == $model::TKT_STATUS_CLOSED) {
								return '';
							} elseif ($model->ticket_status == $model::TKT_STATUS_CANCELED) {
								return '';
							}
							return Html::button('<span class="glyphicon glyphicon-pencil"></span>',
									['value' => Url::to(['ticket/update', 'id'=>$model->ticket_id_pk]),
									 'class' => 'showModalButton btn btn-sm btn-default',
									 //'style' => 'width: 75px;',
									 'title'=> 'Update Ticket',
									 'data-toggle'   => 'tooltip',
									 'data-selector' => 'true',
									 'data-title'    => 'Click to update record.',
									 ]);
						},
					], // End of action buttons 
				], // End of actionColumn
				// ['class' => 'yii\grid\ActionColumn'],
			],
			//'layout' => "{items}\n{pager}",         //'layout' => "{summary}\n{pager}\n{items}\n{pager}",
			
		]); ?>
		</div>
		
		<!--<div class="col-xs-1"></div>-->
		
	</div>
	<?php Pjax::end(); ?>
</div>
