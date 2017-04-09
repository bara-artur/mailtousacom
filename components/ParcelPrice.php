<?php

namespace app\components;

use Yii;
use yii\base\Widget;
use yii\db\Query;
use app\modules\orderElement\models\OrderElement;
use app\modules\logs\models\Log;

class ParcelPrice extends Widget
{
  public $weight;
  public $user;

  public function run()
  {
    parent::init();

    //OrderElement
    $time=time()-YII::$app->params['preiod_parcel_count']*24*60*60;
    $send_cnt=Log::find()->where(['user_id'=>$this->user,'status_id'=>2])->asArray()->all();
    $send_cnt=count($send_cnt);

    $query = new Query;
    $query->select('price')
      ->from('tariffs')
      ->andWhere(['>=', 'weight', $this->weight])
      ->andWhere(['>', 'weight', 0])
      ->andWhere(['<=', 'parcel_count', 1])
      ->orderBy([
        'weight' => SORT_ASC,
        'parcel_count' => SORT_DESC,
      ])
      //->limit(1)
    ;
    $row = $query->one();
    if($row){
      return number_format($row['price'],2,'.','');
    }else{
      return false;
    }
  }
}