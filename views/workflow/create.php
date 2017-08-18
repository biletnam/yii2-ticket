<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model frontend\modules\ticket\models\Workflow */

$this->title = 'Create Workflow';
$this->params['breadcrumbs'][] = ['label' => 'Workflows', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="workflow-create">
    
    <div class='row'>
    
        <div class='col-xs-12 col-sm-6 col-md-4'>
            <h1><?= Html::encode($this->title) ?></h1>
        
            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>
        </div>
    
    </div>

</div>
