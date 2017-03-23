<?php

namespace app\modules\logs\models;

use Yii;

/**
 * This is the model class for table "log".
 *
 * @property int $id
 * @property int $user_id
 * @property int $order_id
 * @property string $description
 * @property int $created_at
 */
class Log extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'order_id', 'description', 'created_at'], 'required'],
            [['user_id', 'order_id'], 'integer'],
           // [['description'], 'string', 'max' => 32],
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
            'order_id' => 'Order ID',
            'description' => 'Description',
            'created_at' => 'Created At',
        ];
    }

  public static function addLog($order_id,$data){

    if($data['test']){
      $description=$data['text'];
    }
    $model = new Log();
    $model->user_id = Yii::$app->user->identity->getId();
    $model->order_id = $order_id;
    $model->description = $description;
    $model->created_at = time();
    $model->save();

  }
}
