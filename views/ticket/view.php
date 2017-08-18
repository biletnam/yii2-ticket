<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
/* @var $this yii\web\View */
/* @var $model frontend\modules\ticket\models\Ticket */

$this->title = $model->ticket_id_pk;
$this->params['breadcrumbs'][] = ['label' => 'Tickets', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ticket-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php
        /*
        Html::a('Update', ['update', 'id' => $model->ticket_id_pk], ['class' => 'btn btn-primary']);
        Html::a('Delete', ['delete', 'id' => $model->ticket_id_pk], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]);
        */
        ?>
    </p>

    <?php
        //print_r($modelData)
    ?>
    
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'ticket_id_pk',
            'process',
            'as_reported:ntext',
            'as_determined:ntext',
            'applied_fix:ntext',
            'machine_status',
            'ticket_status',
            'created_at',
            'created_by',
            'taken_at',
            'taken_by',
            'closed_at',
            'closed_by',
            'canceled_at',
            'canceled_by',
        ],
    ]) ?>
    
    <?php
    /*
    GridView::widget([
        'model' => $modelData,
        //'tableOptions' => ['class' => 'table'],
        'columns' => [
            'obj',
            'val'
        ],
        'layout' => "{items}\n{pager}",
    ]);
    
    */
    ?>
            

</div>
