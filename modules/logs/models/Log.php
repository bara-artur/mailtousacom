<?php

namespace app\modules\logs\models;

use Yii;
use app\modules\user\models\User;

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
        2=>"Change in weight from $1 lb to $2 lb",
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

  public function getUser()
  {
    return $this->hasOne(User::className(), ['id' => 'user_id']);
  }

  public static function addLog($order_id,$data,$order=false){
    if(is_numeric($data)){
      $description=Log::getMsg($data);
      //импорт из...
      if($data==1){
        $txt="somewhere";
        switch ($order) {
          case 1:
            $txt='eBay';
            break;
        }
        $description=str_replace('$1',$txt,$description);
      }

      //смана веса с .. на..
      if($data==2){
        $description=str_replace('$1',$order[0],$description);
        $description=str_replace('$2',$order[1],$description);
      }

    }else if($data['text']){
      $description=$data['text'];
    }
    $model = new Log();
    $model->user_id = Yii::$app->user->identity->getId();
    $model->order_id = $order_id;
    $model->description = $description;
    $model->created_at = time();
    $model->save();
    return true;
  }
}
