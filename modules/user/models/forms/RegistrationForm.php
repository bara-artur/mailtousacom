<?php

namespace app\modules\user\models\forms;
use Yii;
use app\modules\user\models\User;
use app\modules\user\models\forms\CreateForm;


class RegistrationForm extends CreateForm
{
    public $password;   // Пароль
    public $captcha;    // Капча

    public function rules()
    {
        $rules = parent::rules();
        return $rules;
    }

    /**
     * Отправка письма согласно шаблону "confirmEmail"
     * после регистрации
     * @param bool $insert
     * @param array $changedAttributes
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        $view = '@app/modules/user/mail/confirmEmail';
        if (method_exists(\Yii::$app->controller->module, 'getCustomMailView')) {
            $view = \Yii::$app->controller->module->getCustomMailView('confirmEmail', $view);
        }

        Yii::$app->mailer->compose($view, ['model' => $this])
            ->setFrom([Yii::$app->config->get('adminEmail')])
            ->setTo($this->email)
            ->setSubject('Confirmation of registration at the website')
            ->send();
    }

    public function beforeSave($insert)
    {
         if (parent::beforeSave($insert)) {
            $this->status = 2;
            return true;
        }
        return false;
    }
}