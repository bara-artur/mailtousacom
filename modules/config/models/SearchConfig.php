<?php

namespace app\modules\config\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\config\models\Config;

/**
 * SearchConfig represents the model behind the search form about `app\modules\config\models\Config`.
 */
class SearchConfig extends Config
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'updated'], 'integer'],
            [['param', 'value', 'default', 'label', 'type'], 'safe'],
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
        $query = Config::find();

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
            'updated' => $this->updated,
        ]);

        $query->andFilterWhere(['like', 'param', $this->param])
            ->andFilterWhere(['like', 'value', $this->value])
            ->andFilterWhere(['like', 'default', $this->default])
            ->andFilterWhere(['like', 'label', $this->label])
            ->andFilterWhere(['like', 'type', $this->type]);

        return $dataProvider;
    }
}
