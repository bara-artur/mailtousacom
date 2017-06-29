<?php

namespace app\modules\ebay\controllers;

use Yii;
use yii\web\Controller;
use app\modules\orderElement\models\OrderElement;
use app\modules\orderInclude\models\OrderInclude;
use app\modules\order\models\Order;
use yii\web\NotFoundHttpException;

use app\modules\user\models\User;
use app\modules\importAccount\models\ImportParcelAccount;

/**
 * DefaultController implements the CRUD actions for Order model.
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

  public function actionConnection($id){
    $order=Order::findOne($id);

    if($order->user_id!=\Yii::$app->user->identity->id){
      throw new NotFoundHttpException('Order can editing only by creator.');
    }

    /*if($order->payment_state>0 || $order->order_status>1){
      throw new NotFoundHttpException('You can not edit your order.');
    }*/

    $ebay_tokens=ImportParcelAccount::find()->where(['type'=>1,'client_id'=>Yii::$app->user->identity->id])->all();

    //Если нет то получаем новый токен
    if (strlen(\Yii::$app->user->identity->ebay_token)<30){
      return $this->getTocken($id);
    }else{
      return $this->redirect('/orderInclude/create-order/'.$id);
    }

  }

  public function actionGetOrder($id){
    $order=Order::findOne($id);

    if($order->user_id!=\Yii::$app->user->identity->id){
      throw new NotFoundHttpException('Order can editing only by creator.');
    }

    $ebay_tokens=ImportParcelAccount::find()->where(['type'=>1,'client_id'=>Yii::$app->user->identity->id])->all();

    //Если нет то получаем новый токен
    if (!$ebay_tokens){
      return $this->actionGetToken($id);
      /*return $this->render('view', [
        'order_id' => $id
      ]);*/
    };

    if (strlen($order->el_group) < 1) {
      $el_group = [];
    } else {
      $el_group = explode(',', $order->el_group);
    };

    $new_parcel_count=0;
    foreach ($ebay_tokens as $ebay_token) {
      $model = \Yii::$app->getModule('ebay');;
      global $EBAY;

      $rers=$model->getOrders($ebay_token->token,$ebay_token->last_update);

      if(!$rers){
        continue;
      }

      $ebay_token->last_update=time();
      $ebay_token->save();

      //if there are error nodes
      if ($rers['errors']->length > 0) {
        continue;
      } else { //If there are no errors, continue
        if ($rers['entries'] == 0) {
          continue;
        }
        $orders = $rers['response']->OrderArray->Order;
        if ($orders == null) {
          continue;
        }

        foreach ($orders as $order_) {
          $box = new OrderElement();
          $shippingAddress = $order_->ShippingAddress;
          $name = explode(' ', $shippingAddress->Name);
          //$box->order_id=$id;
          $box->first_name = $name[0];
          unset($name[0]);
          $box->import_code=(string)$order_->OrderID;
          $box->import_id=$ebay_token->id;
          $box->last_name = implode(' ', $name);
          $box->company_name = 'Personal address';
          $box->adress_1 = (String)$shippingAddress->Street1;
          $box->adress_2 = (String)$shippingAddress->Street2;
          $box->city = (String)$shippingAddress->CityName;
          $box->zip = (String)$shippingAddress->PostalCode;
          $box->phone = (String)$shippingAddress->Phone;
          $box->state = (String)$shippingAddress->StateOrProvince;
          $box->user_id = Yii::$app->user->identity->id;
          $box->created_at = time();
          $box->address_type = 0;
          $box->source = 1;
          $transactions = $order_->TransactionArray;

          if(
            $transactions &&
            $transactions->Transaction &&
            $transactions->Transaction->ShippingDetails &&
            $transactions->Transaction->ShippingDetails->ShipmentTrackingDetails &&
            $transactions->Transaction->ShippingDetails->ShipmentTrackingDetails->ShipmentTrackingNumber
          ){
            $box->track_number=(String)$transactions->Transaction->ShippingDetails->ShipmentTrackingDetails->ShipmentTrackingNumber;
          }else{
            $box->track_number_type=1;
          }
        };


        if($box->save()){
          $el_group[]=$box->id;
          //d($box);
          //d($el_group);
          if($transactions){
            foreach ($transactions->Transaction as $transaction) {
              $item = new OrderInclude;
              $item->order_id = $box->id;
              $item->name = substr((String)$transaction->Item->Title,0,450);
              $item->price = (String)$transaction->TransactionPrice;
              $item->quantity = (int)$transaction->QuantityPurchased;
              $item->country = "none";

              //d($transaction);
              //d($transaction->Item);
              $item->save();
              d($item);

            }
          }
          $new_parcel_count++;
        }
      }
    }
    $order->el_group = implode(',', $el_group);
    $order->save();

    if($new_parcel_count==0){
      \Yii::$app
        ->getSession()
        ->setFlash(
          'info',
          'New parcel not found'
        );
    }else {
      \Yii::$app
        ->getSession()
        ->setFlash(
          'success',
          'Check imported from eBay'
        );
    }
    return $this->redirect('/orderInclude/create-order/'.$id);
  }

  public function actionGetToken($id){

    $request = Yii::$app->request;
    if(!$request->isPost) {
      return $this->render('view', [
        'order_id' => $id
      ]);
    }

    $ebay=\Yii::$app->getModule('ebay');;
    global $EBAY;
    $EBAY=$ebay->config;

    // your private parameters
    $params = array('order_id' => $id,'days'=>(int)$request->post('days'));

    // eBay's required parameters
    $query = array('RuName' => $EBAY['RuName']);

    $query['SessID'] = $params['SessionID'] =
      $ebay->TradeAPI('GetSessionID', "\n  <RuName>{$EBAY['RuName']}</RuName>\n", 'SessionID');

    $query['ruparams'] = http_build_query($params);

    $url = $EBAY['signinUrl'] . http_build_query($query);
    return $this->redirect($url);
  }

  public function actionCallback(){
    $ebay=\Yii::$app->getModule('ebay');;
    global $EBAY;
    $EBAY=$ebay->config;

    while (isset($_GET['ebaytkn']))
    {
      if (strlen($_GET['ebaytkn']))
      {
        $token = $_GET['ebaytkn'];
      }

      else
      {
        if (isset($_GET['SessionID'])){
          $body = "\n  <SessionID>{$_GET['SessionID']}</SessionID>\n";
        }else if (isset($_GET['SecretID'])){
          if (!isset($_GET['username'])){
            NotFoundHttpException('Cannot retrieve token without username!');
            break;
          }
          $body =
            "\n  <SecretID>{$_GET['SecretID']}</SecretID>"
            . "\n  <RequesterCredentials><Username>{$_GET['username']}</Username></RequesterCredentials>\n";
        }else{
          break;
        }

        $eBayUser=$_GET['username'];
        $token=$ebay->TradeAPI('FetchToken', $body, 'eBayAuthToken');

        $import=ImportParcelAccount::find()->where(['type'=>1,'name'=>$eBayUser])->one();
        if($import){
          Yii::$app
            ->getSession()
            ->setFlash(
              'error',
              'This binding already exists in the system.'
            );
        }else{
          $import=new ImportParcelAccount();
          $import->type=1;
          $import->name=$eBayUser;
          $import->token=$token;
          $import->last_update=time() - 60 * 60 * 24*(int)$_GET['days'];
          $import->save();

          Yii::$app
            ->getSession()
            ->setFlash(
              'info',
              'Account successfully linked.'
            );
        }

        if((int)$_GET['order_id']>0){
          return $this->redirect('/ebay/get-order/'.$_GET['order_id']);
        }else{
          return $this->redirect('/importAccount');
        }
      }
      break;
    }

    throw new NotFoundHttpException('Page not found');
  }

  public function actionTest($id)
  {
    $ebay_token = ImportParcelAccount::find()->where([
      'type' => 1,
      'client_id' => Yii::$app->user->identity->id,
      'id' => $id
    ])->one();

    if (!$ebay_token) {
      return "Error access.";
    }

    $model = \Yii::$app->getModule('ebay');;
    global $EBAY;

    $rers = $model->getOrders($ebay_token->token, time() - 5 * 30 * 24 * 60 * 60);

    if (!$rers) {
      return "eBay error access";
    }

    $upd_parcel_count = 0;
    $new_parcel_count = 0;

    //if there are error nodes
    if ($rers['errors']->length > 0) {
      return "eBay error access";
    } else { //If there are no errors, continue
      if ($rers['entries'] == 0) {
        return "Not found orders on eBay";

      }
      $orders = $rers['response']->OrderArray->Order;
      ddd($orders);
    }
  }


  public function actionTrackUpdate($id){
    $ebay_token=ImportParcelAccount::find()->where([
      'type'=>1,
      'client_id'=>Yii::$app->user->identity->id,
      'id'=>$id
    ])->one();

    if(!$ebay_token){
      return "Error access.";
    }

    $model = \Yii::$app->getModule('ebay');;
    global $EBAY;

    $rers=$model->getOrders($ebay_token->token,time()-5*30*24*60*60);

    if(!$rers){
      return "eBay error access";
    }

    $upd_parcel_count=0;
    $new_parcel_count=0;

    //if there are error nodes
    if ($rers['errors']->length > 0) {
      return "eBay error access";
    } else { //If there are no errors, continue
      if ($rers['entries'] == 0) {
        return "Not found orders on eBay";

      }
      $orders = $rers['response']->OrderArray->Order;
      if ($orders == null) {
        return "Not found orders on eBay";
      }

      foreach ($orders as $order_) {
        $w=[
          'import_code'=>(string)$order_->OrderID,
          'import_id'=>$ebay_token->id,
          'user_id'=>Yii::$app->user->identity->id
        ];
        $pac=OrderElement::find()->where($w)->one();

        if($pac) {
          $transactions = $order_->TransactionArray;
          if (
            $transactions &&
            $transactions->Transaction &&
            $transactions->Transaction->ShippingDetails &&
            $transactions->Transaction->ShippingDetails->ShipmentTrackingDetails &&
            $transactions->Transaction->ShippingDetails->ShipmentTrackingDetails->ShipmentTrackingNumber
          ) {
            $track_number = (String)$transactions->Transaction->ShippingDetails->ShipmentTrackingDetails->ShipmentTrackingNumber;

            if ($pac->track_number != $track_number) {
              $pac->track_number = $track_number;
              $pac->track_number_type = 0;
              if($pac->save()){
                $upd_parcel_count++;
              }
            }
          }
        }else{
          $new_parcel_count++;
        }
      }
      $out='';
      if($upd_parcel_count){
        $out.="Parcels update : ".$upd_parcel_count;
      }
      if($new_parcel_count){
        $out.="Parcels not import : ".$new_parcel_count;
      }
      if($upd_parcel_count || $new_parcel_count) {
        return $out;
      }else{
        return 'Not found new data on eBay';
      }
    }


    return $id;
  }

}

