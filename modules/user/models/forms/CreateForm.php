<?php

namespace app\modules\user\models\forms;
use Yii;
use app\modules\user\models\User;


class CreateForm extends User
{
    public $password;   // Пароль
    public $captcha;    // Капча

    public function rules()
    {
        $rules = parent::rules();
        $rules[] = [['password', 'email'], 'required'];
        return $rules;
    }
    /**
     * Генерация ключа авторизации, токена подтверждения регистрации
     * и хеширование пароля перед сохранением
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        //var_dump($this);

        //проверяем существовние пользователя
        if ($this->findByEmail($this->email)) {
            $this->addError('email', 'Email already exists');
        }

        if($this->findByUsername($this->username)) {
            $this->addError('username', 'Username already exists');
        }

        if ($this->hasErrors()) return false;

        if (parent::beforeSave($insert)) {
            $this->status = 1;
            $this->photo = '';
            $this->created_at=time();
            $this->updated_at = time();
            //var_dump($this);
            $this->setPassword($this->password);
            //var_dump($this);
            $this->generateAuthKey();
            $this->generateEmailConfirmToken();
            return true;
        }
        return false;
    }

}