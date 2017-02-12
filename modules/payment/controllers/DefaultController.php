<?php

//http://paypal.github.io/PayPal-PHP-SDK/sample/
//https://github.com/paypal/PayPal-PHP-SDK/blob/master/sample/payments/ExecutePayment.php
//http://paypal.github.io/PayPal-PHP-SDK/sample/doc/payments/OrderGet.html
//http://paypal.github.io/PayPal-PHP-SDK/sample/doc/payments/OrderCreateForVoid.html

namespace app\modules\payment\controllers;

use Yii;
use app\modules\payment\models\PaymentsList;
use app\modules\payment\models\PaymentSearch;
use app\modules\payment\models\DoPayment;

use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use PayPal\Api\Address;
use PayPal\Api\CreditCard;
use PayPal\Api\Amount;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\Transaction;
use PayPal\Api\FundingInstrument;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Api\Details;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\RedirectUrls;
use PayPal\Rest\ApiContext;
use PayPal\Api\ExecutePayment;
use PayPal\Api\PaymentExecution;

/**
 * DefaultController implements the CRUD actions for PaymentsList model.
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
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all PaymentsList models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PaymentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lists all PaymentsList models.
     * @return mixed
     */
    public function actionTest(){
      //$pay=new DoPayment('credit_card');
      $pay=new DoPayment();

      $pay->addItem(array(
        'name'=>'Ground Coffee 40 oz',
        'price'=>7,
        'tax'=>0.52
      ));
      $pay->addItem(array(
        'name'=>'Granola bars',
        'quantity'=>2,
        'price'=>30,
        'tax'=>0.15
      ));
      $payment= $pay->make_payment();

      echo $payment->getId().'<br>';
      $approvalUrl = $payment->getApprovalLink();
      echo $approvalUrl;
      //return $this->redirect($approvalUrl);
    }

    public function actionFinish(){
      $pay=new DoPayment();
      $payment=$pay->finishPayment();

      echo $payment->getId().'<br>';
      echo $payment->getState().'<br>';
      ddd($payment->getTransactions());
    }
    /**
     * Displays a single PaymentsList model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new PaymentsList model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new PaymentsList();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing PaymentsList model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing PaymentsList model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the PaymentsList model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return PaymentsList the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = PaymentsList::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
