<?php

namespace app\modules\address\models;

use Yii;

/**
 * This is the model class for table "address".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $send_first_name
 * @property string $send_last_name
 * @property string $send_company_name
 * @property string $send_adress_1
 * @property string $send_adress_2
 * @property integer $send_city
 * @property string $return_first_name
 * @property string $return_last_name
 * @property string $return_company_name
 * @property string $return_adress_1
 * @property string $return_adress_2
 * @property integer $return_city
 */
class Address extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'address';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'send_first_name', 'send_last_name', 'send_company_name', 'send_adress_1', 'send_adress_2', 'send_city', 'return_first_name', 'return_last_name', 'return_company_name', 'return_adress_1', 'return_adress_2', 'return_city'], 'required'],
            [['user_id', 'send_city', 'return_city'], 'integer'],
            [['send_first_name', 'send_last_name', 'return_first_name', 'return_last_name'], 'string', 'max' => 60],
            [['send_company_name', 'return_company_name'], 'string', 'max' => 128],
            [['send_adress_1', 'send_adress_2', 'return_adress_1', 'return_adress_2'], 'string', 'max' => 256],
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
            'send_first_name' => 'Send First Name',
            'send_last_name' => 'Send Last Name',
            'send_company_name' => 'Send Company Name',
            'send_adress_1' => 'Send Adress 1',
            'send_adress_2' => 'Send Adress 2',
            'send_city' => 'Send City',
            'return_first_name' => 'Return First Name',
            'return_last_name' => 'Return Last Name',
            'return_company_name' => 'Return Company Name',
            'return_adress_1' => 'Return Adress 1',
            'return_adress_2' => 'Return Adress 2',
            'return_city' => 'Return City',
        ];
    }
}
