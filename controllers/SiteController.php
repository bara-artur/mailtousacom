<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\modules\order\models\OrderSearch;
use app\modules\order\models\Order;
use app\modules\payment\models\PaymentSearch;
use app\modules\order\models\OrderFilterForm;
use app\modules\user\models\User;

class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public $show = 0;
    public function actionIndex()
    {
      if (!Yii::$app->user->isGuest) {
        $user = User::find()->where(['id' => Yii::$app->user->id])->one();
        if (!$user->isManager()) {
          $haveOneAddress = Address::find()->where('user_id = :id', [':id' => Yii::$app->user->identity->id])->one();
          if (!$haveOneAddress) {
            return $this->redirect(['/address/create', 'first_address' => '1']);
          }
        }
      }
/*        $orderTable = Order::find()->where(['user_id'=>Yii::$app->user->id])->with(['orderElement'])->all();
      $emptyOrder = null;
      foreach ($orderTable as $i=>$order){
          if ($emptyOrder==null){
              if (count($order->orderElement)==0) $emptyOrder =$order->id;
          }
      }*/

      $query['OrderSearch'] = Yii::$app->request->queryParams;
      $time_to['created_at_to'] = null;
      $time_to['transport_date_to'] = null;
      // Загружаем фильтр из формы
      $filterForm = new OrderFilterForm();
      if(Yii::$app->request->post()) {
          $filterForm = new OrderFilterForm();
          $filterForm->load(Yii::$app->request->post());
          $query['OrderSearch'] = $filterForm->toArray();
          $time_to = ['created_at_to' => $filterForm->created_at_to];
          $time_to += ['transport_date_to' => $filterForm->transport_data_to];
      }

      Yii::$app->params['showAdminPanel'] = 0;
      if (($user!=null)&&($user->isManager())) Yii::$app->params['showAdminPanel'] = 1;

      $orderSearchModel = new OrderSearch();
      //$query = Yii::$app->request->queryParams;
      if (Yii::$app->params['showAdminPanel']==0) {
        if (array_key_exists('OrderSearch', $query)) $query['OrderSearch'] += ['user_id' => Yii::$app->user->id];
        else $query['OrderSearch'] = ['user_id' => Yii::$app->user->id];
      }
      $searchModel = new OrderSearch();
      $orders = $searchModel->search($query,$time_to);
      //$orders = $orderSearchModel->search(null,null);

      return $this->render('index',[
          'orders' => $orders,
          'searchModel' => $orderSearchModel,
          'filterForm' => $filterForm,
          //'emptyOrder' => $emptyOrder
      ]);
    }

    /**
     * Login action.
     *
     * @return string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return string
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }
}
