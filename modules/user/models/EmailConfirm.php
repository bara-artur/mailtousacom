<?php
/**
 * @package   yii2-user
 * @author    Yuri Shekhovtsov <shekhovtsovy@yandex.ru>
 * @copyright Copyright &copy; Yuri Shekhovtsov, lowbase.ru, 2015 - 2016
 * @version   1.0.0
 */
namespace app\modules\user\models;
use yii\base\InvalidParamException;
use yii\base\Model;
use Yii;
use app\modules\user\models\User;

class EmailConfirm extends Model
{
    /**
     * @var User
     */
    private $_user;
    /**
     * @param  string $token - токен
     * @param  array $config - параметры
     * @throws \yii\base\InvalidParamException - при пустом или неправильном токене
     */
    public function __construct($token, $config = [])
    {
        if (empty($token) || !is_string($token)) {
            throw new InvalidParamException('No confirmation code.');
        }
        $this->_user = User::findByEmailConfirmToken($token);
        if (!$this->_user) {
            throw new InvalidParamException('Invalid token.');
        }
        parent::__construct($config);
    }

    public function beforeSave($insert)
    {
      return true;
    }
    /**
     * Подтверждение электронной почты
     * @return bool|int
     */
    public function confirmEmail()
    {
      $user = $this->_user;
      $user->status = User::STATUS_ACTIVE;
      return ($user->removeEmailConfirmToken()) ? $user->id : false;
    }
}