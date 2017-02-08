<?php

namespace app\modules\tariff\models;

/**
 * This is the ActiveQuery class for [[Tariffs]].
 *
 * @see Tariffs
 */
class TariffsQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return Tariffs[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Tariffs|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
