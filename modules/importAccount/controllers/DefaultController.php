<?php

namespace app\modules\importAccount\controllers;

use Yii;
use app\modules\importAccount\models\ImportParcelAccount;
use app\modules\importAccount\models\ImportParcelAccountSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * DefaultController implements the CRUD actions for ImportParcelAccount model.
 */
class DefaultController extends Controller
{

  public function beforeAction($action)
  {
    if (Yii::$app->user->isGuest || Yii::$app->user->identity->isManager()) {
      $this->redirect(['/parcels']);
      return false;
    }
    return parent::beforeAction($action);
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
     * Lists all ImportParcelAccount models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ImportParcelAccountSearch();
        $dataProvider = $searchModel->search(['client_id'=>Yii::$app->user->id]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }


    /**
     * Deletes an existing ImportParcelAccount model.
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
     * Finds the ImportParcelAccount model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ImportParcelAccount the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ImportParcelAccount::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
