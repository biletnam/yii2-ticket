<?php

namespace bausch\ticket\models;

use Yii;

/**
 * This is the model class for table "ticket_module.workflow".
 *
 * @property integer $workflow_id_pk
 * @property string $workflow
 * @property string $created_at
 * @property string $created_by
 * @property string $updated_at
 * @property string $updated_by
 */
class Workflow extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ticket_module.workflow';
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
            [['workflow', 'created_by'], 'required'],
            [['created_at', 'updated_at'], 'safe'],
            [['workflow'], 'string', 'max' => 255],
            [['created_by', 'updated_by'], 'string', 'max' => 30],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'workflow_id_pk' => 'Workflow Id Pk',
            'workflow'   => 'Workflow',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * @inheritdoc
     * @return WorkflowQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new WorkflowQuery(get_called_class());
    }
    
     /**
     * @inheritdoc
     * @return .
     */
    public function getSteps()
    {
        return $this->hasMany(Step::classname(), ['workflow_id_fk' => 'workflow_id_pk']);
    }
}
