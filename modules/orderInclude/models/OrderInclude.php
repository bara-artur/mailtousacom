<?php

namespace app\modules\orderInclude\models;

use Yii;
/**
 * This is the model class for table "order_include".
 *
 * @property integer $id
 * @property integer $order_id
 * @property string $name
 * @property double $price
 * @property integer $weight
 * @property integer $quantity
 */
class OrderInclude extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'order_include';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id', 'name', 'price', 'weight', 'quantity'], 'required'],
            [['order_id', 'weight', 'quantity'], 'integer'],
            [['price'], 'number'],
            [['name'], 'string', 'max' => 60],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_id' => 'Order ID',
            'name' => 'Name',
            'price' => 'Price',
            'weight' => 'Weight',
            'quantity' => 'Quantity',
        ];
    }

}
