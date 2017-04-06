<?php

namespace app\modules\additional_services\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\additional_services\models\AdditionalServices;

/**
 * AdditionalServicesSearch represents the model behind the search form about `app\modules\additional_services\models\AdditionalServices`.
 */
class AdditionalServicesSearch extends AdditionalServices
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'type', 'client_id', 'user_id', 'quantity'], 'integer'],
            [['parcel_id_lst', 'detail', 'status_pay'], 'safe'],
            [['price', 'gst', 'qst'], 'number'],
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
        $query = AdditionalServices::find();

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
            'type' => $this->type,
            'client_id' => $this->client_id,
            'user_id' => $this->user_id,
            'quantity' => $this->quantity,
            'price' => $this->price,
            'gst' => $this->gst,
            'qst' => $this->qst,
        ]);

        $query->andFilterWhere(['like', 'parcel_id_lst', $this->parcel_id_lst])
            ->andFilterWhere(['like', 'detail', $this->detail])
            ->andFilterWhere(['like', 'status_pay', $this->status_pay]);

        return $dataProvider;
    }
}
