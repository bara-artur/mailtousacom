<?php

namespace app\modules\order\models;

use Yii;

/**
 * This is the model class for table "order".
 *
 * @property int $id
 * @property int $user_id
 * @property int $created_at
 */
class OrderFilterForm extends Order
{
    public $created_at_to;
    /**
     * @inheritdoc
     */
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id','user_id','created_at','created_at_to','user_input'], 'safe']
        ];
    }


}
