<?php

namespace app\modules\ebay\controllers;

use Yii;
use yii\web\Controller;
use app\modules\orderElement\models\OrderElement;
use app\modules\orderInclude\models\OrderInclude;
use app\modules\order\models\Order;
use yii\web\NotFoundHttpException;

use app\modules\user\models\User;
/**
 * DefaultController implements the CRUD actions for Order model.
 */
class DefaultController extends Controller
{

  public function beforeAction($action)
  {
    if (Yii::$app->user->isGuest) {
      return $this->redirect(['/']);
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
/*
    if($order->payment_state>0 || $order->order_status>1){
      throw new NotFoundHttpException('You can not edit your order.');
    }*/

    $request = Yii::$app->request;
    if($request->isPost){
      if((int)$request->post('days')>0){
        $user=User::findOne(\Yii::$app->user->identity->id);
        $user->ebay_last_update=time()-(int)$request->post('days')*24*60*60;
        if($user->save()){
          return $this->getTocken($id);
        };
      }
      Yii::$app
        ->getSession()
        ->setFlash(
          'error',
          'The number of days should be a positive number.'
        );
    }

    //Если нет то получаем новый токен
    if (strlen(\Yii::$app->user->identity->ebay_token)<30){
      return $this->render('view', [
        'order_id' => $id
      ]);
    };
    $model=\Yii::$app->getModule('ebay');;
    global $EBAY;
    $EBAY=$model->config;

    //SiteID must also be set in the Request's XML
    //SiteID = 0  (US) - UK = 3, Canada = 2, Australia = 15, ....
    //SiteID Indicates the eBay site to associate the call with
    $siteID = 0;
    //the call being made:
    $verb = 'GetOrders';

    //Time with respect to GMT
    //by default retreive orders in last 7 day
    $TimeFrom=
      \Yii::$app->user->identity->ebay_last_update>100?
        \Yii::$app->user->identity->ebay_last_update:
        time()-60*60*24*7;
    $CreateTimeFrom = gmdate("Y-m-d\TH:i:s",$TimeFrom); //current time minus 30 minutes
    $CreateTimeTo = gmdate("Y-m-d\TH:i:s");

    ///Build the request Xml string
    $requestXmlBody = '<?xml version="1.0" encoding="utf-8" ?>';
    $requestXmlBody .= '<GetOrdersRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
    $requestXmlBody .= '<DetailLevel>ReturnAll</DetailLevel>';
    $requestXmlBody .= "<CreateTimeFrom>$CreateTimeFrom</CreateTimeFrom><CreateTimeTo>$CreateTimeTo</CreateTimeTo>";
    $requestXmlBody .= '<OrderRole>Seller</OrderRole><OrderStatus>'.$EBAY['orderStatus'].'</OrderStatus>';
    $requestXmlBody .= "<RequesterCredentials><eBayAuthToken>".\Yii::$app->user->identity->ebay_token."</eBayAuthToken></RequesterCredentials>";
    $requestXmlBody .= '</GetOrdersRequest>';

    //Create a new eBay session with all details pulled in from included keys.php
    $session = new eBaySession(
      \Yii::$app->user->identity->ebay_token,
      $EBAY['credentials']['devId'],
      $EBAY['credentials']['appId'],
      $EBAY['credentials']['certId'],
      $EBAY['tradeUrl'],
      $EBAY['compatabilityLevel'],
      $siteID,
      $verb
    );

    //send the request and get response
    $responseXml = $session->sendHttpRequest($requestXmlBody);
    if (stristr($responseXml, 'HTTP 404') || $responseXml == '') {
      Yii::$app
        ->getSession()
        ->setFlash(
          'error',
          'eBay not found'
        );
      return $this->redirect('/orderInclude/create-order/'.$id);
    }

    //Xml string is parsed and creates a DOM Document object
    $responseDoc = new \DomDocument();
    $responseDoc->loadXML($responseXml);


    //get any error nodes
    $errors = $responseDoc->getElementsByTagName('Errors');
    $response = simplexml_import_dom($responseDoc);
    $entries = $response->PaginationResult->TotalNumberOfEntries;


    //if there are error nodes
    if ($errors->length > 0) {
      //display each error
      //Get error code, ShortMesaage and LongMessage
      $code = $errors->item(0)->getElementsByTagName('ErrorCode');
      $shortMsg = $errors->item(0)->getElementsByTagName('ShortMessage');
      $longMsg = $errors->item(0)->getElementsByTagName('LongMessage');

      Yii::$app
        ->getSession()
        ->setFlash(
          'error',
          'eBay returned error(s)'
        );
      return $this->redirect('/orderInclude/create-order/'.$id);
    }else { //If there are no errors, continue
      if ($entries == 0) {
        Yii::$app
          ->getSession()
          ->setFlash(
            'info',
            'New orders were found'
          );
        return $this->redirect('/orderInclude/create-order/'.$id);
      }
      $orders = $response->OrderArray->Order;
      if ($orders == null){
        Yii::$app
          ->getSession()
          ->setFlash(
            'info',
            'No Order Found.'
          );
        return $this->redirect('/orderInclude/create-order/'.$id);
      }

      if(strlen($order->el_group)<1){
        $el_group=[];
      }else{
        $el_group=explode(',',$order->el_group);
      };

      foreach ($orders as $order_) {
        $box = new OrderElement();
        $shippingAddress = $order_->ShippingAddress;
        $name=explode(' ',$shippingAddress->Name );
        //$box->order_id=$id;
        $box->first_name=$name[0];
        unset($name[0]);
        $box->last_name=implode(' ',$name);
        $box->company_name='Personal address';
        $box->adress_1=(String)$shippingAddress->Street1;
        $box->adress_2=(String)$shippingAddress->Street2;
        $box->city=(String)$shippingAddress->CityName;
        $box->zip=(String)$shippingAddress->PostalCode;
        $box->phone=(String)$shippingAddress->Phone;
        $box->state=(String)$shippingAddress->StateOrProvince;
        $box->user_id=Yii::$app->user->identity->id;
        $box->created_at=time();
        $box->address_type=0;
        $box->source=1;
        $transactions = $order_->TransactionArray;

        if($box->save() && $transactions){
          foreach ($transactions->Transaction as $transaction) {
            $item = new OrderInclude;
            $item->order_id = $box->id;
            $item->name = (String)$transaction->Item->Title;
            $item->price = (String)$transaction->TransactionPrice;
            $item->quantity = (int)$transaction->QuantityPurchased;
            $item->country = "none";

            /*d($transaction);
            d($item);
            d($transaction->Item);*/
            $item->save();
          }
          $el_group[]=$box->id;
        }
        ddd($box);
      }

      $order->el_group=implode(',',$el_group);
      $order->save();

      \Yii::$app
        ->getSession()
        ->setFlash(
          'success',
          'Check imported from eBay'
        );
      $user=User::findOne(\Yii::$app->user->identity->id);
      $user->ebay_last_update=time();
      $user->save();
    }
    return $this->redirect('/orderInclude/create-order/'.$id);
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

    throw new NotFoundHttpException('Page not found');
  }
}

class eBaySession
{
  private $requestToken;
  private $devID;
  private $appID;
  private $certID;
  private $serverUrl;
  private $compatLevel;
  private $siteID;
  private $verb;

