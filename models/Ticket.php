<?php

namespace bausch\ticket\models;

use Yii;
use yii\helpers\Json;
use bausch\ticket\models\TicketData;

/**
 * This is the model class for table "ticket_module.ticket".
 *
 * @property integer $ticket_id_pk
 * @property string $process
 * @property string $as_reported
 * @property string $as_determined
 * @property string $applied_fix
 * @property string $machine_status
 * @property string $ticket_status
 * @property string $created_at
 * @property string $created_by
 * @property string $taken_at
 * @property string $taken_by
 * @property string $closed_at
 * @property string $closed_by
 * @property string $canceled_at
 * @property string $canceled_by
 */
class Ticket extends \yii\db\ActiveRecord
{
    const TKT_STATUS_CANCELED   = 'Canceled';
    const TKT_STATUS_TAKEN      = 'In Repair';
    const TKT_STATUS_CLOSED     = 'Closed';
    const TKT_STATUS_OPEN       = 'Open';
    
    const MACH_STATUS_UP        = 'Up';
    const MACH_STATUS_DOWN      = 'Down';
    //const MACH_STATUS_NA        = 'N/A';
    
    public $a;
    public $b;
    public $c;
    public $d;
    public $e;
    public $val1;
    public $val2;
    public $val3;
    public $val4;
    public $val5;
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ticket_module.ticket';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('db_ticket');
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        // Only validate user inputs.
        return [
            [['process', 'as_reported', 'machine_status', 'created_by'],  'required', 'on' => 'create'],
            [['process', 'as_reported', 'machine_status', 'created_by'],  'required', 'on' => 'update'],
            [['taken_by'], 'required', 'on' => 'take'],
            [['taken_by'], 'required', 'on' => 'open'],
            [['as_determined', 'applied_fix', 'closed_by'],   'required', 'on' => 'close'],
            [['canceled_reason', 'canceled_by'], 'required', 'on' => 'cancel'],
            [['as_reported', 'as_determined', 'applied_fix', 'machine_status', 'ticket_status'], 'string'],
            [['process'], 'string', 'max' => 255],
            [['val1', 'val2', 'val3', 'val4', 'val5'], 'safe'],
            //[['ticketData.val'], 'safe'],
            //[['ticketdatavals'], 'safe'],
            //[['created_at', 'taken_at', 'closed_at', 'canceled_at'], 'safe'],
            //[['created_by', 'taken_by', 'closed_by', 'canceled_by'], 'string', 'max' => 30],
        ];
        
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ticket_id_pk' => 'Ticket #',
            'process' => 'Process',
            'as_reported' => 'As Reported',
            'as_determined' => 'As Determined',
            'applied_fix' => 'Applied Fix',
            'machine_status' => 'Machine Status',
            'ticket_status' => 'Ticket Status',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'taken_at' => 'Taken At',
            'taken_by' => 'Taken By',
            'closed_at' => 'Closed At',
            'closed_by' => 'Closed By',
            'canceled_at' => 'Canceled At',
            'canceled_by' => 'Canceled By',
            'canceled_reason' => 'Cancel Reason',
        ];
    }

   
    /**
     * @inheritdoc
     */
    public function afterFind()
    {
        //Yii::warning('After find.' . Json::encode($items), 'Ticket');
        //foreach($items as $item) {
        //    if($item)   Yii::warning('After find.' . Json::encode($item->obj), 'Ticket');
        //}
        return true;
    }
    
    /**
     * @inheritdoc
     * @description Executes after an update to ticket.
     * @todo Create function to check each item, modify or remove as necessary.
     */
    public function afterSave($insert=false, $changedAttributes=null)
    {
        //get post data and db for comparison.
        /*
        $items_post = Yii::$app->request->post('TicketData');
        $items_db   = TicketData::find()->select('obj','val')->where(['ticket_id_fk' => $this->ticket_id_pk ])->asArray()->all();
        
        Yii::warning('Test after save trigger post.' . Json::encode($items_post), 'Ticket');
        Yii::warning('Test after save trigger db.' . Json::encode($items_db), 'Ticket');
        
        //Delete all associated TicketData.
        TicketData::deleteAll(['ticket_id_fk' => $this->ticket_id_pk]);
        
        //Save to db.
        foreach ($items_post as $item) {
            $m = new TicketData();
            $m->ticket_id_fk = $this->ticket_id_pk;
            $m->obj = $item['obj'];
            $m->val = $item['val'];
            $m->save();
        }
        return false;
        */
    }
    /*
    */
    
    /**
     * @inheritdoc
     * @return TicketModuleTicketQuery the active query used by this AR class.
     */
    //public function getTicketVal()
    //{
    //    return $this->hasMany(TicketData::className(), ['ticket_id_fk' => 'ticket_id_pk']);
    //}
    
    /**
     * @inheritdoc
     * @return TicketModuleTicketQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new TicketQuery(get_called_class());
    }
    
    /**
     * @return \yii\db\ActiveQuery ticketdatavals
     */
    //public function getTicketdatavals()
    //{
        //return $this->hasMany(TicketData::className(), ['ticket_id_fk' => 'ticket_id_pk']);
    //}
    
    public function getTicketData()
    {
        return $this->hasMany(TicketData::className(), ['ticket_id_fk' => 'ticket_id_pk']);
    }
    
    public function active($q)
    {
        return $this->andOnCondition(['obj' => $q]);
    }
    
    public function fetchValue($q) {
        foreach ($this->ticketData as $key => $item) {
            if($item->obj == $q) {
                return $item->val;
            }
        }
        return '';
    }
    
    public function getVals() {
        $val_r = [];
        foreach ($this->ticketData as $key => $item) {
            array_push($val_r, $item->val);
        }
        sort($val_r);
        return implode(array_unique($val_r, SORT_STRING), ", ");
    }
    /*    
    */
}
