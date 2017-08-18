<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\ContactForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;

$this->title = 'B+Link';
$this->params['breadcrumbs'][] = $this->title;

$this->registerJs($this->render('_script_confirmation.js'));
?>
<div class="site-contact">
    
	<div class="jumbotron">
        <h1>Maintenance Radios Alerted!</h1>

        <p class="lead">Click the following button to return to the dashboard.</p>

        <p>
			<h2 id="countdown" class='danger'></h2>
			<br/>
			<?= Html::a(
				'<span class="glyphicon glyphicon-tasks" aria-hidden=true></span>Dashboard',
				'/ticket/ticket/index', [
					'id' => 'ticketDashboard',
					'class' => 'btn btn-lg btn-success',
			]);
			?>
		</p>
    </div>
	
    <div style="height: 50px;">
		<hr/>
	</div>
	
	<div class="row">
        <div class="col-sm-12">
			<div class='text-center'>
				<h2>Fixture Response</h2>
				<p class='well'><?= $message ?></p>
			</div>
        </div>
    </div>
	
</div>
