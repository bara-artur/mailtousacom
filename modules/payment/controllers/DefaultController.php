<?php

//http://paypal.github.io/PayPal-PHP-SDK/sample/
//https://github.com/paypal/PayPal-PHP-SDK/blob/master/sample/payments/ExecutePayment.php
//http://paypal.github.io/PayPal-PHP-SDK/sample/doc/payments/OrderGet.html
//http://paypal.github.io/PayPal-PHP-SDK/sample/doc/payments/OrderCreateForVoid.html

namespace app\modules\payment\controllers;

use Yii;
use app\modules\payment\models\PaymentsList;
use app\modules\payment\models\PaymentSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;


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
 * DefaultController implements the CRUD actions for PaymentsList model.
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
     * Lists all PaymentsList models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PaymentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionFinish()
    {
      $apiContext = new \PayPal\Rest\ApiContext(
        new \PayPal\Auth\OAuthTokenCredential(
          'AYSq3RDGsmBLJE-otTkBtM-jBRd1TCQwFf9RGfwddNXWz0uFU9ztymylOhRS',     // ClientID
          'EGnHDxD_qRPdaLdZz8iCr8N7_MzF-YHPTkjs6NKYQvQSBngp4PTTVWkPZRbL'      // ClientSecret
        )
      );
      $apiContext->setConfig(
        [
          'mode' => 'sandbox', // development (sandbox) or production (live) mode
          'http.ConnectionTimeOut' => 30,
          'http.Retry' => 1,
          'log.LogEnabled' => YII_DEBUG ? 1 : 0,
          'log.FileName' => Yii::getAlias('@runtime/logs/paypal.log'),
          'log.LogLevel' => 'FINE',
          'validation.level' => 'log',
          'cache.enabled' => 'true'
        ]);


      if (isset($_GET['success']) && $_GET['success'] == 'true') {
        $paymentId = $_GET['paymentId'];
        $payment = Payment::get($paymentId, $apiContext);

        $execution = new PaymentExecution();
        $execution->setPayerId($_GET['PayerID']);

        // ### Optional Changes to Amount
        // If you wish to update the amount that you wish to charge the customer,
        // based on the shipping address or any other reason, you could
        // do that by passing the transaction object with just `amount` field in it.
        // Here is the example on how we changed the shipping to $1 more than before.
        $transaction = new Transaction();
        $amount = new Amount();
        $details = new Details();
        $details->setShipping(2.2)
          ->setTax(1.3)
          ->setSubtotal(17.50);
        $amount->setCurrency('USD');
        $amount->setTotal(21);
        $amount->setDetails($details);
        $transaction->setAmount($amount);
        // Add the above transaction object inside our Execution object.
        $execution->addTransaction($transaction);
        try {
          // Execute the payment
          // (See bootstrap.php for more on `ApiContext`)
          $result = $payment->execute($execution, $apiContext);
          // NOTE: PLEASE DO NOT USE RESULTPRINTER CLASS IN YOUR ORIGINAL CODE. FOR SAMPLE ONLY
          echo "Executed Payment " . $payment->getId();
          d($execution);
          d($result);
          try {
            $payment = Payment::get($paymentId, $apiContext);
          } catch (Exception $ex) {
            // NOTE: PLEASE DO NOT USE RESULTPRINTER CLASS IN YOUR ORIGINAL CODE. FOR SAMPLE ONLY
            echo "Get Payment";
            d($ex);
            exit(1);
          }
        } catch (Exception $ex) {
          // NOTE: PLEASE DO NOT USE RESULTPRINTER CLASS IN YOUR ORIGINAL CODE. FOR SAMPLE ONLY
          echo "Executed Payment";
          d($ex);
          exit(1);
        }
        // NOTE: PLEASE DO NOT USE RESULTPRINTER CLASS IN YOUR ORIGINAL CODE. FOR SAMPLE ONLY
        echo "Get Payment ".$payment->getId();
        d($payment);
        return $payment;
      } else {
        // NOTE: PLEASE DO NOT USE RESULTPRINTER CLASS IN YOUR ORIGINAL CODE. FOR SAMPLE ONLY
        echo "User Cancelled the Approval";
        exit;
      }
    }


    /**
     * Lists all PaymentsList models.
     * @return mixed
     */
    public function actionTest()
    {
      $payer = new Payer();
      $payer->setPaymentMethod("paypal");

      $item1 = new Item();
      $item1->setName('Ground Coffee 40 oz')
        ->setCurrency('USD')
        ->setQuantity(1)
        ->setPrice(7.5);
      $item2 = new Item();
      $item2->setName('Granola bars')
        ->setCurrency('USD')
        ->setQuantity(5)
        ->setPrice(2);

      $itemList = new ItemList();
      $itemList->setItems(array($item1, $item2));

      $details = new Details();
      $details->setShipping(1.2)
        ->setTax(1.3)
        ->setSubtotal(17.50);

      $amount = new Amount();
      $amount->setCurrency("USD")
        ->setTotal(20)
        ->setDetails($details);

      $transaction = new Transaction();
      $transaction->setAmount($amount)
        ->setItemList($itemList)
        ->setDescription("Payment description")
        ->setInvoiceNumber(uniqid());

      $baseUrl = 'http://127.0.0.1:8080';
      $redirectUrls = new RedirectUrls();
      $redirectUrls->setReturnUrl("$baseUrl/payment/default/finish?success=true")
        ->setCancelUrl("$baseUrl/payment/default/finish?success=false");

      $payment = new Payment();
      $payment->setIntent("order")
        ->setPayer($payer)
        ->setRedirectUrls($redirectUrls)
        ->setTransactions(array($transaction));

      $request = clone $payment;


      $apiContext = new \PayPal\Rest\ApiContext(
        new \PayPal\Auth\OAuthTokenCredential(
          'AYSq3RDGsmBLJE-otTkBtM-jBRd1TCQwFf9RGfwddNXWz0uFU9ztymylOhRS',     // ClientID
          'EGnHDxD_qRPdaLdZz8iCr8N7_MzF-YHPTkjs6NKYQvQSBngp4PTTVWkPZRbL'      // ClientSecret
        )
      );
      $apiContext->setConfig(
      [
        'mode'                      => 'sandbox', // development (sandbox) or production (live) mode
        'http.ConnectionTimeOut'    => 30,
        'http.Retry'                => 1,
        'log.LogEnabled'            => YII_DEBUG ? 1 : 0,
        'log.FileName'              => Yii::getAlias('@runtime/logs/paypal.log'),
        'log.LogLevel'              => 'FINE',
        'validation.level'          => 'log',
        'cache.enabled'             => 'true'
      ]);

      $payment = new Payment();
      $payment->setIntent("order")
        ->setPayer($payer)
        ->setRedirectUrls($redirectUrls)
        ->setTransactions(array($transaction));

      $request = clone $payment;

      try {
        $payment->create($apiContext);
      } catch (Exception $ex) {
        echo "Created Payment Order Using PayPal. Please visit the URL to Approve.";
        exit(1);
      }
      $approvalUrl = $payment->getApprovalLink();

      echo $approvalUrl;
      ddd($payment);

      return $payment;

/*      $addr = new Address();
      $addr->setLine1('52 N Main ST');
      $addr->setCity('Johnstown');
      $addr->setCountryCode('US');
      $addr->setPostalCode('43210');
      $addr->setState('OH');

      $card = new CreditCard();
      $card->setNumber('4417119669820331');
      $card->setType('visa');
      $card->setExpireMonth('11');
      $card->setExpireYear('2018');
      $card->setCvv2('874');
      $card->setFirstName('Joe');
      $card->setLastName('Shopper');
      $card->setBillingAddress($addr);
      $fi = new FundingInstrument();
      $fi->setCreditCard($card);
      $payer = new Payer();
      $payer->setPaymentMethod('credit_card');
      $payer->setFundingInstruments(array($fi));
      $amountDetails = new Details();
      $amountDetails->setSubtotal('15.99');
      $amountDetails->setTax('0.03');
      $amountDetails->setShipping('0.03');
      $amount = new Amount();
      $amount->setCurrency('USD');
      $amount->setTotal('7.47');
      $amount->setDetails($amountDetails);
      $transaction = new Transaction();
      $transaction->setAmount($amount);
      $transaction->setDescription('This is the payment transaction description.');
      $payment = new Payment();
      $payment->setIntent('sale');
      $payment->setPayer($payer);
      $payment->setTransactions(array($transaction));

      return $payment->create($apiContext);*/
    }

    /**
     * Displays a single PaymentsList model.
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
     * Creates a new PaymentsList model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new PaymentsList();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing PaymentsList model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing PaymentsList model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the PaymentsList model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return PaymentsList the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = PaymentsList::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
