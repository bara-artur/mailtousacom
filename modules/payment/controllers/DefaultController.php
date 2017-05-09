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
use app\modules\additional_services\models\AdditionalServices;
use app\modules\invoice\models\Invoice;

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
        return array();
    }

  public function beforeAction($action)
  {
    if (Yii::$app->user->isGuest) {
      $this->redirect(['/']);
      return false;
    }

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
      if (($user!=null)&&(Yii::$app->user->identity->isManager())){
        $admin = 1;
      }



      $query = null;
      $time_to = null;
      if(Yii::$app->request->post()) {
        $filterForm->load(Yii::$app->request->post());
        $query['PaymentSearch'] = $filterForm->toArray();
        $time_to = ['pay_time_to' => $filterForm->pay_time_to];
      }

      if(!$admin){
        if(!isset($query['PaymentSearch'])){
          $query['PaymentSearch']=array();
        }
        $query['PaymentSearch']['client_id']=Yii::$app->user->id;
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

  /**
   * Lists all PaymentsList models.
   * @return mixed
   */
  public function actionOrder($id){

    $request = Yii::$app->request;

    $order = Order::findOne($id);
    $session = Yii::$app->session;
    $session->set('last_order',$id);

    if(strlen($order->el_group)<1){
      throw new NotFoundHttpException('There is no data for payment.');
    };

    $pay_data=$order->getPaymentData();
    $user_id=$pay_data['user']->id;

    //если пост запрос
    if($request->isPost) {
      if(Yii::$app->user->identity->isManager()){
        //Для админа

        //помечаем посылки как принятые на точку выдачи
        if(Yii::$app->user->can("takeParcel")) {
          $order->setData([
            'status'=>2,
            'payment_state'=>2,
            'status_dop'=>Yii::$app->user->identity->last_receiving_points,
          ]);
          \Yii::$app->getSession()->setFlash('success', 'The order is waiting for dispatch.');
        }

        //Проверяем оплачено ли все. Если принимать деньги не надо то редиректим на стротовую и выдаем сообщение
        if($total['price']==0) {
          \Yii::$app->getSession()->setFlash('success', 'The order is accepted to the warehouse and is waiting for dispatch.');
          return $this->redirect(['/']);
        }

        //проверяем возможность приема платежа при приеме. Если нет то перебрасываем на стртовую.
        if(!Yii::$app->user->can("takePay") && $total['sum']>0) {
          return $this->redirect(['/']);
        };

        //если есть раздрешения и выбрали оплату за месяц
        if($user->month_pay==1 && $request->post('payment_type')==3){
          $order->setData([
            'payment_state'=>3,
          ]);

          \Yii::$app->getSession()->setFlash('success', 'The order is marked for payment once a month and accepted to the warehouse and is waiting for dispatch.');
          return $this->redirect(['/']);
        }

        //в остальных случаях создаем платеж и проводим его
        //Генерируем новый платеж оплаты налом
        $pays = PaymentsList::create([
          'client_id' => $user_id,
          'type' => 3,
          'status' => 1,
          'pay_time' => time(),
          'price' => $pays_total['price'],
          'qst' => $pays_total['qst'],
          'gst' => $pays_total['gst'],
        ]);
        $pays->save();

        //Сохраняем детали платежа
        foreach ($pay_array as $item) {
          $pay_include = new PaymentInclude();
          $pay_include->attributes=$item;

          //если нет отказа от элемента то принимаем оплату
          if($pay_include->status==0){
            $pay_include->status=1;
          }
          $pay_include->payment_id = $pays->id;
          $pay_include->save();

        };

        $order->setData([
          'payment_state'=>2,
        ]);

        \Yii::$app->getSession()->setFlash('success', 'The order is pay and accepted to the warehouse.');
        return $this->redirect(['/']);

      }else{
        //для пользователя

        //если есть раздрешения и выбрали оплату за месяц
        if($pay_data['user']->month_pay==1 && $request->post('payment_type')==3){
          $order->setData([
            'payment_state'=>3,
          ]);

          \Yii::$app->getSession()->setFlash('success', 'The order is marked for payment once a month and successfully issued.');
          return $this->redirect(['/']);
        }

        //Когда выбрали оплату на точке
        if($request->post('payment_type')==2){
          //помечаем посылки как ожидаемые к принятию
          $order->setData(['status'=>1,'payment_state'=>1]);
          \Yii::$app->getSession()->setFlash('success', 'Your order is successfully issued');
          return $this->redirect(['/']);
        };

        //Когда выбрали оплату paypal
        if($request->post('payment_type')==1){
          //Создоем экземпляр для оплаты через PayPal
          $pay = new DoPayment();

          //Генерируем новый платеж оплаты налом
          $pays = PaymentsList::create([
            'client_id' => $user_id,
            'type' => 3,
            'status' => 0,
            'pay_time' => time(),
            'price' => $pay_data['pays_total']['price'],
            'qst' => $pay_data['pays_total']['qst'],
            'gst' => $pay_data['pays_total']['gst'],
          ]);
          $pays->save();

          //Сохраняем детали платежа
          foreach ($pay_data['pay_array'] as $item) {
            $pay_include = new PaymentInclude();
            $pay_include->attributes=$item;
            $pay_include->payment_id = $pays->id;
            $pay_include->save();
            $item_ = [
              'name' => 'Type '.$item['element_type'].' id#' . $item['element_id'],
              'vat' => $item['gst']+ $item['qst'],
              'price'=>$item['price']
            ];
            $pay->addItem($item_);
          };

          $payment = $pay->make_payment();
          $pays->code=$payment->getId();
          $pays->save();

          $session->set('last_pays',$pays->id);
          $approvalUrl = $payment->getApprovalLink();
          return $this->redirect($approvalUrl);

        }
      }

    }

    return $this->render('to_pay', $pay_data);
  }

  //платеж по инвойсу
  public function actionInvoice($id){
    $invoice=Invoice::find()->where(['id'=>$id])->one();

    $sel_pac=[];

    $services_list=explode(',',$invoice->services_list);
    $parcels_list=explode(',',$invoice->parcels_list);

    $orderElement=AdditionalServices::find()
      ->where(['id'=>$services_list])
      ->asArray()
      ->all();

    foreach ($orderElement as $pac){
      $pacs_id=explode(',',$pac['parcel_id_lst']);
      foreach ($pacs_id as $id){
        if(!in_array($id,$sel_pac)){
          $sel_pac[]=$id;
        }
      }
    };

    foreach ($parcels_list as $id){
      if(!in_array($id,$sel_pac)){
        $sel_pac[]=$id;
      }
    }

    $order=new Order();
    $order->el_group=implode(',',$sel_pac);
    $order->user_id=Yii::$app->user->id;


    $pay_data=$order->getPaymentData($services_list,$parcels_list);

    $pay_data['inv_id']=$id;

    return $this->render('to_pay', $pay_data);
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

      $pays_in=PaymentInclude::find()->where(['payment_id'=>$pay->id])->all();
      foreach ($pays_in as $include){
        $include->status=1;
        $include->save();
      };

      \Yii::$app->getSession()->setFlash('success', 'Payment for your order was successful.');
      return $this->redirect(['/']);
    }
    return $this->return_last_order('Try later or contact your administrator.');
  }

  public function actionTrackInvoice($id){
    $order = Order::findOne($id);

    $session = Yii::$app->session;
    $session->set('last_order', $id);

    if (strlen($order->el_group) < 1) {
      throw new NotFoundHttpException('There is no data.');
    };

    $model = $order->getOrderElement();

    $user_id=Yii::$app->user->id;
    if($model[0]['user_id']!=$user_id){
      throw new NotFoundHttpException('Access is denied. Try to log in with your account.');
    };

    $pay_array=[];
    $pays_total=array(
      'price'=>0,
      'gst'=>0,
      'qst'=>0
    );
    foreach ($model as $pac){
      $invoice=$pac->trackInvoice;
      if(!$invoice->isNewRecord){
        //для инвойса  храним все в промежуточном массиве
        $invoice_total=array();

        $invoice_total['price']=$invoice->price;
        $invoice_total['qst']=$invoice->qst;
        $invoice_total['gst']=$invoice->gst;

        $invoice_total['price']+=$invoice->dop_price;
        $invoice_total['qst']+=$invoice->dop_qst;
        $invoice_total['gst']+=$invoice->dop_gst;

        //получаем данные о уже осуществленных платежах
        $paySuccessful=$invoice->paySuccessful;
        if($paySuccessful AND count($paySuccessful)>0){
          $invoice_total['price']-=$paySuccessful[0]['price'];
          $invoice_total['qst']-=$paySuccessful[0]['qst'];
          $invoice_total['gst']-=$paySuccessful[0]['gst'];
        };

        //если есть сумма к оплате добовляем ее к глобальному массиву платежа
        if($invoice_total['price']>0) {
          $pay_array[] = [
            'element_id' => $pac->id,
            'element_type' => 1,
            'status' => 0,
            'comment' => '',
            'price' => $invoice_total['price'],
            'qst' => $invoice_total['qst'],
            'gst' => $invoice_total['gst'],
          ];
          $pays_total['price']+=$invoice_total['price'];
          $pays_total['qst']+=$invoice_total['qst'];
          $pays_total['gst']+=$invoice_total['gst'];
        }
        break;
      }
    }

    if(count($pay_array)==0){
      throw new NotFoundHttpException('Everything has already been paid for this invoice');
    };

    //Создоем экземпляр для оплаты через PayPal
    $pay = new DoPayment();

    //Генерируем новый платеж оплаты налом
    $pays = PaymentsList::create([
      'client_id' => $user_id,
      'type' => 3,
      'status' => 0,
      'pay_time' => time(),
      'price' => $pays_total['price'],
      'qst' => $pays_total['qst'],
      'gst' => $pays_total['gst'],
    ]);
    $pays->save();

    //Сохраняем детали платежа
    foreach ($pay_array as $item) {
      $pay_include = new PaymentInclude();
      $pay_include->attributes=$item;
      $pay_include->payment_id = $pays->id;
      $pay_include->save();
      $item_ = [
        'name' => 'Type '.$item['element_type'].' id#' . $item['element_id'],
        'vat' => $item['gst']+ $item['qst'],
        'price'=>$item['price']
      ];
      $pay->addItem($item_);
    };

    $payment = $pay->make_payment();
    $pays->code=$payment->getId();
    $pays->save();

    $session->set('last_pays',$pays->id);
    $approvalUrl = $payment->getApprovalLink();
    return $this->redirect($approvalUrl);
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
