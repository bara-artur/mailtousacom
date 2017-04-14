<?php

namespace app\modules\additional_services\models;

use Yii;

/**
 * This is the model class for table "additional_services".
 *
 * @property integer $id
 * @property integer $type
 * @property string $parcel_id_lst
 * @property integer $client_id
 * @property integer $user_id
 * @property string $detail
 * @property string $status_pay
 * @property integer $quantity
 * @property double $price
 * @property double $gst
 * @property double $qst
 */
class AdditionalServices extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'additional_services';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type'], 'required'],
            [['type', 'client_id', 'user_id', 'quantity', 'create','status_pay'], 'integer'],
            [['price', 'gst', 'qst','dop_price', 'dop_gst', 'dop_qst','kurs'], 'number'],
            [['parcel_id_lst', 'detail'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => 'Type',
            'parcel_id_lst' => 'Parcel Id Lst',
            'client_id' => 'Client ID',
            'user_id' => 'User ID',
            'detail' => 'Detail',
            'status_pay' => 'Status Pay',
            'quantity' => 'Quantity',
            'price' => 'Price',
            'gst' => 'Gst',
            'qst' => 'Qst',
        ];
    }
}
