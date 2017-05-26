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
use app\modules\user\models\User;
/**
 * DefaultController implements the CRUD actions for OrderInclude model.
 */
class DefaultController extends Controller
{

  public function beforeAction($action) {
    if (Yii::$app->user->isGuest) {
      $this->redirect(['/']);
      return false;
    }

    // ...set `$this->enableCsrfValidation` here based on some conditions...
    // call parent method that will check CSRF if such property is true.
    if ($action->id === 'create') {
      # code...
      $this->enableCsrfValidation = false;
    }
    return parent::beforeAction($action);
  }

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


  public function actionCreateOrder($user=0) // создание заказа
  {
    return $this->createOrder($user,$this);
  }

  //id  - № заказа
  //посылка = 1 строка order_element
  public function actionCreateOrder2($id){
    //получаем посылки в заказе
    //  var_dump(Yii::$app->request);
    //$totalPriceArray = [0];
    $totalPriceArray=[];
/*     $model = OrderElement::find()->where(['order_id'=>$id])->with(['orderInclude'])->all();
*/
    $model = new OrderElement();
    $hideNext =0 ;
    $order = Order::find()->where(['id'=>$id])->one();
    if (!$order) { // первый заход на сайт обычного user
      $order = new Order();
      $order->user_id = Yii::$app->user->id;
      $order->created_at = time();
      $order->el_group = '';
      $order->save();
    }
    $numbers = explode(',',$order->el_group);

    $edit_not_prohibited = 1;
    $hideNext = 0;
    $order_elements = [];
    $ids = '';
    $user_ids = '';
    $track_number_types = '';
    if ($order->el_group != '') {
      foreach ($numbers as $parcel_id) {
        $parcel = OrderElement::find()->where(['id' => $parcel_id])->with(['orderInclude'])->one();
        if ($parcel!=null) {
          $ids = $ids.$parcel->id.',';
          $user_ids = $user_ids.$parcel->user_id.',';
          $track_number_types = $track_number_types.$parcel->track_number_type.',';
          $order_elements[] = $parcel;
          if ($parcel->status > 1){
            $edit_not_prohibited = 0;
          }
          $totalPrice = 0;
          foreach ($parcel->orderInclude as $ordInclude) {
            $totalPrice += ($ordInclude->price * $ordInclude->quantity);
          }
          if ($totalPrice > Yii::$app->config->get('parcelMaxPrice')) $hideNext = 1;
          $totalPriceArray[] = $totalPrice;
        }
      }
    }

    $message_for_edit_prohibited_order = 'Editing order prohibited';

    return $this->render('createOrder', [
      'edit_not_prohibited' => $edit_not_prohibited,
      'order_elements' => $order_elements,
      'createNewAddress'=>!$order_elements,
      'order_id'=>$id,
      'message_for_edit_prohibited_order' => $message_for_edit_prohibited_order,
      'totalPriceArray' => $totalPriceArray,
      'hideNext' => $hideNext,
      'ids' =>$ids,
      'user_ids' => $user_ids,
      'track_number_types' => $track_number_types,
      /*'searchModel' => $searchModel,
      'dataProvider' => $dataProvider,
      'order' => $model,*/
    ]);
  }


  public function actionViewOrder($id){
    if(!Yii::$app->user->can('orderChangeForAdmin')){
      throw new \yii\web\ForbiddenHttpException('You are not allowed to perform this action.');
      return false;
    }

    $order = Order::find()->where(['id'=>$id])->one();
    $numbers = explode(',',$order->el_group);
    $parcels = OrderElement::find()->where(['id' => $numbers])->all();
    $users=[];
    $users_parcel=[];
    $total=[
      'weight'=>0,
      'weight_by_user'=>[]
    ];

    foreach($parcels as $parcel){
      if(!isset($users_parcel[$parcel->user_id])){
        $users_parcel[$parcel->user_id]=[];
        $total['weight_by_user'][$parcel->user_id]=0;
        $users[]=$parcel->user_id;
      }
      $total['weight']+=$parcel->weight;
      $total['weight_by_user'][$parcel->user_id]+=$parcel->weight;
      $users_parcel[$parcel->user_id][]=$parcel;
    }

    $users=User::find()->where(['id' => $users])->all();
    //d($users);
    //ddd($users_parcel);

    return $this->render('orderView', [
      'users'=>$users,
      'users_parcel'=>$users_parcel,
      'parcels'=>$parcels,
      'total'=>$total,
      'order_id'=>$id,
    ]);
  }

