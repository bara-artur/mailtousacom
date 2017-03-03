<?php

namespace app\modules\address\controllers;

use Yii;
use app\modules\address\models\Address;
use app\modules\address\models\AddressSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use \yii\web\Response;
use yii\helpers\Html;
use app\modules\order\models\Order;


/**
 * DefaultController implements the CRUD actions for Address model.
 */
class DefaultController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                    'bulk-delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all Address models.
     * @return mixed
     */
    public function actionCreateOrderBilling()
    {
      $order = Order::find()->where(['user_id'=>Yii::$app->user->id])->one();
      $update_button =0;
      if ($order) $update_button = 1;

      $model = Address::find()->where('user_id = :id', [':id' => Yii::$app->user->id])->one();
      if(!$model){
        $model= new Address();
      }

      $request = Yii::$app->request;
      if($request->getIsPost()){
        if($model->load($request->post()) && $model->save()){
            if ($update_button) return $this->redirect(['/']);
            else return $this->redirect(['addressusa']);
        }
        \Yii::$app->getSession()->setFlash('error', 'Error saving. Check the correctness of filling');

      }

      return $this->render('createorderbilling', [
        //'searchModel' => $searchModel,
        'model' => $model,
        'update_button' => $update_button,
        //'mainBillingAddress' => $mainBillingAddress,
        //'state_names' => $state_names,
      ]);
    }

    public function actionIndex()
    {
        $searchModel = new AddressSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $mainBillingAddress = 0;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'mainBillingAddress' => $mainBillingAddress
        ]);
    }

    public function actionAddressusa()
    {
      $order = Order::find()->where(['user_id'=>Yii::$app->user->id])->one();
      $show_button =1;
      if ($order) $show_button = 0;

      $model = Address::find()->where('user_id = :id', [':id' => Yii::$app->user->id])->one();
      if(!$model) {
        \Yii::$app->getSession()->setFlash('error', 'First you need to fill in billing address.');
        return $this->redirect(['create-order-billing']);
      }

      return $this->render('usaAddress', [
          'user' => $model,
          'show_button' => $show_button
      ]);
    }


    /**
     * Finds the Address model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Address the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Address::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    public function beforeAction($action)
    {
        // ...set `$this->enableCsrfValidation` here based on some conditions...
        // call parent method that will check CSRF if such property is true.
        if ($action->id === 'addressusa') {
            # code...
            $this->enableCsrfValidation = false;
        }
        return parent::beforeAction($action);
    }
}
