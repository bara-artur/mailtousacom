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
use yii\db\Query;
use kartik\mpdf\Pdf;
use app\modules\user\models\User;

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
      if (!Yii::$app->user->can('trackInvoice')){
        throw new NotFoundHttpException('Access is denied.');
      }
        $request = Yii::$app->request;

        $order = Order::findOne($id);

        $session = Yii::$app->session;
        $session->set('last_order', $id);

        if (strlen($order->el_group) < 1) {
          throw new NotFoundHttpException('There is no data.');
        };

        $el_group = explode(',', $order->el_group);
        $model = $order->getOrderElement();

      $data=[
        'invoice'=>'',
        'ref_code'=>'',
        'contract_number'=>'',
      ];

      $request = Yii::$app->request;
      if($request->isPost) {

        $data=[
          'invoice'=>$request->post('invoice'),
          'ref_code'=>$request->post('ref_code'),
          'contract_number'=>$request->post('contract_number'),
        ];
        //и узнаем его налог
        $query = new Query;
        $query->select('state')
          ->from('new_address')
          ->where(['user_id'=>$model[0]->user_id]);
        $row = $query->one();

        if(!$row){
          Yii::$app->getSession()->setFlash('error', 'Missing billing address.');
          return $this->redirect(['/']);
        }

        $state=$row['state'];

        $query = new Query;
        $query->select(['qst','gst'])
          ->from('state')
          ->where(['name'=>$state]);
        $tax = $query->one();

        $flat_rate=[];
        $total=[
          'sub_total'=>0,
          'gst'=>0,
          'qst'=>0,
          'total'=>0,
          'paypal'=>0,
        ];
        foreach ($model as $parcel){
          if($request->post('tr_number_'.$parcel->id)){
            $parcel->track_number=$request->post('tr_number_'.$parcel->id);
            $parcel->save();

            $data['price_tk']=(float)$request->post('tr_external_price_'.$parcel->id);
            $ti=$parcel->trackInvoice;
            $ti->price=number_format((float)$request->post('tr_gen_price_'.$parcel->id),2,'.','');
            $ti->qst=round($ti->price*$tax['qst']/100,2);
            $ti->gst=round($ti->price*$tax['gst']/100,2);

            $ti->dop_price=round($ti->kurs*$data['price_tk'],2);
            $ti->dop_qst=round($ti->dop_price*$tax['qst']/100,2);
            $ti->dop_gst=round($ti->dop_price*$tax['gst']/100,2);

            $ti->detail=json_encode($data);
            $ti->status_pay=0;
            $ti->save();

            if(isset($flat_rate[$ti->price])){
              $flat_rate[$ti->price]++;
            }else{
              $flat_rate[$ti->price]=1;
            };

            $total['sub_total']+=$ti->price+$data['price_tk'];
            $total['qst']+=$ti->dop_qst+$ti->qst;
            $total['gst']+=$ti->dop_gst+$ti->gst;
          }
        }

        $total['total']=$total['sub_total']+$total['qst']+$total['gst'];
        $total['paypal']=($total['total']*1.029+0.3);

        $content = $this->renderPartial('transportInvoicePdf',[
          'users_parcel'=>$model,
          'order_id'=>$id,
          'data'=>$data,
          'tax'=>$tax,
          'user'=>User::findOne($model[0]->user_id),
          'flat_rate'=>$flat_rate,
          'total'=>$total
        ]);

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
      }else{
        foreach ($model as $parcel){
          $ti=$parcel->trackInvoice;
          if(!$ti->isNewRecord){
            $data=json_decode($ti->detail,true);
            break;
          }
        }
      };
      //ddd($data);
      return $this->render('transportInvoice', [
        'users_parcel'=>$model,
        'order_id'=>$id,
        'data'=>$data,
      ]);
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
