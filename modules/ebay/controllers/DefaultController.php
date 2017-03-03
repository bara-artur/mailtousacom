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
    //by default retreive orders in last 30 minutes
    $CreateTimeFrom = gmdate("Y-m-d\TH:i:s",((time()-18000000)||(\Yii::$app->user->identity->ebay_last_update))); //current time minus 30 minutes
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
      throw new NotFoundHttpException("eBay not found!");
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

      //Display code and shortmessage
      throw new NotFoundHttpException("eBay returned error(s).\n");
    }else { //If there are no errors, continue
      if ($entries == 0) {
        throw new NotFoundHttpException("Sorry No entries found in the Time period requested. Change CreateTimeFrom/CreateTimeTo and Try again");
      }
      $orders = $response->OrderArray->Order;
      if ($orders == null){
        throw new NotFoundHttpException("No Order Found.");
      }

      echo '<pre>';
      foreach ($orders as $order) {
        // get the buyer's shipping address
        $shippingAddress = $order->ShippingAddress;
        $address = $shippingAddress->Name . ",\n";
        if ($shippingAddress->Street1 != null) {
          $address .=  $shippingAddress->Street1 . ",";
        }
        if ($shippingAddress->Street2 != null) {
          $address .=  $shippingAddress->Street2 . "\n";
        }
        if ($shippingAddress->CityName != null) {
          $address .=
            $shippingAddress->CityName . ",\n";
        }
        if ($shippingAddress->StateOrProvince != null) {
          $address .=
            $shippingAddress->StateOrProvince . "-";
        }
        if ($shippingAddress->PostalCode != null) {
          $address .=
            $shippingAddress->PostalCode . ",\n";
        }
        if ($shippingAddress->CountryName != null) {
          $address .=
            $shippingAddress->CountryName . ".\n";
        }
        if ($shippingAddress->Phone != null) {
          $address .=  $shippingAddress->Phone . "\n";
        }
        if($address){
          echo "Shipping Address : " . $address;
        }else echo "Shipping Address: Null" . "\n";

        $transactions = $order->TransactionArray;
        if ($transactions) {
          echo "\nTransaction Array \n";
          // iterate through each transaction for the order
          foreach ($transactions->Transaction as $transaction) {
            // get the OrderLineItemID, Quantity, buyer's email and SKU

            echo "OrderLineItemID : " . $transaction->OrderLineItemID . "\n";
            echo "QuantityPurchased  : " . $transaction->QuantityPurchased . "\n";
            echo "Buyer Email : " . $transaction->Buyer->Email . "\n";
            $SKU = $transaction->Item->SKU;
            if ($SKU) {
              echo "Transaction -> SKU  :" . $SKU ."\n";
            }

            // if the item is listed with variations, get the variation SKU
            $VariationSKU = $transaction->Variation->SKU;
            if ($VariationSKU != null) {
              echo "Variation SKU  : " . $VariationSKU. "\n";
            }
            echo "TransactionID: " . $transaction->TransactionID . "\n";
            $transactionPriceAttr = $transaction->TransactionPrice->attributes();
            echo "TransactionPrice : " . $transaction->TransactionPrice . " " . $transactionPriceAttr["currencyID"] . "\n";
            echo "Platform : " . $transaction->Platform . "\n";
          }
        }
      }
      echo '</pre>';
      ddd($response);
    }
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