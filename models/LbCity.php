<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "lb_city".
 *
 * @property integer $id
 * @property integer $country_id
 * @property string $city
 * @property string $city_ru
 * @property string $state
 * @property string $lat
 * @property string $lon
 * @property string $timezone
 */
class LbCity extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'lb_city';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['country_id'], 'integer'],
            [['city', 'timezone'], 'required'],
            [['city', 'state'], 'string', 'max' => 255],
            [['city_ru'], 'string', 'max' => 100],
            [['lat', 'lon'], 'string', 'max' => 10],
            [['timezone'], 'string', 'max' => 50],
            [['country_id'], 'exist', 'skipOnError' => true, 'targetClass' => LbCountry::className(), 'targetAttribute' => ['country_id' => 'id']],
        ];
    }C:\OpenServer\domains\orhid\models\LbCity.php

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'country_id' => 'Country ID',
            'city' => 'City',
            'city_ru' => 'City Ru',
            'state' => 'State',
            'lat' => 'Lat',
            'lon' => 'Lon',
            'timezone' => 'Timezone',
        ];
    }
}
