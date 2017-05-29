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