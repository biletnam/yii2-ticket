<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use frontend\modules\ticket\models\Workflow;

/* @var $this yii\web\View */
/* @var $model frontend\modules\ticket\models\Ticket */
/* @var $form yii\widgets\ActiveForm */

$script = file_get_contents(Url::to('@frontend/modules/ticket/assets/dynamic-form.js'));
$this->registerJs($script);

$this->title = 'Tickets';
?>

<div class="ticket-form-create">
	
    <?php $form = ActiveForm::begin([
		//'id' => 'ticket-form',
		'options' => ['class' => 'form'],
	]); ?>

	<?= $form->field($model, 'as_reported')->textarea(['rows' => 1]) ?>

    <?= $form->field($model, 'machine_status')->dropDownList(['Up' => 'Up','Down' => 'Down','N/A' => 'N/A',], ['prompt' => '--Select--']) ?>
	
	<div class="form-group">
		<?= Html::label('Workflow Name', 'select-workflow-id', ['class' => 'control-label']) ?>
		<?= Html::activeDropDownList($model,
			'process',
			ArrayHelper::map(Workflow::find()->asArray()->all(),'workflow','workflow'),[
				'class'=>'form-control',
				'prompt' => '--Select--' ])
		?>
		<span class="help-block hidden"></span>
	</div>
	
	<!-- Dynamic Content. -->
	<div class="dyn-gen" hidden></div>
	<?php if(0): foreach($items as $i=>$item): ?>
		<tr>
			<td><?= $form->field($item,"[$i]obj"); ?></td>
			<td><?= $form->field($item,"[$i]val"); ?></td>
		</tr>
	<?php endforeach; endif; ?>
	<!-- End of Dynamic Content. -->
	
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', [
			'id' => 'new-ticket-button',
			'class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
			'disabled' => true
		]) ?>
    </div>
	
	
    <?php ActiveForm::end(); ?>

</div>
