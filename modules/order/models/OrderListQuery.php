<?php

namespace app\modules\order\models;

/**
 * This is the ActiveQuery class for [[OrderList]].
 *
 * @see OrderList
 */
class OrderListQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return OrderList[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return OrderList|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
