<?php

namespace app\modules\additional_services\models;

use Yii;

/**
 * This is the model class for table "additional_services_list".
 *
 * @property integer $id
 * @property string $name
 * @property integer $type
 * @property double $base_price
 * @property integer $dop_connection
 * @property integer $only_one
 * @property integer $active
 */
class AdditionalServicesList extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'additional_services_list';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['type', 'dop_connection', 'only_one', 'active'], 'integer'],
            [['base_price'], 'number'],
            [['name'], 'string', 'max' => 255],
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
            'type' => 'Type',
            'base_price' => 'Base Price',
            'dop_connection' => 'Dop Connection',
            'only_one' => 'Only One',
            'active' => 'Active',
        ];
    }
}
