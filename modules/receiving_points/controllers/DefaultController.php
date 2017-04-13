<?php

namespace app\modules\receiving_points\controllers;

use Yii;
use app\modules\receiving_points\models\ReceivingPoints;
use app\modules\receiving_points\models\ReceivingPointsSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\modules\user\models\User;
use \yii\web\Response;
use yii\helpers\Html;

/**
 * DefaultController implements the CRUD actions for ReceivingPoints model.
 */
class DefaultController extends Controller
{

  function beforeAction($action)
  {
    if (Yii::$app->user->isGuest || !Yii::$app->user->can('admin_reference')) {
      throw new \yii\web\ForbiddenHttpException('You are not allowed to perform this action.');
      return false;
    }
    return true;
  }
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
     * Lists all ReceivingPoints models.
     * @return mixed
     */
    public function actionIndex()
    {
      $searchModel = new ReceivingPointsSearch();
      $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

      return $this->render('index', [
        'searchModel' => $searchModel,
        'dataProvider' => $dataProvider,
      ]);

    }

    /**
     * Creates a new ReceivingPoints model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
      if (Yii::$app->user->can("takeParcel")) {
        $model = new ReceivingPoints();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
          return $this->redirect(['/receiving_points']);
        } else {
          return $this->render('create', [
            'model' => $model,
          ]);
        }
      }else{
        $this->redirect("/");
      }
    }

    /**
     * Updates an existing ReceivingPoints model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
      if (Yii::$app->user->can("takeParcel")) {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
          return $this->redirect(['/receiving_points']);
        } else {
          return $this->render('update', [
            'model' => $model,
          ]);
        }
      }else{
        $this->redirect("/");
      }
    }

    /**
     * Deletes an existing ReceivingPoints model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
      if (Yii::$app->user->can("takeParcel")) {
        $this->findModel($id)->delete();
      }
      return $this->redirect(['index']);
    }

  public function actionChoose() // Выбор receiving Point
  {
    $request = Yii::$app->request;

    if($request->isAjax){
      $user = User::findOne(['id' => Yii::$app->user->id]);
      /*
      *   Process for ajax request
      */
      Yii::$app->response->format = Response::FORMAT_JSON;
      if($request->isGet){
        $select_number = $user->last_receiving_points;
        $points = ReceivingPoints::find()->andWhere(['!=', 'active', '0'])->all();
        $arr =[];
        foreach ($points as $i=>$p){
          if ($p->address!=null){
            $arr[$p->id] = $p->name.' ( '.$p->address.' )';
          }else{
            $arr[$p->id] = $p->name;
          }
        }

        return [
          'title'=> "Choose",
          'content'=>$this->renderAjax('choose', [
            'model' => $user,
            'points' => $arr,
            'select_number' => $select_number,
          ]),
          'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
            Html::button('Save',['class'=>'btn btn-primary ','type'=>"submit"])
        ];
      }else if($request->post()){
         if (isset($_POST['User']['last_receiving_points'])) {
           $user->last_receiving_points = $_POST['User']['last_receiving_points'];
           $user->save();
         }
        //$model->order_id = $request->post('order_id');

         $this->redirect(['/'],200);
         return [
           'title'=> "Recieving point has been saved",
           'content'=> '',
           'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"])
         ];
      }else{
        $this->redirect(['/']);
        return $this->redirect(['/'],200);
      }
    }else{

    }

  }


    /**
     * Finds the ReceivingPoints model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ReceivingPoints the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ReceivingPoints::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
