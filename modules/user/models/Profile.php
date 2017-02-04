<?php

namespace app\modules\user\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\user\models\User;

/**
 * UserSearch represents the model behind the search form about `app\modules\user\models\User`.
 */
class Profile extends User{
  /**
   * @inheritdoc
   */
  public function rules()
  {
    $rule=parent::rules();

    return $rule;
  }

  /**
   * Наименования дополнительных
   * полей формы
   * @return array
   */
  public function attributeLabels()
  {
    $labels = parent::attributeLabels();
    $labels['password'] = 'New password';
    return $labels;
  }

}