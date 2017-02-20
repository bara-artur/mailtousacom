<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\modules\order\models\OrderSearch;
use app\modules\payment\models\PaymentSearch;
use  app\modules\order\models\OrderFilterForm;

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
    public function actionIndex()
    {
        if (Yii::$app->session->hasFlash('toAddressCreate')){
            return $this->redirect(['/address/create', 'first_address'=>'1']);
        }

// Загружаем фильтр из формы
        $filterForm = new OrderFilterForm();
        if(Yii::$app->request->post()) {
            $filterForm = new OrderFilterForm();
            $filterForm->load(Yii::$app->request->post());
            $query['OrderSearch'] = $filterForm->toArray();
            $time_to = ['created_at_to' => $filterForm->created_at_to];
            $time_to += ['transport_date_to' => $filterForm->transport_data_to];
        }

        $orderSearchModel = new OrderSearch();
        //$query = Yii::$app->request->queryParams;
        //if (array_key_exists('OrderSearch', $query)) $query['OrderSearch'] += ['client_id' => Yii::$app->user->id];
        //else $query['OrderSearch'] = ['client_id' => Yii::$app->user->id];

        $orders = $orderSearchModel->search($query,$time_to);

        return $this->render('index',[
            'orders' => $orders,
            'searchModel' => $orderSearchModel,
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
