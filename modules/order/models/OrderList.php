<?php

namespace app\modules\order\models;

use Yii;

/**
 * This is the model class for table "order_list".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $adress_id
 */
class OrderList extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'order_list';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'adress_id','status'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'adress_id' => 'Adress ID',
            'status' => 'Status',

        ];
    }

    /**
     * @inheritdoc
     * @return OrderListQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new OrderListQuery(get_called_class());
    }
}
