<?php

namespace app\modules\invoice\controllers;

use app\modules\additional_services\models\AdditionalServices;
use app\modules\invoice\models\Invoice;
use app\modules\invoice\models\SearchInvoice;
use app\modules\orderElement\models\OrderElement;
use yii\web\Controller;
use Yii;
use app\modules\order\models\Order;
use app\modules\additional_services\models\AdditionalServicesList;
use app\modules\user\models\User;
use kartik\mpdf\Pdf;

/**
 * Default controller for the `invoice` module
 */
class DefaultController extends Controller
{

  public function beforeAction($action)
  {
    if (Yii::$app->user->isGuest) {
      $this->redirect(['/parcels']);
      return false;
    }
    return parent::beforeAction($action);
  }

  /**
   * Создание инвосов
   * Renders the index view for the module
   * @return string
   */
  public function actionCreate($id, $cron = 0)
  {
    if (($cron == 0) && (!Yii::$app->user->can('trackInvoice'))){
      throw new NotFoundHttpException('Access is denied.');
    }

    $order = Order::findOne($id);

    if (strlen($order->el_group) < 1) {
      throw new NotFoundHttpException('There is no data.');
    };

    $invoice_data=$order->getInvoiceData();

    $request = Yii::$app->request;
    if (($cron == 1) || ($request->isPost)) {
      $order_service=$order->getAdditionalService();

      $invoice=[];
      $parcel=[];

      foreach ($order_service as $as) {
        if(($cron == 1)||($request->post('ch_invoice_'.$as->id)==1)){
          $invoice[]=$as->id;
        }
      }

      $model=$order->getOrderElement();
      foreach ($model as $pac) {
        if (($cron == 1)||($request->post('ch_parcel_'.$pac->id)==1)){
          $parcel[]=$pac->id;
        }
        $as = $pac->trackInvoice;
        if((($cron == 1) && $as && !$as->isNewRecord)||($as && !$as->isNewRecord && $request->post('ch_invoice_track_'.$pac->id)==1)){
          $invoice[]=$as->id;
        }

        $services=$pac->getAdditionalServiceList(false);

        foreach ($services as $as){
          if(($cron == 1)||($request->post('ch_invoice_'.$as->id)==1)){
            $invoice[]=$as->id;
          }
        }
      }

      sort($parcel);
      sort($invoice);

      $parcel=implode(',',$parcel);
      $invoice=implode(',',$invoice);

      $inv=Invoice::find()->where(['parcels_list'=>$parcel,'services_list'=>$invoice])->one();
      if(!$inv){
        $inv=new Invoice;
        $inv->parcels_list=$parcel;
        $inv->services_list=$invoice;
        $inv->create=time();
        $inv->detail=$cron;
      }
      if ($cron){
        $inv->detail = json_encode([
          'cron' => $cron,
        ]);
      }else {
        $session = Yii::$app->session;

        $inv->detail = json_encode([
          'invoice' => $session['invoice_' . $id],
          'ref_code' => $session['ref_code_' . $id],
          'contract_number' => $session['contract_number_' . $id],
        ]);
      }
      $inv->save();
      if ($cron == 1) {
        return $inv->id;
      } else {
        return $this->redirect(['/invoice/pdf/' . $inv->id]);
      }
    }

    return $this->render('invoiceCreate', $invoice_data);
  }

  /**
   * Lists all Config models.
   * @return mixed
   */
  public function actionIndex()
  {
    $searchModel = new SearchInvoice();
    $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

    return $this->render('index', [
      'searchModel' => $searchModel,
      'dataProvider' => $dataProvider,
    ]);
  }

  public function actionEdit($id)
  {
    if (!Yii::$app->user->can('trackInvoice')){
      throw new NotFoundHttpException('Access is denied.');
    }

    $invoice=Invoice::find()->where(['id'=>$id])->one();
    $sel_pac=$invoice->getParcelList();
    $order=Order::find()->where(['el_group'=>implode(',',$sel_pac)])->one();


    if(!$order) {
      $order = new Order();
      $order->el_group = implode(',', $sel_pac);
      $order->user_id = Yii::$app->user->id;
      $order->save();
    }

    $invoice_data=$order->getInvoiceData($invoice);

    //ddd($invoice_data);
    return $this->render('invoiceCreate', $invoice_data);
  }

