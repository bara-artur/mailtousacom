<?php

namespace app\modules\orderElement\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\orderElement\models\OrderElement;

/**
 * OrderElementSearch represents the model behind the search form about `app\modules\orderElement\models\OrderElement`.
 */
class OrderElementSearch extends OrderElement
{
    /**
     * @inheritdoc
     */
    public $price_end;

    public function rules()
    {
        return [
            [['id', 'status',
              'payment_state'], 'integer'],
            [['first_name', 'last_name', 'company_name', 'adress_1', 'adress_2',
               'city', 'zip', 'phone', 'state','created_at', 'transport_data',
               'transport_data_to','created_at_to','user_id','payment_state','price','price_end','track_number'], 'safe'],
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
    public function search($params,$time_to)
    {
        $query = OrderElement::find();

        $dataProvider = new ActiveDataProvider([
          'query' => $query,
          'sort'=> ['defaultOrder' => ['id'=>SORT_DESC]],
        ]);

        $this->load($params);
        $date_from = strtotime($this['created_at']);
        $date_to =   strtotime($time_to['created_at_to']);

        if (!$this->validate()) {
          // uncomment the following line if you do not want to return any records when validation fails
          // $query->where('0=1');
          return $dataProvider;
        }
      //  var_dump($params);
        // grid filtering conditions
        if ($date_from!=null) $query->andFilterWhere(['>=', 'created_at', $date_from]);
        if ($date_to!=null) $query->andFilterWhere(['<=', 'created_at', $date_to+24*3600]);
        if (isset($this['price'])) $query->andFilterWhere(['>=', 'price', $this['price']]);
        if (isset($time_to['price_end'])) $query->andFilterWhere(['<=', 'price', $time_to['price_end']]);
        $query->andFilterWhere([
          'id' => $this->id,
          'user_id' => $this->user_id,
          'status' => $this->status,
          'payment_state' => $this->payment_state,
          'track_number' => $this->track_number,
        ]);

        $query->andFilterWhere(['like', 'first_name', $this->first_name])
            ->andFilterWhere(['like', 'last_name', $this->last_name])
            ->andFilterWhere(['like', 'company_name', $this->company_name])
            ->andFilterWhere(['like', 'adress_1', $this->adress_1])
            ->andFilterWhere(['like', 'adress_2', $this->adress_2])
            ->andFilterWhere(['like', 'city', $this->city])
            ->andFilterWhere(['like', 'zip', $this->zip])
            ->andFilterWhere(['like', 'phone', $this->phone])
            ->andFilterWhere(['like', 'state', $this->state]);

        return $dataProvider;
    }
}
