<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\additional_services\models\AdditionalServicesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Additional Services';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="additional-services-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
          ['attribute'=> 'type',
            'content'=> function($data){
                return $data->textType;
            }
          ],
            //'parcel_id_lst',
          ['attribute'=> 'client_id',
            'content'=> function($data){ if ($data->client!=null)
                return $data->client->lineInfo; else return '-empty-';
            }
          ],
          ['attribute'=> 'user_id',
            'content'=> function($data){ if ($data->user!=null)
                return $data->user->lineInfo; else return '-empty-';
            }
          ],
            // 'detail',
          //  'status_pay',
          'parcel_id_lst',
          ['attribute'=> 'status_pay',
            'content'=> function($data){
                return ($data->textStatus);
            }
          ],
          ['attribute'=> 'price',
            'content'=> function($data){
                return
                  ($data->price)?
                    number_format($data->price,2,'.',''):
                    '-';
            }
          ],
          ['attribute'=> 'Tax',
            'content'=> function($data){
                return
                  ($data->gst+$data->qst)?
                    number_format($data->gst+$data->qst,2,'.',''):
                    '-';
            }
          ],
          ['attribute'=> 'dop_price',
            'content'=> function($data){
                return
                  ($data->dop_price)?
                    number_format($data->dop_price,2,'.',''):
                    '-';
            }
          ],
          'quantity',
           // ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
