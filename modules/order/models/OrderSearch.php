<?php

namespace app\modules\order\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\order\models\Order;

/**
 * OrderSearch represents the model behind the search form about `app\modules\order\models\Order`.
 */
class OrderSearch extends Order
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'user_id'], 'integer'],
            [['created_at','created_at_to'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params, $time_to)
    {
        $query = Order::find();
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
          'query' => $query,
          'sort'=>array(
            'defaultOrder'=>[
              'id'=>SORT_DESC
              ]
          ),
        ]);

        $this->load($params);
        $date_from = strtotime($this['created_at']);
        $date_to =   strtotime($time_to['created_at_to']);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        // grid filtering conditions
        if ($date_from!=null) $query->andFilterWhere(['>=', 'created_at', $date_from]);
        if ($date_to!=null) $query->andFilterWhere(['<=', 'created_at', $date_to+24*3600]);
        $query->andFilterWhere([
            'id' => $this->id,
            'user_id' => $this->user_id,
        ]);
        return $dataProvider;
    }
}
