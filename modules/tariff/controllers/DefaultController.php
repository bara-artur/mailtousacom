<?php

namespace app\modules\tariff\controllers;

use Yii;
use app\modules\tariff\models\Tariffs;
use app\modules\tariff\models\TariffsSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use \yii\web\Response;
use yii\helpers\Html;

/**
 * DefaultController implements the CRUD actions for Tariffs model.
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
                    'delete' => ['post'],
                    'bulk-delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all Tariffs models.
     * @return mixed
     */
    public function actionIndex()
    {

        $searchModel = new TariffsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

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

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'parcel_count'=>$parcel_count,
            'weights'=>$weight,
            'tarifs'=>$out,
        ]);
    }

    public function actionSavePrice(){
      $request = Yii::$app->request;
      if(!$request->getIsPost()){
        throw new \yii\web\NotFoundHttpException("Page not found.");
      }
      $post = $request->post();

      $data=Tariffs::find()
        ->where([
        'weight'=>$post['weight'],
        'parcel_count'=>$post['count']
        ])
        ->one();
      if(!$data){
        $data= new Tariffs();
      }

      $data->price=$post['value'];
      $data->weight=$post['weight'];
      $data->parcel_count=$post['count'];

      if($data->save()){
        $data=Tariffs::find()
          ->where([
            'weight'=>$post['weight'],
            'parcel_count'=>$post['count']
          ])
          ->limit(1)
          ->asArray()
          ->all();
        $data=$data[0];
        $data['price']=number_format($data['price'],2,'.','');
        return json_encode($data);
      }

      //~d($data);
      return ;
    }


    /**
     * Displays a single Tariffs model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {   
        $request = Yii::$app->request;
        if($request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                    'title'=> "Tariffs #".$id,
                    'content'=>$this->renderAjax('view', [
                        'model' => $this->findModel($id),
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                            Html::a('Edit',['Update','id'=>$id],['class'=>'btn btn-science-blue','role'=>'modal-remote'])
                ];    
        }else{
            return $this->render('view', [
                'model' => $this->findModel($id),
            ]);
        }
    }

    /**
     * Creates a new Tariffs model.
     * For ajax request will return json object
     * and for non-ajax request if creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $request = Yii::$app->request;
        $model = new Tariffs();

        $weight = Tariffs::find()->one();
        $weight=$weight->weight;

        if($request->isAjax){
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            if($request->isGet){
                return [
                    'title'=> "Create new min parcel count",
                    'content'=>$this->renderAjax('create', [
                      'model' => $model,
                      'weight' => $weight,
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                                Html::button('Save',['class'=>'btn btn-success','type'=>"submit"])

                ];
            }else if($model->load($request->post()) && $model->save()){
                return [
                    'forceReload'=>'#crud-datatable-pjax',
                    'title'=> "Create new min parcel count",
                    'content'=>'<span class="text-success">Create Tariffs success</span>',
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                            Html::a('Create More',['create'],['class'=>'btn btn-science-blue','role'=>'modal-remote'])
        
                ];         
            }else{           
                return [
                    'title'=> "Create new Tariffs",
                    'content'=>$this->renderAjax('create', [
                        'model' => $model,
                        'weight' => $weight,
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                                Html::button('Save',['class'=>'btn btn-primary','type'=>"submit"])
        
                ];         
            }
        }
        return $this->redirect(['/tariff/default/']);
    }

  /**
   * Creates a new Tariffs model.
   * For ajax request will return json object
   * and for non-ajax request if creation is successful, the browser will be redirected to the 'view' page.
   * @return mixed
   */
  public function actionCreate_weight()
  {
    $request = Yii::$app->request;
    $model = new Tariffs();

    $count = Tariffs::find()->one();
    $count=$count->parcel_count;

    if($request->isAjax){
      /*
      *   Process for ajax request
      */
      Yii::$app->response->format = Response::FORMAT_JSON;
      if($request->isGet){
        return [
          'title'=> "Create new min parcel count",
          'content'=>$this->renderAjax('create_weight', [
            'model' => $model,
            'count'=>$count,
          ]),
          'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
            Html::button('Save',['class'=>'btn btn-primary','type'=>"submit"])

        ];
      }else if($model->load($request->post()) && $model->save()){
        return [
          'forceReload'=>'#crud-datatable-pjax',
          'title'=> "Create new min parcel count",
          'content'=>'<span class="text-success">Create new weight success</span>',
          'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
            Html::a('Create More',['create'],['class'=>'btn btn-primary','role'=>'modal-remote'])

        ];
      }else{
        return [
          'title'=> "Create new min parcel count",
          'content'=>$this->renderAjax('create_weight', [
            'model' => $model,
            'count'=>$count,
          ]),
          'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
            Html::button('Save',['class'=>'btn btn-primary','type'=>"submit"])

        ];
      }
    }
    return $this->redirect(['/tariff/default/']);
  }
    /**
     * Updates an existing Tariffs model.
     * For ajax request will return json object
     * and for non-ajax request if update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $request = Yii::$app->request;
        $model = $this->findModel($id);       

        if($request->isAjax){
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            if($request->isGet){
                return [
                    'title'=> "Update Tariffs #".$id,
                    'content'=>$this->renderAjax('update', [
                        'model' => $model,
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                                Html::button('Save',['class'=>'btn btn-primary','type'=>"submit"])
                ];         
            }else if($model->load($request->post()) && $model->save()){
                return [
                    'forceReload'=>'#crud-datatable-pjax',
                    'title'=> "Tariffs #".$id,
                    'content'=>$this->renderAjax('view', [
                        'model' => $model,
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                            Html::a('Edit',['update','id'=>$id],['class'=>'btn btn-primary','role'=>'modal-remote'])
                ];    
            }else{
                 return [
                    'title'=> "Update Tariffs #".$id,
                    'content'=>$this->renderAjax('update', [
                        'model' => $model,
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                                Html::button('Save',['class'=>'btn btn-primary','type'=>"submit"])
                ];        
            }
        }else{
            /*
            *   Process for non-ajax request
            */
            if ($model->load($request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                return $this->render('update', [
                    'model' => $model,
                ]);
            }
        }
    }

    /**
     * Delete an existing Tariffs model.
     * For ajax request will return json object
     * and for non-ajax request if deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete()
    {
      $request = Yii::$app->request;
      if($request->isAjax){
        $w=array();
        if($request->get('count')){
          $w['parcel_count']=$request->get('count');
        }
        if($request->get('weight')){
          $w['weight']=$request->get('weight');
        }
        Tariffs::deleteAll($w);
        Yii::$app->response->format = Response::FORMAT_JSON;
        return ['forceClose'=>true,'forceReload'=>'#crud-datatable-pjax'];
      }else{

          return $this->redirect(['index']);
      }
    }





    /**
     * Finds the Tariffs model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Tariffs the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Tariffs::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
