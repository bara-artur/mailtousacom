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
            [['id', 'billing_address_id', 'order_type',
                'user_id', 'user_id_750', 'order_status',
                'payment_state','payment_type'], 'integer'],
            [['created_at', 'transport_data'], 'safe'],
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
    public function search($params)
    {
        $query = Order::find();
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);
        $date_from = strtotime(substr($this->created_at, 0, 10));

        $date_to = $date_from+2592000;// strtotime(substr($this->created_at, 13, 10));
        //var_dump('---'.$date_to);
        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        // grid filtering conditions
        $query->andFilterWhere(['>=', 'created_at', $date_from]);
        $query->andFilterWhere(['<=', 'created_at', $date_to]);
        $query->andFilterWhere([
            'id' => $this->id,
            'billing_address_id' => $this->billing_address_id,
            'order_type' => $this->order_type,
            'user_id' => $this->user_id,
            'user_id_750' => $this->user_id_750,
            'order_status' => $this->order_status,
            'transport_data' => $this->transport_data,
            'payment_state' => $this->payment_state,
            'payment_type' => $this->payment_type,
        ]);
  
        return $dataProvider;
    }
}
