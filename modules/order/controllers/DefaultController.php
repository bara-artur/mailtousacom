<?php

namespace app\modules\order\controllers;

use Yii;
use app\modules\order\models\Order;
use app\modules\order\models\OrderSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

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

    /**
     * Creates a new Order model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $request = Yii::$app->request;
        $address_id = $request->post( 'id' );
        $model = new Order();

        $model->user_id = Yii::$app->user->id;
        $model->billing_address_id = $address_id;
        $model->order_status = 0;
        $model->order_type = 0;
        $model->user_id_750 = $model->user_id + 750;
        $model->created_at = time();
        $model->transport_data = time();
        $model->save();

        return $this->render('update', [
            'model' => $model,
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
    public function beforeAction($action)
    {
        // call parent method that will check CSRF if such property is true.
        if ($action->id === 'create') {
            $this->enableCsrfValidation = false;
        }
        return parent::beforeAction($action);
    }
}
