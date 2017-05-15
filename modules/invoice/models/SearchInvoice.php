<?php

namespace app\modules\invoice\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\invoice\models\Invoice;

/**
 * SearchConfig represents the model behind the search form about `app\modules\config\models\Config`.
 */
class SearchInvoice extends Invoice
{
  /**
   * @inheritdoc
   */
  public function rules()
  {
    return [
      [['pay_status', 'create'], 'integer'],
      [['parcels_list', 'services_list','detail'], 'string', 'max' => 500],
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
        $query = Invoice::find();

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
        ]);


        return $dataProvider;
    }
}
