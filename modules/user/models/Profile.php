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

  /**
   * Генерация пароля и ключа авторизации,
   * преобразование дня рождения в необходимый
   * формат перед сохранением
   *
   * @param bool $insert
   * @return bool
   */
  public function beforeSave($insert)
  {
    if (parent::beforeSave($insert)) {
      // Если указан новый пароль
      if ($this->password) {
        $this->setPassword($this->password);
        $this->generateAuthKey();
      }
      return true;
    }
    return false;
  }

  /**
   * Валидация пароля
   *
   * Указывается обязательно при отсутствии
   * ключей авторизации соц. сетей
   */
  public function passwordValidate()
  {
    if ($this->password_hash === null && !$this->password && !UserOauthKey::isOAuth($this->id)) {
      $this->addError('password', Yii::t('user', 'Необходимо указать пароль.'));
    }
  }
}