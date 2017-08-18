<?php

use yii\widgets\Pjax;
use yii\helpers\Html;
use yii\widgets\DetailView;
use bausch\ticket\assets\TicketAsset;
//use yii\boostrap\Alert;
/* @var $this yii\web\View */
/* @var $model frontend\modules\ticket\models\Workflow */

$this->title = 'Workflow: ' . $model->workflow;
$this->params['breadcrumbs'][] = ['label' => 'Ticket', 'url' => '/ticket'];
$this->params['breadcrumbs'][] = ['label' => 'Workflow', 'url' => '/ticket/workflow/index'];
$this->params['breadcrumbs'][] = $this->title;

TicketAsset::register($this); //Register the vis.js and vis.css vendor files.
$this->registerJs("var _steps = {}; var _items = {};");
$this->registerJs($this->render('_script.js'));

$animateIcon = '<i class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></i>';
$addIcon     = '<i class="glyphicon glyphicon-plus"></i>New';
$editIcon    = '<i class="glyphicon glyphicon-pencil"></i>Update';
$deleteIcon  = '<i class="glyphicon glyphicon-minus"></i>Delete';
$refreshIcon = '<i class="glyphicon glyphicon-refresh"></i>Refresh';

?>
<div class="workflow-view">
    
    <div class="row">
        <div class="col-xs-12 col-md-4">
            <h1><?= Html::encode($this->title) ?></h1>
            <p>
                <?= Html::a('Update', ['update', 'id' => $model->workflow_id_pk], ['class' => 'btn btn-primary']) ?>
                <!--
                <?= Html::a('Delete', ['delete', 'id' => $model->workflow_id_pk], [
                    'class' => 'btn btn-danger',
                    'data' => [
                        'confirm' => 'Are you sure you want to delete this item?',
                        'method' => 'post',
                    ],
                ]) ?>
                -->
            </p>
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    //'workflow_id_pk',
                    'workflow',
                    'created_at',
                    'created_by',
                    //'updated_at',
                    //'updated_by',
                ],
            ]) ?>
        </div>
        <div class="col-xs-12 col-md-7 well">
              <div id="mynetwork" style="height: 300px; "></div>
              <br/>
              
        </div>
    </div>
    <hr/>
    <div class="row">
        <div id="messages"></div>
    </div>
    
    <div class="row">
        
        <!--Start of Steps-->
        <div class="col-xs-12 col-md-6" style="border-right: 1px dashed #333;">
            <div class="row">
                <div class="col-xs-4 col-md-2">
                   <label>ID</label>
                    <input  class="form-control step_id_pk" data-target="steps"
                    value='' type='text' readonly>
                </div>
                <div class="col-xs-4 col-md-3">
                   <label>Label</label><br/>
                   <input class="form-control workflow_id_pk" data-target="steps"
                           value='<?= $model->workflow_id_pk ?>' type='hidden'>
                    <input class="form-control step" data-target="steps"
                           placeholder="Step Name">
                </div>
                <div class="col-xs-4 col-md-3">
                   <label>Group Type</label><br/>
                    <select class="form-control category" data-target="steps">
                        <option value=""></option>
                        <option value="area">Area</option>
                        <option value="location">Location</option>
                        <option value="line">Line</option>
                        <option value="machine">Machine</option>
                        <option value="section">Section</option>
                        <option value="station">Station</option>
                        <option value="unit">Unit</option>
                    </select>
                </div>
                
                <div class="col-xs-4 col-md-3">
                   <label>Type</label><br/>
                    <select class="form-control step_type" data-target="steps">
                        <option value="start">Start</option>
                        <option value="flow">Flow</option>
                        <option value="end">End</option>
                    </select>
                </div>
            </div>
            
            <div class="row">
                <br/>
                <div class="col-xs-8 col-md-8">
                    <select size="15" class="form-control list" data-target="steps">
                    </select>
                </div>
                <div class="col-xs-4 col-md-2">
                    <span class="align-middle">
                        <?= Html::a($addIcon . $animateIcon, ['add-step', 'id' => $model->workflow_id_pk], [
                            'class' => 'btn btn-primary btn-add-step',
                            'data-target' => 'steps',
                            'title' => 'Add Step',
                            'style' => 'width: 100px'
                        ]) ?>
                        <br/>
                        <?= Html::a($editIcon . $animateIcon, ['update-step', 'id' => $model->workflow_id_pk], [
                            'class' => 'btn btn-info btn-update-step',
                            'data-target' => 'steps',
                            'title' => 'Update Step',
                            'style' => 'width: 100px'
                        ]) ?>
                        <br/>
                        <?= Html::a($refreshIcon . $animateIcon, ['get-steps', 'id' => $model->workflow_id_pk], [
                            'class' => 'btn btn-success btn-get-steps',
                            'data-target' => 'steps',
                            'title' => 'Refresh Step',
                            'style' => 'width: 100px'
                        ]) ?>
                        <br/>
                        <?= Html::a($deleteIcon . $animateIcon, ['delete-step', 'id' => $model->workflow_id_pk], [
                            'class' => 'btn btn-danger btn-delete-step',
                            'data-target' => 'steps',
                            'title' => 'Delete Step',
                            'style' => 'width: 100px'
                        ]) ?>
                    </span>
                </div>
            </div>
            
        </div> <!--End of Steps-->
        
        <!--Start of Items-->
        <div class="col-xs-12 col-md-6">
            
            <div class="row">
                <div class="col-xs-2 col-md-2">
                    <label>ID</label>
                    <br/>
                    <input  class="form-control item_id_pk" data-target="items"
                    value='' type='text' readonly>
                </div>
                 <div class="col-xs-4 col-md-4">
                   <label>Name</label><br/>
                   <input class="form-control item" data-target="items"
                          placeholder="Item Name">
                </div>
                <div class="col-xs-4 col-md-4">
                   <label>To Step</label><br/>
                   <select class="form-control to_step_id_fk" data-target="items">
                   </select>
                </div>
                <div class="col-xs-2 col-md-2">
                   <label for="append_input">Append</label>
                   <br/>
                   <input type="checkbox"
                        class="form-control append_input"
                        data-target="items"
                        style="width: 25px; height: 25px;"
                        name="append_input"
                        value="1">
                </div>
                
            </div>
            <div class="row">
                <br/>
                <div class="col-xs-8 col-md-8">
                    <select size="15" class="form-control list" data-target="items">
                    </select>
                </div>
                <div class="col-xs-4 col-md-2">
                    <?= Html::a($addIcon . $animateIcon, ['add-item'], [
                        'class' => 'btn btn-primary btn-add-item',
                        'data-target' => 'items',
                        'title' => 'Add Item',
                        'style' => 'width: 100px'
                    ]) ?>
                    <br/>
                    <?= Html::a($editIcon . $animateIcon, ['update-item'], [
                        'class' => 'btn btn-info btn-update-item',
                        'data-target' => 'items',
                        'title' => 'Update Item',
                        'style' => 'width: 100px'
                    ]) ?>
                    <br/>
                    <?= Html::a($refreshIcon . $animateIcon, ['get-items'], [
                        'class' => 'btn btn-success btn-get-items',
                        'data-target' => 'items',
                        'title' => 'Refresh Items',
                        'style' => 'width: 100px'
                    ]) ?>
                    <br/>
                    <?= Html::a($deleteIcon . $animateIcon, ['delete-item'], [
                        'class' => 'btn btn-danger btn-delete-item',
                        'data-target' => 'items',
                        'title' => 'Delete Item',
                        'style' => 'width: 100px'
                    ]) ?>
                </div>
            </div>
           
        </div>
        <!--End of Items-->
        
    </div> <!--End of Row-->
    <div class="row">
        <div class='col-md-12'>
           
        </div>
    </div>
</div>
