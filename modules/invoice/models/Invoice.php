<?php

namespace app\modules\invoice\models;

use Yii;

/**
 * This is the model class for table "invoices".
 *
 * @property integer $id
 * @property string $parcels_list
 * @property string $services_list
 * @property integer $pay_status
 * @property integer $create
 */
class Invoice extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'invoices';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pay_status', 'create'], 'integer'],
            [['parcels_list', 'services_list'], 'string', 'max' => 500],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'parcels_list' => 'Parcels List',
            'services_list' => 'Services List',
            'pay_status' => 'Pay Status',
            'create' => 'Create',
        ];
    }
}
