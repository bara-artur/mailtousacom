<?php

namespace app\modules\user\models;

use app\modules\user\models;
use yii\base\InvalidParamException;
use yii\base\Model;
use Yii;
/**
 * Восстановление пароля
 */
class ResetPassword extends Model
{
    // Новый пароль
    public $password;
    private $_user;
    /**
     * @param array $token - токен
     * @param $password - пароль
     * @param array $config - праметры
     */
    public function __construct($token, $password, $config = [])
    {
        if (empty($token) || !is_string($token)) {
            throw new InvalidParamException('The key was not found for password recovery.');
        }
        if (empty($password) || !is_string($password)) {
            throw new InvalidParamException('Password is not set.');
        }
        $this->_user = User::findByPasswordResetToken($token);
        $this->password = $password;
        if (!$this->_user) {
            throw new InvalidParamException('Wrong key for password recovery.');
        }
        parent::__construct($config);
    }
    /**
     * Сброс пароля на новый
     * @return bool|int
     */
    public function resetPassword()
    {
        $user = $this->_user;
        $user->setPassword($this->password);
        $user->removePasswordResetToken();  // Удаление токена восстановления пароля
        $user->status = User::STATUS_ACTIVE;
        $user->removeEmailConfirmToken();
        return (($user->save())) ? $user->id : false;
    }
}