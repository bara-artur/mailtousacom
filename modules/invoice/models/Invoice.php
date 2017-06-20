<?php

namespace app\modules\invoice\models;

use app\modules\additional_services\models\AdditionalServices;
use app\modules\additional_services\models\AdditionalServicesList;
use Yii;
use app\modules\orderElement\models\OrderElement;
use app\modules\user\models\User;
use yii\db\Query;
use kartik\mpdf\Pdf;

/**
 * This is the model class for table "invoices".
 *
 * @property integer $id
 * @property string $parcels_list
 * @property string $services_list
 * @property integer $pay_status
 * @property integer $create
 */
class Invoice extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'invoices';
    }

    static function statusList(){
      return [
        ''=>'',
        0=>"Created",
        1=>"Sent to customer",
        2=>"Paid",
        3=>"Refunded",
        4=>"Cancelled"
      ];
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
          [['price'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'parcels_list' => 'Parcels List',
            'services_list' => 'Services List',
            'pay_status' => 'Pay Status',
            'create' => 'Create',
        ];
    }

  public function getUser(){
    return User::find()->where(['id'=>$this->user_id])->one();
  }

  public function getEmail(){
    return $this->getUser()->email;
  }
  /*
   * Вывод общих данных с платежами по инвойсу
   */
  public function getTable(){
    $orders=OrderElement::find()->where(['id'=>explode(',',$this->parcels_list)])->asArray()->all();

    $total=[
      'price'=>0,
      'qst'=>0,
      'gst'=>0,
    ];

    $user=false;

    $payments_list_pac=[];
    foreach ($orders as $pac){
      if(!$user){
        $user=$pac['user_id'];
      }

      $key=$pac['price'];
      if(isset($payments_list_pac[$key])){
        $payments_list_pac[$key]['quantity']+=1;
      }else{
        foreach (Yii::$app->params['pac_title'] as $k=>$title){
          if($k>=$pac['weight'])break;
        };
        $item=[
          'title'=>$title,
          'quantity'=>1,
          'price'=>number_format($pac['price'],2,'.',''),
          'qst'=>number_format($pac['qst'],2,'.',''),
          'gst'=>number_format($pac['gst'],2,'.',''),
        ];
        $payments_list_pac[$key]=$item;
      }

      $total['price']+=$pac['price'];
      $total['qst']+=$pac['qst'];
      $total['gst']+=$pac['gst'];
    }

    $kurs=false;
    $payments_list=[];
    $invoices=AdditionalServices::find()->where(['id'=>explode(',',$this->services_list)])->all();
    foreach ($invoices as $invoice){
      if(!$user){
        $user=$invoice->client_id;
      };

      if(!$kurs){
        $kurs=$invoice->kurs;
      };
      if($invoice->type==1){
        $dop=json_decode($invoice->detail,true);
        $title=$dop['track_company'].
          ' shipping label '.
          $dop['track_number'].
          ' - $'.
          number_format($dop['price_tk'],2,'.','').
          ' USD ';
        $item=[
          'title'=>$title,
          'quantity'=>1,
          'price'=>number_format($invoice['dop_price'],2,'.',''),
          'qst'=>number_format($invoice['dop_qst'],2,'.',''),
          'gst'=>number_format($invoice['dop_gst'],2,'.',''),
        ];

        $total['price']+=$invoice['dop_price'];
        $total['qst']+=$invoice['dop_qst'];
        $total['gst']+=$invoice['dop_gst'];
        $payments_list[]=$item;

        $title='Flat rate service fee';
      }else{
        $title=$invoice->getTitle();
      }

      $key=$invoice['type'].'_'.$invoice['price'];

      if(isset($payments_list_pac[$key])){
        $payments_list_pac[$key]['quantity']+=1;
      }else{
        $item=[
          'title'=>$title,
          'price'=>number_format($invoice['price'],2,'.',''),
          'quantity'=>1,
          'qst'=>number_format($invoice['qst'],2,'.',''),
          'gst'=>number_format($invoice['gst'],2,'.',''),
        ];
        $payments_list_pac[$key]=$item;
      }

      $total['price']+=$invoice['price'];
      $total['qst']+=$invoice['qst'];
      $total['gst']+=$invoice['gst'];
    }

    $total['price']=number_format($total['price'],2,'.','');
    $total['vat']=number_format($total['qst']+$total['gst'],2,'.','');
    $total['qst']=number_format($total['qst'],2,'.','');
    $total['gst']=number_format($total['gst'],2,'.','');
    $total['total']=number_format($total['price']+$total['vat'],2,'.','');

    $paypal_tax=$total['total']*Yii::$app->config->get('paypal_commision_dolia')/100
                +Yii::$app->config->get('paypal_commision_fixed');
    $total['paypal']=number_format($total['total']+$paypal_tax,2,'.','');

    if(Yii::$app->user->id==$user){
      $user_data=Yii::$app->user->identity;
    }else{
      $user_data=User::find()->where(['id'=>$user])->one();
    }

    return [
      'total'=>$total,
      'pay_list'=>array_merge($payments_list_pac,$payments_list),
      'data'=>json_decode($this->detail,true),
      'user_id'=>$user,
      'invoice_id'=>$this->id,
      'user'=>$user_data,
      'kurs'=>$kurs,
      'date'=>$this->create,
    ];
  }

  public function getParcelList(){
    $sel_pac=[];

    $services_list=explode(',',$this->services_list);
    $parcels_list=explode(',',$this->parcels_list);

    $orderElement=AdditionalServices::find()
      ->where(['id'=>$services_list])
      ->asArray()
      ->all();

    foreach ($orderElement as $pac){
      $pacs_id=explode(',',$pac['parcel_id_lst']);
      foreach ($pacs_id as $id){
        if(!in_array($id,$sel_pac)){
          $sel_pac[]=$id;
        }
      }
    };

    foreach ($parcels_list as $id){
      if(!in_array($id,$sel_pac)){
        $sel_pac[]=$id;
      }
    }

    return $sel_pac;
  }

  public function getTextStatus(){
    $status=Invoice::statusList();
    return $status[$this->pay_status];
  }

  public function beforeValidate(){
    $this->price=0;
    if(strlen($this->parcels_list)) {
      $sql = "
          SELECT
            sum(price) as price,
            user_id
          FROM order_element
          WHERE id in (" . $this->parcels_list . ")
          GROUP BY user_id
          ";
      $command = Yii::$app->db->createCommand($sql)->queryOne();

      $this->user_id=$command['user_id'];
      $this->price+=$command['price'];
    }

    if(strlen($this->services_list)) {
      $sql = "
          SELECT
            sum(price+dop_price) as price,
            client_id
          FROM additional_services
          WHERE id in (" . $this->services_list . ")
          GROUP BY client_id
          ";
      $command = Yii::$app->db->createCommand($sql)->queryOne();

      $this->user_id=$command['client_id'];
      $this->price+=$command['price'];
    }

    return parent::beforeValidate(); // TODO: Change the autogenerated stub
  }

  public function sendMail($mail=false)
  {
    $data = $this->getTable();
    $content = Yii::$app->controller->renderPartial('invoicePdf', $data);
    $pdf = new Pdf([
      //'content' => $content,
      //'cssFile' => '@vendor/kartik-v/yii2-mpdf/assets/kv-mpdf-bootstrap.min.css',
      'cssFile' => '@app/web/css/pdf_CBP_Form_7533.css',
      'cssInline' => '.kv-heading-1{font-size:180px}',
      'options' => ['title' => '_invoice_' . $this->id],
      'methods' => [
        //'SetHeader'=>['Krajee Report Header'],
        //'SetFooter'=>['{PAGENO}'],
      ],
    ]);

    $pdfContent = $pdf->Output($content,'', 'S'); // отсюда https://stackoverflow.com/questions/41058147/yii2-generate-pdf-on-the-fly-and-attach-to-email
                                        //   http://demos.krajee.com/mpdf
    //return $pdfContent;
    if ($mail == false) {
      $mail = $this->getEmail();
    }

    $message=\Yii::$app->mailer->compose('invoice', $data);
    //$message = Yii::$app->mailer->compose();
    $message->attachContent($pdfContent, ['fileName' => '_invoice_' . $this->id.'.pdf', 'contentType' => 'application/pdf']);
    //$message->attachContent($content, ['fileName' => '_invoice_' . $this->id.'.pdf', 'contentType' => 'application/pdf']);
    //$message->attachContent($content, ['fileName' => '_invoice_' . $this->id.'.html', 'contentType' => 'application/html']);
    $message->setFrom(\Yii::$app->config->get('adminEmail'))
      ->setTo($mail)
      ->setSubject('Invoice')
      ->send();


    if ($this->pay_status == 0) {
      $this->pay_status = 1;
      $this->save();
    }
  }
}
