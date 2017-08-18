<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\modules\ticket\models\Workflow */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="workflow-form">
    
    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'workflow')->textInput(['maxlength' => true]) ?>
    
    <?php if (false): ?>
        
        <?= $form->field($model, 'created_at')->textInput() ?>
        
        <?= $form->field($model, 'created_by')->textInput(['maxlength' => true]) ?>
    
        <?= $form->field($model, 'updated_at')->textInput() ?>
    
        <?= $form->field($model, 'updated_by')->textInput(['maxlength' => true]) ?>
    
    <?php endif; ?>
    
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
