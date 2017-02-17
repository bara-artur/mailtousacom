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
use app\modules\order\models\Order;
use app\modules\orderInclude\models\OrderInclude;
use app\modules\orderElement\models\OrderElement;
use yii\db\Query;

use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\components\ParcelPrice;

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
    public function actionOrder($id){
      $order = Order::findOne($id);

      if($order->payment_state!=0){
        \Yii::$app->getSession()->setFlash('info', 'Order paid previously and can not be re-paid.');
        return $this->redirect(['/']);
      }

      if($order->transport_data<time())$order->transport_data=strtotime('+1 days');
      $order->transport_data=date('d-M-Y', $order->transport_data);

      $model = OrderElement::find()->where(['order_id'=>$id])->all();

      $payments=array();


      $total=array(
        'price'=>0,
        'weight'=>0,
        'quantity'=>0,
        'gst'=>0,
        'qst'=>0,
        'sum'=>0,
      );

      $query = new Query;
      $query->select('state')
        ->from('new_address')
        ->where(['id'=>$order->billing_address_id]);
      $row = $query->one();
      $state=$row['state'];

      $query = new Query;
      $query->select(['qst','gst'])
        ->from('state')
        ->where(['name'=>$state]);
      $tax = $query->one();


      foreach ($model as &$pac) {
        $pac->includes_packs = $pac->getIncludes();
        $this_weight = 0;
        foreach ($pac->includes_packs as $pack) {
          $total['price'] += $pack['price'] * $pack['quantity'];
          $total['weight'] += $pack['weight'] * $pack['quantity'];
          $total['quantity'] += $pack['quantity'];
          $this_weight += $pack['weight'] * $pack['quantity'];
        }

        $t=array();
        $t['price']=(float)ParcelPrice::widget(['weight'=>$this_weight]);
        $t['qst']=round($t['price']*$tax['qst']/100,2);
        $t['gst']=round($t['price']*$tax['gst']/100,2);
        $t['vat']=$t['qst']+$t['gst'];
        $t['name']='parcel #'.$pac->id;
        $t['quantity']=1;

        $pac->price=$t['price'];
        $pac->qst=$t['qst'];
        $pac->gst=$t['gst'];
        $pac->save();

        $total['sum']+=$t['price'];
        $total['qst']+=$t['qst'];
        $total['gst']+=$t['gst'];

        $payments[]=$t;
      }

      $order->price=$total['sum'];
      $order->qst=$total['qst'];
      $order->gst=$total['gst'];
      $order->save();

      $request = Yii::$app->request;
      if($request->isPost){
        if($order->load($request->post()) && $order->save()){
          if($order->payment_type==1){
            $pay=new DoPayment();

            foreach($payments as $item) {
              $pay->addItem($item);
            }

            $payment= $pay->make_payment();

            $customer = new PaymentsList();
            $customer->type = 1;
            $customer->order_id = $id;
            $customer->create_time = time();
            $customer->price = $total['sum'];
            $customer->qst=$total['qst'];
            $customer->gst=$total['gst'];
            $customer->client_id = Yii::$app->user->getId();
            $customer->code = $payment->getId();
            $customer->save();

            $approvalUrl = $payment->getApprovalLink();

            return $this->redirect($approvalUrl);
          }
          if($order->payment_type==2){
            \Yii::$app->getSession()->setFlash('success', 'Your order is successfully issued');
            return $this->redirect(['/']);
          }

        }
      }

      return $this->render('order', [
        'order_elements' => $model,
        'createNewAddress'=>!$model,
        'order_id'=>$id,
        'total'=>$total,
        'model'=>$order,
        /*'searchModel' => $searchModel,
        'dataProvider' => $dataProvider,
        'order' => $model,*/
      ]);

    }

    public function actionFinish(){
      $pay=new DoPayment();
      try {
        $payment=$pay->finishPayment();
      } catch (Exception $e) {
        throw new NotFoundHttpException('Error payment. Contact your administrator.');
      }
      $pay=PaymentsList::find()->where(['code'=>$payment->getId(),'status'=>0])->one();
      if(!$pay){
        throw new NotFoundHttpException('Error payment. Contact your administrator.');
      }
      if($payment->getState()=='approved') {
        $pay->status = 1;
        $pay->pay_time = time();
        $pay->save();

        $order=Order::find()->where(['id'=>$pay->order_id])->one();
        $order->payment_type=1;
        $order->payment_state=1;
        $order->save();

        \Yii::$app->getSession()->setFlash('success', 'Payment for your order was successful.');
        return $this->redirect(['/']);
      }
      throw new NotFoundHttpException('Error payment. Contact your administrator.');

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
