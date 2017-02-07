<?php

namespace app\modules\user\models\forms;
use Yii;
use app\modules\user\models\User;
use yii\base\Model;
/**
 * Форма авторизации
 * Class LoginForm
 * @package lowbase\user\models\forms
 */

class LoginForm extends Model
{
    public $email;              // Электронная почта
    public $password;           // Пароль
    public $rememberMe = true;  // Запомнить меня
    private $_user = false;
    /**
     * Правила валидации
     * @return array
     */
    public function rules()
    {
        return [
            // И Email и пароль должны быть заполнены
            [['email', 'password'], 'required'],
            // Булево значение (галочка)
            ['rememberMe', 'boolean'],
            // Валидация пароля из метода "validatePassword"
            ['password', 'validatePassword'],
            // Электронная почта
            ['email', 'email'],
        ];
    }
    /**
     * Наименование полей формы
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'password' => 'Password',
            'email' => 'Email',
            'rememberMe' => 'Remember me',
        ];
    }
    /**
     * Проверка комбинации Email - Пароль
     * @param $attribute
     */
    public function validatePassword($attribute)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError( $attribute, 'Email or Password incorrectly entered.');
            } elseif ($user && $user->status == User::STATUS_WAIT) {
                $this->addError('email','Account isn\'t confirmed. Check Email.');
            } elseif ($user && $user->status == User::STATUS_BLOCKED) {
                $this->addError('email', 'Account blocked. Contact administrator.');
            }
        }
    }
    /**
     * Авторизация
     * @return bool
     */
    public function login()
    {
        if ($this->validate()) {
            return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600*24*30 : 0);
        }
        return false;
    }
    /**
     * Получение модели пользователя
     * @return bool|null|static
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = User::findOne(['email' => $this->email]);
        }
        return $this->_user;
    }
}