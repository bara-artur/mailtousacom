<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\modules\orderElement\models\OrderElement;
use app\modules\orderElement\models\OrderElementSearch;
use app\modules\user\models\User;
use app\modules\orderElement\models\ElementFilterForm;
use app\modules\user\models\ShowParcelTableForm;


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
        if (!Yii::$app->user->identity->isManager()) {
          $haveOneAddress = Address::find()->where('user_id = :id', [':id' => Yii::$app->user->identity->id])->one();
          if (!$haveOneAddress) {
            return $this->redirect(['/address/create-order-billing', 'first_address' => '1']);
          }
        }
      }

      $query['OrderElementSearch'] = Yii::$app->request->queryParams;
      $time_to['created_at_to'] = null;
      $time_to['transport_date_to'] = null;
      // Загружаем фильтр из формы
      $filterForm = new ElementFilterForm();
      if(Yii::$app->request->post()) {
        $filterForm = new ElementFilterForm();
        $filterForm->load(Yii::$app->request->post());
        $query['OrderElementSearch'] = $filterForm->toArray();
        $time_to = ['created_at_to' => $filterForm->created_at_to];
        $time_to += ['transport_date_to' => $filterForm->transport_data_to];
      }
     // var_dump($query);
      Yii::$app->params['showAdminPanel'] = 0;
      $user = User::find()->where(['id' => Yii::$app->user->id])->one();
      if (($user!=null)&&($user->isManager())) Yii::$app->params['showAdminPanel'] = 1;

      //$query = Yii::$app->request->queryParams;
      if (Yii::$app->params['showAdminPanel']==0) {
        if (array_key_exists('OrderElementSearch', $query)) $query['OrderElementSearch'] += ['user_id' => Yii::$app->user->id];
        else $query['OrderElementSearch'] = ['user_id' => Yii::$app->user->id];
      }
      $searchModel = new OrderElementSearch();
      $dataProvider = $searchModel->search($query,$time_to);

      $showTable = new ShowParcelTableForm();
$showTable->showSerial =1;
$showTable->showID =1;
      return $this->render('index', [
        'searchModel' => $searchModel,
        'orderElements' => $dataProvider,
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
