<?php

namespace app\modules\user\controllers;

use Yii;
use app\modules\user\models\User;
use app\modules\user\models\UserSearch;
use app\modules\user\models\EmailConfirm;
use app\modules\user\models\forms\RegistrationForm;
use app\modules\user\models\forms\PasswordResetForm;
use app\modules\user\models\forms\ProfileForm;
use app\modules\user\models\ResetPassword;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;


class UserController extends Controller
{
    /**
     * Разделение ролей
     * @inheritdoc
     */
    public function behaviors()
    {
        return [

        ];
    }
    /**
     * Деавторизация
     * @return \yii\web\Response
     */
    public function actionLogout(){
        Yii::$app->user->logout();
         return $this->goHome();
    }

    /**
     * Подтверждение аккаунта с помощью
     * электронной почты
     * @param $token - токен подтверждения, высылаемый почтой
     * @return \yii\web\Response
     * @throws BadRequestHttpException
     */
    public function actionConfirm($token){
        try {
            $model = new EmailConfirm($token);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
        if ($user_id = $model->confirmEmail()) {
            // Авторизируемся при успешном подтверждении
            //echo $user_id;
            $identity = User::findIdentity($user_id);
            Yii::$app->user->login($identity);
        }
        //return $token;
        return $this->redirect(['/']);
    }

    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionRegistration()
    {
        $model = new RegistrationForm();
        $check_the_mail = 'Check mail after registration';
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
          return 9;
            //обработка поступивших данных
            Yii::$app
                ->getSession()
                ->setFlash(
                    'signup-success',
                    'Please, confirm your account,instruction for activation sent on your specified Email.'
                );
            //Yii::$app->user->login(User::findByUsername($model- // login после регистрации. Пока убрали
            return $this->redirect(array('/'));
            //return $this->redirect(['view', 'id' => 'user']);
        }


        //выводим стндартную форму
        return $this->render('registration', [
            'model' => $model,
        ]);

    }
    /**
     * Сброс пароля
     * @return string|\yii\web\Response
     */
    public function actionResetpassword()
    {
        // Уже авторизированных отправляем на домашнюю страницу
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }
        //Восстановление пароля
        $forget = new PasswordResetForm();
        if ($forget->load(Yii::$app->request->post()) && $forget->validate()) {
            if ($forget->sendEmail()) { // Отправлено подтверждение по Email
                Yii::$app->getSession()->setFlash('reset-success', 'Instruction for activation password sent to your Email.');
            }
            return $this->goHome();
        }
        return $this->render('resetpaessword', [
            'forget' => $forget
        ]);

    }

    /**
     * Сброс пароля через электронную почту
     * @param $token - токен сброса пароля, высылаемый почтой
     * @param $password - новый пароль
     * @return \yii\web\Response
     * @throws BadRequestHttpException
     */
    public function actionReset($token, $password){
        try {
            $model = new ResetPassword($token, $password);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
        if ($user_id = $model->resetPassword()) {
            // Авторизируемся при успешном сбросе пароля
            Yii::$app->user->login(User::findIdentity($user_id));
        }
        return $this->redirect(['/']);
    }

    public function actionOnline(){
        if(Yii::$app->user->isGuest)return;

        User::getDb()->createCommand()->update(User::tableName(), [
            'last_online'=> time(),
        ], ['id' => Yii::$app->user->id])->execute();
        return 'is online';
    }



}