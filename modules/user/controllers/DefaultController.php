<?php

namespace app\modules\user\controllers;

use Yii;
use app\modules\user\models\User;
use app\modules\user\models\UserSearch;
use app\modules\user\models\forms\RegistrationForm;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
/**
 * DefaultController implements the CRUD actions for User model.
 */
class DefaultController extends Controller
{
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? '869356856766867' : null,
                'foreColor' => '3373751', //синий

            ],
        ];
    }


}
