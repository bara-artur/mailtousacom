<?php

namespace app\modules\orderInclude\controllers;

use app\modules\payment\models\PaymentsList;
use Codeception\Lib\Console\Message;
use Faker\Provider\ar_SA\Payment;
use Yii;
use app\modules\order\models\Order;
use app\modules\orderInclude\models\OrderInclude;
use app\modules\orderElement\models\OrderElement;
use app\modules\orderInclude\models\OrderIncludeSearch;
use app\modules\orderInclude\models\OrderAddItems;
use app\modules\address\models\Address;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use \yii\web\Response;
use yii\helpers\Html;
use yii\db\Query;
use kartik\mpdf\Pdf;

/**
 * DefaultController implements the CRUD actions for OrderInclude model.
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
     * Lists all OrderInclude models.
     * @return mixed
     */
    public function actionCreateOrder()
    {
        $request = Yii::$app->request;
        if(!Yii::$app->user->isGuest && !$request->isAjax && $request->getIsGet()) {
            $address = Address::find()->where('user_id = :id', [':id' => Yii::$app->user->id])->one();
            $last_order = Order::find()->where('user_id = :id', [':id' => Yii::$app->user->id])->orderBy('created_at DESC')->one();
            $address_id = $address->id;
            $model = new Order();
            $model->user_id = Yii::$app->user->id;
            $model->billing_address_id = $address_id;
            $model->order_status = 0;
            $model->order_type = 0;
            $model->user_id_750 = $model->user_id + 750;
            if ($last_order->userOrder_id != null) {
                $tmp = strripos($last_order->userOrder_id, '_'); // ищем начало индекса номера заказа
                if ($tmp) $tmp = substr($last_order->userOrder_id, $tmp+1); // если tmp не 0(не может быть заказа без юзера) и не false,  то берем индекс заказа
                else $tmp = 0;
                $model->userOrder_id = Yii::$app->user->id.'_'.((int)$tmp+1); // создаем новый номер
            }
            else {
                $model->userOrder_id = Yii::$app->user->id.'_1'; // создаем первый номер
            }
            $model->created_at = time();
            $model->transport_data = time();
            if($model->save()) {
              $this->createLog($model->user_id,$model->id,"Draft");
              return $this->redirect('/orderInclude/create-order/'.$model->id);
            }
            //return ddd($model);
        }

        if(Yii::$app->user->isGuest){
          Yii::$app
            ->getSession()
            ->setFlash(
              'error',
              'You must login.'
            );
          return $this->redirect('/');
        }else {
          Yii::$app
            ->getSession()
            ->setFlash(
              'error',
              'An error has occurred. Try to create order again.'
            );
          return $this->redirect('address/create-order-billing');
        }
    }

    //id  - № заказа
    //посылка = 1 строка order_element
    public function actionCreateOrder2($id){
      //получаем посылки в заказе
      //  var_dump(Yii::$app->request);
      //$totalPriceArray = [0];
      $model = OrderElement::find()->where(['order_id'=>$id])->with(['orderInclude'])->all();
      foreach($model as $percel)
        {
            $totalPrice = 0;
            foreach ($percel->orderInclude as $ordInclude) {
                $totalPrice += $ordInclude->price;
            }
            $totalPriceArray[] = $totalPrice;
        }
      $order = Order::find()->where(['id'=>$id])->one();
      $payment = PaymentsList::find()->where(['order_id'=>$id])->one();
      $message_for_edit_prohibited_order = " ";
      $edit_not_prohibited = 1;
      if ($payment->status > 0) {
          $edit_not_prohibited = 0;
          $message_for_edit_prohibited_order = "Editing order prohibited, because the order has been paid.";
      }
      if ($order->order_status > 1) {
          $edit_not_prohibited = 0;
          $message_for_edit_prohibited_order = $message_for_edit_prohibited_order."<br>Editing order prohibited, because the order has been received at MailtoUSA facility.";
      }
      return $this->render('createOrder', [
        'edit_not_prohibited' => $edit_not_prohibited,
        'order_elements' => $model,
        'createNewAddress'=>!$model,
        'order_id'=>$id,
        'message_for_edit_prohibited_order' => $message_for_edit_prohibited_order,
        'totalPriceArray' => $totalPriceArray,
        /*'searchModel' => $searchModel,
        'dataProvider' => $dataProvider,
        'order' => $model,*/
      ]);
    }

    /**
     * Displays a single OrderInclude model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {

        $request = Yii::$app->request;
        if($request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                    'title'=> "OrderInclude",
                    'content'=>$this->renderAjax('view', [
                        'model' => $this->findModel($id),
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                            Html::a('Edit',['update','id'=>$id],['class'=>'btn btn-primary','role'=>'modal-remote'])
                ];
        }else{
            return $this->render('view', [
                'model' => $this->findModel($id),
            ]);
        }
    }

    /**
     * Creates a new OrderInclude model.
     * For ajax request will return json object
     * and for non-ajax request if creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $request = Yii::$app->request;
        $model = new OrderInclude();  

        if($request->isAjax){
            $data = Yii::$app->request->get('order-id');
            /*
            *   Process for ajax request
            */
            $model->order_id = $data;
            Yii::$app->response->format = Response::FORMAT_JSON;
            if($request->isGet){
                return [
                    'title'=> "Create",
                    'content'=>$this->renderAjax('create', [
                        'model' => $model,
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                                Html::button('Save',['class'=>'btn btn-primary ','type'=>"submit"])
                ];
            }else if($model->load($request->post())&&($model->save())){
                //$model->order_id = $request->post('order_id');

                return [
                    'forceReload'=>'#crud-datatable-pjax',
                    'title'=> "Create new OrderInclude",
                    'content'=>'<span class="text-success">Create OrderInclude success</span>',
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"])
                ];
            }else{
                 return [
                    'title'=> "Create new OrderInclude",
                    'content'=>$this->renderAjax('create', [
                        'model' => $model,
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                                Html::button('Save',['class'=>'btn btn-primary','type'=>"submit"])
        
                ];         
            }
        }else{
            /*
            *   Process for non-ajax request
            */
            if ($model->load($request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                return $this->render('create', [
                    'model' => $model,
                ]);
            }
        }
       
    }

    /**
     * Updates an existing OrderInclude model.
     * For ajax request will return json object
     * and for non-ajax request if update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $request = Yii::$app->request;
        $model = $this->findModel($id);       
        if($request->isAjax){
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            if($request->isGet){
                return [
                    'title'=> "Update OrderInclude #".$id,
                    'content'=>$this->renderAjax('update', [
                        'model' => $model,
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                                Html::button('Save',['class'=>'btn btn-primary','type'=>"submit"])
                ];         
            }else if($model->load($request->post()) && $model->save()){
                return [
                    'forceReload'=>'#crud-datatable-pjax',
                    'title'=> "OrderInclude #".$id,
                    'content'=>$this->renderAjax('view', [
                        'model' => $model,
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                            Html::a('Edit',['update','id'=>$id],['class'=>'btn btn-primary','role'=>'modal-remote'])
                ];
            }else{
                 return [
                    'title'=> "Update OrderInclude #".$id,
                    'content'=>$this->renderAjax('update', [
                        'model' => $model,
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                                Html::button('Save',['class'=>'btn btn-primary','type'=>"submit"])
                ];        
            }
        }else{
            /*
            *   Process for non-ajax request
            */
            if ($model->load($request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                return $this->render('update', [
                    'model' => $model,
                ]);
            }
        }
    }

    /**
     * Delete an existing OrderInclude model.
     * For ajax request will return json object
     * and for non-ajax request if deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $request = Yii::$app->request;
        $this->findModel($id)->delete();

        if($request->isAjax){
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['forceClose'=>true,'forceReload'=>'#crud-datatable-pjax'];
        }else{
            /*
            *   Process for non-ajax request
            */
            return $this->redirect(['index']);
        }


    }

    public function actionBorderForm($id){
      $order = Order::findOne($id);
      $request = Yii::$app->request;
      if($request->isPost){
        if($order->load($request->post()) && $order->save()){
          return $this->redirect(['/payment/order/'.$id]);
        }
      }

      if($order->transport_data<time())$order->transport_data=strtotime('+1 days');
      $order->transport_data=date(\Yii::$app->params['data_format_php'], $order->transport_data);

      $model = OrderElement::find()->where(['order_id'=>$id])->all();

      $total=array(
        'price'=>0,
        'weight'=>0,
        'quantity'=>0,
      );

      $query = new Query;
      $query->select('weight')
        ->from('tariffs')
        ->orderBy([
          'weight' => SORT_DESC
        ]);
      $row = $query->one();
      $max_weight=$row['weight'];

      foreach ($model as $pac) {
        $pac->includes_packs = $pac->getIncludes();
        if (count($pac->includes_packs) == 0) {
          Yii::$app
            ->getSession()
            ->setFlash(
              'error',
              'The package must have at least one attachment.'
            );
          return $this->redirect('/orderInclude/create-order/' . $id);
        }
        $this_weight = 0;
        foreach ($pac->includes_packs as $pack) {
          $total['price'] += $pack['price'] * $pack['quantity'];
          $total['quantity'] += $pack['quantity'];
        }
        $this_weight =  $pac->weight;
        $total['weight']+=$this_weight;

        $total['weight_lb']=floor($total['weight']);
        $total['weight_oz']=floor(($total['weight']-$total['weight_lb'])*16);

        if($this_weight>$max_weight){
          Yii::$app
            ->getSession()
            ->setFlash(
              'error',
              'Allowable weight of the parcel is '.$max_weight.'lb.'
            );
          return $this->redirect('/orderInclude/create-order/' . $id);
        }
      }

      return $this->render('BorderForm', [
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

  public function actionBorderSave(){
    $request = Yii::$app->request;
    if(!Yii::$app->user->isGuest && !$request->isAjax && !$request->post()){
      throw new NotFoundHttpException('The requested page does not exist.');
    }
    $order = Order::findOne($request->post('order'));
    if($order->user_id!=Yii::$app->user->id){
      throw new NotFoundHttpException('The requested page does not exist.');
    }
    $order->transport_data=strtotime($request->post('value'));
    $order->save();
    return ;
  }

  public function actionBorderFormPdf($id){
    $this->layout = 'pdf';
    Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
    $headers = Yii::$app->response->headers;
    $headers->add('Content-Type', 'application/pdf');

    $order = Order::findOne($id);
    if($order->transport_data<time())$order->transport_data=strtotime('+1 days');

    $model = OrderElement::find()->where(['order_id'=>$id])->all();

    $total=array(
      'price'=>0,
      'weight'=>0,
      'quantity'=>0,
    );

    $query = new Query;
    $query->select('weight')
      ->from('tariffs')
      ->orderBy([
        'weight' => SORT_DESC
      ]);
    $row = $query->one();
    $max_weight=$row['weight'];

    foreach ($model as &$pac) {
      $pac->includes_packs = $pac->getIncludes();
      if (count($pac->includes_packs) == 0) {
        Yii::$app
          ->getSession()
          ->setFlash(
            'error',
            'The package must have at least one attachment.'
          );
        return $this->redirect('/orderInclude/create-order/' . $id);
      }
      foreach ($pac->includes_packs as $pack) {
        $total['price'] += $pack['price'] * $pack['quantity'];
        $total['quantity'] += $pack['quantity'];
      }
      $this_weight=$pac->weight;
      $total['weight']+=$this_weight;
      if($this_weight>$max_weight){
        Yii::$app
          ->getSession()
          ->setFlash(
            'error',
            'Allowable weight of the parcel is '.$max_weight.'lb.'
          );
        return $this->redirect('/orderInclude/create-order/' . $id);
      }
    }

    $total['weight_lb']=floor($total['weight']);
    $total['weight_oz']=floor(($total['weight']-$total['weight_lb'])*16);

    $content = $this->renderPartial('borderFormPdf',[
      'order_elements' => $model,
      'order'=>$order,
      'order_id'=>$id,
      'total'=>$total,
    ]);

    //echo '<link rel="stylesheet" type="text/css" href="/css/pdf_CBP_Form_7533.css">';
    //return $content;
    // setup kartik\mpdf\Pdf component
    $pdf = new Pdf([
      'content' => $content,
      //'cssFile' => '@vendor/kartik-v/yii2-mpdf/assets/kv-mpdf-bootstrap.min.css',
      'cssFile' => '@app/web/css/pdf_CBP_Form_7533.css',
      'cssInline' => '.kv-heading-1{font-size:18px}',
      'options' => ['title' => 'CBP Form 7533 for order №'.$id],
      'methods' => [
        //'SetHeader'=>['Krajee Report Header'],
        //'SetFooter'=>['{PAGENO}'],
      ]
    ]);

    // return the pdf output as per the destination setting
    return $pdf->render();
  }
    /**
     * Finds the OrderInclude model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return OrderInclude the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = OrderInclude::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    public function beforeAction($action)
    {
        // ...set `$this->enableCsrfValidation` here based on some conditions...
        // call parent method that will check CSRF if such property is true.
        if (($action->id === 'index')||($action->id === 'create-order')) {
            # code...
            $this->enableCsrfValidation = false;
        }
        return parent::beforeAction($action);
    }

    public function createLog($user_id,$order_id,$description){
        \app\modules\logs\controllers\DefaultController::createLog($user_id,$order_id,$description);
    }
}

