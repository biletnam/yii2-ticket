<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\modules\ticket\models\TicketSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="ticket-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'ticket_id_pk') ?>

    <?= $form->field($model, 'process') ?>

    <?= $form->field($model, 'as_reported') ?>

    <?= $form->field($model, 'as_determined') ?>

    <?= $form->field($model, 'applied_fix') ?>

    <?php echo $form->field($model, 'machine_status') ?>

    <?php echo $form->field($model, 'ticket_status') ?>

    <?php echo $form->field($model, 'created_at') ?>

    <?php echo $form->field($model, 'created_by') ?>

    <?php echo $form->field($model, 'accepted_at') ?>

    <?php echo $form->field($model, 'accepted_by') ?>

    <?php echo $form->field($model, 'closed_at') ?>

    <?php echo $form->field($model, 'closed_by') ?>

    <?php echo $form->field($model, 'canceled_at') ?>

    <?php echo $form->field($model, 'canceled_by') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
