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
use \yii\web\Response;
use yii\web\UploadedFile;

/**
 * DefaultController implements the CRUD actions for User model.
 */
class DefaultController extends Controller
{

  public function beforeAction($action)
  {
    if (Yii::$app->user->isGuest) {
      $this->redirect(['/']);
      return false;
    }
    return parent::beforeAction($action);
  }

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

  public function actionRequestMonthPay(){
    $request = Yii::$app->request;

    if(!$request->isAjax || !$request->isPost) {
      throw new NotFoundHttpException('The requested page could not be found.');
    }

    Yii::$app->getSession()->setFlash('success', 'Request has been sent. Wait for the administrator\'s response.');

    $user=User::findIdentity(Yii::$app->user->id);
    $user->month_pay=2;
    $user->save();

    Yii::$app->response->format = Response::FORMAT_JSON;
    return [
      'title' => "Request has been sent.",
      'content' => '',
      'forceClose'=>true,
      'forceReload'=>'#crud-datatable-pjax'
    ];
  }

  public function actionFileDelete(){
    $request=Yii::$app->request;
    if(!$request->isPost && !$request->isAjax){
      Yii::$app
        ->getSession()
        ->setFlash(
          'error',
          'Document not found'
        );
      return false;
    }

    $id=Yii::$app->user->id;
    $user=User::findOne([$id]);

    $user->delFile($request->post('key'));
    Yii::$app->response->format = Response::FORMAT_JSON;
    return true;

  }

  public function actionFileUpload(){
    $request=Yii::$app->request;
    if(!$request->isPost && !$request->isAjax){
      Yii::$app
        ->getSession()
        ->setFlash(
          'error',
          'Document not found'
        );
      return $this->redirect(['/parcels']);
    }

    $id=Yii::$app->user->id;
    $user=User::findOne([$id]);


    $files=UploadedFile::getInstances($user, 'files');
    return $user->loadDoc($files);
  }
}
