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
            [['id', 'user_id', 'city', 'address_type'], 'integer'],
            [['first_name', 'last_name', 'company_name', 'adress_1', 'adress_2'], 'safe'],
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
            'user_id' => $this->user_id,
            'city' => $this->city,
            'address_type' => $this->address_type,
        ]);

        $query->andFilterWhere(['like', 'first_name', $this->first_name])
            ->andFilterWhere(['like', 'last_name', $this->last_name])
            ->andFilterWhere(['like', 'company_name', $this->company_name])
            ->andFilterWhere(['like', 'adress_1', $this->adress_1])
            ->andFilterWhere(['like', 'adress_2', $this->adress_2]);

        return $dataProvider;
    }
}
