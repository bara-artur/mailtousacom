<?php

namespace app\modules\order\models;

use Yii;

/**
 * This is the model class for table "order_items".
 *
 * @property integer $id
 * @property integer $order_id
 * @property string $product_name
 * @property double $item_price
 * @property integer $quantity
 */
class OrderItems extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'order_items';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id', 'product_name', 'item_price','status'], 'required'],
            [['order_id', 'quantity'], 'integer'],
            [['item_price'], 'number'],
            [['product_name'], 'string', 'max' => 255],
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
            'product_name' => 'Product Name',
            'item_price' => 'Item Price',
            'quantity' => 'Quantity',
            'status' => 'Status',
        ];
    }

    /**
     * @inheritdoc
     * @return OrderItemsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new OrderItemsQuery(get_called_class());
    }
}
