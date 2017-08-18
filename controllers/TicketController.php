<?php

namespace bausch\ticket\controllers;

use Yii;

use bausch\ticket\models\Ticket;
use bausch\ticket\models\TicketSearch;
use bausch\ticket\models\TicketData;
use bausch\ticket\models\TicketDataSearch;
use bausch\ticket\components\Model;

use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\Expression;
use yii\helpers\Json;
use yii\data\SqlDataProvider;
use yii\base\DynamicModel;
//use yii\base\Controller;
/**
 * TicketController implements the CRUD actions for Ticket model.
 */
class TicketController extends Controller
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

    public function actionDownload() {
        $from_date = Yii::$app->request->get('from_date');
        $to_date = Yii::$app->request->get('to_date');
        if ($from_date && $to_date) {
            $sql = "
                SELECT 
                    x.ticket_id_pk  
                  , z.workflow AS 'process'
                  , y1.val 'Location'
                  , y2.val 'Line'
                  , y3.val 'Machine'
                  , y4.val 'Section'
                  , y5.val 'Unit'
                  , x.machine_status 
                  , x.as_reported
                  , x.as_determined
                  , x.applied_fix
                  , x.ticket_status 
                  , TIMESTAMPDIFF(MINUTE, x.created_at, x.taken_at)  min_response
                  , TIMESTAMPDIFF(MINUTE, x.taken_at, x.closed_at)   min_worked
                  , TIMESTAMPDIFF(MINUTE, x.created_at, x.closed_at) min_total
                  , x.created_at 
                  , x.created_by 
                  , x.taken_at
                  , x.taken_by 
                  , x.closed_at
                  , x.closed_by 
                  , x.canceled_at
                  , x.canceled_by 
                  , x.canceled_reason 
                FROM  ticket_module.ticket 		x
                LEFT JOIN ticket_module.ticket_data 	y1 ON y1.ticket_id_fk = ticket_id_pk AND y1.category = 'Location'
                LEFT JOIN ticket_module.ticket_data 	y2 ON y2.ticket_id_fk = ticket_id_pk AND y2.category = 'Line'
                LEFT JOIN ticket_module.ticket_data 	y3 ON y3.ticket_id_fk = ticket_id_pk AND y3.category = 'Machine'
                LEFT JOIN ticket_module.ticket_data 	y4 ON y4.ticket_id_fk = ticket_id_pk AND y4.category IN ('Section','Station')
                LEFT JOIN ticket_module.ticket_data 	y5 ON y5.ticket_id_fk = ticket_id_pk AND y5.category = 'Unit'
                LEFT JOIN ticket_module.workflow 		z  ON z.workflow = x.process
                -- WHERE x.created_at >= :from_date AND x.created_at <= :to_date
                ORDER BY
                    x.ticket_id_pk DESC
                ;
            ";
            $params = []; //[':from_date' => $from_date, ':to_date' => $to_date];
            $data = Yii::$app->db_ticket->createCommand($sql)->bindValues($params)->queryAll();
            if(count($data) > 0) {
                $titles = array_keys($data[0]);
                $file = \Yii::createObject([
                    'class' => 'codemix\excelexport\ExcelFile',
                    'sheets' => [
                        'tickets' => [
                            'data' => $data,
                            'titles' => $titles,
                            'formats' => [
                                15 => 'yyyy/mm/dd hh:mm:ss',
                                17 => 'yyyy/mm/dd hh:mm:ss',
                                19 => 'yyyy/mm/dd hh:mm:ss',
                                21 => 'yyyy/mm/dd hh:mm:ss',
                            ],
                            'formatters' => [
                                // Dates and datetimes must be converted to Excel format
                                15 => function ($value, $row, $data) {
                                    return \PHPExcel_Shared_Date::PHPToExcel(strtotime($value));
                                },
                                17 => function ($value, $row, $data) {
                                    return \PHPExcel_Shared_Date::PHPToExcel(strtotime($value));
                                },
                                19 => function ($value, $row, $data) {
                                    return \PHPExcel_Shared_Date::PHPToExcel(strtotime($value));
                                },
                                21 => function ($value, $row, $data) {
                                    return \PHPExcel_Shared_Date::PHPToExcel(strtotime($value));
                                },
                            ],
                        ]
                    ]
                ]);
                $file->send('ticket_data.xlsx');
            } 
        }
    }
    
    /**
     * Lists all Ticket models.
     * @return mixed
     */
    public function actionIndex()
    {
        $this->layout = "@app/views/layouts/main-app";
        
        $searchModel = new TicketSearch();
        $queryParams = Yii::$app->request->queryParams;
        $dataProvider = $searchModel->search($queryParams);
        
        //Default to show only active (open or taken) tickets.
        if (!isset($queryParams['TicketSearch'])) {
            $dataProvider->query->andWhere(['or',
               ['ticket_status'=>$searchModel::TKT_STATUS_OPEN],
               ['ticket_status'=>$searchModel::TKT_STATUS_TAKEN],
            ])->orderBy(['ticket_id_pk' => SORT_DESC ]);
        }
        
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'queryParams' => $queryParams,
        ]);
    }
    
     /**
     * Reports and graphs.
     * @return mixed
     */
    public function actionDashboard()
    {
        $model = new DynamicModel(['to_date','from_date']);
        $model->addRule(['to_date','from_date'], 'safe');
        $model->addRule(['to_date','from_date'], 'date');
        $model->validate();
        $req = Yii::$app->request;
        if ($model->load($req->post())) {
            return $this->render('dashboard', [
                'model' => $model,
            ]);
        } else {
            //Default values
            if(!$model->to_date)    $model->to_date   = date('Y-m-d', strtotime('now'));
            if(!$model->from_date)  $model->from_date = date('Y-m-d', strtotime('-3 weeks'));
            return $this->render('dashboard', [
                'model' => $model 
            ]);
        }
    }
    
    /**
     * Displays a single Ticket model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        //$modelData = TicketData::findAll(['ticket_id_fk' => $id]);
        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('view', [
                'model' => $model,
                //'modelData' => $modelData,
            ]);
        } else {
            return $this->render('view', [
                'model' => $model,
                //'modelData' => $modelData,
            ]);
        }
    }

    /**
     * Create a new Ticket.
     * If creation is successful, ....
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Ticket();
        
        //Set scenario for validation.
        $model->scenario = 'create';
        $user = (Yii::$app->user->isGuest) ? ''
            : Yii::$app->user->identity->username;
        $model->created_by = $user;
        $model->created_at = new Expression('NOW()');
        $model->ticket_status = $model::TKT_STATUS_OPEN;
        
        //Handle ticketData models.
        $count = count(Yii::$app->request->post('TicketData'));
        $modelsData = [];
        for ($i=1; $i <= $count; $i++) {
            $modelsData[$i] = new TicketData();
        }
        $post = Yii::$app->request->post();
        if ($model->load($post) && Model::loadMultiple($modelsData, $post) && $model->save()) {
            $error = false;
           for ($i=1; $i <= count($modelsData); $i++) {
                if ($modelsData[$i]->extra) {
                    $modelsData[$i]->val = $modelsData[$i]->val . ' ' .$modelsData[$i]->extra;
                }
                $modelsData[$i]->ticket_id_fk = $model->ticket_id_pk;
                $modelsData[$i]->created_by = $model->created_by;
                if (!$modelsData[$i]->save(false)) {
                    $model->delete();
                    $error = true;
                    break;
                }
            }
            if (!$error) {
                return $this->redirect(['radio']);
            }
        } 
        
        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('_form-create', [
                'model' => $model,
                'modelsData' => (empty($modelsData)) ? [new TicketData] : $modelsData
            ]);
        } else {
            return $this->render('_form-create', [
                'model' => $model,
                'modelsData' => (empty($modelsData)) ? [new TicketData] : $modelsData
            ]);
        }

    }
    
    /**
     * Assigns a ticket.
     * If assigning is successful, ...
     * @return mixed
     */
    public function actionTake($id)
    {
        $model = $this->findModel($id);
        $model->scenario = 'take';
        $model->taken_by = Yii::$app->user->identity->username; 
        $model->taken_at = new Expression('NOW()');
        $model->ticket_status = $model::TKT_STATUS_TAKEN;
        
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        } elseif (Yii::$app->request->isAjax) {
            return $this->renderAjax('_form-take', [
                'model' => $model
            ]);
        } else {
            return $this->render('_form-take', [
                'model' => $model
            ]);
        }
    }
    
    /**
     * Opten a closed ticket.
     * If assigning is successful, ...
     * @return mixed
     */
    public function actionOpen($id)
    {
        $model = $this->findModel($id);
        $model->scenario = 'open';
        $model->taken_by = Yii::$app->user->identity->username; 
        $model->taken_at = new Expression('NOW()');
        $model->ticket_status = $model::TKT_STATUS_OPEN;
        
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        } elseif (Yii::$app->request->isAjax) {
            return $this->renderAjax('_form-take', [
                'model' => $model
            ]);
        } else {
            return $this->render('_form-take', [
                'model' => $model
            ]);
        }
    }
    
    /**
     * Close a ticket.
     * If assigning is successful, ...
     * @return mixed
     */
    public function actionClose($id)
    {
        $model = $this->findModel($id);
        $model->scenario = 'close';
        $model->closed_by = Yii::$app->user->identity->username; 
        $model->closed_at = new Expression('NOW()');
        $model->ticket_status = $model::TKT_STATUS_CLOSED;
        
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        } elseif (Yii::$app->request->isAjax) {
            return $this->renderAjax('_form-close', [
                'model' => $model
            ]);
        } else {
            return $this->render('_form-close', [
                'model' => $model
            ]);
        }
    }
    
    /**
     * Cancel a ticket.
     * If assigning is successful, ...
     * @return mixed
     */
    public function actionCancel($id)
    {
        $model = $this->findModel($id);
        $model->scenario = 'cancel';
        $model->canceled_by = Yii::$app->user->identity->username; 
        $model->canceled_at = new Expression('NOW()');
        $model->ticket_status = $model::TKT_STATUS_CANCELED;
        
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        } elseif (Yii::$app->request->isAjax) {
            return $this->renderAjax('_form-cancel', [
                'model' => $model
            ]);
        } else {
            return $this->render('_form-cancel', [
                'model' => $model
            ]);
        }
    }

    /**
     * Updates an existing Ticket model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->scenario = 'update';
        $modelsData = TicketData::findAll(['ticket_id_fk' => $id]);
        
        $ticketData = Yii::$app->request->post('TicketData');
        $t = Yii::$app->request->post();
        Yii::warning('Ticket data: ' . $id . Json::encode($ticketData), 'Ticket');
        Yii::warning('Post: ' . $id . Json::encode($t), 'Ticket');
        
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            
            //Delete existing records.
            TicketData::deleteAll(['ticket_id_fk' => $id]);
            //Add new selection.
            foreach ($ticketData as $item) {
                Yii::warning('Data Row: ' . $id . Json::encode($item), 'Ticket');
                $m = new TicketData();
                $m->attributes = $item;
                $m->created_by = Yii::$app->user->identity->username; 
                $m->created_at = new Expression('NOW()');
                $m->val = isset($m->extra) ? $m->val.' '.$m->extra : $m->val;
                $m->ticket_id_fk = $id;
                Yii::warning('Data Errors: val' . $id . Json::encode([$m->val, $m->obj, $m->category]), 'Ticket');
                $m->save();
                if ($m->getErrors()) {
                    Yii::warning('Data Errors: ' . $id . Json::encode($m->getErrors()), 'Ticket');
                }
            }
            return $this->redirect(['index']);
        } elseif (Yii::$app->request->isAjax) {
            Yii::warning('Ticket Errors: ' . $id . Json::encode($model->getErrors()), 'Ticket');
            return $this->renderAjax('update', [
                'model' => $model,
                'modelsData' => (empty($modelsData)) ? [new TicketData] : $modelsData,
            ]);
        }
        //else {
        //    return $this->render('update', [
        //        'model' => $model,
        //        'modelsData' => (empty($modelsData)) ? [new TicketData] : $modelsData,
        //    ]);
        //}
    }

    /**
     * Deletes an existing Ticket model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        return $this->redirect(['index']);
    }
    
    /**@todo needs to be tested.
     * Sends a signal to the remote Raspberry Pi
     * which triggers the alert over the radio.
     * 
     */
    public function actionRadio()
    {
        if (isset(\Yii::$app->params["ticket_radio_alert_url"])) {
            $radio_alert_url = \Yii::$app->params["ticket_radio_alert_url"];
            $fields = array(
                'radio_alert'=>$_POST['radio_alert'] = 'alert',
            );
            $postvars = '';
            $sep = '';
            foreach ($fields as $key=>$value) {
                $postvars.= $sep.urlencode($key).'='.urlencode($value);
                $sep='&';
            }
            
            $ch = curl_init();
            
            curl_setopt($ch,CURLOPT_URL,$radio_alert_url);
            curl_setopt($ch,CURLOPT_POST,count($fields));
            curl_setopt($ch,CURLOPT_POSTFIELDS,$postvars);
            curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
            
            $message = curl_exec($ch);
            
            curl_close($ch);
        } else {
            $message = "Radio alert disabled. Is  param 'ticket_radio_alert_url' configured in \frontend\config\params.php";
        }
        return $this->render('confirmation', [
           'message' => $message     
        ]);
    }
    
    /**
     * Finds the Ticket model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Ticket the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Ticket::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
