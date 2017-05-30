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
{
  public $fullName,$role;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'status', 'created_at', 'updated_at', 'last_online', 'ebay_account', 'month_pay'], 'integer'],
            [['username', 'email', 'first_name', 'last_name', 'fullName', 'password_hash', 'photo', 'password_reset_token', 'email_confirm_token', 'auth_key', 'login_at', 'ip', 'phone', 'docs', 'role'], 'safe'],
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

        $sort=$dataProvider->getSort();
        $sort->attributes['fullName']=[
          'asc' => ['first_name' => SORT_ASC, 'last_name' => SORT_ASC],
          'desc' => ['first_name' => SORT_DESC, 'last_name' => SORT_DESC],
          'label' => 'Full Name',
          'default' => SORT_ASC
        ];
        $dataProvider->setSort($sort);

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'status' => $this->status,
            'month_pay' => $this->month_pay,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'login_at' => $this->login_at,
            'last_online' => $this->last_online,
            'ebay_account' => $this->ebay_account,
        ]);

        $query->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'first_name', $this->first_name])
            ->andFilterWhere(['like', 'last_name', $this->last_name])
            ->andFilterWhere(['like', 'password_hash', $this->password_hash])
            ->andFilterWhere(['like', 'photo', $this->photo])
            ->andFilterWhere(['like', 'password_reset_token', $this->password_reset_token])
            ->andFilterWhere(['like', 'email_confirm_token', $this->email_confirm_token])
            ->andFilterWhere(['like', 'auth_key', $this->auth_key])
            ->andFilterWhere(['like', 'ip', $this->ip])
            ->andFilterWhere(['like', 'phone', $this->phone])
            ->andFilterWhere(['like', 'docs', $this->docs]);

        if($this->role && strlen(trim($this->role))>0) {
          $query->leftJoin('auth_assignment', 'user.id= auth_assignment.user_id');
          if($this->role==-1){
            $query->andWhere('auth_assignment.item_name  IS NULL');
          }else {
            $query->andWhere('auth_assignment.item_name=\'' . $this->role . '\'');
          }
        }
        // фильтр по имени
        if($this->fullName && strlen(trim($this->fullName))>0) {
          $this->fullName=trim($this->fullName);
          $query->andWhere('first_name LIKE "%' . $this->fullName . '%" ' .
            'OR last_name LIKE "%' . $this->fullName . '%"'
          );
        }


      return $dataProvider;
    }
}
