<?php

namespace app\modules\order\models;

use Yii;
use app\modules\orderElement\models\OrderElement;
use app\modules\user\models\User;

/**
 * This is the model class for table "order".
 *
 * @property integer $id
 * @property string $el_group
 * @property integer $user_id
 * @property string $created_at
 */
class Order extends \yii\db\ActiveRecord
{
    public static function getTextStatus(){
        return array(
            ''=>'All',
            '0'=>'Draft',
            '1'=>'Awaiting at MailtoUSA facility',
            '2'=>'Received at MailtoUSA facility',
            '3'=>'On route to USA border',
            '4'=>'Transferred to XXX faclitity',
            '5'=>'YYY status',
            '6'=>'Returned at MailtoUSA facility',
        );
    }

    public static function orderStatusText($param)
    {
        $textForStatus =  Order::getTextStatus();
        if ($param < (count($textForStatus)-1)) return  $textForStatus[$param];
        else return 'Unknown status';
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'order';
    }

    public function getUser()
    {
      return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function getOrderElement()
    {
        return $this->hasMany(OrderElement::className(),['order_id' => 'id']);
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [[ 'user_id'], 'integer'],
            [[ 'el_group'], 'string'],
            [['created_at'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'Order ID',
            'user_id' => 'User ID',
            'el_group' => 'User ID',
            'created_at' => 'Created At',

        ];
    }

}
