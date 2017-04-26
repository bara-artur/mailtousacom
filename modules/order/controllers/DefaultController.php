<?php

namespace app\modules\order\controllers;

use app\modules\orderElement\models\OrderElement;
use app\modules\orderInclude\models\OrderInclude;
use Yii;
use app\modules\order\models\Order;
use app\modules\order\models\OrderSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\modules\user\models\User;
use \yii\web\Response;
use yii\helpers\Html;
use app\modules\order\models\OrderFilterForm;

/**
 * DefaultController implements the CRUD actions for Order model.
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
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

  public function actionCreate()   // попадаем если админ начинает оформлять заказ
  {
    if(!Yii::$app->user->identity->isManager()){  // user зашел не по адресу
      if (!Yii::$app->user->isGuest){
        return OrderInclude::createOrder(Yii::$app->user->id,$this);
      }
      Yii::$app
        ->getSession()
        ->setFlash(
          'error',
          'There is not enough user access.'
        );
      return $this->redirect(['/']);
    };

    $model= new User;
    $request = Yii::$app->request;
    if ($request->isAjax) {
      Yii::$app->response->format = Response::FORMAT_JSON;
      if ($request->isPost) {  // выбрали user_id для создания заказа ... идём оформлять посылки
        $model->load($request->post());
        if($model->user_id) {
          return OrderInclude::createOrder($model->user_id,$this);
          //$this->redirect('/user/view');
          //return 'Redirect to order create';
        }
      }
      Yii::$app->response->format = Response::FORMAT_JSON;
      //  показываем модалку для выбора пользователя или создания нового
      return [
        'title' => "Select a user for the new order",
        'content' => $this->renderAjax('createByAdmin',[
          'model'=>$model,
        ]),
        'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
          Html::a('<i class="fa fa-plus"></i> Create new user', '/user/admin/create', [
            'class'=>'btn btn-science-blue',
            'role'=>'modal-remote',
            'title'=> 'Add User',
            'data-pjax'=>0,
          ]).
          Html::button('<i class="fa fa-magic"></i>Create order', [
            'class' => 'btn btn-success admin_choose_user',
            'type' => "submit",
            'disabled'=>true
          ])
      ];
    }
    return $this->redirect(['/']);
  }

    public function actionUpdate()
    {
      $user = User::find()->where(['id' => Yii::$app->user->id])->one();
      if (($user!=null)&&(1)) {
        $order_id = $_POST['order_id'];
        $request = Yii::$app->request;
        $success = false;
        if (($order_id) && ($request->isAjax)) {
          $oldModel = Order::find()->where(['id' => $order_id])->one();
          if ($oldModel) {
           // if (($_POST['order_status'] != null)&&($_POST['order_status'] != 'none')) $oldModel->order_status = $_POST['order_status'];
           // if (($_POST['payment_state'] != null)&&($_POST['payment_state'] != 'none')) $oldModel->payment_state = $_POST['payment_state'];
            if (isset($_POST['track_number'])){
              $parcel = OrderElement::find()->where(['track_number' => $_POST['track_number']])->one();
              if ($parcel){
                if ($oldModel->el_group == '') {
                  $oldModel->el_group = $parcel->id;
                }
                else {
                  $oldModel->el_group = $oldModel->el_group.','.$parcel->id; // можно вставить проверку на нахождение этой посылки в заказе
                }
                if ($oldModel->save()){$success = 1;}
                else {$success=2;}
              }else{
                if (OrderElement::GetShippingCarrier($_POST['track_number'])){
                  $parcel = new OrderElement();
                  $parcel->first_name = '[default]';
                  $parcel->last_name = '[default]';
                  $parcel->company_name = '[default]';
                  $parcel->adress_1 = '[default]';
                  $parcel->city = '[default]';
                  $parcel->zip = '11111';
                  $parcel->phone = '434342342';
                  $parcel->state = '33';
                  $parcel->created_at = time();
                  $parcel->track_number = $_POST['track_number'];
                  $parcel->track_number_type = 0;
                  $parcel->weight = 1;
                  $parcel->address_type = 0;
                  if ($parcel->save()) {
                    if ($oldModel->el_group == '') {
                      $oldModel->el_group = $parcel->id;
                    } else {
                      $oldModel->el_group = $oldModel->el_group . ',' . $parcel->id; // можно вставить проверку на нахождение этой посылки в заказе
                    }
                    if ($oldModel->save()) {
                      $success = 3;
                    } else {
                      $success = 4;
                    }
                  }else{
                    $success = 6;
                  }
                }else{
                  $success = 5; // не прошла валидация трэк номера по компаниям
                }
              }
            }

          }
        }
        return $success;
      }
    //  return $this->redirect(['/']);
    }

    public function actionSelect($order_id)
    {
      $cookies = Yii::$app->response->cookies;
      $order = Order::find()->where(['id' => $order_id])->one();
      if ($order) {
        $cookies->remove('parcelCheckedId');
        $cookies->remove('parcelCheckedUser');
          Yii::$app->response->cookies->add(new \yii\web\Cookie([
            'name' => 'parcelCheckedId',
              'value' => $order->el_group
          ]));
          $count = count(explode(',',$order->el_group));  // количество посылок в заказе
          $str = '';
          for ($i=0;$i<$count;$i++){            // создаем строку с таким же количеством id юзера как и количество посылок в заказе
            $str = $str.$order->user_id;       // дублируем id юзера
            if ($i!=($count-1)) $str = $str.',';  // последнюю запятую не ставим
          }
          Yii::$app->response->cookies->add(new \yii\web\Cookie([
            'name' => 'parcelCheckedUser',
            'value' => $str
          ]));
        return $this->redirect('/');
      }
      return $this->redirect(['/']);
    }
    /**
     * Lists all Order models.
     * @return mixed
     */
    public function actionIndex()
    {
      if (!Yii::$app->user->isGuest) {
        $user = User::find()->where(['id' => Yii::$app->user->id])->one();
        if (!$user->isManager()) { }
      }
      /*        $orderTable = Order::find()->where(['user_id'=>Yii::$app->user->id])->with(['orderElement'])->all();
            $emptyOrder = null;
            foreach ($orderTable as $i=>$order){
                if ($emptyOrder==null){
                    if (count($order->orderElement)==0) $emptyOrder =$order->id;
                }
            }*/

      $query['OrderSearch'] = Yii::$app->request->queryParams;
      $time_to['created_at_to'] = null;
      $time_to['transport_date_to'] = null;
      // Загружаем фильтр из формы
      $filterForm = new OrderFilterForm();
      if(Yii::$app->request->post()) {
        $filterForm = new OrderFilterForm();
        $filterForm->load(Yii::$app->request->post());
        $query['OrderSearch'] = $filterForm->toArray();
        $time_to = ['created_at_to' => $filterForm->created_at_to];
      }

      Yii::$app->params['showAdminPanel'] = 0;
      if (($user!=null)&&($user->isManager())) Yii::$app->params['showAdminPanel'] = 1;

      $orderSearchModel = new OrderSearch();
      //$query = Yii::$app->request->queryParams;
      if (Yii::$app->params['showAdminPanel']==0) {
        if (array_key_exists('OrderSearch', $query)) $query['OrderSearch'] += ['user_id' => Yii::$app->user->id];
        else $query['OrderSearch'] = ['user_id' => Yii::$app->user->id];
      }
      $searchModel = new OrderSearch();
      $orders = $searchModel->search($query,$time_to);
      //$orders = $orderSearchModel->search(null,null);

      return $this->render('index',[
        'orders' => $orders,
        'searchModel' => $orderSearchModel,
        'filterForm' => $filterForm,
        //'emptyOrder' => $emptyOrder
      ]);
    }

    /**
     * Finds the Order model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Order the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Order::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
