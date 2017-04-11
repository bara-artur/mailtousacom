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
      $data=OrderElement::find()->orderBy(['cron_refresh' => SORT_ASC])->limit(10)->all();
      foreach ($data as $parcel){
        $parcel->cron_refresh = time();
        $parcel->save();
      }
      return $this->render('index', [
       'data' => $data,
      ]);
    }
}