  public function actionGroupRemove($order_id,$id)
  {
    if(!Yii::$app->user->can('orderChangeForAdmin')){
      throw new \yii\web\ForbiddenHttpException('You are not allowed to perform this action.');
      return false;
    }
    $order = Order::find()->where(['id'=>$order_id])->one();
    $numbers = explode(',',$order->el_group);
    $numbers_new=[];
    foreach ($numbers as $i){
      if($i!=$id){
        $numbers_new[]=$i;
      }
    }
    $order->el_group=implode(',',$numbers_new);
    $order->save();

    Yii::$app->response->format = Response::FORMAT_JSON;
    return ['forceClose'=>true,'forceReload'=>'#crud-datatable-pjax'];
  }

  public function actionChooseStatus($id){
    if(!Yii::$app->user->can('orderChangeForAdmin')){
      throw new \yii\web\ForbiddenHttpException('You are not allowed to perform this action.');
      return false;
    }
    $request = Yii::$app->request;
    $order = Order::find()->where(['id'=>$id])->one();

    $status_list=OrderElement::getTextStatus();
    foreach($status_list as $k=>&$v){
      if(strlen($k)==0 || $k<0){
        unset($status_list[$k]);
      }
    }
    if($request->isAjax) {
      Yii::$app->response->format = Response::FORMAT_JSON;
      if ($request->isGet) {
        return [
          'title' => "Choose status to parcels",
          'content' => $this->renderAjax('chooseStatus', [
            'status_list' => $status_list,
            'order_id' => $id,
          ]),
          'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
            Html::button('Save', ['class' => 'btn btn-success', 'type' => "submit"])

        ];
      }else{
        $order->setStatus($request->post('status'),$request->post('send_mail'));
        return [
          'title' => "Choose status to parcels",
          'content' => "Status successfully updated",
          'forceReload'=>'#crud-datatable-pjax',
          'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"])

        ];
      }
    }
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
  public function actionCreate() // создание товара внутри Посылки в ЗАКАЗЕ. Модалки
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
        throw new NotFoundHttpException('The requested page does not exist.');
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

    if(($request->isPost)&&($_POST['OrderElement']['agreement'])=="1"){ // юзер выбрал дату транспортип=ровки и подписал соглашение
      $pac = new OrderElement();
      $pac->load($request->post());
      $transport_date = strtotime($_POST['OrderElement']['transport_data']);
      $arr = explode(',', $order->el_group);
      foreach ($arr as $parcel_id){                            // редактируем дату доставки и соглашение для всех посылок в заказе
        $pac = OrderElement::findOne(['id'=>$parcel_id]);
        $pac->transport_data = $transport_date;
        $pac->agreement = '1';
        $pac->save();
      }
      return $this->redirect(['/payment/order/'.$id]);
    }

    $arr = explode(',', $order->el_group);
    if ($order->el_group=='') {
      return $this->redirect('/');
    }

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

    $order_elements = [];
    $param_name='receive_max_time'.(Yii::$app->user->identity->isManager() ? '_admin' : '');
    $max_time=time()+(24-Yii::$app->config->get($param_name))*60*60;

    foreach ($arr as $i=>$parcel_id) {
      $pac = OrderElement::find()->where(['id'=>$parcel_id])->one();
      $order_elements[] = $pac;

      if($pac->transport_data<$max_time){
        $pac->transport_data=$max_time;
      }

      $pac->transport_data=date("d-M-Y", $pac->transport_data);

      if ($pac->track_number == '' && $pac->track_number_type==0) {
        Yii::$app
          ->getSession()
          ->setFlash(
            'error',
            'Track number validation failed in Parcel №'.($i+1)
          );
        return $this->redirect('/orderInclude/create-order/' . $id);
      }

      $pac->includes_packs = $pac->getIncludes();
      if (count($pac->includes_packs) == 0) {
        Yii::$app
          ->getSession()
          ->setFlash(
            'error',
            'The package №'.($i+1).' must have at least one attachment.'
          );
        return $this->redirect('/orderInclude/create-order/' . $id);
      }
      $this_weight = 0;
        $no_country = false;
      foreach ($pac->includes_packs as $pack) {
        $total['price'] += $pack['price'] * $pack['quantity'];
        $total['quantity'] += $pack['quantity'];
        if (($pack['country']=='')||($pack['country'] >= count(Yii::$app->params['country']))) {
            $no_country=true;
        }
      }
      $this_weight =  $pac->weight;
      $total['weight']+=$this_weight;

      $total['weight_lb']=floor($total['weight']);
      $total['weight_oz']=floor(($total['weight']-$total['weight_lb'])*16);

      if($no_country==true){
          Yii::$app
              ->getSession()
              ->setFlash(
                  'error',
                  'Enter a country in parcel-table'
              );
          return $this->redirect('/orderInclude/create-order/' . $id);
      }
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
      'order_elements' => $order_elements,
      'createNewAddress'=>!$order_elements,
      'order_id'=>$id,
      'total'=>$total,
      'model'=>$pac,
      'admin'=>(Yii::$app->user->identity->isManager())?(1):(0),
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
    if(!$order){
      throw new NotFoundHttpException('The requested page does not exist.');
    }

    $connection = Yii::$app->getDb();
    $sql="
      UPDATE 
        `order_element`
      SET `transport_data` = '".strtotime($request->post('value'))."'
      WHERE `id` in (".$order->el_group.")";

    if(!Yii::$app->user->identity->isManager()){
      $sql.=" AND user_id=".Yii::$app->user->id;
    }
    $command = $connection->createCommand($sql);
    $command->queryAll();

    return ;
  }

  public function actionBorderFormPdf($id,$for_each=false){
    $this->layout = 'pdf';

    $order = Order::findOne($id);
    $order_data=$order->getSumData($id,true);
    if(!$order_data)return false;

    Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
    $headers = Yii::$app->response->headers;
    $headers->add('Content-Type', 'application/pdf');

    $tpl=(count($order_data['order_elements'])==1||$for_each)?'borderFormPdf_one_pac':'borderFormPdf';
    $content = $this->renderPartial($tpl,$order_data);

    //echo '<link rel="stylesheet" type="text/css" href="/css/pdf_CBP_Form_7533.css">';
    //return $content;
    // setup kartik\mpdf\Pdf component
    $pdf = new Pdf([
      'content' => $content,
      //'cssFile' => '@vendor/kartik-v/yii2-mpdf/assets/kv-mpdf-bootstrap.min.css',
      'cssFile' => '@app/web/css/pdf_CBP_Form_7533.css',
      'cssInline' => '.kv-heading-1{font-size:180px}',
      'options' => ['title' => 'CBP Form 7533 for order №'.$id],
      'methods' => [
        //'SetHeader'=>['Krajee Report Header'],
        //'SetFooter'=>['{PAGENO}'],
      ]
    ]);
    // return the pdf output as per the destination setting
    return $pdf->render();
  }

  public function actionBorderFormPdfForEach($id){
    return $this->actionBorderFormPdf($id,true);
  }

  public function actionPdf($id){
    $this->layout = 'pdf';
    $order = Order::findOne($id);
    $order_data=$order->getSumData($id,true);
    if(!$order_data)return false;

    $tpl='orderIncludePdf';

    $content = $this->renderPartial($tpl,$order_data);


    Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
    $headers = Yii::$app->response->headers;
    $headers->add('Content-Type', 'application/pdf');
    //echo '<link rel="stylesheet" type="text/css" href="/css/pdf_CBP_Form_7533.css">';
    //return $content;
    // setup kartik\mpdf\Pdf component
    $pdf = new Pdf([
      'content' => $content,
      //'cssFile' => '@vendor/kartik-v/yii2-mpdf/assets/kv-mpdf-bootstrap.min.css',
      'cssFile' => '@app/web/css/pdf_CBP_Form_7533.css',
      'cssInline' => '.kv-heading-1{font-size:180px}',
      'options' => ['title' => 'Table for order №'.$id],
      'methods' => [
        //'SetHeader'=>['Krajee Report Header'],
        //'SetFooter'=>['{PAGENO}'],
      ]
    ]);
    // return the pdf output as per the destination setting
    return $pdf->render();
  }

  public function actionCommercialInvoice($id){
    $this->layout = 'pdf';

    $order = Order::findOne($id);
    $order_data=$order->getSumData($id,true);
    if(!$order_data)return false;
    //ddd($order_data);
    $tpl='commercialInvoicePdf';

    $content = $this->renderPartial($tpl,$order_data);

    //echo '<link rel="stylesheet" type="text/css" href="/css/pdf_CBP_Form_7533.css">';
    //return $content;

    Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
    $headers = Yii::$app->response->headers;
    $headers->add('Content-Type', 'application/pdf');
    // setup kartik\mpdf\Pdf component
    $pdf = new Pdf([
      'content' => $content,
      //'cssFile' => '@vendor/kartik-v/yii2-mpdf/assets/kv-mpdf-bootstrap.min.css',
      'cssFile' => '@app/web/css/pdf_CBP_Form_7533.css',
      'cssInline' => '.kv-heading-1{font-size:180px}',
      'options' => ['title' => 'Commercial invoice for order №'.$id],
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

    /*public function createLog($user_id,$order_id,$description){
        \app\modules\logs\controllers\DefaultController::createLog($user_id,$order_id,$description);
    }*/
}