  /**
   * обновление инвосов
   **/
  public function actionUpdate($id) {
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
        return $this->redirect(['/parcels']);
      }

      $inv->qst=round($inv->price*$tax['qst']/100,2);
      $inv->gst=round($inv->price*$tax['gst']/100,2);

      if(!$is_invoice){
        $inv->dop_price=round($inv->kurs*$data['tr_external_price_'.$id_inv],2);
        $inv->dop_qst=round($inv->dop_price*$tax['qst']/100,2);
        $inv->dop_gst=round($inv->dop_price*$tax['gst']/100,2);

        if(strpos($request->post('name'),'tr_number_')!==false) {
          $order_element->track_number = $request->post('value');
          $order_element->save();
        }

        $detail=[
          "price_tk"=>$data['tr_external_price_'.$id_inv],
          'track_number'=>$data['tr_number_'.$id_inv],
          'track_company'=>$order_element->GetShippingCarrierName(true)
        ];
        $inv->detail=json_encode($detail);
      }
      $inv->save();
    }

    return $id;
  }


  /*
   * Печать PDF с инвойсом
   */
  public function actionPdf($id)
  {
    $inv=Invoice::find()->where(['id'=>$id])->one();

    $data=$inv->getTable();

    if(!$data){
      throw new NotFoundHttpException('Access is denied.');
    }

    if (
      !(Yii::$app->user->id==$data['user_id'] || Yii::$app->user->can('trackInvoice'))
    ) {
      throw new NotFoundHttpException('Access is denied.');
    }


    $content = $this->renderPartial('invoicePdf',$data);


    $pdf = new Pdf([
      'content' => $content,
      //'cssFile' => '@vendor/kartik-v/yii2-mpdf/assets/kv-mpdf-bootstrap.min.css',
      'cssFile' => '@app/web/css/pdf_CBP_Form_7533.css',
      'cssInline' => '.kv-heading-1{font-size:180px}',
      'options' => ['title' => '_invoice_'.$id],
      'methods' => [
        //'SetHeader'=>['Krajee Report Header'],
        //'SetFooter'=>['{PAGENO}'],
      ],
    ]);
    //return \yii\helpers\Url::to('@web/img/mailtousa.png', true);
    //return Yii::$app->urlManager->createAbsoluteUrl("/img/mailtousa.png");
    $this->layout = 'pdf';
    Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
    $headers = Yii::$app->response->headers;
    $headers->add('Content-Type', 'application/pdf');
    // return the pdf output as per the destination setting

    return $pdf->render();
  }

  //добавление услуги к посылке
  public function actionAddServiceToParcel($id,$service){
    if (Yii::$app->user->isGuest || !Yii::$app->user->can('trackInvoice')){
      throw new NotFoundHttpException('Access is denied.');
    }

    $pac=OrderElement::find()->where(['id'=>$id])->one();
    //$this_service=$pac->addAdditionalService($service,true);

    $request = Yii::$app->request;
    if($request->get('order')) {
      return $this->redirect(['/invoice/create/' . $request->get('order')]);
    }else{
      return $this->redirect(['/invoice/edit/' . $request->get('invoice')]);
    }
  }

  //добавление услуги к заказу/всем посылкам в заказе
  public function actionAddServiceToAll($id,$service){
    if (Yii::$app->user->isGuest || !Yii::$app->user->can('trackInvoice')){
      throw new NotFoundHttpException('Access is denied.');
    }

    $order=Order::find()->where(['id'=>$id])->one();
    $order->addAdditionalService($service);

    $request = Yii::$app->request;
    if($request->get('invoice')) {
      return $this->redirect(['/invoice/edit/' . $request->get('invoice')]);
    }else{
      return $this->redirect(['/invoice/create/'.$id]);
    }
  }

}
