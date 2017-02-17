<?php

namespace app\modules\payment\models;

use yii\base\Model;
use Yii;
use yii\helpers\ArrayHelper;

use PayPal\Api\Address;
use PayPal\Api\CreditCard;
use PayPal\Api\Amount;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\Transaction;
use PayPal\Api\FundingInstrument;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Api\Details;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\RedirectUrls;
use PayPal\Rest\ApiContext;
use PayPal\Api\ExecutePayment;
use PayPal\Api\PaymentExecution;

/**
 * This is the model class for table "payments_list".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $order_id
 * @property integer $status
 */
class DoPayment extends Model
{
  public $Module;
  public $apiContext;
  public $payer;
  public $paymentType;
  public $itemsList=array();
  public $shipping =0;
  public $paymentDescription="Payment without description";

  function __construct($paymentType = 'paypal')
  {
    $this->paymentType=$paymentType;
    $this->init();
  }

  /**
   *
   */
  public function init()
  {
    $this->Module=\Yii::$app->getModule('payment');

    $this->apiContext = new \PayPal\Rest\ApiContext(
      new \PayPal\Auth\OAuthTokenCredential(
        $this->Module->clientId,     // ClientID
        $this->Module->clientSecret      // ClientSecret
      )
    );

    $this->apiContext->setConfig($this->Module->config);

    $this->payer = new Payer();
    $this->payer->setPaymentMethod($this->paymentType);
  }

  public function addItem($item){
    $item=ArrayHelper::merge(
      [
        'name'              => 'NO NAME',
        'currency'          => $this->Module->config['currency'],
        'quantity'          => 1,
        'vat'               => 0
      ],$item);

    $this->itemsList[]=$item;
  }

  public function setShipping($val){
    $this->shipping=$val;
  }

  public function setPaymentDescription($val){
    $this->paymentDescription=$val;
  }

  public function make_payment(){
    $itemList = new ItemList();
    $subTotal = 0;
    $vat = 0;

    foreach ($this->itemsList as $item){
      $item1 = new Item();
      $item1->setName($item['name'])
        ->setCurrency($item['currency'])
        ->setQuantity($item['quantity'])
        ->setPrice($item['price']);

      $subTotal+=$item['quantity']*$item['price'];
      $vat+=$item['vat']*$item['quantity'];

      $itemList->addItem($item1);
    }

    $details = new Details();
    $details->setShipping($this->shipping)
      ->setTax($vat)
      ->setSubtotal($subTotal);

    $amount = new Amount();
    $amount->setCurrency($this->Module->config['currency'])
      ->setTotal($subTotal+$vat+$this->shipping)
      ->setDetails($details);

    $transaction = new Transaction();
    $transaction->setAmount($amount)
      ->setItemList($itemList)
      ->setDescription($this->paymentDescription)
      ->setInvoiceNumber(uniqid());

    $redirectUrls = new RedirectUrls();
    $redirectUrls->setReturnUrl($this->Module->baseUrl."?success=true")
      ->setCancelUrl($this->Module->baseUrl."?success=false");

    $payment = new Payment();
    $payment->setIntent('order')
      ->setPayer($this->payer)
      ->setRedirectUrls($redirectUrls)
      ->setTransactions(array($transaction));

    $request = clone $payment;

    try {
      $payment->create($this->apiContext);
    } catch (Exception $ex) {
      echo "Created Payment Order Using PayPal. Please visit the URL to Approve.";
      exit(1);
    }
    //$approvalUrl = $payment->getApprovalLink();

    return $payment;
  }

  public function finishPayment(){
    if (isset($_GET['success']) && $_GET['success'] == 'true') {
      $paymentId = $_GET['paymentId'];
      $payment = Payment::get($paymentId, $this->apiContext);

      $execution = new PaymentExecution();
      $execution->setPayerId($_GET['PayerID']);

      /*echo $payment->getId().'<br>';
      d($payment->getCart());
      d($payment->getState());
      d($payment->getTransactions());*/

      try {
        // Execute the payment
        // (See bootstrap.php for more on `ApiContext`)
        $result = $payment->execute($execution, $this->apiContext);
        // NOTE: PLEASE DO NOT USE RESULTPRINTER CLASS IN YOUR ORIGINAL CODE. FOR SAMPLE ONLY
        //echo "Executed Payment " . $payment->getId();
        /*d($execution);
        d($result)*/
        try {
          $payment = Payment::get($paymentId, $this->apiContext);
        } catch (Exception $ex) {
          // NOTE: PLEASE DO NOT USE RESULTPRINTER CLASS IN YOUR ORIGINAL CODE. FOR SAMPLE ONLY
          /*echo "Get Payment";
          d($ex);
          exit(1);*/
          return false;
        }
      } catch (Exception $ex) {
        // NOTE: PLEASE DO NOT USE RESULTPRINTER CLASS IN YOUR ORIGINAL CODE. FOR SAMPLE ONLY
        /*echo "Executed Payment";
        d($ex);
        exit(1);*/
        return false;
      }

      // NOTE: PLEASE DO NOT USE RESULTPRINTER CLASS IN YOUR ORIGINAL CODE. FOR SAMPLE ONLY
      //echo "Get Payment ".$payment->getId();
      //d($payment);
      return $payment;

    }else {
      // NOTE: PLEASE DO NOT USE RESULTPRINTER CLASS IN YOUR ORIGINAL CODE. FOR SAMPLE ONLY
      return false;
    }

  }
}