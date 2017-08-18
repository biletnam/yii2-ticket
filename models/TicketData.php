<?php

namespace bausch\ticket\models;

use Yii;

/**
 * This is the model class for table "ticket_module.ticket_data".
 *
 * @property integer $ticket_data_id_pk
 * @property integer $ticket_id_fk
 * @property string $category
 * @property string $obj
 * @property string $val
 * @property string $extra
 * @property string $created_at
 * @property string $created_by
 *
 * @property Ticket $ticketIdFk
 */
class TicketData extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ticket_module.ticket_data';
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
        return [
            [['ticket_id_fk'], 'integer'],
            [['obj', 'val', 'category'], 'required'],
            [['obj', 'val', 'category', 'extra'], 'string', 'max' => 255],
            [['extra'], 'safe'],
            [['ticket_id_fk'], 'exist',
              'skipOnError' => true,
              'targetClass' => Ticket::className(),
              'targetAttribute' => ['ticket_id_fk' => 'ticket_id_pk']],
        ];
    }
    
    

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ticket_data_id_pk' => 'Ticket Data Id Pk',
            'ticket_id_fk'      => 'Ticket Id Fk',
            'category' => 'Category',
            'obj' => 'Obj',
            'val' => 'Val',
            'extra' => 'Extra',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTicket()
    {
        return $this->hasOne(Ticket::className(), ['ticket_id_pk' => 'ticket_id_fk']);
    }

    /**
     * @inheritdoc
     * @return TicketModuleTicketDataQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new TicketDataQuery(get_called_class());
    }
}
