<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\modules\ticket\models\Ticket */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="ticket-form-cancel">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'process')->textInput(['maxlength' => true]) ?>

		
    <?= $form->field($model, 'as_reported')->textarea(['rows' => 2])->textInput(['readonly' => true]) ?>
    <?= $form->field($model, 'canceled_reason')->textarea(['rows' => 2]) ?>
    <?= $form->field($model, 'ticket_status')->dropDownList([ 'Open' => 'Open', 'In Repair' => 'In Repair', 'Closed' => 'Closed', 'Canceled' => 'Canceled', ], ['prompt' => '--Select--']) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div> <!--ticket-form-cancel-->
