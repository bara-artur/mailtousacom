<?php

namespace app\modules\invoice\controllers;

use app\modules\additional_services\models\AdditionalServices;
use app\modules\orderElement\models\OrderElement;
use yii\web\Controller;
use Yii;
use app\modules\order\models\Order;
use app\modules\additional_services\models\AdditionalServicesList;
use app\modules\user\models\User;
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
      'session'=>Yii::$app->session,
    ]);
  }

  /**
   * обновление инвосов
   * Renders the index view for the module
   * @return string
   */
  public function actionUpdate($id)
  {
    if (Yii::$app->user->isGuest || !Yii::$app->user->can('trackInvoice')) {
      throw new NotFoundHttpException('Access is denied.');
    }

    $request = Yii::$app->request;
    if(!$request->isAjax || !$request->isPost){
      throw new NotFoundHttpException('Page not found.');
    }

    $session = Yii::$app->session;
    $save_in_session=[
      'invoice',
      'ref_code',
      'contract_number'
    ];

    foreach ($save_in_session as $item){
      if($request->post($item)){
        $session[$item.'_'.$id] = $request->post($item);
      }
    }

    if(
      $request->post('name') &&
      $request->post('data') &&
      !in_array($request->post('name'),$save_in_session)
    ) {
      $data = $request->post('data');
      if (gettype($data) == 'string') {
        $data = json_decode($data, true);
      }

      preg_match_all('|\d+|', $request->post('name'), $regs);
      $id_inv = $regs[0][0];

      $is_invoice = (strpos($request->post('name'), 'invoice') !== false);

      if ($is_invoice) {
        $inv = AdditionalServices::find()->where(['id' => $id_inv])->one();
      } else {
        $order_element = OrderElement::find()->where(['id' => $id_inv])->one();
        $inv = $order_element->getTrackInvoice();
      }

      if (!$inv) {
        return false;
      };
      if($is_invoice) {
        $inv->price = $data['tr_invoice_' . $id_inv];
      }else {
        $inv->price = $data['tr_gen_price_' . $id_inv];
      }

      $tax=User::find()->where(['id'=>$inv->client_id])->one()->getTax();

      if(!$tax){
        Yii::$app->getSession()->setFlash('error', 'Missing billing address.');
        return $this->redirect(['/']);
      }
      $inv->qst=round($inv->price*$tax['qst']/100,2);
      $inv->gst=round($inv->price*$tax['gst']/100,2);

      if(!$is_invoice){
        $inv->dop_price=round($inv->kurs*$data['tr_external_price_'.$id_inv],2);
        $inv->dop_qst=round($inv->dop_price*$tax['qst']/100,2);
        $inv->dop_gst=round($inv->dop_price*$tax['gst']/100,2);

        $detail=[
          "price_tk"=>$data['tr_external_price_'.$id_inv]
        ];
        $inv->detail=json_encode($detail);

        if(strpos($request->post('name'),'tr_number_')!==false) {
          $order_element->track_number = $request->post('value');
          $order_element->save();
        }
      }

      $inv->save();
      return 1;
    }

    var_dump($request->post('name'));
    return $id;
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
