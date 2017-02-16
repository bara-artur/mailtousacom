<?php

namespace app\modules\tariff\models;

use Yii;

/**
 * This is the model class for table "tariffs".
 *
 * @property integer $id
 * @property integer $parcel_count
 * @property double $price_count
 */
class Tariffs extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tariffs';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['parcel_count','weight'], 'required'],
            [['parcel_count'], 'integer'],
            [['price','weight'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'parcel_count' => 'Parcel Count',
            'price_count' => 'Price Count',
        ];
    }

    /**
     * @inheritdoc
     * @return TariffsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new TariffsQuery(get_called_class());
    }
}
