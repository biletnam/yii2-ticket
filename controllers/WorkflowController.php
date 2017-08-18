<?php

namespace bausch\ticket\controllers;

use Yii;
use bausch\ticket\models\Item;
use bausch\ticket\models\Step;
use bausch\ticket\models\Workflow;
use bausch\ticket\models\WorkflowSearch;

use yii\db\Expression;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
//yii\db\Command
/**
 * WorkflowController implements the CRUD actions for Workflow model.
 */
class WorkflowController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Workflow models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new WorkflowSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Workflow model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $steps = Step::find()
                ->select(['step_id_pk', 'workflow_id_fk', 'step', 'step_type'])
                ->where(['workflow_id_fk' => $id])
                ->asArray()
                ->orderBy('step_type', 'Start','Flow','End')
                ->all()
                ;
        return $this->render('view', [
            'model' => $this->findModel($id),
            'steps' => $steps,
        ]);
    }

    /**
     * Creates a new Workflow model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Workflow();
        $model->created_by = Yii::$app->user->identity->username; 
        $model->created_at = new Expression('NOW()');
        
        $post = Yii::$app->request->post();
        $jsonMsg = \yii\helpers\Json::encode($post);
        Yii::warning('Data: ' . $jsonMsg, 'Wf Create');
        
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->workflow_id_pk]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Workflow model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->updated_by = Yii::$app->user->identity->username; 
        $model->updated_at = new Expression('NOW()');
        
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->workflow_id_pk]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Workflow model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Deletes an existing Workflow model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @todo Move get next step SQL to components/WorflowHelper
     */
    public function actionNextStep()
    {
        $request = Yii::$app->request;
        $result = array();
        $result['msg']  = 'default';
        $result['code'] = 0;
        if (isset($request->isPost)) {
            //Fetch required post variables;
            $wf     = $request->post('workflow');
            $step   = $request->post('step');
            $val    = $request->post('val');
            
            if($wf && $step && $val) {
                // Get next step
                $sql = "
                     SELECT 
                        x.workflow
                       ,y.step_id_pk
                       ,y.step
                       ,y.category
                       ,z.item
                       ,z.to_step_id_fk
                       ,z.append_input
                    FROM ticket_module.workflow x
                    JOIN ticket_module.step y ON y.workflow_id_fk 	= x.workflow_id_pk
                    JOIN ticket_module.item z ON z.step_id_fk 		= y.step_id_pk
                    WHERE 
                        x.workflow = :wf
                    AND y.step = :step
                    AND z.item = :item
                ";
                $params = [':wf' => $wf, ':step' => $step, 'item' => $val];
                $wfs = Yii::$app->db_ticket->createCommand($sql)->bindValues($params)->queryAll();
                $cnt = count($wfs);
                //We are expecting just one step.
                if ($cnt == 1) {
                    $step_id = $wfs[0]['to_step_id_fk'];
                    $sql = "
                        SELECT 
                           x.workflow
                          ,y.step_id_pk
                          ,y.step
                          ,y.category
                          ,z.item
                          ,z.to_step_id_fk
                          ,z.append_input
                       FROM ticket_module.workflow x
                       JOIN ticket_module.step y ON y.workflow_id_fk 	= x.workflow_id_pk
                       JOIN ticket_module.item z ON z.step_id_fk 		= y.step_id_pk
                       WHERE 
                           x.workflow = :wf
                       AND y.step_id_pk = :step_id
                    ";
                    $params = [':wf' => $wf, ':step_id' => $step_id];
                    $wfs = Yii::$app->db_ticket->createCommand($sql)->bindValues($params)->queryAll();
                }
                else{
                    //We received too many steps.
                    $result['msg'] = 'error: too many steps returned.';
                    $result['code'] = -1;
                }
                
            } else {
                // Get first step.
                $sql = "
                    SELECT 
                        x.workflow
                       ,y.step_id_pk
                       ,y.step
                       ,y.category
                       ,z.item
                       ,z.to_step_id_fk
                       ,z.append_input
                   FROM ticket_module.workflow x
                   JOIN ticket_module.step y ON y.workflow_id_fk 	= x.workflow_id_pk
                   JOIN ticket_module.item z ON z.step_id_fk 		= y.step_id_pk
                   WHERE 
                       x.workflow = :wf
                    AND y.step_type = 'Start'
               ";
                $params = [':wf' => $wf];
                $wfs = Yii::$app->db_ticket->createCommand($sql)->bindValues($params)->queryAll();
            }
            $cnt = count($wfs);
            $result['count']  = $cnt;
            if ($cnt > 0) {
                //Return step details.
                $result['step']      = $wfs[0]['step'];
                $result['category']  = $wfs[0]['category'];
                //Return items details.
                foreach($wfs as $row){
                    $item = $row['item'];
                    //ex: array('Item' => 1 or 0);
                    $items[$item] = $row['append_input'];
                }
                $result['options'] = $items;
                $result['msg'] = 'success';
                $result['code'] = 1;
            } else{
                $result['code'] = -2;
                $result['msg']  = 'No result returned.';
            }
            $result['wfs']    = $wfs;
        }
        
        return \yii\helpers\Json::encode($result);
    }
    
     /**
     * Returns workflow model for workflow visual display using vis.js.
     * 
     * @param integer $id
     * @return mixed
     * @todo 
     */
    public function actionGetWorkflow($id)
    {
       //Get step items and paths.
        $sql = "
            SELECT
                 w.workflow_id_pk
                ,w.workflow 
                
                ,s.step
                ,i.item
                ,ns.step to_step
                
                ,s.step_id_pk
                ,concat(s.step_id_pk,'.',i.item_id_pk) step_item_id
                ,i.to_step_id_fk
                ,i.append_input
                
            FROM  workflow w
            JOIN step s ON s.workflow_id_fk = w.workflow_id_pk
            JOIN item i ON i.step_id_fk = s.step_id_pk
            LEFT JOIN step ns ON ns.step_id_pk = i.to_step_id_fk
            WHERE w.workflow_id_pk = :wf_id
        ";
        $params = [':wf_id' => $id];
        $result['workflow'] = Yii::$app->db_ticket->createCommand($sql)->bindValues($params)->queryAll();
        
        //Get unique steps.
        $sql = "
            SELECT
                w.workflow_id_pk
               ,w.workflow 
               ,s.step
               ,s.step_id_pk
               ,s.step_type
           FROM  workflow w
           LEFT JOIN step s ON s.workflow_id_fk = w.workflow_id_pk
           WHERE w.workflow_id_pk = :wf_id
        ";
        $params = [':wf_id' => $id];
        $result['steps'] = Yii::$app->db_ticket->createCommand($sql)->bindValues($params)->queryAll();
        
        return \yii\helpers\Json::encode($result);
    }
    
    /**
     * Assign step to workflow.
     * @param string $id
     * @return array
     */
    public function actionAddStep($id)
    {
        $model = new Step();
        $post['Step'] = Yii::$app->request->post();
        $model->workflow_id_fk = $id;
        $model->created_by = Yii::$app->user->identity->username; 
        $model->created_at = new Expression('NOW()');
        
        if ($model->load($post, 'Step') && $model->save()) {
            $result['success'] = true;
        } 
        
        $result = [];
        $result['post'] = $post;
        $result['errors'] = $model->getErrors();
        $result['model'] = $model;
        
        return \yii\helpers\Json::encode($result);
    }
    
     /**
     * Update step.
     * @param string $id
     * @return array
     */
    public function actionUpdateStep()
    {
        $post['Step'] = Yii::$app->request->post();
        $id = Yii::$app->request->post('step_id_pk');
        $model = Step::findOne($id);
        $model->updated_by = Yii::$app->user->identity->username; 
        $model->updated_at = new Expression('NOW()');
        
        if ($model->load($post, 'Step') && $model->save()) {
            Yii::warning('-->' . \yii\helpers\Json::encode($post));
            $result['success'] = true;
        }
        
        $result = [];
        $result['post']   = $post;
        $result['errors'] = $model->getErrors();
        $result['model']  = $model;
        
        return \yii\helpers\Json::encode($result);
    }
    
     /**
     * Delete step.
     * @param string $id
     * @return array
     */
    public function actionDeleteStep($id)
    {
        $post = Yii::$app->request->post();
        $step_id_pk = Yii::$app->request->post('step_id_pk');
        $model = Step::findOne($step_id_pk); 
        if ($model) {
            if($model->delete()) {
                $result['success'] = true;
            }
        } 
        
        $result = [];
        $result['post'] = $post;
        $result['errors'] = $model->getErrors();
        $result['model'] = $model;
        
        return \yii\helpers\Json::encode($result);
    }
    
    /**
     * Get steps.
     * @param string $id
     * @return array
     */
    public function actionGetSteps($id)
    {
        $steps = Step::find()
            ->select(['step_id_pk', 'workflow_id_fk', 'step', 'category', 'step_type'])
            ->where(['workflow_id_fk' => $id])
            ->asArray()
            ->orderBy('step_type', 'Start','Flow','End')
            ->all();
        $result['steps'] = count($steps) ? $steps : false;
        return \yii\helpers\Json::encode($result);
    }
    
    /**
     * Get items.
     * @param string $id
     * @return array
     */
    public function actionGetItems($id)
    {
        $items = Item::find()
            //->select(['step_id_pk', 'workflow_id_fk', 'step', 'step_type'])
            ->where(['step_id_fk' => $id])
            ->asArray()
            //->orderBy('step_type', 'Start','Flow','End')
            ->all();
        $result['items'] = count($items) ? $items : false;
        return \yii\helpers\Json::encode($result);
    }
    
     /**
     * Create item.
     * @param string $id
     * @return array
     */
    public function actionAddItem()
    {
        $model = new Item();
        $post['Item'] = Yii::$app->request->post();
        //$model->workflow_id_fk = $id;
        $model->created_by = Yii::$app->user->identity->username; 
        $model->created_at = new Expression('NOW()');
        
        if ($model->load($post, 'Item') && $model->save()) {
            $result['success'] = true;
        } 
        
        $result = [];
        $result['post'] = $post;
        $result['errors'] = $model->getErrors();
        $result['model'] = $model;
        
        return \yii\helpers\Json::encode($result);
    }
    
     /**
     * Update item.
     * @param string $id
     * @return array
     */
    public function actionUpdateItem()
    {
        $post['Item'] = Yii::$app->request->post();
        $id = Yii::$app->request->post('item_id_pk');
        $model = Item::findOne($id);
        $model->updated_by = Yii::$app->user->identity->username; 
        $model->updated_at = new Expression('NOW()');

        
        yii::error('post: ' . \yii\helpers\Json::encode(Yii::$app->request->post()), 'update item');
        
        if ($model->load($post, 'Item') && $model->save()) {
            $result['success'] = true;
        }
        
        $result = [];
        $result['post']   = $post;
        $result['errors'] = $model->getErrors();
        $result['model']  = $model;
        
        return \yii\helpers\Json::encode($result);
    }
    
     /**
     * Delete item.
     * @param string $id
     * @return array
     */
    public function actionDeleteItem()
    {
        $post = Yii::$app->request->post();
        $item_id = Yii::$app->request->post('item_id');
        $model = Item::findOne($item_id); 
        if ($model) {
            if($model->delete()) {
                $result['success'] = true;
            }
        } 
        
        $result = [];
        $result['post'] = $post;
        $result['errors'] = $model->getErrors();
        $result['model'] = $model;
        
        return \yii\helpers\Json::encode($result);
    }
    
    /**
     * Finds the Workflow model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Workflow the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Workflow::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
