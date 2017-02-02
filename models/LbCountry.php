<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "lb_country".
 *
 * @property integer $id
 * @property string $name_ru
 * @property string $name
 * @property string $code
 * @property string $currency_code
 * @property string $currency
 */
class LbCountry extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'lb_country';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name_ru', 'currency'], 'string', 'max' => 255],
            [['name'], 'string', 'max' => 100],
            [['code'], 'string', 'max' => 2],
            [['currency_code'], 'string', 'max' => 5],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name_ru' => 'Name Ru',
            'name' => 'Name',
            'code' => 'Code',
            'currency_code' => 'Currency Code',
            'currency' => 'Currency',
        ];
    }
}
