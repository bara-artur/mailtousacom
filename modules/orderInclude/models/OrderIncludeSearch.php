<?php

namespace app\modules\orderInclude\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\orderInclude\models\OrderInclude;

/**
 * OrderIncludeSearch represents the model behind the search form about `app\modules\orderInclude\models\OrderInclude`.
 */
class OrderIncludeSearch extends OrderInclude
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'order_id', 'weight', 'quantity'], 'integer'],
            [['name'], 'safe'],
            [['price'], 'number'],
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
    public function search($params,$order_id)
    {
        $query = OrderInclude::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        $this->order_id = $order_id;

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'order_id' => $this->order_id,
            'price' => $this->price,
            'weight' => $this->weight,
            'quantity' => $this->quantity,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }
}