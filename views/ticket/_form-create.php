<?php

use yii\helpers\Json;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use wbraganca\dynamicform\DynamicFormWidget;
use bausch\ticket\models\Workflow;
use bausch\ticket\models\Item;
use bausch\ticket\modules\ticket\models\Step;

/* @var $this yii\web\View */
/* @var $model frontend\modules\ticket\models\Ticket */
/* @var $form yii\widgets\ActiveForm */

$this->title = 'Tickets--';

$this->registerJs($this->render('_script_create.js'));
?>

<div class="ticket-form-create">
	
    <?php $form = ActiveForm::begin([
		'id' => 'dynamic-form',
		'options' => ['class' => 'form'],
	]); ?>
	<?= $form->field($model, 'as_reported')->textarea(['rows' => 1]) ?>

    <?= $form->field($model, 'machine_status')->dropDownList(['Up' => 'Up','Down' => 'Down'], ['prompt' => '--Select--']) ?>
	
	<div class="form-group">
		<?= Html::label('Process', 'select-workflow-id', ['class' => 'control-label']) ?>
		<?= Html::activeDropDownList($model,
			'process',
			ArrayHelper::map(Workflow::find()->asArray()->all(), 'workflow', 'workflow'),[
				'class'=>'form-control',
				'prompt' => '--Select--' ])
		?>
		<span class="help-block hidden"></span>
	</div>
	
	<div class="dyn-gen" hidden></div>
	 
	
	<?php
	//If update, populate the lists and options.
	foreach ($modelsData as $i => $modelData) { 
		$j = $i + 1;
		if($modelData->obj)
		{
			// <!-- Dynamic Content. -->
			echo '<div class="dyn-gen" onchange="on_change( this )">';
			//Fetch the existing dropdown selections.
			$sql = "
				SELECT 
				  z.item,
				  append_input
			   FROM ticket_module.workflow x
			   JOIN ticket_module.step y ON y.workflow_id_fk 	= x.workflow_id_pk
			   JOIN ticket_module.item z ON z.step_id_fk 		= y.step_id_pk
			   WHERE 
				   x.workflow = :wf
			   AND y.step = :step
			";
			$params = [
				':wf' => $model->process,
				':step' => $modelData->obj
			];
			$options = Yii::$app->db_ticket->createCommand($sql)->bindValues($params)->queryAll();
			$options[] = array('item'=>$modelData->val);
			echo '<hr/>';
			//Append the value set.
			$tst = ArrayHelper::map($options,'item','item');
			echo $form->field($modelData, "[$j]category")->textInput(['type'=>'hidden', 'class' => 'form-control category'])->label(false);
			echo $form->field($modelData, "[$j]obj")->textInput(['type'=>'hidden', 'data-n'=>'4', 'class' => 'form-control obj'])->label(false);
			echo $form->field($modelData, "[$j]val")->dropDownList(
				ArrayHelper::map($options,'item','item'),[
				'prompt' => '--Select--',
				'class' => 'form-control',
				'options' => [$modelData->val => ['Selected'=>'selected']]
			])->label($modelData->obj);
			echo '</div>';
		}
	}
	?>
	
	<?php
		//Don't show created_by if user is logged in.
		if(Yii::$app->user->isGuest) {
			echo $form->field($model, 'created_by');
		} else {
			echo $form->field($model, 'created_by')->textInput(['readonly' => true, 'value' => $model->created_by]);
		}
	?>
	
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', [
			'id' => 'new-ticket-button',
			'class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
			'disabled' => true // Will be enabled via jquery when workflow is complete.
		]) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
