<?php

namespace app\modules\logs\controllers;

use Yii;
use yii\helpers\Html;
use \yii\web\Response;
use app\modules\logs\models\Log;
use app\modules\logs\models\LogSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\modules\orderElement\models\OrderElement;

/**
 * DefaultController implements the CRUD actions for Log model.
 */
class DefaultController extends Controller
{
    /**
     * Lists all Log models.
     * @return mixed
     */
    public function actionIndex($id)
    {
      $request = Yii::$app->request;
      if(!$request->isAjax){
        throw new NotFoundHttpException('Only for ajax.');
      };

      if(Yii::$app->user->isGuest){
        throw new NotFoundHttpException('This page is not available without authorization!');
      }


      if(!Yii::$app->user->identity->isManager()){
        $order=OrderElement::find()->andWhere(['id' => $id])->one();
        if($order->user_id!=Yii::$app->user->identity->getId()){
          throw new NotFoundHttpException('Access is denied.');
        }
      }

      $model = Log::find()->where(['order_id'=>$id])->orderBy('id desc')->all();

      Yii::$app->response->format = Response::FORMAT_JSON;
      return [
        'title'=> "Status history",
        'content'=>$this->renderAjax('index', [
          'model' => $model
        ]),
        'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"])

      ];
    }


}
