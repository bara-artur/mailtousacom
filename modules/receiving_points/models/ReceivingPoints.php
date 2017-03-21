<?php

namespace app\modules\receiving_points\models;

use Yii;

/**
 * This is the model class for table "receiving_points".
 *
 * @property integer $id
 * @property string $name
 * @property string $address
 * @property integer $active
 */
class ReceivingPoints extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'receiving_points';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['active'], 'integer'],
            [['name', 'address'], 'string', 'max' => 255],
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
            'address' => 'Address',
            'active' => 'Active',
        ];
    }
}
