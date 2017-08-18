<?php

namespace bausch\ticket\models;

/**
 * This is the ActiveQuery class for [[TicketModuleItem]].
 *
 * @see TicketModuleItem
 */
class ItemQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return TicketModuleItem[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return TicketModuleItem|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
