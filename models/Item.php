<?php

namespace bausch\ticket\models;

use Yii;

/**
 * This is the model class for table "ticket_module.item".
 *
 * @property integer $item_id_pk
 * @property integer $step_id_fk
 * @property string $item
 * @property string $to_step_id_fk
 * @property string $created_at
 * @property string $created_by
 * @property string $updated_at
 * @property string $updated_by
 *
 * @property Step $stepIdFk
 */
class Item extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ticket_module.item';
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
            [['step_id_fk', 'append_input', 'to_step_id_fk'], 'integer'],
            [['item', 'created_by'], 'required'],
            [['created_at', 'updated_at', 'to_step_id_fk'], 'safe'],
            [['item'], 'string', 'max' => 255],
            [['created_by', 'updated_by'], 'string', 'max' => 30],
            [['step_id_fk', 'item'], 'unique', 'targetAttribute' => ['step_id_fk', 'item'], 'message' => 'The combination of Step Id Fk and Item has already been taken.'],
            [['step_id_fk'], 'exist', 'skipOnError' => true, 'targetClass' => Step::className(), 'targetAttribute' => ['step_id_fk' => 'step_id_pk']],
            [['created_at', 'updated_at', 'append_input'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'item_id_pk' => 'Item ID PK',
            'step_id_fk' => 'Step ID FF',
            'item' => 'Item',
            'to_step_id_fk' => 'To Step ID FK',
            'append_input' => 'Append Input',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStep()
    {
        return $this->hasOne(Step::className(), ['step_id_pk' => 'step_id_fk']);
    }
    
 

    /**
     * @inheritdoc
     * @return TicketModuleItemQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ItemQuery(get_called_class());
    }
}
