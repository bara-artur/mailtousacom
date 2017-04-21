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
use app\modules\tariff\models\TariffsSearch;
use app\modules\tariff\models\Tariffs;

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

      $user = User::find()->where(['id' => Yii::$app->user->id])->one();
      $user_btn='';
      if(Yii::$app->user->can('rbac')){
        $user_btn.='{rbac}';
      }
      $user_btn.='{update}{delete}{billing}{tariff}';
      return $this->render('index', [
        'searchModel' => $searchModel,
        'dataProvider' => $dataProvider,
        'user_btn'=>$user_btn,
      ]);
    }

    public function actionFindUser(){
      $request = Yii::$app->request;

      if ($request->isAjax) {
        Yii::$app->response->format = Response::FORMAT_JSON;
        if ($request->isGet) {
          if (isset($_GET['term'])) {
            $tmp = $_GET['term'];

            //фомируем список
            $listdata = User::find()
              ->orWhere(['like', 'email', $tmp])
              ->orWhere(['like', 'first_name', $tmp])
              ->orWhere(['like', 'last_name', $tmp])
              ->orWhere(['like', 'phone', $tmp])
              ->select(['id,concat(first_name, \' \',last_name,\', \',phone,\', \',email) as value', "concat(first_name, ' ',last_name,', ',phone,', ',email) as label"])
              ->asArray()
              ->all();

            return $listdata;
          } else {
            return $this->redirect(['/']);
          }
        } else {
          return $this->redirect(['/']);
        }
      }
      return $this->redirect(['/']);
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
            'title' => "Create user " . $model->getFullName(),
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
              Do you want edit Billing address?
              ';

          return [
            //'forceReload'=>'#crud-datatable-pjax',
            'title'=> "User saved",
            'content'=>$content,
            'footer'=>
              Html::button('Close',['class'=>'btn btn-default pull-left reload_on_click','data-dismiss'=>"modal"]).
              Html::a('Billing address', ['billing?id='.$model->id], [
                'title' => 'Create billing address',
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
    $request = Yii::$app->request;
    if($request->isGet) {
      $user_id = $request->get('id');
    }else{
      if($_POST['Address']['user_id']) {
        $user_id = $_POST['Address']['user_id'];
      }else{
        $user_id = $request->post('id');
      }
    }

  //  $order = Order::find()->where(['user_id'=>$user_id])->one();
  //  $update_button =0;
  //  if ($order) $update_button = 1;

    $model = Address::find()->where(['user_id' => $user_id])->one();
    $update_button = 1;
    if(!$model){
      $model= new Address();
      $update_button = 0;
    }

    if($request->isAjax) {
      Yii::$app->response->format = Response::FORMAT_JSON;
      if ($request->isGet) {
        $model->user_id=$user_id;
        return [
          'title' => (($update_button==0)?('Create billing address'):('Update billing address')) ,
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

        $content= $this->renderAjax('bilingSaveOk',[
          'model'=>new User,
          'user_id'=>$model->user_id
        ]);
        return [
          //'forceReload'=>'#crud-datatable-pjax',
          'title'=> "Billing address saved",
          'content'=>$content,
          'footer'=>
            Html::button('Close',['class'=>'btn btn-default pull-left reload_on_click','data-dismiss'=>"modal"]).
            Html::button('<i class="fa fa-magic"></i>Create order', [
              'class' => 'btn btn-success',
              'type' => "submit"
            ])
        ];
      }else{
        $model->user_id=$user_id;
        $user=$model->getUser();
        $user_name='';
        /*if($user && $user->getFullName){
          $user_name=$user->getFullName();
        };*/
        return [
          'title' => "Update user " . $user_name,
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
            'title'=> "Create new user",
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
    if(!Yii::$app->user->can('rbac')){
      throw new NotFoundHttpException('Access is denied.');
    }

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

    public function actionTariff($id){
      if(!Yii::$app->user->can('changeTariff')){
        throw new NotFoundHttpException('Access is denied.');
      }
      $user = User::find()->where(['id' => $id])->one();
      $searchModel = new TariffsSearch();
      $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

      $tariff_array = json_decode($user->tariff,true);
      if(gettype($tariff_array)=="integer"){
        $tariff_type = $tariff_array;
      } else{
        $tariff_type = 'unic';
      }

      $tarifs=Tariffs::find()->asArray()->all();

      $out=array();
      $parcel_count=array();
      $weight=array();
      foreach ($tarifs as $tarif){
        if(!in_array($tarif['parcel_count'],$parcel_count)){
          $parcel_count[]=$tarif['parcel_count'];
          $out[$tarif['parcel_count']]=array();
        }
        if(!in_array($tarif['weight'],$weight)){
          $weight[]=$tarif['weight'];
        }
        $out[$tarif['parcel_count']][$tarif['weight']]=$tarif['price'];
      }
      sort($parcel_count);
      sort($weight);
      $request = Yii::$app->request;
      if ($request->isAjax) {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $modal_param=[
          'title' => 'Tariffs',
          //'forceReload' => "true",
          'content' => $this->renderPartial('tariffDetalization', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'parcel_count'=>$parcel_count,
            'weights'=>$weight,
            'tarifs'=>$out,
            'tariff_array' => $tariff_array,
            'tariff_type' => $tariff_type,
          ]),
          'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
            Html::button('Save',['class'=>'btn btn-primary ','type'=>"submit"])
        ];

        if ($request->isPost) {
          foreach ($parcel_count as $cnt){
            if ($_POST['tariff_radio']!='unic') {
              $user->tariff = $_POST['tariff_radio'];
              $user->save();
            }
          }
          if ($_POST['tariff_radio']=='unic') {
            $error = 0;
            $arr = [];

            foreach ($weight as $w){
              if (isset($_POST['unic'.$w])&&($_POST['unic'.$w]!='')) {
                $arr[$w] = $_POST['unic'.$w];
            }else{
               $error = 1;
              }
            }
            if ($error == 0) {
              $user->tariff = json_encode($arr);
              $user->save();
            }
          }
          return [
            'title' => 'Tariffs',
            'content' => 'Complete',
            'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"])
          ];
        }
        return $modal_param;
      } else {
        return $this->render('tariffDetalization', [
     //     'model' => $model,
       //   'formModel' => $formModel,
        ]);
      }
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
