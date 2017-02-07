<?php

namespace app\modules\address\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\address\models\Address;

/**
 * AddressSearch represents the model behind the search form about `app\modules\address\models\Address`.
 */
class AddressSearch extends Address
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'send_city', 'return_city'], 'integer'],
            [['send_first_name', 'send_last_name', 'send_company_name', 'send_adress_1', 'send_adress_2', 'return_first_name', 'return_last_name', 'return_company_name', 'return_adress_1', 'return_adress_2'], 'safe'],
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
        $query = Address::find();

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

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'user_id' => $this->user_id,
            'send_city' => $this->send_city,
            'return_city' => $this->return_city,
        ]);

        $query->andFilterWhere(['like', 'send_first_name', $this->send_first_name])
            ->andFilterWhere(['like', 'send_last_name', $this->send_last_name])
            ->andFilterWhere(['like', 'send_company_name', $this->send_company_name])
            ->andFilterWhere(['like', 'send_adress_1', $this->send_adress_1])
            ->andFilterWhere(['like', 'send_adress_2', $this->send_adress_2])
            ->andFilterWhere(['like', 'return_first_name', $this->return_first_name])
            ->andFilterWhere(['like', 'return_last_name', $this->return_last_name])
            ->andFilterWhere(['like', 'return_company_name', $this->return_company_name])
            ->andFilterWhere(['like', 'return_adress_1', $this->return_adress_1])
            ->andFilterWhere(['like', 'return_adress_2', $this->return_adress_2]);

        return $dataProvider;
    }
}
