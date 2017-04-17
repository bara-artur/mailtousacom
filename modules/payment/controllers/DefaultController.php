<?php

//http://paypal.github.io/PayPal-PHP-SDK/sample/
//https://github.com/paypal/PayPal-PHP-SDK/blob/master/sample/payments/ExecutePayment.php
//http://paypal.github.io/PayPal-PHP-SDK/sample/doc/payments/OrderGet.html
//http://paypal.github.io/PayPal-PHP-SDK/sample/doc/payments/OrderCreateForVoid.html

namespace app\modules\payment\controllers;

use yii\bootstrap\Modal;
use Yii;
use app\modules\payment\models\PaymentsList;
use app\modules\payment\models\PaymentInclude;
use app\modules\payment\models\PaymentSearch;
use app\modules\payment\models\DoPayment;
use app\modules\order\models\Order;
use app\modules\orderInclude\models\OrderInclude;
use app\modules\orderElement\models\OrderElement;
use yii\db\Query;
use app\modules\payment\models\PaymentFilterForm;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use \yii\web\Response;

use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\components\ParcelPrice;
use app\modules\user\models\User;

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
        /*return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];*/
        return [];
    }

  public function beforeAction($action)
  {
    // ...set `$this->enableCsrfValidation` here based on some conditions...
    // call parent method that will check CSRF if such property is true.
    if ($action->id === 'order') {
      # code...
      $this->enableCsrfValidation = false;
    }
    return parent::beforeAction($action);
  }
  /**
     * Lists all PaymentsList models.
     * @return mixed
     */
    public function actionIndex()
    {
        $filterForm = new PaymentFilterForm();

        $admin = 0;
        $user = User::find()->where(['id' => Yii::$app->user->id])->one();
        if (($user!=null)&&($user->isManager())){
          $admin = 1;
        }


        if (Yii::$app->user->isGuest) {
          return $this->redirect(['/']);
        }else {
            $query = null;
            $time_to = null;
            if(Yii::$app->request->post()) {
              $filterForm->load(Yii::$app->request->post());
              $query['PaymentSearch'] = $filterForm->toArray();
              $time_to = ['pay_time_to' => $filterForm->pay_time_to];
            }

            $searchModel = new PaymentSearch();
            $dataProvider = $searchModel->search($query,$time_to);
            $user_array = [];
            foreach ($dataProvider->models as $p){
              $user_array[] = $p->client_id;            // создаем массив id пользователей
            }
            $user_array = array_unique($user_array);  // оставляем только уникальные id
            $users = User::find()->andWhere(['in', 'id', $user_array])->all();
            $info = [];
            foreach ($users as $user) {           // создаем ассоциативный массив id -> lineinfo
              $info[$user->id] = $user->lineinfo;
            }
            foreach ($dataProvider->models as $p){   // заменяем id на развернутое описание
              if (array_key_exists($p->client_id,$info)) {
                $p->client_id = $info[$p->client_id];
              }else{
                $p->client_id = '-empty-';
              }
            }
            return $this->render('index', [
                'dataProvider' => $dataProvider,
                'filterForm' => $filterForm,
                'admin' => $admin,
            ]);
        }
    }

    /**
     * Lists all PaymentsList models.
     * @return mixed
     */
    public function actionOrder($id){

      $order = Order::findOne($id);

      $session = Yii::$app->session;
      $session->set('last_order',$id);

      if(strlen($order->el_group)<1){
        throw new NotFoundHttpException('There is no data for payment.');
      };

      $el_group=$order->getOrderElement();
      $user_id=$el_group[0]->user_id;

      //Проверяем что б пользователю хватало прав на просмотр
      if(!Yii::$app->user->identity->isManager() && $user_id!=Yii::$app->user->identity->id){
        throw new NotFoundHttpException('You can pay only for your packages.');
      }

      //узнаем налог пользователя
      $query = new Query;
      $query->select('state')
        ->from('new_address')
        ->where(['user_id'=>$user_id]);
      $row = $query->one();

      if(!$row){
        Yii::$app->getSession()->setFlash('error', 'Missing billing address.');
        return $this->redirect(['/']);
      }
      $user_state=$row['state'];

      $query = new Query;
      $query->select(['qst','gst'])
        ->from('state')
        ->where(['name'=>$user_state]);
      $tax = $query->one();

      $total=array(
        'price'=>0,
        'gst'=>0,
        'qst'=>0
      );

      //проверяем посылки на принадлежность пользователю и при необходимости делаем пересчет цены
      foreach ($el_group as &$pac) {
        $sub_total=array(
          'price'=>0,
          'gst'=>0,
          'qst'=>0
        );

        //проверка принадлежности
        if ($user_id != $pac->user_id) {
          throw new NotFoundHttpException('You can not pay parcels for different users.');
        }

        //проверка необходимости пересчета
        if($pac->status<2 || $pac->price==0){
          $item['price']=(float)ParcelPrice::widget(['weight'=>$pac->weight,'user'=>$user_id]);
          $item['qst']=round($item['price']*$tax['qst']/100,2);
          $item['gst']=round($item['price']*$tax['gst']/100,2);
          $pac->save();
        };

        $sub_total['price']+=$item['price'];
        $sub_total['qst']+=$item['qst'];
        $sub_total['gst']+=$item['gst'];

        //получаем данные о уже осуществленных платежах
        $paySuccessful=$pac->getPaySuccessful();
        if($paySuccessful AND count($paySuccessful)>0){
          $sub_total['price']-=$paySuccessful[0]->price;
          $sub_total['qst']-=$paySuccessful[0]->qst;
          $sub_total['gst']-=$paySuccessful[0]->gst;
        };

        //получаем данные о инвойсах
        $invoice=$pac->getTrackInvoice();
        if(!$invoice->getIsNewRecord()){
          $sub_total['price']+=$invoice->price;
          $sub_total['qst']+=$invoice->qst;
          $sub_total['gst']+=$invoice->gst;

          $sub_total['price']+=$invoice->dop_price;
          $sub_total['qst']+=$invoice->dop_qst;
          $sub_total['gst']+=$invoice->dop_gst;

          //получаем данные о уже осуществленных платежах
          $paySuccessful=$invoice->getPaySuccessful();
          if($paySuccessful AND count($paySuccessful)>0){
            $sub_total['price']-=$paySuccessful[0]->price;
            $sub_total['qst']-=$paySuccessful[0]->qst;
            $sub_total['gst']-=$paySuccessful[0]->gst;
          };
        }

        $sub_total['vat']=$sub_total['qst']+$sub_total['qst'];
        $sub_total['sum']=$sub_total['price']+$sub_total['vat'];

        $pac->sub_total=$sub_total;

        $total['price']+=$sub_total['price'];
        $total['price']+=$sub_total['qst'];
        $total['price']+=$sub_total['gst'];
      }
      $total['vat']=$total['qst']+$total['qst'];
      $total['sum']=$total['price']+$total['vat'];

      return $this->render('to_pay', [
        'order_id'=>$id,
        'paces'=>$el_group,
        'total'=>$total
      ]);
    }

    public function actionFinish(){
      $pay=new DoPayment();
      try {
        $payment=$pay->finishPayment();
      } catch (Exception $e) {
        return $this->return_last_order('Error payment. Try later or contact your administrator.');
      }

      if(!$payment){
        return $this->return_last_order();
      };

      $pay=PaymentsList::find()->where(['code'=>$payment->getId(),'status'=>0])->one();
      if(!$pay){
        return $this->return_last_order('Error payment. Try later or contact your administrator.');
      }
      if($payment->getState()=='approved') {
        $pay->status = 1;
        $pay->pay_time = time();
        $pay->save();
        $pay->setData(['status'=>1]);

        \Yii::$app->getSession()->setFlash('success', 'Payment for your order was successful.');
        return $this->redirect(['/']);
      }
      return $this->return_last_order('Try later or contact your administrator.');
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

    private function return_last_order($msg=false){

      if($msg){
        \Yii::$app->getSession()->setFlash('error', $msg);
      }
      $session = Yii::$app->session;
      $last_order=$session->get('last_order');
      if(!$last_order){
        return $this->redirect(['/']);
      }else{
        $last_pays=$session->get('last_pays');
        $pay=PaymentsList::findOne($last_pays);
        $pay->status=-1;
        $pay->save();
        $pay->setData(['status'=>-1]);

        return $this->redirect(['/payment/order/'.$last_order]);
      }
    }

  public function actionShowIncludes($id){
    $request = Yii::$app->request;

    if($request->isAjax) {
      Yii::$app->response->format = Response::FORMAT_JSON;
      if($request->isGet){

        $query = PaymentInclude::find();

        $dataProvider = new ActiveDataProvider(['query' => $query,'sort'=>new \yii\data\Sort(['attributes'=>['empty']])]);
        $query->andFilterWhere([
          'payment_id' => $id,
         // 'client_id' => Yii::$app->user->id
         // 'user_id' => Yii::$app->user->id
        ]);
        $footer=Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]);
        if($request->get('back')){
          $footer.=Html::a('Back', ['/payment/show-parcel-includes/'.(int)$request->get('back')],
            [
              'id'=>'payment-show-includes',
              'role'=>'modal-remote',
              'class'=>'btn btn-default btn-info big_model',
            ]
          );
        }
        return [
          'title'=> "View Payment Includes",
          'content'=>$this->renderAjax('viewPaymentsInclude', [
            'dataProvider' => $dataProvider,
            'routing' => 'paymentTable',
          ]),
          'footer'=> $footer
        ];
      }


    }
  }

  public function actionShowParcelIncludes($id){
    $request = Yii::$app->request;

    if($request->isAjax) {
      Yii::$app->response->format = Response::FORMAT_JSON;
      if($request->isGet){

        $query = PaymentInclude::find();

        $dataProvider = new ActiveDataProvider(['query' => $query,'sort'=>new \yii\data\Sort(['attributes'=>['empty']])]);
        $query->andFilterWhere([
          'element_id' => $id,
         // 'client_id' => Yii::$app->user->id
         // 'user_id' => Yii::$app->user->id
        ]);

        return [
          'title'=> "View Payment Includes for Parcel",
          'content'=>$this->renderAjax('viewPaymentsInclude', [
            'dataProvider' => $dataProvider,
            'routing' => 'parcel',
          ]),
          'footer'=> Html::button('Close',['class'=>'btn btn-default pull-right','data-dismiss'=>"modal"]),
        ];
      }


    }
  }
}
