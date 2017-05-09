<?php

namespace app\modules\orderElement\controllers;

use app\modules\order\models\Order;
use Yii;
use app\modules\orderInclude\models\OrderInclude;
use app\modules\orderElement\models\OrderElement;
use app\modules\orderElement\models\OrderElementSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use \yii\web\Response;
use yii\helpers\Html;
use app\components\ParcelPrice;
use yii\web\UploadedFile;

/**
 * DefaultController implements the CRUD actions for OrderElement model.
 */
class DefaultController extends Controller
{

  public function beforeAction($action)
  {
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

    /**
     * Lists all OrderElement models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new OrderElementSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    /**
     * Displays a single OrderElement model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {   
        $request = Yii::$app->request;
        if($request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                    'title'=> "OrderElement #".$id,
                    'content'=>'<span class="text-success">Create OrderInclude success</span>',
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                            Html::a('Edit',['Update','id'=>$id],['class'=>'btn btn-science-blue','role'=>'modal-remote'])
                ];    
        }else{
            return $this->render('view', [
                'model' => $this->findModel($id),
            ]);
        }
    }

    /**
     * Creates a new OrderElement model.
     * For ajax request will return json object
     * and for non-ajax request if creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreateOrder(){
        //получаем посылки в заказе
        $percel_id = $_POST['percel_id'];
        $order_id = $_POST['order_id'];

        $request = Yii::$app->request;
        if($request->isAjax) {
            $oldModel = OrderElement::find()->andWhere(['id' => $percel_id])->one();
            $user_id = $oldModel->user_id;
            if ($oldModel) {
                $weight=0;
                if ($_POST['lb'] != null) {
                  $weight = (int)$_POST['lb'];
                }
                if ($_POST['oz'] != null) {
                  $weight += ((int)$_POST['oz']) / 16;//oldModel->oz = $_POST['oz'];
                }

                $oldModel->weight = $weight;
                // $weight = $_POST['lb'] + $oz;
                if (isset($_POST['track_number_type']))
                  $oldModel->track_number_type = 1;
                else
                  $oldModel->track_number_type = 0;

                if (($_POST['track_number'] != null)&&($oldModel->track_number_type==0)) {
                  if ((OrderElement::find()->andWhere(['not in','id',$percel_id])->andWhere(['track_number'=> $_POST['track_number']])->one() ==null)&&
                      (OrderElement::GetShippingCarrier($_POST['track_number'])!=null)){
                    $oldModel->track_number = $_POST['track_number'];
                  }else{
                    if (OrderElement::GetShippingCarrier($_POST['track_number'])==null) {
                      return "Undefined track number. We can't recognize shipping company.";
                    }else{
                      return "Bad number. ".$_POST['track_number']." already exist in our system";
                    }
                  }
                }
                $oldModel->save();
            }
            $ParcelPrice=ParcelPrice::widget(['weight'=>$weight,'user'=>$user_id]);
            if($ParcelPrice!=false){
                $ParcelPrice.=' $ (without tax)';
            }else{
                $ParcelPrice='<b style="color: red;">Exceeded weight of a parcel.</b>';
            }
        }
       // $model = OrderElement::find()->where(['order_id'=>$id])->all();
        return $ParcelPrice;
    }

    public function actionCreate($id)
    {
        $request = Yii::$app->request;
        $model = new OrderElement();

        if($request->isAjax){

            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            if($request->isGet){
                return [
                    'title'=> "Adding new packages",
                    'content'=>$this->renderAjax('create', [
                      'model' => $model,
                      'order_id'=>$id,
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                                Html::button('Save',['class'=>'btn btn-success','type'=>"submit"])
        
                ];         
            }else if($model->load($request->post())  ){
                $order = Order::find()->where(['id'=> $_POST['OrderElement']['order_id']])->one();
                $model-> user_id = $order->user_id;
                $model-> created_at = time();
                if ($model->save()) {
                  Yii::$app->response->cookies->add(new \yii\web\Cookie([
                    'name' => 'parcelAnchorId',
                    'value' => $model->id,
                  ]));
                  if ($order->el_group == null) {
                    $order->el_group = '' . $model->id;
                  } else {
                    $order->el_group = $order->el_group . ',' . $model->id;
                  }
                  if ($order->save()) {
                    return [
                      'forceReload' => '#crud-datatable-pjax',
                      'title' => "Adding new packages",
                      'content' => '<span class="text-success">Create packages success</span>',
                      'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"])
                    ];
                  } else  throw new NotFoundHttpException('Order not requested');
                }else{
                  return [
                    'title'=> "Adding new packages",
                    'content'=>$this->renderAjax('create', [
                      'model' => $model,
                      'order_id'=>$id,
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                      Html::button('Save',['class'=>'btn btn-success','type'=>"submit"])

                  ];
                }
            }else{
                return [
                    'title'=> "Adding new packages",
                    'content'=>$this->renderAjax('create', [
                      'model' => $model,
                      'order_id'=>$id,
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                                Html::button('Save',['class'=>'btn btn-success','type'=>"submit"])

                ];
            }
        }else{
          throw new NotFoundHttpException('Invalid request.');
        }
       
    }

    /**
     * Updates an existing OrderElement model.
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
                    'title'=> "Change the recipient data #".$id,
                    'content'=>$this->renderAjax('update', [
                        'model' => $model,
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                                Html::button('Save',['class'=>'btn btn-success','type'=>"submit"])
                ];         
            }else if($model->load($request->post()) && $model->save()){
                return [
                    'forceReload'=>'#crud-datatable-pjax',
                    'title'=> "OrderElement #".$id,
                    'content'=>'<span class="text-success">Create OrderInclude success</span>',
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                            Html::a('Edit',['Update','id'=>$id],['class'=>'btn btn-science-blue','role'=>'modal-remote'])
                ];    
            }else{
                 return [
                    'title'=> "Change the recipient data #".$id,
                    'content'=>$this->renderAjax('update', [
                        'model' => $model,
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                                Html::button('Save',['class'=>'btn btn-success','type'=>"submit"])
                ];        
            }
        }else{
          throw new NotFoundHttpException('Invalid request.');
        }
    }

    /**
     * Delete an existing OrderElement model.
     * For ajax request will return json object
     * and for non-ajax request if deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id,$order_id)
    {
      $request = Yii::$app->request;
      OrderInclude::deleteAll(['order_id'=>$id]);
      OrderElement::deleteAll(['id'=>$id]);
      $order = Order::find()->where(['id' => $order_id])->one();
      if ($order){

        $arr = explode(',', $order->el_group);
        $ind=null;
        foreach ($arr as $i=>$a){
          if ($a==$id) $ind=$i;
        }
        unset($arr[$ind]);
        $order->el_group = implode(',', $arr);
        $order->save();
      }
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

  public function findOrCreateOrder($parcels_id, $admin = 0){
    $arr = explode(',', $parcels_id);
    asort($arr);
    $user_id = null;
    $status = null;
    $flag = 0;
    $flag_dif_clients = 0;
    foreach ($arr as $id) {
        $parcel = OrderElement::findOne(['id' => $id]);
        if ($parcel) {
          if (($flag == 1) &&   //  если это уже не первая посылка из списка
              ((($user_id != $parcel->user_id) && ($admin == 0)) ||    // несовпадение юзерров у неАдмина
               (($status>1) && ($parcel->status<=1)) ||
                (($status<=1) && ($parcel->status>1))  )) {           // несовпадение статусов
            return null;
          }
          if (($user_id != $parcel->user_id)&&($flag == 1)) $flag_dif_clients = 1;
          $user_id = $parcel->user_id;
          $status = $parcel->status;
          $flag = 1;
        }else{
          return null; // нет посылки из списка
        }
    }

    if ($flag == 1) {  // посылки существуют
      $parcels_id = implode(',', $arr);

      $order = Order::find()->where(["el_group" => $parcels_id])->one();
      if ($order) {
        return $order->id;
      }else {
        $order = new Order();
        $order->el_group = $parcels_id;
        $order->created_at = time();
        if ($admin==0){
          $order->user_id = $user_id;
        }else{
          $order->user_id = Yii::$app->user->id;
        }
        $order->client_id = $user_id;
        if ($flag_dif_clients == 1) $order->client_id = 0;
        $order->save();
        return $order->id;
      }
    }
    else return null;
  }

  public function actionGroupUpdate($parcels_id = null){
      $order_id = $this->findOrCreateOrder($parcels_id);
      $cookies = Yii::$app->response->cookies;
      $cookies->remove('parcelCheckedId');
      $cookies->remove('parcelCheckedUser');
      if ($order_id != null) {
        return $this->redirect(['/orderInclude/create-order/' . $order_id]);
      } else {
        Yii::$app->response->cookies->add(new \yii\web\Cookie(['name' => 'showTheGritter','value' => "gritterAdd('Error','Update error. Bad parcel IDs','gritter-danger')",]));
        return $this->redirect(['/']);
      }
  }

  public function actionGroupPrint($parcels_id=null,$for_each=false){
      $order_id = $this->findOrCreateOrder($parcels_id);
      if ($order_id != null) {
        if($for_each) {
          $this->redirect(['/orderInclude/border-form-pdf-for-each/' . $order_id]);
        }else {
          $this->redirect(['/orderInclude/border-form-pdf/' . $order_id]);
        }
        return "Create pdf for order " .  $order_id;
      } else {
        Yii::$app->response->cookies->add(new \yii\web\Cookie(['name' => 'showTheGritter','value' => "gritterAdd('Error','Print error. Bad parcel IDs','gritter-danger')",]));
        return $this->redirect(['/']);
      }
  }

  //Выводим спсок файлов для посвлки
  public function actionFiles($parcels_id){
    $request=Yii::$app->request;
    if(!$request->isPost && !$request->isAjax){
      Yii::$app
        ->getSession()
        ->setFlash(
          'error',
          'Document not found'
        );
      return $this->redirect(['/']);
    }

    $pac=OrderElement::findOne($parcels_id);
    if($pac->user_id!=Yii::$app->user->getId() && !Yii::$app->user->identity->isManager()){
      Yii::$app
        ->getSession()
        ->setFlash(
          'error',
          'Not enough access rights'
        );
      return $this->redirect(['/']);
    };

    if($pac->getDocsCount()==0){
      Yii::$app
        ->getSession()
        ->setFlash(
          'error',
          'The package does not have any attached documents'
        );
      return $this->redirect(['/']);
    }

    Yii::$app->response->format = Response::FORMAT_JSON;
    return [
      'title'=> "Documents for parcel #".$parcels_id,
      'content'=>$this->renderAjax('files_view', [
        'percel' => $pac,
      ]),
      'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"])
    ];
  }

  public function actionFileUpload($parcels_id){
    $request=Yii::$app->request;
    if(!$request->isPost && !$request->isAjax){
      Yii::$app
        ->getSession()
        ->setFlash(
          'error',
          'Document not found'
        );
      return $this->redirect(['/']);
    }
    $pac=OrderElement::findOne([$parcels_id]);
    if($pac->user_id!=Yii::$app->user->getId() && !Yii::$app->user->identity->isManager()){
      Yii::$app
        ->getSession()
        ->setFlash(
          'error',
          'Not enough access rights'
        );
      return $this->redirect(['/']);
    };
    $files=UploadedFile::getInstances($pac, 'files');
    return $pac->loadDoc($files);
  }

  public function actionFileDelete($parcels_id){
    $request=Yii::$app->request;
    if(!$request->isPost && !$request->isAjax){
      Yii::$app
        ->getSession()
        ->setFlash(
          'error',
          'Document not found'
        );
      return $this->redirect(['/']);
    }
    $pac=OrderElement::findOne([$parcels_id]);
    if($pac->user_id!=Yii::$app->user->getId() && !Yii::$app->user->identity->isManager()){
      Yii::$app
        ->getSession()
        ->setFlash(
          'error',
          'Not enough access rights'
        );
      return $this->redirect(['/']);
    };

    $pac->delFile($request->post('key'));
    Yii::$app->response->format = Response::FORMAT_JSON;
    return [
      'title'=> "",
      'content'=>'<script>
          modal.hide();
          a=$(\'[data-key="'.$request->post('key').'"]\').parentsUntil(\'.file-preview-thumbnails\').last();
          b=a.closest(\'.file-drop-zone\')
          a.remove();
          col_file=b.find(\'.file-preview-thumbnails>div:not(.kv-zoom-cache) .file-remove\').length
          if(col_file==0){
            b.append(\'<div class="file-drop-zone-title">Drag &amp; drop files here …<br>(or click to select file)</div>\')
          }
          b.closest(\'.order-include-index\').find(\'[col_file]\').attr("col_file",col_file)
          gritterAdd(\'File deleting\', \'Delete successful\', \'gritter-success\');
        </script>',
      'footer'=> ""
    ];

  }

  public function actionCommercial_inv_print($parcels_id=null){
      $order_id = $this->findOrCreateOrder($parcels_id);
      if ($order_id != null) {
        $this->redirect(['/orderInclude/commercial-invoice/' . $order_id]);
        return "Create pdf for order " .  $order_id;
      } else {
        Yii::$app->response->cookies->add(new \yii\web\Cookie(['name' => 'showTheGritter','value' => "gritterAdd('Error','Print error. Bad parcel IDs','gritter-danger')",]));
        return $this->redirect(['/']);
      }
  }

  public function actionGroupPrintAdvanced($parcels_id=null)
  {
      $order_id = $this->findOrCreateOrder($parcels_id);
      if ($order_id != null) {
        $this->redirect(['/orderInclude/pdf/' . $order_id]);
        return "Create pdf for order " .  $order_id;
      } else {
        Yii::$app->response->cookies->add(new \yii\web\Cookie(['name' => 'showTheGritter','value' => "gritterAdd('Error','Update error. Bad parcel IDs','gritter-danger')",]));
        return $this->redirect(['/']);
      }
  }

  public function actionGroupDelete($parcels_id=null){
    $arr = explode(',', $parcels_id);
    asort($arr);
    foreach ($arr as $parcel_id) {
      $parcel = OrderElement::findOne(['id' => $parcel_id]);
      if ($parcel){
        if ($parcel->payment_state==0) {
          OrderInclude::deleteAll(['order_id' => $parcel_id]);
          OrderElement::deleteAll(['id' => $parcel_id]);
        }
      }
    }
    $cookies = Yii::$app->response->cookies;
    $cookies->remove('parcelCheckedId');
    $cookies->remove('parcelCheckedUser');
    $this->redirect(['/'],200);
    return "Parcels delete complete successfully";
  }

  public function actionGroup($act){
    $parcels_id = $_COOKIE['parcelCheckedId'];
    if ($parcels_id!=null) {
      switch ($act) {
        case 'update':  {return $this->actionGroupUpdate($parcels_id); break;}
        case 'print':   {return $this->actionGroupPrint($parcels_id);break;}
        case 'print_for_each':   {return $this->actionGroupPrint($parcels_id,true);break;}
        case 'advanced_print':  {return $this->actionGroupPrintAdvanced($parcels_id);break;}
        case 'commercial_inv_print':    {return $this->actionCommercial_inv_print($parcels_id);break;}
        case 'delete':  {return $this->actionGroupDelete($parcels_id);break;}
        case 'view':    {return $this->actionGroupView($parcels_id);break;}
        case 'track_invoice':    {return $this->actionTrackInvoice($parcels_id);break;}
      }
    }
    Yii::$app->getSession()->setFlash('error', 'Action not found.');
    return $this->redirect(['/']);
  }

    public function actionTrackInvoice($parcels_id){
      //может запустить только админ. Но для 1-го пользоавателя
      $order_id = $this->findOrCreateOrder($parcels_id,1);
      if ($order_id != null) {
        $this->redirect(['/additional_services/transport-invoice/' . $order_id]);
        return $order_id;
      } else {
        Yii::$app->response->cookies->add(new \yii\web\Cookie(['name' => 'showTheGritter','value' => "gritterAdd('Error','Invoice error. Bad parcel IDs','gritter-danger')",]));
        return $this->redirect(['/']);
      }
    }

    public function actionGroupView($parcels_id = null){
        $order_id = $this->findOrCreateOrder($parcels_id,1);
        $cookies = Yii::$app->response->cookies;
        $cookies->remove('parcelCheckedId');
        $cookies->remove('parcelCheckedUser');
        if ($order_id != null) {
          $this->redirect(['/orderInclude/view-order/' . $order_id]);
          return $order_id;
        } else {
          Yii::$app->response->cookies->add(new \yii\web\Cookie(['name' => 'showTheGritter','value' => "gritterAdd('Error','View error. Bad parcel IDs','gritter-danger')",]));
          return $this->redirect(['/']);
        }
    }
    /**
     * Finds the OrderElement model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return OrderElement the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = OrderElement::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
