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
        $model = new ReceivingPoints();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['/receiving_points']);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
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
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['/receiving_points']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
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
        $this->findModel($id)->delete();

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
        $points = ReceivingPoints::find()->select(['id','address'])->andWhere(['!=', 'active', '0'])->all();
        $arr =[];
        foreach ($points as $i=>$p){
          $arr[$p->id] = $p->address;
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
         $this->redirect(['/']);
        return $user->last_receiving_points;
      }else{
        $this->redirect(['/']);
        return '2';
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
