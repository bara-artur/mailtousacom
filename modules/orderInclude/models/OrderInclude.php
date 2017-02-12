<?php

namespace app\modules\orderInclude\models;

use Yii;

/**
 * This is the model class for table "order_include".
 *
 * @property integer $id
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
            [['name', 'price', 'weight', 'quantity'], 'required'],
            [['price'], 'number'],
            [['weight', 'quantity','order_id'], 'integer'],
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
            'name' => 'Name',
            'price' => 'Price',
            'weight' => 'Weight',
            'quantity' => 'Quantity',
        ];
    }
}
