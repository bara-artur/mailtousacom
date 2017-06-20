<?php

namespace app\modules\ebay;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * payment module definition class
 */
class Module extends \yii\base\Module
{

  public $config;
  public $mode;

  public $controllerNamespace = 'app\modules\ebay\controllers';

  /**
   * @setConfig
   * _apiContext in init() method
   */
  public function init()
  {
    if($this->mode=='sandbox') {
      $config = [
        'compatabilityLevel' => 717,
        'tradeUrl'=>'https://api.sandbox.ebay.com/ws/api.dll',
        'signinUrl' => 'https://signin.sandbox.ebay.com/ws/eBayISAPI.dll?SignIn&',
        'orderStatus'=>'All',
      ];
    }else{
      $config = [
        'compatabilityLevel' => 717,
        'tradeUrl'=>'https://api.ebay.com/ws/api.dll',
        'signinUrl' => 'https://signin.ebay.com/ws/eBayISAPI.dll?SignIn&',
        'orderStatus'=>'All',
      ];
    }
    $this->config=ArrayHelper::merge($config,$this->config[\Yii::$app->user->identity->ebay_account]);

  }

  public function getOrders($token,$last_update){
    $EBAY = $this->config;

    //SiteID must also be set in the Request's XML
    //SiteID = 0  (US) - UK = 3, Canada = 2, Australia = 15, ....
    //SiteID Indicates the eBay site to associate the call with
    $siteID = 0;
    //the call being made:
    $verb = 'GetOrders';

    //Time with respect to GMT
    //by default retreive orders in last 7 day

    $TimeFrom = $last_update > 100 ? $last_update : time() - 60 * 60 * 24 * 30;

    //$TimeFrom = time() - 60 * 60 * 24 * 300;

    $CreateTimeFrom = gmdate("Y-m-d\TH:i:s", $TimeFrom); //current time minus 30 minutes
    $CreateTimeTo = gmdate("Y-m-d\TH:i:s");

    ///Build the request Xml string
    $requestXmlBody = '<?xml version="1.0" encoding="utf-8" ?>';
    $requestXmlBody .= '<GetOrdersRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
    $requestXmlBody .= '<DetailLevel>ReturnAll</DetailLevel>';
    $requestXmlBody .= "<CreateTimeFrom>$CreateTimeFrom</CreateTimeFrom><CreateTimeTo>$CreateTimeTo</CreateTimeTo>";
    $requestXmlBody .= '<OrderRole>Seller</OrderRole><OrderStatus>' . $EBAY['orderStatus'] . '</OrderStatus>';
    $requestXmlBody .= "<RequesterCredentials><eBayAuthToken>" . $token . "</eBayAuthToken></RequesterCredentials>";
    $requestXmlBody .= '</GetOrdersRequest>';

    //Create a new eBay session with all details pulled in from included keys.php
    $session = new eBaySession(
      $token,
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
      return false;
    }

    //Xml string is parsed and creates a DOM Document object
    $responseDoc = new \DomDocument();
    $responseDoc->loadXML($responseXml);


    //get any error nodes
    $errors = $responseDoc->getElementsByTagName('Errors');
    $response = simplexml_import_dom($responseDoc);
    $entries = $response->PaginationResult->TotalNumberOfEntries;

    return [
      'errors'=>$errors,
      'entries'=>$entries,
      'response'=>$response,
    ];
  }

