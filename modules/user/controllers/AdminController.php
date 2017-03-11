<?php

namespace app\modules\user\controllers;

use Yii;
use app\modules\user\models\User;
use app\modules\user\models\UserAdminCreate;
use app\modules\user\models\UserSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use \yii\web\Response;
use yii\helpers\Html;
use johnitvn\rbacplus\models\AssignmentSearch;
use johnitvn\rbacplus\models\AssignmentForm;
use app\modules\address\models\Address;
use app\modules\order\models\Order;

/**
 * AdminController implements the CRUD actions for User model.
 */
class AdminController extends Controller
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

  function beforeAction($action) {
    if (Yii::$app->user->isGuest || !Yii::$app->user->can('userManager')) {
      throw new \yii\web\ForbiddenHttpException('You are not allowed to perform this action.');
      return false;
    }
    //$this->view->registerJsFile('/js/bootstrap.min.js');
    $this->view->registerJsFile('/js/admin.js');
    //$this->view->registerCssFile('/css/bootstrap.min.css');
    $this->view->registerCssFile('/css/admin.css',['depends'=>['app\assets\AppAsset']]);
    return true;
  }
    /**
     * Lists all User models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

      $user_btn='{rbac}{update}{delete}{billing}';
      return $this->render('index', [
        'searchModel' => $searchModel,
        'dataProvider' => $dataProvider,
        'user_btn'=>$user_btn,
      ]);
    }


    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
      $model =new UserAdminCreate;
      $request = Yii::$app->request;
      if($request->isAjax) {
        Yii::$app->response->format = Response::FORMAT_JSON;
        if ($request->isGet) {
          return [
            'title' => "Update user " . $model->getFullName(),
            'content' => $this->renderAjax('update', [
              'model' => $model,
            ]),
            'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
              Html::button('Save', ['class' => 'btn btn-primary', 'type' => "submit"])
          ];
        }else if(
            $model->load($request->post()) &&
            //($model->docs='')&&
            ($model->status=User::STATUS_ACTIVE)&&
            ($model->save())
          ){
          $model->removeEmailConfirmToken();
          $model->removePasswordResetToken();
          $model->save();
          $content='
              <span class="text-success">Update user success</span>
              Do you wont edit Billing address?
              ';

          return [
            //'forceReload'=>'#crud-datatable-pjax',
            'title'=> "Create new OrderInclude",
            'content'=>$content,
            'footer'=>
              Html::button('Close',['class'=>'btn btn-default pull-left reload_on_click','data-dismiss'=>"modal"]).
              Html::a('Billing address', ['Billing'], [
                'title' => '',
                'class'=>'btn btn-success',
                'role'=>'modal-remote',
                'data-pjax'=>0,
              ])
          ];
        }else{
          return [
            'title' => "Update user " . $model->getFullName(),
            'content' => $this->renderAjax('update', [
              'model' => $model,
            ]),
            'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
              Html::button('Save', ['class' => 'btn btn-primary', 'type' => "submit"])
          ];
        }
      }else{
        throw new NotFoundHttpException('The requested page does not exist.');
      }
    }

  public function actionBilling()
  {
    $order = Order::find()->where(['user_id'=>Yii::$app->user->id])->one();
    $update_button =0;
    if ($order) $update_button = 1;

    $model = Address::find()->where('user_id = :id', [':id' => Yii::$app->user->id])->one();
    if(!$model){
      $model= new Address();
    }

    $request = Yii::$app->request;
    if($request->isAjax) {
      Yii::$app->response->format = Response::FORMAT_JSON;
      if ($request->isGet) {
        return [
          'title' => $update_button==0?'Create billing address':'Update billing address' ,
          'content' => $this->renderAjax("@app/modules/address/views/default/createorderbilling.php", [
            'model' => $model,
            'update_button'=>2
          ]),
          'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
            Html::button('Save', ['class' => 'btn btn-primary', 'type' => "submit"])
        ];
      }else if(
        $model->load($request->post()) &&
        ($model->save())
      ){
        $content='
              <span class="text-success">Update billing address successful</span>
              Do you want create order?
              ';

        return [
          //'forceReload'=>'#crud-datatable-pjax',
          'title'=> "Create new OrderInclude",
          'content'=>$content,
          'footer'=>
            Html::button('Close',['class'=>'btn btn-default pull-left reload_on_click','data-dismiss'=>"modal"])
        ];
      }else{
        return [
          'title' => "Update user " . $model->getFullName(),
          'content' => $this->renderAjax('update', [
            'model' => $model,
          ]),
          'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
            Html::button('Save', ['class' => 'btn btn-primary', 'type' => "submit"])
        ];
      }
    }else{
      throw new NotFoundHttpException('The requested page does not exist.');
    }
  }

    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdateStatus()
    {
      $request = Yii::$app->request;
      $user_id=$request->post('user_id');
      $user = User::find()->where(['id' => $user_id])->one();
      if ($user) {
         if (($user_id) && ($request->isAjax)) {
           $user->status=$request->post('status');
           $user->removeEmailConfirmToken();
           $user->removePasswordResetToken();
           $user->save();
           return 1;
         }
      }
      return false;
    }

    public function actionUpdate($id)
    {
      $model = $this->findModel($id);
      $request = Yii::$app->request;
      if($request->isAjax) {
        Yii::$app->response->format = Response::FORMAT_JSON;
        if ($request->isGet) {
          return [
            'title' => "Update user " . $model->getFullName(),
            'content' => $this->renderAjax('update', [
              'model' => $model,
            ]),
            'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
              Html::button('Save', ['class' => 'btn btn-primary', 'type' => "submit"])
          ];
        }else if($model->load($request->post())&&($model->save())){
          return [
            'forceReload'=>'#crud-datatable-pjax',
            'title'=> "Create new OrderInclude",
            'content'=>'<span class="text-success">Update user success</span>',
            'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"])
          ];
        }else{
          return [
            'title' => "Update user " . $model->getFullName(),
            'content' => $this->renderAjax('update', [
              'model' => $model,
            ]),
            'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
              Html::button('Save', ['class' => 'btn btn-primary', 'type' => "submit"])
          ];
        }
      }else{
        throw new NotFoundHttpException('The requested page does not exist.');
      }
    }

  public function actionRbac($id){
    $rbacModule = Yii::$app->getModule('rbac');
    $model = call_user_func($rbacModule->userModelClassName . '::findOne', $id);
    $formModel = new AssignmentForm($id);
    $request = Yii::$app->request;
    if ($request->isAjax) {
      Yii::$app->response->format = Response::FORMAT_JSON;
      $modal_param=[
        'title' => $model->{$rbacModule->userModelLoginField},
        //'forceReload' => "true",
        'content' => $this->renderPartial('assignment', [
          'model' => $model,
          'formModel' => $formModel,
        ]),
        'footer' => Html::button(Yii::t('rbac', 'Close'), ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
          Html::button(Yii::t('rbac', 'Save'), ['class' => 'btn btn-primary', 'type' => "submit"])
      ];

      if ($request->isPost) {
        $formModel->load(Yii::$app->request->post());
        if($formModel->save()){
          $modal_param['forceReload'] = "true";
        };
      }
      return $modal_param;
    } else {
      return $this->render('assignment', [
        'model' => $model,
        'formModel' => $formModel,
      ]);
    }
  }


    /**
     * Deletes an existing User model.
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
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
