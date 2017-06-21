<?php

namespace app\controllers;

use app\modules\invoice\models\Invoice;
use app\modules\receiving_points\models\ReceivingPointsSearch;
use EasyPost\Error;
use Yii;
use yii\base\ErrorException;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\modules\user\models\forms\LoginForm;
use app\models\ContactForm;
use app\modules\orderElement\models\OrderElement;
use app\modules\order\models\Order;
use app\modules\orderElement\models\OrderElementSearch;
use app\modules\user\models\User;
use app\modules\orderElement\models\ElementFilterForm;
use app\modules\user\models\ShowParcelTableForm;
use app\modules\address\models\Address;
use app\modules\receiving_points\models\ReceivingPoints;
use easypost\EasyPost;

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

  public function exception_error_handler() {

    throw new ErrorException('hello');
    return 1;
  }

  public function actionIndex()
    {
      $gritter = Yii::$app->request->cookies['showTheGritter'];
      Yii::$app->response->cookies->remove('showTheGritter');

      if ((Yii::$app->request->cookies['parcelCheckedId'])&&
          (Yii::$app->request->cookies['parcelCheckedUser'])){  // если мы получили куки от order/select/$id
        setcookie('parcelCheckedId',Yii::$app->request->cookies['parcelCheckedId']->value);
        setcookie('parcelCheckedUser',Yii::$app->request->cookies['parcelCheckedUser']->value);
      }
      if (Yii::$app->user->isGuest) {
        return $this->render('index_login');
      }

      $user = User::find()->where(['id' => Yii::$app->user->id])->one();

      if (Yii::$app->user->can("takeParcel")){
        $receiving_point = ReceivingPoints::findOne(['id' => $user->last_receiving_points]);
      }

      if (!Yii::$app->user->identity->isManager()) {
        $haveOneAddress = Address::find()->where('user_id = :id', [':id' => Yii::$app->user->identity->id])->one();
        if (!$haveOneAddress) {
          return $this->redirect(['/address/create-order-billing', 'first_address' => '1']);
        }
      }

      $show_modal_for_point = 0;
      if (Yii::$app->session->getFlash('choose_receiving_point')=='1') {
         Yii::$app->getSession()->setFlash('choose_receiving_point','0');
         $show_modal_for_point =1;
      }

      $query['OrderElementSearch'] = Yii::$app->request->queryParams;
      $time_to['created_at_to'] = null;
      $time_to['transport_date_to'] = null;
      // Загружаем фильтр из формы
      $filterForm = new ElementFilterForm();
      if(Yii::$app->request->post()) {
        //ddd($_POST);
        $filterForm = new ElementFilterForm(); // форма фильтра
        $showTable = new ShowParcelTableForm(-1); // форма настройки столбцов таблицы
        if (isset($_POST['ShowParcelTableForm'])) {
          $showTable->load(Yii::$app->request->post());
          $user->parcelTableOptions = $showTable->getAllFlags();
          if ($user)$user->save();
        }
        $filterForm->load(Yii::$app->request->post());
        $show_filter = isset($_POST['ElementFilterForm']);
        $query['OrderElementSearch'] = $filterForm->toArray();
        $time_to = ['created_at_to' => $filterForm->created_at_to];
        $time_to += ['transport_date_to' => $filterForm->transport_data_to];
        $time_to += ['price_end' => $filterForm->price_end];
      }else {
        $show_filter = false;
      }
      $admin = 0;
      $user = User::find()->where(['id' => Yii::$app->user->id])->one();
      if (($user!=null)&&($user->isManager())) $admin = 1;

      //$query = Yii::$app->request->queryParams;
      if ($admin==0) {
        if (array_key_exists('OrderElementSearch', $query)) $query['OrderElementSearch'] += ['user_id' => Yii::$app->user->id];
        else $query['OrderElementSearch'] = ['user_id' => Yii::$app->user->id];
      }
      $query['OrderElementSearch']['archive'] = 0; //  не выводим архивные посылки на главную

      $searchModel = new OrderElementSearch();
      $dataProvider = $searchModel->search($query,$time_to);

      $showTable = new ShowParcelTableForm($user->parcelTableOptions);
      return $this->render('index', [
        'searchModel' => $searchModel,
        'orderElements' => $dataProvider,
        'showTable' => $showTable,
        'filterForm' => $filterForm,
        'show_modal_for_point' => $show_modal_for_point,
        'receiving_point' => (isset($receiving_point))?($receiving_point->address):(''),
        'show_view_button' => Yii::$app->user->can('orderChangeForAdmin'),
        'show_trackInvoice_button' => Yii::$app->user->can('trackInvoice'),
        'admin' => $admin,
        'gritter' => $gritter,
        'show_filter' => $show_filter,
      ]);
    }

    public function actionArchive()
    {
      if (Yii::$app->user->isGuest) {
        return $this->render('index_login');
      }
      $admin = 0;
      $user = User::find()->where(['id' => Yii::$app->user->id])->one();
      if (($user!=null)&&($user->isManager())) $admin = 1;

      $query['OrderElementSearch'] = Yii::$app->request->queryParams;
      $time_to['created_at_to'] = null;
      $time_to['transport_date_to'] = null;
      // Загружаем фильтр из формы
      $filterForm = new ElementFilterForm();
      if(Yii::$app->request->post()) {
        $filterForm = new ElementFilterForm(); // форма фильтра
        $showTable = new ShowParcelTableForm(-1); // форма настройки столбцов таблицы
        $showTable->load(Yii::$app->request->post());
        if (($showTable->getAllFlags() != $user->parcelTableOptions)) {
          $user->parcelTableOptions = $showTable->getAllFlags();

          if ($user)$user->save();
        }
        $filterForm->load(Yii::$app->request->post());

        $query['OrderElementSearch'] = $filterForm->toArray();
        $time_to = ['created_at_to' => $filterForm->created_at_to];
        $time_to += ['transport_date_to' => $filterForm->transport_data_to];
        $time_to += ['price_end' => $filterForm->price_end];
      }

      //$query = Yii::$app->request->queryParams;
      if ($admin==0) {
        if (array_key_exists('OrderElementSearch', $query)) $query['OrderElementSearch'] += ['user_id' => Yii::$app->user->id];
        else $query['OrderElementSearch'] = ['user_id' => Yii::$app->user->id];
      }
      $query['OrderElementSearch']['archive'] = 1; //  не выводим архивные посылки на главную

      $showTable = new ShowParcelTableForm($user->parcelTableOptions);
      $searchModel = new OrderElementSearch();
      $dataProvider = $searchModel->search($query,$time_to);
      return $this->render('archive',[
        'admin' => $admin,
        'dataProvider' => $dataProvider,
        'showTable' => $showTable,
        'filterForm' => $filterForm,

      ]);
    }

  /**
     * Login action.
     *
     * @return string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {   // если мы уже авторизованы
          return $this->redirect('/parcels');
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && ($model->login())) { // если форма авторизация была отправлена с данными
          return $this->redirect('/parcels');
        }else{
          return $this->render('index_login');   // начало авторизации
        }
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

    public function actionConfidentiality()
    {
      return $this->render('confidentiality');
    }

    public function actionLanding()
    {
      return $this->render('landing');
    }

    public function actionPricing()
    {
      return $this->render('pricing');
    }

}
