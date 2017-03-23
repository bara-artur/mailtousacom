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

    public static function getMsg($i){
      $msg= [
        0=>"Created parcel",
        1=>"Import parcel from $1",
      ];
      return $msg[$i];
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

  public static function addLog($order_id,$data,$order=false){
    if(is_numeric($data)){
      $description=Log::getMsg($data);
      if($data==1){
        $txt="somewhere";
        switch ($order) {
          case 1:
            $txt='eBay';
            break;
        }
        $description=str_replace('$1',$txt,$description);
      }
    }else if($data['test']){
      $description=$data['text'];
    }
    $model = new Log();
    $model->user_id = Yii::$app->user->identity->getId();
    $model->order_id = $order_id;
    $model->description = $description;
    $model->created_at = time();
    $model->save();
    var_dump($model);
  }
}
