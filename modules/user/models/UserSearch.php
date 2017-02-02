<?php

namespace app\modules\user\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\user\models\User;

/**
 * UserSearch represents the model behind the search form about `app\modules\user\models\User`.
 */
class UserSearch extends User
{   public $fullName;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'city', 'sex','status', 'role', 'created_at', 'updated_at','moderate'], 'integer'],
            [[ 'email', 'first_name','fullName', 'last_name', 'password', 'password_reset_token', 'auth_key'], 'safe'],
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
        $query = User::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $sort=$dataProvider->getSort();
        $sort->attributes['fullName']=[
            'asc' => ['first_name' => SORT_ASC, 'last_name' => SORT_ASC],
            'desc' => ['first_name' => SORT_DESC, 'last_name' => SORT_DESC],
            'label' => 'Full Name',
            'default' => SORT_ASC
        ];
        unset($sort->attributes['country']);
        unset($sort->attributes['ip']);
        $dataProvider->setSort($sort);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'user.id' => $this->id,
            'city' => $this->city,
            'country' => $this->country,
            'sex' => $this->sex,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'first_name', $this->first_name])
            ->andFilterWhere(['like', 'last_name', $this->last_name]);

        $query->joinWith(['country']);

        // фильтр по имени
        if(strlen($this->fullName)>0) {
            $query->andWhere('first_name LIKE "%' . $this->fullName . '%" ' .
                'OR last_name LIKE "%' . $this->fullName . '%"'
            );
        }

        return $dataProvider;
    }
}
