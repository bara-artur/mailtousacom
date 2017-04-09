<?php

namespace app\components;

use Yii;
use yii\base\Widget;
use yii\db\Query;
use app\modules\orderElement\models\OrderElement;
use app\modules\logs\models\Log;
use app\modules\user\models\User;

class ParcelPrice extends Widget
{
  public $weight;
  public $user;

  public function run()
  {
    parent::init();

    if(!$this->user){
      $this->user=YII::$app->user->id;
    }
    //OrderElement
    $time=time()-YII::$app->params['preiod_parcel_count']*24*60*60;
    $send_cnt=Log::find()->where(['user_id'=>$this->user,'status_id'=>2])->asArray()->all();
    $send_cnt=count($send_cnt);

    $user_tarif=User::find()->where(['id'=>$this->user])->asArray()->one();
    if(!$user_tarif)return false;
    $user_tarif=json_decode($user_tarif['tariff'],true);
    if(
      gettype($user_tarif)=="integer" ||
      (isset($user_tarif['count']) && $user_tarif=$user_tarif['count'])
    ){
      $send_cnt=($user_tarif>$send_cnt)?$user_tarif:$send_cnt;
      $user_tarif=false;
    }else{

    };
    $query = new Query;
    $query->select('price')
      ->from('tariffs')
      ->andWhere(['>=', 'weight', $this->weight])
      ->andWhere(['>', 'weight', 0])
      ->andWhere(['<=', 'parcel_count', $send_cnt])
      ->orderBy([
        'weight' => SORT_ASC,
        'parcel_count' => SORT_DESC,
      ])
      //->limit(1)
    ;
    $row = $query->one();
    if($row){
      $price=$row['price'];
      $price_t=false;
      if($user_tarif){
        foreach ($user_tarif as $w=>$val){
          if($w>$this->weight && !$price_t){
            $price_t=$val;
          }
        }
        if($price_t && $price_t<$price){
          $price=$price_t;
        }
      }
      return number_format($price,2,'.','');
    }else{
      return false;
    }
  }
}