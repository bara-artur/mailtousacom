<?php
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\bootstrap\Modal;
use johnitvn\ajaxcrud\BulkButtonWidget;
use yii\widgets\Pjax;


$this->title = 'eBay import configuration';
$this->params['breadcrumbs'][] = $this->title;

?>

<?php $form = ActiveForm::begin([
    'options' => ['class'=>'ebay_config'],
    'id'=>'created_address',
    'validateOnChange' => true,
]); ?>
    <h4 class="modernui-neutral2">eBay import configuration</h4>
    <div class="row">
        <div class="col-md-4 col-md-offset-4 text-center">
            <div class="trans_text">
                This is your first import from eBay ....
            </div>
            <hr class="podes">
            <div class="trans_text">
                I would like to import orders for the last
            </div>
            <div class="row">
                <div class="col-xs-6 col-xs-offset-2">
                    <input size="2" type="text" class="lb-oz-tn-onChange num form_lb form-control" name="days" maxlength="3" max=100 value="7">
                </div>
                <div class="col-xs-4 padding-off-left text-left">
                    <span class="trans_count">days</span>
                </div>
            </div>
        </div>
    </div>
    <hr>
    <div class="form-group">
        <?=Html::a('< Back', ['/orderInclude/create-order/'.$order_id],
            ['class' => 'btn btn-default pull-left'])?>

        <?= Html::submitButton('NEXT<i class="icon-metro-arrow-right-5"></i>', ['class' => 'btn btn-success pull-right']) ?>

    </div>


<?php ActiveForm::end(); ?>