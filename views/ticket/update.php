<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model frontend\modules\ticket\models\Ticket */

$this->title = 'Update Ticket: ' . $model->ticket_id_pk;
$this->params['breadcrumbs'][] = ['label' => 'Tickets', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->ticket_id_pk, 'url' => ['view', 'id' => $model->ticket_id_pk]];
$this->params['breadcrumbs'][] = 'Update';

?>
<div class="ticket-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form-create', [
        'model' => $model,
        'modelsData' => $modelsData,
    ]) ?>

</div>
