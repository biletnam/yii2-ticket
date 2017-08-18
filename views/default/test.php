<?php

use yii\helpers\Html;

use yii\data\ArrayDataProvider;
use yii\data\ActiveDataProvider;

use frontend\modules\ticket\models\item;

/* @var $this yii\web\View */
/* @var $searchModel frontend\modules\ticket\models\ItemSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Test';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="item-index">

    <h1><?= Html::encode($this->title) ?></h1>
	<?php
	$rs = array();
	//->andWhere(['workflow' => 'HEMA'])
	$rs = Item::find()->joinWith('step s')->asArray()->limit(5)->all(); //->select(['item']);
	//print_r($rs);
	?>
	<table class='table table-striped table-bordered'>
		<tr>
			<th width='100px'>StepID</th>
			<th width='100px'>Step</th>
			<th width='100px'>Items</th>
		</tr>
		<?php
		foreach ($rs as $row) {
			echo '<tr>';
			echo '<td>'.$row['item_id_pk'].'</td>';
			echo '<td>'.$row['item'].'</td>';
			echo '<td>';
			$items = array();
			//foreach ($row['steps'] as $item) {
				//$items[] = $item['step'];
			//}
			//echo implode(',',$items);
			echo '</td>';
			echo '</tr>';
		}
		?>
	</table>
	<?php
	/*
	foreach (Item::find()->batch(10) as $item) {
		echo '<hr/>';
		foreach ($item as $i){
			echo '<br/>';
			echo $i->item;
		}
	}
	echo '<hr/>';
	
	$sql = "
	    SELECT
			  x.workflow
			, y.step
			, z.item
			, y.step_id_pk 		'from_step'
			, z.to_step_id_fk 	'to_step'
	    FROM ticket_module.workflow x
	    JOIN ticket_module.step y ON y.workflow_id_fk 	= x.workflow_id_pk
	    JOIN ticket_module.item z ON z.step_id_fk 		= y.step_id_pk
		WHERE
			x.workflow=:workflow
		-- AND y.step=:step
		-- AND z.item=:item
	";
	$rs = Yii::$app->db->createCommand($sql)
           ->bindValue(':workflow', 'HEMA')
           //->bindValue(':step', 'Line')
           //->bindValue(':item', 'Line 1')
           ->queryAll();
	print_r($rs);
	foreach ($rs as $item){
		echo implode(', ', $item).'<br/>';
	}
	*/
	?>
</div>