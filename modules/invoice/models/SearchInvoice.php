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
      [['id','user_id','price','price_end',
        'create','created_at_to',
        'pay_status','user_input'], 'safe'],
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
    public function search($params, $time_to)
    {
      $query = Invoice::find();

      // add conditions that should always apply here

      $dataProvider = new ActiveDataProvider([
          'query' => $query,
      ]);

      $this->load($params);
      $date_from = strtotime($this['create']);
      $date_to =   strtotime($time_to['created_at_to']);
      $price = (int)$this->price;
      if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
      if ($date_from!=null) $query->andFilterWhere(['>=', 'create', $date_from]);
      if ($date_to!=null) $query->andFilterWhere(['<=', 'create', $date_to+24*3600]);
      if (isset($time_to['price_end'])) {
        $query->andFilterWhere(['<=', 'price', $time_to['price_end']]);
      }
      $query->andFilterWhere(['>=', 'price', $price]);
      $query->andFilterWhere([
        'id' => $this->id,
        'user_id' => $this->user_id,
        'pay_status' => $this->pay_status,
      ]);


      return $dataProvider;
    }
}
