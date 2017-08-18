<?php

namespace bausch\ticket\models;

/**
 * This is the ActiveQuery class for [[TicketModuleTicketData]].
 *
 * @see TicketModuleTicketData
 */
class TicketDataQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return TicketModuleTicketData[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return TicketModuleTicketData|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
