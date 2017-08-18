<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\modules\ticket\models\Workflow */

$this->title = 'Update Workflow: ' . $model->workflow_id_pk;
$this->params['breadcrumbs'][] = ['label' => 'Workflows', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->workflow_id_pk, 'url' => ['view', 'id' => $model->workflow_id_pk]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="workflow-update">
    <div class='row'>
        <h1><?= Html::encode($this->title) ?></h1>
        <div class='col-xs-6 col-md-6'>
            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>
        </div>
    </div>
</div>
