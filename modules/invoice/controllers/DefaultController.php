<?php

namespace app\modules\invoice\controllers;

use app\modules\orderElement\models\OrderElement;
use yii\web\Controller;
use Yii;
use app\modules\order\models\Order;
use app\modules\additional_services\models\AdditionalServicesList;

/**
 * Default controller for the `invoice` module
 */
class DefaultController extends Controller
{
  /**
   * Создание инвосов
   * Renders the index view for the module
   * @return string
   */
  public function actionCreate($id)
  {
    if (Yii::$app->user->isGuest || !Yii::$app->user->can('trackInvoice')){
      throw new NotFoundHttpException('Access is denied.');
    }

    $request = Yii::$app->request;
    $order = Order::findOne($id);

    if (strlen($order->el_group) < 1) {
      throw new NotFoundHttpException('There is no data.');
    };

    $model = $order->getOrderElement();
    $data=[
      'invoice'=>'',
      'ref_code'=>'',
      'contract_number'=>'',
    ];

    $request = Yii::$app->request;
    if($request->isPost) {
      //
    }

    $usluga=[
      'parcel'=>[],
      'many'=>[],
    ];
    $uslugaList=AdditionalServicesList::find()
      ->where(['active'=>1])
      //->andWhere(['!=', 'id', 1])
      ->asArray()
      ->all();
    foreach ($uslugaList as $item){
      if($item['id']==1){

        continue;
      }
      if($item['type']==1){
        $usluga['parcel'][]=$item;
      }else{
        $usluga['many'][]=$item;
      }
    };

    return $this->render('invoiceCreate', [
      'users_parcel'=>$model,
      'order_id'=>$id,
      'data'=>$data,
      'usluga'=>$usluga,
      'order_service'=>$order->getAdditionalService(),
    ]);
  }

  //добавление услуги к посылке
  public function actionAddServiceToParcel($id,$service){
    if (Yii::$app->user->isGuest || !Yii::$app->user->can('trackInvoice')){
      throw new NotFoundHttpException('Access is denied.');
    }

    $pac=OrderElement::find()->where(['id'=>$id])->one();
    $this_service=$pac->addAdditionalService($service,true);

    $request = Yii::$app->request;
    return $this->redirect(['/invoice/create/'.$request->get('order')]);
  }

  //добавление услуги к заказу/всем посылкам в заказе
  public function actionAddServiceToAll($id,$service){
    if (Yii::$app->user->isGuest || !Yii::$app->user->can('trackInvoice')){
      throw new NotFoundHttpException('Access is denied.');
    }

    $order=Order::find()->where(['id'=>$id])->one();
    $order->addAdditionalService($service);

    return $this->redirect(['/invoice/create/'.$id]);
  }

}
