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


\conquer\momentjs\MomentjsAsset::register($this);
$this->registerJs(<<<JS
var datetime = null,
        date = null;

var update = function () {
    date = moment(new Date())
    datetime.html(date.format('YYYY-MM-DD, H:mm:ss'));
};

$(document).ready(function(){
    datetime = $('#datetime')
    update();
    setInterval(update, 1000);
});
JS
);

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
            <span id='datetime' style="color: #337ab7; font-size: 25px;" class="text-center" ></span>
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
		
		<div class="col-xs-1">
		</div>
		<div class="col-xs-10">
			
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
					[
						'attribute' => 'ticket_id_pk',
						//'headerOptions' => ['width' => '100'],
						'label' => 'Ticket #',
						'format'=> 'ntext',
					],
					[
						'attribute'=> 'process',
						//'headerOptions' => ['width' => '150'],
					],
					[
						'label' => 'Location',
						'format' => 'raw',
						'attribute'=>'param1',
						'value' => 'val1',
						//'headerOptions' => ['width' => '100'],
					],
					[
						'label' => 'Line',
						'format' => 'raw',
						'attribute'=>'param2',
						'value' => 'val2',
						//'headerOptions' => ['width' => '100'],
					],
					[
						'label' => 'Machine',
						'format' => 'raw',
						'attribute'=>'param3',
						'value' => 'val3',
						//'headerOptions' => ['width' => '100'],
					],
					[
						'label' => 'Sec|Station',
						'format' => 'raw',
						'attribute'=>'param4',
						'value' => 'val4',
						//'headerOptions' => ['width' => '100'],
					],
					[
						'label' => 'Unit',
						'format' => 'raw',
						'attribute'=>'param5',
						'value' => 'val5',
						//'headerOptions' => ['width' => '100'],
					],
					[
						'label' => 'Downtime (Hr:Min)',
						'format'=> 'html',
						 'value' => function ($model) {
							$wait  = DateDiffInterval($model->created_at, $model->taken_at, true);
							$work  = (!$model->taken_at) ? '' : DateDiffInterval($model->taken_at, $model->closed_at, true);
							$total = DateDiffInterval($model->created_at, $model->closed_at);
							$tbl = "<table>";
							$tbl .= "<tr><th style='text-align:right'>Wait: </th><td>&nbsp;" . $wait . "</td></tr>";
							$tbl .= "<tr><th style='text-align:right'>Work: </th><td>&nbsp;" . $work . "</td></tr>";
							$tbl .= "<tr><th style='text-align:right'>Total: </th><td>&nbsp;" . $total . "</td></tr>";
							$tbl .= "</table>";
							return $tbl;
						 },
						 //'headerOptions' => ['width' => '100'],
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
					    'class' => 'yii\grid\DataColumn',
					    'attribute' => 'as_reported',
					    'label' => 'Description',
					    'format'=> 'html',
					    'value' => function($model){
							$tmp_r = [];
					        if($model->as_reported)   $tmp_r[] = '<b>Reported: </b><br/>' . $model->as_reported . '<br/>';
							if($model->as_determined) $tmp_r[] = '<b>Determined: </b><br/>' . $model->as_determined . '<br/>';
							if($model->applied_fix)   $tmp_r[] = '<b>Applied Fix: </b><br/>' . $model->applied_fix . '<br/>';
							return implode('--------------<br/>', $tmp_r);
					    },
						//'headerOptions' => ['width' => '300'],
					],
					[
						'attribute' => 'ticket_status',
						//'headerOptions' => ['width' => '100'],
						'filter'=>array(
								'Open'=>'Open',
								'In Repair'=>'In Repair',
								'Closed' =>'Closed',
								'Canceled' =>'Canceled'),
						'label' => 'Ticket Status',
						'format'=> 'ntext',
					],            
					[
						'class' => 'yii\grid\ActionColumn',
						'template' => Helper::filterActionColumn('{take} {log} {cancel} {view} {update}'),
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
							'log' => function ($url, $model) {
								return Html::button('<span class="glyphicon glyphicon-book"></span>',
										['value' => Url::to(['ticket/log', 'id'=>$model->ticket_id_pk]),
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
				],
				'layout' => "{errors}\n{summary}\n{pager}\n{items}\n{pager}",
			]); ?>
			
		</div> <!--End of Col-->
		
		<div class="col-xs-1">
		</div>
		
	</div> <!--End of Row-->
	
	<?php Pjax::end(); ?>
	
</div> <!--End of ticket-index-->
