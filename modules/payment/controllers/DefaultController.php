<?php

//http://paypal.github.io/PayPal-PHP-SDK/sample/
//https://github.com/paypal/PayPal-PHP-SDK/blob/master/sample/payments/ExecutePayment.php
//http://paypal.github.io/PayPal-PHP-SDK/sample/doc/payments/OrderGet.html
//http://paypal.github.io/PayPal-PHP-SDK/sample/doc/payments/OrderCreateForVoid.html

namespace app\modules\payment\controllers;

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

            return $this->render('index', [
                'dataProvider' => $dataProvider,
                'filterForm' => $filterForm,
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

      $el_group=explode(',',$order->el_group);

      $model = OrderElement::find()->where(['id'=>$el_group])->all();
      $user_id=false;
      $payments_list=[];


      foreach ($model as &$pac) {
        if(!$user_id){
          //если это 1-я запись то берем из нее пользователя
          $user_id=$pac->user_id;

          //и узнаем его налог
          $query = new Query;
          $query->select('state')
            ->from('new_address')
            ->where(['user_id'=>$user_id]);
          $row = $query->one();

          if(!$row){
            Yii::$app->getSession()->setFlash('error', 'Missing billing address.');
            return $this->redirect(['/']);
          }

          $state=$row['state'];

          $query = new Query;
          $query->select(['qst','gst'])
            ->from('state')
            ->where(['name'=>$state]);
          $tax = $query->one();
        }else{
          if($user_id!=$pac->user_id){
            throw new NotFoundHttpException('You can not pay parcels for different users.');
          }
        }

        $item=[
          'element_id'=>$pac->id,
          'track_number'=>$pac->track_number,
          'track_number_type'=>$pac->track_number_type,
          'weight'=>$pac->weight,
          'source'=>$pac->source,
          'source_text'=>\Yii::$app->params[package_source_list][$pac->source]
        ];
        $item['price']=(float)ParcelPrice::widget(['weight'=>$item['weight'],'user'=>$user_id]);
        $item['qst']=round($item['price']*$tax['qst']/100,2);
        $item['gst']=round($item['price']*$tax['gst']/100,2);
        $pac->price=$item['price'];
        $pac->qst=$item['qst'];
        $pac->gst=$item['gst'];

        $payments_list[$pac->id]=$item;
      }

      if(!Yii::$app->user->identity->isManager() && $user_id!=Yii::$app->user->identity->id){
        throw new NotFoundHttpException('You can pay only for your packages.');
      }

      $payments=PaymentInclude::find()
        ->select(['element_id','sum(price) as already_price','sum(qst) as already_qst','sum(gst) as already_gst'])
        ->where([
          'element_type'=>0,
          'element_id'=>$el_group,
          'status'=>1
        ])
        ->groupBy(['element_id'])
        ->asArray()
        ->all();

      $tot_already_pays=0;
      foreach ($payments as $pay) {
         $pay['already_price']=round($pay['already_price'],2);
        $pay['already_qst']=round($pay['already_qst'],2);
        $pay['already_gst']=round($pay['already_gst'],2);
        $pay['already_sum']=round($pay['already_price']+$pay['already_gst']+$pay['already_qst'],2);
        $payments_list[$pay['element_id']]=array_merge($pay,$payments_list[$pay['element_id']]);
        $tot_already_pays+=$pay['already_price'];
      };

      $tot_already_pays=round($tot_already_pays,2);

      $total=array(
        'price'=>0,
        'gst'=>0,
        'qst'=>0,
        'sum'=>0
      );

      $tot_pays=0;
      foreach ($payments_list as &$item) {
        $item['sum']=$item['price']+$item['qst']+$item['gst'];
        $tot_pays+=$item['price'];

        $item['total_price']=$item['price']-$item['already_price'];
        $item['total_qst']=$item['qst']-$item['already_qst'];
        $item['total_gst']=$item['gst']-$item['already_gst'];
        $item['total_sum']=$item['total_price']+$item['total_qst']+$item['total_gst'];

        $item['total_price']=round($item['total_price'],2);
        $item['total_qst']=round($item['total_qst'],2);
        $item['total_gst']=round($item['total_gst'],2);
        $item['total_sum']=round($item['total_sum'],2);

        if($item['price']<$item['already_price']){
          Yii::$app->getSession()->setFlash('info', 'For the selected parcels there is an overpayment.');
          $item['err']='For the this parcel there is an overpayment.';
        }else {
          $total['price'] += $item['total_price'];
          $total['gst'] += $item['total_qst'];
          $total['qst'] += $item['total_gst'];
          $total['sum'] +=$total['price']+$total['qst']+$total['gst'];
        }
      }
      $tot_pays=round($tot_pays,2);

      if($tot_already_pays==$tot_pays && !Yii::$app->user->identity->isManager()){
        Yii::$app->getSession()->setFlash('error', 'All selected orders have already been paid.');
        return $this->redirect(['/orderInclude/create-order/'.$id]);
      }

      $request = Yii::$app->request;
      if($request->isPost) {
        //d($request->post());
        //Обработчик для админа
        if(Yii::$app->user->identity->isManager()){
          //помечаем посылки как принятые на точку выдачи
          if(Yii::$app->user->can("takeParcel")) {
            $order->setData([
              'status'=>2,
              'payment_state'=>2,
              'status_dop'=>Yii::$app->user->identity->last_receiving_points,
            ]);
            \Yii::$app->getSession()->setFlash('success', 'The order is waiting for dispatch.');
          }

          if($total['price']==0) {
            //все оплаченно
            \Yii::$app->getSession()->setFlash('success', 'The order is accepted to the warehouse and is waiting for dispatch.');
            return $this->redirect(['/']);
          }

          if(Yii::$app->user->can("takePay") && $total['sum']>0) {
            //посылки пряняты, оплата налом
            $pays = PaymentsList::create([
              'client_id' => $user_id,
              'type' => 3,
              'status' => 1,
              'pay_time' => time(),
            ]);

            $price = 0;
            $qst = 0;
            $gst = 0;
            //d($pays);

            foreach ($payments_list as $item) {
              //только для посылок с стоимостью оплаты более 0
              if ($item['total_price'] > 0) {
                $pay_include = new PaymentInclude();
                $pay_include->payment_id = $pays->id;
                $pay_include->element_id = $item['element_id'];
                $pay_include->price = $item['total_price'];
                $pay_include->qst = $item['total_qst'];
                $pay_include->gst = $item['total_gst'];
                $pay_include->element_type = 0;
                $pay_include->status = 1; //оплачен
                $pay_include->create_at = time();


                //если посылку отказались платить
                if ($request->post('agree_' . $item['element_id'])) {
                  $pay_include->status = -1;//Отказ от оплаты
                  $pay_include->comment = $request->post('text_not_agree_' . $item['element_id']);//Отказ от оплаты
                } else {
                  $price += $item['total_price'];
                  $qst += $item['total_qst'];
                  $gst += $item['total_gst'];
                }
                $pay_include->save();

                \Yii::$app->getSession()->setFlash('success', 'The order is pay and accepted to the warehouse and is waiting for dispatch.');

                return $this->redirect(['/']);
              }
            }
            $pays->price = $price;
            $pays->qst = $qst;
            $pays->gst = $gst;
            $pays->save();
          }
        }else{
          //d($request->post());
          //для обычного пользователя

          //Когда выбрали оплату на терминале
          if($request->post('payment_type')==2){
            //помечаем посылки как принятые на точку выдачи
            $order->setData(['status'=>1,'payment_state'=>1]);
            \Yii::$app->getSession()->setFlash('success', 'Your order is successfully issued');
            return $this->redirect(['/']);
          };

          //Когда выбрали оплату paypal
          if($request->post('payment_type')==1){
            $pay = new DoPayment();

            foreach ($payments_list as $pac) {
              if($pac['total_price']>0) {
                $item_ = [
                  'name' => 'Pac #' . $pac['element_id'],
                  'vat' => $pac['total_gst'] + $pac['total_qst'],
                  'price'=>$pac['total_price']
                ];
                $pay->addItem($item_);
              }
            }
            $payment = $pay->make_payment();

            $pays=PaymentsList::create([
              'client_id'=>$user_id,
              'type'=>1,
              'status'=>0,
              'code'=>$payment->getId(),
              'price'=>$total['price'],
              'qst'=> $total['gst'],
              'gst'=>$total['qst']
            ]);

            foreach ($payments_list as $pac) {
              if ($pac['total_price'] > 0) {
                $pay_include = new PaymentInclude();
                $pay_include->payment_id = $pays->id;
                $pay_include->element_id = $pac['element_id'];
                $pay_include->price=$pac['total_price'];
                $pay_include->qst=$pac['total_qst'];
                $pay_include->gst=$pac['total_gst'];
                $pay_include->element_type = 0;
                $pay_include->status = 0; //оплачен
                $pay_include->create_at = time();
                $pay_include->save();
              }
            }

            $session->set('last_pays',$pays->id);
            $approvalUrl = $payment->getApprovalLink();
            return $this->redirect($approvalUrl);
          }
        }
        \Yii::$app->getSession()->setFlash('error', 'Your order error. Check the order or try again later.');
        return $this->redirect(['/']);
      }

      return $this->render('to_pay', [
        'order_id'=>$id,
        'total'=>$total,
        'payments_list'=>$payments_list,
        'item'=>$pac
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
          ]);

          return [
            'title'=> "View Payment Includes",
            'content'=>$this->renderAjax('viewPaymentsInclude', [
              'dataProvider' => $dataProvider,
            ]),
            'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"])
          ];
        }


      }
    }
}
