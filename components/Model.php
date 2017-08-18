<?php
namespace bausch\ticket\components;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

class Model extends \yii\base\Model
{
    /**
     * Creates and populates a set of models.
     *
     * @param string $modelClass
     * @param array $multipleModels
     * @return array
     */
    public static function createMultiple($modelClass, $multipleModels = [])
    {
        $model    = new $modelClass;
        $formName = $model->formName();
        $post     = Yii::$app->request->post($formName);
        $models   = [];
		
		if (! empty($multipleModels)) {
			$keys = array_keys(ArrayHelper::map($multipleModels, 'obj', 'val'));
            $multipleModels = array_combine($keys, $multipleModels);
        }

        if ($post && is_array($post)) {
			foreach ($post as $i => $item) {
				if (isset($item['obj']) && !empty($item['obj']) && isset($multipleModels[$item['obj']])) {
                    $models[] = $multipleModels[$item['obj']];
                }
            }
        }

        unset($model, $formName, $post);

        return $models;
    }
	/*
	public function actionTestCreate()
	{
		$model = new LearnQuestion();
		// http://www.yiiframework.com/doc-2.0/guide-input-tabular-input.html
		$count = count(Yii::$app->request->post('LearnQuestionPart', []));
		$partModels = [new LearnQuestionPart()];
		for($i = 1; $i < $count; $i++) {
			$partModels[] = new LearnQuestionPart();
		}
		$post = Yii::$app->request->post();
		if ($model->load($post) && Model::loadMultiple($partModels, $post) && $model->save()) {
			$error = false;
			foreach ($partModels as $partModel) {
				$partModel->question_id = $model->id;
				if (!$partModel->save()) {
					$model->delete();
					$error = true;
					break;
				}
			}
			if (!$error) {
				return $this->redirect(['view', 'id' => $model->id]);
			}
		}
		return $this->render('create', [
			'model' => $model,
			'partModels' => $partModels
		]);
	}

	*/
}
