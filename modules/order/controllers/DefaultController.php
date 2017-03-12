<?php

namespace app\modules\order\controllers;

use Yii;
use app\modules\order\models\Order;
use app\modules\order\models\OrderSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\modules\user\models\User;
use \yii\web\Response;
use yii\helpers\Html;

/**
 * DefaultController implements the CRUD actions for Order model.
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

  public function actionCreate()
  {

    $request = Yii::$app->request;

    if ($request->isAjax) {
      Yii::$app->response->format = Response::FORMAT_JSON;
      if ($request->isGet) {
          /*
          *   Process for ajax request
          */

          Yii::$app->response->format = Response::FORMAT_JSON;

          $model= new User;
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
      } else {
        return $this->redirect(['/']);
      }
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
            if (($_POST['order_status'] != null)&&($_POST['order_status'] != 'none')) $oldModel->order_status = $_POST['order_status'];
            if (($_POST['payment_state'] != null)&&($_POST['payment_state'] != 'none')) $oldModel->payment_state = $_POST['payment_state'];

            $success = $oldModel->save();
          }
        }
        return $success;
      }
    //  return $this->redirect(['/']);
    }
    /**
     * Lists all Order models.
     * @return mixed
     */
    /*    public function actionIndex()
    {
          $searchModel = new OrderSearch();
          $dataProvider = $searchModel->search(['OrderSearch' => [
              'user_id' => Yii::$app->user->id,
          ]]);

          return $this->render('index', [
              'searchModel' => $searchModel,
              'dataProvider' => $dataProvider,
          ]);
      }
  */
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
