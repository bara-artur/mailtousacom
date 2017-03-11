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
        if (isset($_GET['term'])) {
          $tmp = $_GET['term'];

          //фомируем список
          $listdata = User::find()
            ->orWhere(['like', 'email', $tmp])
            ->orWhere(['like', 'first_name', $tmp])
            ->orWhere(['like', 'last_name', $tmp])
            ->orWhere(['like', 'phone', $tmp])
            ->select(['username as value', 'email as label'])
            ->asArray()
            ->all();

          return ['11111111111','2222222222','33333333331','4444444444441','555555555551','5666666666661','77777771','4444444441','3333331',',199999999','8888881'];
        } else {
          /*
          *   Process for ajax request
          */

          Yii::$app->response->format = Response::FORMAT_JSON;

          return [
            'title' => "Adding new packages",
            'content' => $this->renderAjax('createByAdmin'),
            //'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
            //  Html::button('Save', ['class' => 'btn btn-success', 'type' => "submit"])

          ];
        }
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
