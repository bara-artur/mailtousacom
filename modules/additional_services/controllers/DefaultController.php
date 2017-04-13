<?php

namespace app\modules\additional_services\controllers;

use Yii;
use app\modules\additional_services\models\AdditionalServices;
use app\modules\additional_services\models\AdditionalServicesSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\modules\order\models\Order;
use app\modules\orderElement\models\OrderElement;

/**
 * DefaultController implements the CRUD actions for AdditionalServices model.
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

    /**
     * Lists all AdditionalServices models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AdditionalServicesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    public function actionTransportInvoice($id){
      if (Yii::$app->user->can('trackInvoice')) {
        $request = Yii::$app->request;

        $order = Order::findOne($id);

        $session = Yii::$app->session;
        $session->set('last_order', $id);

        if (strlen($order->el_group) < 1) {
          throw new NotFoundHttpException('There is no data for payment.');
        };

        $el_group = explode(',', $order->el_group);

        $model = OrderElement::find()->where(['id' => $el_group])->all();

        $data = [
          'invoice' => '',
          'ref_code' => '',
          'contact_number' => '',
        ];

        $request = Yii::$app->request;
        if ($request->isPost) {
          $data = [
            'invoice' => $request->post('invoice'),
            'ref_code' => $request->post('ref_code'),
            'contact_number' => $request->post('contact_number'),
          ];
          foreach ($model as $parcel) {
            if ($request->post('tr_number_' . $parcel->id)) {
              $parcel->track_number = $request->post('tr_number_' . $parcel->id);
              $parcel->save();
            }
          };
          //ddd($request->post());
        };
        //ddd($data);
        return $this->render('transportInvoice', [
          'users_parcel' => $model,
          'order_id' => $id,
          'data' => $data,
        ]);
      }else{
        return $this->redirect(['/']);
      }


    }

    /**
     * Displays a single AdditionalServices model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }



    /**
     * Finds the AdditionalServices model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return AdditionalServices the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AdditionalServices::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
