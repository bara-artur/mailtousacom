<?php

namespace app\components;

use Yii;
use yii\base\Widget;
use yii\db\Query;

class ParcelPrice extends Widget
{
  public $weight;
  public $user;

  public function run()
  {
    parent::init();
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