  public function TradeAPI($call, $body, $field=false)
  {
    global $EBAY;

    if (($response = @file_get_contents($EBAY['tradeUrl'], 'r', stream_context_create(array('http' => array(
        'method' => 'POST',

        'header' =>
          "Content-Type: text/xml; charset=utf-8\r\n"
          . "X-EBAY-API-SITEID: 0\r\n"
          . "X-EBAY-API-COMPATIBILITY-LEVEL: 959\r\n"
          . "X-EBAY-API-CALL-NAME: {$call}\r\n"

          // these headers are only required for GetSessionID and FetchToken
          . "X-EBAY-API-DEV-NAME: {$EBAY['credentials']['devId']}\r\n"
          . "X-EBAY-API-APP-NAME: {$EBAY['credentials']['appId']}\r\n"
          . "X-EBAY-API-CERT-NAME: {$EBAY['credentials']['certId']}\r\n",

        'content' => $request =
          "<?xml version='1.0' encoding='utf-8'?>\n".
          "  <RequesterCredentials>
    <TransactionID>AgAAAA**AQAAAA**aAAAAA**3jkoWQ**nY+sHZ2PrBmdj6wVnY+sEZ2PrA2dj6wFk4GlCZmAoA2dj6x9nY+seQ**ex0EAA**AAMAAA**GGp5yVwOGDfZgFsdrPjpRuP24IXNDkqrJWluDAZjXre5WZTSJUgzCUgmrZGTja78edBv0UMNACiIIK3asEuWpXDjgCdvByOAE3A1w7eFFl8PT4aDCU28OuyuyoEPB9JEAsDlR2IHObTdWXqUsyxSlHi65cE3NJmY9w5ugkzU5dffb5IPdgowMHl9wMEKxCyBEcC1ZOD8x44gKBYqJd/i4YnjBYclYLUxWA98f2zvjR9P2CzGSYGzC58OsuUJlxEBQULPXumUhPsQXoWcwqmD52ay2ma7NTPOWNvHGUMFEtEPNmlZmZHwaJApn2ByB8Hvk21PrbvaA0XttYP5m6J/tEhqUi5EC5JH4aLtoPBrBtMSz2nCaasskjqsWnzSHkReN8kcQfHM7rKjXlmFuB/hOBjhsgM0VyNF/uauZ0k+pKUjSNoPGAMsWZjSIp9I6itH2olyX7tZ7PG+8CvCddWpAiOWTsRp/pGYNJ0oaLQ2Uq2we7J0ORrNt3S+BDf6UQ465RHGAdz27qt+UlUPZDvn8onM2Df+LMkeD4txYyUXVi/TkPjemnT/TLVn37oHl/ZPzRD69+ljU7SO83215awE+Fh94B2g4AectfeNFZEBvInUh61TPTM+PWILpQ4V4Pth3SiEkO4nLgeRfzPGIwWb95O59D5blzCQMK1yaILktzBtfwWR8mtSW16wZcQM3+RqTJyZTbhcYvzyb+uw2wGyiA8gFMA7hF0VBLWW+UdXJyxUlL84nmEshJKagw1A39QI</TransactionID>
  </RequesterCredentials>\n".
          "<{$call} xmlns=\"urn:ebay:apis:eBLBaseComponents\">{$body}</{$call}>"
      ))))) === FALSE)
    {
      throw new NotFoundHttpException('No response from eBay server!');
    }

    if($field) {
      // found open tag?
      if (($begin = strpos($response, "<{$field}>")) !== FALSE) {
        // skip open tag
        $begin += strlen($field) + 2;

        // found close tag?
        if (($end = strpos($response, "</{$field}>", $begin)) !== FALSE) {
          return substr($response, $begin, $end - $begin);
        }
      }
      throw new NotFoundHttpException("Field {$field} not found in eBay response!");
    }else{
      return $response;
    }
  }

  public function setTracNumber($orderId,$token,$ShippingCarrier,$trackNumber){

    $EBAY = $this->config;

    //SiteID must also be set in the Request's XML
    //SiteID = 0  (US) - UK = 3, Canada = 2, Australia = 15, ....
    //SiteID Indicates the eBay site to associate the call with
    $siteID = 0;
    //the call being made:
    $verb = 'CompleteSale';

    ///Build the request Xml string
    $requestXmlBody =   '<?xml version="1.0" encoding="utf-8" ?>';
    $requestXmlBody .=  '<CompleteSaleRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
    $requestXmlBody .=      '<RequesterCredentials>';
    $requestXmlBody .=          '<eBayAuthToken>' . $token . '</eBayAuthToken>';
    $requestXmlBody .=      '</RequesterCredentials>';

    $requestXmlBody .=      '<ItemID>' . $orderId . '</ItemID>';
    $requestXmlBody .=      '<OrderID>' . $orderId . '</OrderID>';

    $requestXmlBody .=      '<Shipment>';
    $requestXmlBody .=          '<ShipmentTrackingDetails>';
    $requestXmlBody .=              '<ShipmentTrackingNumber>' . $trackNumber . '</ShipmentTrackingNumber>';
    $requestXmlBody .=              '<ShippingCarrierUsed>' . $ShippingCarrier . '</ShippingCarrierUsed>';
    $requestXmlBody .=          '</ShipmentTrackingDetails>';
    $requestXmlBody .=      '</Shipment>';

    $requestXmlBody .=  '</CompleteSaleRequest>';

    //Create a new eBay session with all details pulled in from included keys.php
    $session = new eBaySession(
      $token,
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
      return false;
    }

    return $responseXml;
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