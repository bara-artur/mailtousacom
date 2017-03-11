<?php

namespace app\modules\user\models;

use app\modules\user\models;
use app\modules\user\models\User;
use yii\base\InvalidParamException;
use yii\base\Model;
use Yii;
/**
 * Восстановление пароля
 */
class UserAdminCreate extends User
{
  public function rules()
  {
    $rule = parent::rules();
    $rule[]=[['first_name','last_name','phone','password'], 'required'];
    return $rule;
  }
}