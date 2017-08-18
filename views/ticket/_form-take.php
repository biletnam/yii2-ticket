<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Ticket */
/* @var $form ActiveForm */
?>
<div class="ticket-form-take">
<h3>Assign Ticket</h3>
<?php
    $form = ActiveForm::begin(['options' => ['id' => 'takeModal']]);?>
		
    	<?= $form->field($model, 'ticket_id_pk')->textInput(['readonly' => true, 'value' => $model->ticket_id_pk]) ?>
		<?= $form->field($model, 'taken_by') ?>

        <div class="form-group">
            <?= Html::submitButton('Submit', ['class' => 'btn btn-primary']) ?>
        </div>
    <?php ActiveForm::end(); ?>

</div><!-- ticket-form-take -->
