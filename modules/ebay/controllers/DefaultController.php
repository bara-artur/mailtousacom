<?php

namespace app\modules\ebay\controllers;

use Yii;
use yii\web\Controller;
use app\modules\orderElement\models\OrderElement;
use app\modules\order\models\Order;
use yii\web\NotFoundHttpException;

use app\modules\user\models\User;
/**
 * DefaultController implements the CRUD actions for Order model.
 */
class DefaultController extends Controller
{
  public function actionGetOrder($id){
    $order=Order::findOne($id);

    if($order->user_id!=\Yii::$app->user->identity->id){
      throw new NotFoundHttpException('Order can editing only by creator.');
    }

    if($order->payment_state>0 || $order->order_status>1){
      throw new NotFoundHttpException('You can not edit your order.');
    }

    //Если нет то получаем новый токен
    if (strlen(\Yii::$app->user->identity->ebay_token)<30){
      return $this->getTocken($id);
    };


    d($id);
    ddd($order);
  }

  private function getTocken($order){
    $model=\Yii::$app->getModule('ebay');;
    global $EBAY;
    $EBAY=$model->config;

    // your private parameters
    $params = array('order_id' => $order);

    // eBay's required parameters
    $query = array('RuName' => $EBAY['RuName']);

    $query['SessID'] = $params['SessionID'] =
      $this->TradeAPI('GetSessionID', "\n  <RuName>{$EBAY['RuName']}</RuName>\n", 'SessionID');

    $query['ruparams'] = http_build_query($params);

    $url = $EBAY['signinUrl'] . http_build_query($query);
    return $this->redirect($url);
  }

  private function TradeAPI($call, $body, $field)
  {
    global $EBAY;

    if (($response = @file_get_contents($EBAY['tradeUrl'], 'r', stream_context_create(array('http' => array(
      'method' => 'POST',

      'header' =>
        "Content-Type: text/xml; charset=utf-8\r\n"
      . "X-EBAY-API-SITEID: 0\r\n"
      . "X-EBAY-API-COMPATIBILITY-LEVEL: 689\r\n"
      . "X-EBAY-API-CALL-NAME: {$call}\r\n"

  // these headers are only required for GetSessionID and FetchToken
  . "X-EBAY-API-DEV-NAME: {$EBAY['credentials']['devId']}\r\n"
  . "X-EBAY-API-APP-NAME: {$EBAY['credentials']['appId']}\r\n"
  . "X-EBAY-API-CERT-NAME: {$EBAY['credentials']['certId']}\r\n",

      'content' => $request =
    "<?xml version='1.0' encoding='utf-8'?>\n"
    . "<{$call} xmlns='urn:ebay:apis:eBLBaseComponents'>{$body}</{$call}>"
    ))))) === FALSE)
    {
      throw new NotFoundHttpException('No response from eBay server!');
    }

    // found open tag?
    if (($begin = strpos($response, "<{$field}>")) !== FALSE)
    {
      // skip open tag
      $begin += strlen($field) + 2;

      // found close tag?
      if (($end = strpos($response, "</{$field}>", $begin)) !== FALSE)
      {
        return substr($response, $begin, $end - $begin);
      }
    }
    throw new NotFoundHttpException("Field {$field} not found in eBay response!");
  }

  public function actionCallback(){
    $model=\Yii::$app->getModule('ebay');;
    global $EBAY;
    $EBAY=$model->config;

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
        $user=User::findOne(\Yii::$app->user->identity->id);
        $user->ebay_token=$this->TradeAPI('FetchToken', $body, 'eBayAuthToken');
        if($user->save()){
          return $this->redirect('/ebay/get-order/'.$_GET['order_id']);
        }else{
          NotFoundHttpException('Error saving token');
        }
      }
      break;
    }
    return 123;

    throw new NotFoundHttpException('Page not found');
  }
}
