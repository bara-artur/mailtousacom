<?php

namespace app\modules\orderElement\models;

use Yii;
use app\modules\orderInclude\models\OrderInclude;
use yii\data\ActiveDataProvider;
/**
 * This is the model class for table "order_element".
 *
 * @property integer $id
 * @property string $first_name
 * @property string $last_name
 * @property string $company_name
 * @property string $adress_1
 * @property string $adress_2
 * @property string $city
 * @property string $zip
 * @property string $phone
 * @property string $state
 */
class OrderElement extends \yii\db\ActiveRecord
{
    public $includes_packs;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'order_element';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['first_name', 'last_name','company_name', 'adress_1','city', 'zip', 'phone', 'state'], 'required'],
            [['first_name', 'last_name', 'city', 'zip', 'phone', 'state'], 'string', 'max' => 60],
            [['company_name'], 'string', 'max' => 128],
            [['order_id','track_number'], 'integer'],
            [['weight'], 'double'],
            [['track_number_type'], 'integer'],
            [['address_type','weight','track_number','track_number_type'], 'safe'],
            [['adress_1', 'adress_2'], 'string', 'max' => 256],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'company_name' => 'Company Name',
            'adress_1' => 'Adress 1',
            'adress_2' => 'Adress 2',
            'city' => 'City',
            'zip' => 'Zip',
            'phone' => 'Phone',
            'state' => 'State',
        ];
    }

    public function getOrderInclude()
    {
        return $this->hasMany(OrderInclude::className(),['order_id' => 'id']);
    }

  public function getWeight_lb(){
    return floor($this->weight);
  }

  public function getWeight_oz(){
    return floor(($this->weight-floor($this->weight))*16);
  }

  public function getIncludesSearch(){
    $query = OrderInclude::find();
    $dataProvider = new ActiveDataProvider([
      'query' => $query,
    ]);
    $query->andFilterWhere([
      'order_id'=>$this->id
    ]);
    return $dataProvider;

  }
  public function getIncludes(){
    $query = OrderInclude::find()->where(['order_id'=>$this->id])->asArray()->all();
    return $query;
  }
}
