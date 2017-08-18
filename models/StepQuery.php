<?php

namespace bausch\ticket\models;

/**
 * This is the ActiveQuery class for [[TicketModuleStep]].
 *
 * @see TicketModuleStep
 */
class StepQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return TicketModuleStep[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return TicketModuleStep|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
