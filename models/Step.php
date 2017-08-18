<?php

namespace bausch\ticket\models;

use Yii;
use yii\behaviors\BlameableBehavior;

/**
 * This is the model class for table "ticket_module.step".
 *
 * @property integer $step_id_pk
 * @property integer $workflow_id_fk
 * @property string $step
 * @property string $created_at
 * @property string $created_by
 * @property string $updated_at
 * @property string $updated_by
 *
 * @property Workflow $workflowIdFk
 */
class Step extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ticket_module.step';
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
            [['workflow_id_fk'], 'integer'],
            [['step', 'step_type'], 'required'],
            [['step', 'category'], 'string', 'max' => 255],
            [['created_by', 'updated_by'], 'string', 'max' => 30],
            [['created_by', 'created_at', 'updated_by', 'updated_at'], 'safe'],
            [['workflow_id_fk', 'step'], 'unique', 'targetAttribute' => ['workflow_id_fk', 'step'], 'message' => 'The combination of Workflow Id Fk and Step has already been taken.'],
            [['workflow_id_fk'], 'exist', 'skipOnError' => true, 'targetClass' => Workflow::className(), 'targetAttribute' => ['workflow_id_fk' => 'workflow_id_pk']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'step_id_pk' => 'StepID',
            'workflow_id_fk' => 'WorkflowID',
            'category' => 'Category',
            'step' => 'Step',
            'step_type' => 'Step Type',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => BlameableBehavior::className(),
                'createdByAttribute' => 'created_by',
                'updatedByAttribute' => 'updated_by',
            ],
        ];
    }
    
    /**
     * @inheritdoc
     * @return TicketModuleStepQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new StepQuery(get_called_class());
    }
    
    /**
     * @inheritdoc
     * @return .
     */
    public function getWorkflow()
    {
        return $this->hasOne(Workflow::classname(), ['workflow_id_pk' => 'workflow_id_fk']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItems()
    {
        return $this->hasMany(Item::className(), ['step_id_fk' => 'step_id_pk']);
    }

}
