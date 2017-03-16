<?php

namespace app\modules\payment\models;

use Yii;

/**
 * This is the model class for table "payment_include".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $element_id
 * @property integer $element_type
 * @property string $comment
 * @property integer $status
 * @property integer $create_at
 * @property double $price
 * @property double $qst
 * @property double $gst
 */
class PaymentInclude extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'payment_include';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'element_id', 'element_type', 'create_at'], 'required'],
            [['user_id', 'element_id', 'element_type', 'status', 'create_at'], 'integer'],
            [['price', 'qst', 'gst'], 'number'],
            [['comment'], 'string', 'max' => 255],
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
            'element_id' => 'Element ID',
            'element_type' => 'Element Type',
            'comment' => 'Comment',
            'status' => 'Status',
            'create_at' => 'Create At',
            'price' => 'Price',
            'qst' => 'Qst',
            'gst' => 'Gst',
        ];
    }
}
