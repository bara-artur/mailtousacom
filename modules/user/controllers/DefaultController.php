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
use app\modules\user\models\Profile;
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

  public function actionProfile(){

    $model = User::findIdentity(Yii::$app->user->id);
    if ($model === null) {
      throw new NotFoundHttpException('The requested page could not be found.');
    }

    $post=Yii::$app->request->post();
    if(isset($post['Profile'])){
      //удаляем картинку до сохранения
      $post['Profile']['photo']=$model->photo;
      //добавляем метку обновления
      $post['Profile']['updated_at'] = date("Y-m-d H:i:s");;
    }

    $request = Yii::$app->request;
    $request = Yii::$app->request;
    if($request->isPost) {
      if($model->load($post) && $model->validate() && $model->save()){
        Yii::$app->getSession()->setFlash('success', 'Profile updated.');
        return $this->redirect(['profile']);
      }
    }
    //выводим стндартную форму
    return $this->render('profile', [
      'model' => $model,
    ]);
  }
}
