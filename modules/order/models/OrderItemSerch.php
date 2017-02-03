<?php

namespace app\modules\order\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\order\models\OrderItems;

/**
 * OrderItemSerch represents the model behind the search form about `app\modules\order\models\OrderItems`.
 */
class OrderItemSerch extends OrderItems
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'order_id', 'quantity','status'], 'integer'],
            [['product_name'], 'safe'],
            [['item_price'], 'number'],
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
        $query = OrderItems::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'order_id' => $this->order_id,
            'item_price' => $this->item_price,
            'quantity' => $this->quantity,
        ]);

        $query->andFilterWhere(['like', 'product_name', $this->product_name]);

        return $dataProvider;
    }
}
