<?php

namespace app\modules\tariff\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\tariff\models\Tariffs;

/**
 * TariffsSearch represents the model behind the search form about `app\modules\tariff\models\Tariffs`.
 */
class TariffsSearch extends Tariffs
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'parcel_count'], 'integer'],
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
    public function search($params)
    {
        $query = Tariffs::find();

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
            'parcel_count' => $this->parcel_count,
            'price' => $this->price,
        ]);

        return $dataProvider;
    }
}
