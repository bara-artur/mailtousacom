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
/**
 * DefaultController implements the CRUD actions for OrderElement model.
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
            if ($oldModel) {
                if ($_POST['lb'] != null) {
                  $weight = (int)$_POST['lb'];
                }

                if ($_POST['oz'] != null) {
                  $weight += ((int)$_POST['oz']) / 16;//oldModel->oz = $_POST['oz'];
                }

                $oldModel->weight = $weight;
                // $weight = $_POST['lb'] + $oz;
                if ($_POST['track_number'] != null) {
                  $oldModel->track_number = $_POST['track_number'];
                }
                if (isset($_POST['track_number_type']))
                  $oldModel->track_number_type = 1;
                else
                  $oldModel->track_number_type = 0;

                $oldModel->save();
            }
            $ParcelPrice=ParcelPrice::widget(['weight'=>$weight]);
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
                $model->save();
                if ($order->el_group==null) {
                  $order->el_group = ''.$model->id;
                }else{
                  $order->el_group = $order->el_group.','.$model->id;
                }
                if ($order->save()) {
                  return [
                    'forceReload' => '#crud-datatable-pjax',
                    'title' => "Adding new packages",
                    'content' => '<span class="text-success">Create packages success</span>',
                    'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"])
                  ];
                }else  throw new NotFoundHttpException('Order not requested');
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
                    'title'=> "Chaaaa999ange the recipient data #".$id,
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

    public function actionGroupDelete($id){
      return $id;
    }

    public function actionGroupPrint($id){
      return $id;
    }

    public function actionGroupUpdate($id){
      return $id;
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

    public function beforeAction($action)
    {
        // ...set `$this->enableCsrfValidation` here based on some conditions...
        // call parent method that will check CSRF if such property is true.
        if ($action->id === 'create') {
            # code...
            $this->enableCsrfValidation = false;
        }
        return parent::beforeAction($action);
    }
}
