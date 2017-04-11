<?php

namespace app\modules\cron\controllers;

use Yii;
use yii\web\Controller;
use app\modules\orderElement\models\OrderElement;
use app\modules\orderElement\models\OrderElementSearch;

/**
 * Default controller for the `cron` module
 */
class DefaultController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
      $searchModel = new OrderElementSearch();
      $dataProvider = $searchModel->search(Yii::$app->request->queryParams, null);

      return $this->render('index', [
        'searchModel' => $searchModel,
        'dataProvider' => $dataProvider,
      ]);
    }
}
