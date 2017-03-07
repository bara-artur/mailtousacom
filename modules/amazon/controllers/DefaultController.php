<?php

namespace app\modules\amazon\controllers;

use Yii;
use yii\web\Controller;
use app\modules\orderElement\models\OrderElement;
use app\modules\orderInclude\models\OrderInclude;
use app\modules\order\models\Order;
use yii\web\NotFoundHttpException;

use app\modules\user\models\User;
/**
 * DefaultController implements the CRUD actions for Order model.
 */
class DefaultController extends Controller
{
  public function actionTest(){
    $s3 = Yii::$app->get('s3');
    return 123;
  }
}