  /**	__construct
  Constructor to make a new instance of eBaySession with the details needed to make a call
  Input:	$userRequestToken - the authentication token fir the user making the call
  $developerID - Developer key obtained when registered at http://developer.ebay.com
  $applicationID - Application key obtained when registered at http://developer.ebay.com
  $certificateID - Certificate key obtained when registered at http://developer.ebay.com
  $useTestServer - Boolean, if true then Sandbox server is used, otherwise production server is used
  $compatabilityLevel - API version this is compatable with
  $siteToUseID - the Id of the eBay site to associate the call iwht (0 = US, 2 = Canada, 3 = UK, ...)
  $callName  - The name of the call being made (e.g. 'GeteBayOfficialTime')
  Output:	Response string returned by the server
   */
  public function __construct($userRequestToken, $developerID, $applicationID, $certificateID, $serverUrl,
                              $compatabilityLevel, $siteToUseID, $callName)
  {
    $this->requestToken = $userRequestToken;
    $this->devID = $developerID;
    $this->appID = $applicationID;
    $this->certID = $certificateID;
    $this->compatLevel = $compatabilityLevel;
    $this->siteID = $siteToUseID;
    $this->verb = $callName;
    $this->serverUrl = $serverUrl;
  }


  /**	sendHttpRequest
  Sends a HTTP request to the server for this session
  Input:	$requestBody
  Output:	The HTTP Response as a String
   */
  public function sendHttpRequest($requestBody)
  {
    //build eBay headers using variables passed via constructor
    $headers = $this->buildEbayHeaders();

    //initialise a CURL session
    $connection = curl_init();
    //set the server we are using (could be Sandbox or Production server)
    curl_setopt($connection, CURLOPT_URL, $this->serverUrl);

    //stop CURL from verifying the peer's certificate
    curl_setopt($connection, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($connection, CURLOPT_SSL_VERIFYHOST, 0);

    //set the headers using the array of headers
    curl_setopt($connection, CURLOPT_HTTPHEADER, $headers);

    //set method as POST
    curl_setopt($connection, CURLOPT_POST, 1);

    //set the XML body of the request
    curl_setopt($connection, CURLOPT_POSTFIELDS, $requestBody);

    //set it to return the transfer as a string from curl_exec
    curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);

    //Send the Request
    $response = curl_exec($connection);

    //close the connection
    curl_close($connection);

    //return the response
    return $response;
  }



  /**	buildEbayHeaders
  Generates an array of string to be used as the headers for the HTTP request to eBay
  Output:	String Array of Headers applicable for this call
   */
  private function buildEbayHeaders()
  {
    $headers = array (
      //Regulates versioning of the XML interface for the API
      'X-EBAY-API-COMPATIBILITY-LEVEL: ' . $this->compatLevel,

      //set the keys
      'X-EBAY-API-DEV-NAME: ' . $this->devID,
      'X-EBAY-API-APP-NAME: ' . $this->appID,
      'X-EBAY-API-CERT-NAME: ' . $this->certID,

      //the name of the call we are requesting
      'X-EBAY-API-CALL-NAME: ' . $this->verb,

      //SiteID must also be set in the Request's XML
      //SiteID = 0  (US) - UK = 3, Canada = 2, Australia = 15, ....
      //SiteID Indicates the eBay site to associate the call with
      'X-EBAY-API-SITEID: ' . $this->siteID,
    );

    return $headers;
  }
}