<?php
/**
 * Created by PhpStorm.
 * User: Tolik
 * Date: 10.05.2017
 * Time: 7:52
 */
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\widgets\DatePicker;
?>

      <?=Html::a('<i class="icon-metro-clipboard-2"></i> Print cargo manifest', ['/orderElement/group/print'], [
        'class' => 'btn btn-blue-gem margin-bottom-10 agreement_dis',
        'id'=>'group-print',
        'disabled'=>true
      ]); ?>
      <?=Html::a('<i class="icon-metro-clipboard-2"></i> Print cargo manifest(for each)', ['/orderElement/group/print_for_each'], [
        'class' => 'btn btn-blue-gem margin-bottom-10 agreement_dis',
        'disabled'=>true
      ]); ?>
  <?= Html::checkbox('agreement',false,[
    'label' => '
          <span class="fa fa-check col-md-1 col-md-offset-3 col-xs-1 text-right otst"></span>
         
          <div class="col-md-5 col-xs-11 text-left">
          I certify the particulars given in this customs declaration are correct. This form does not contain any undeclared
        dangerous articles, or articles prohibited by Legislation or by postal or customs regulations. I have met all
        applicable export filing requirements under federal law and regulations.</div>',
    'id'=>'order-agreement',
  ]) ?>

<?php
echo DatePicker::widget([
  'name' => 'check_issue_date',
  'removeButton' => false,
  //'value' => date('d-M-Y', $model->transport_data),
  'options' => ['placeholder' => 'Choose date'],
  'pluginOptions' => [
    'startDate' => date(\Yii::$app->config->get('data_format_php')),
    'format' => \Yii::$app->config->get('data_format_js'),
    'todayHighlight' => true,
    'autoclose'=>true,
  ]
]);
?>


      <?=Html::a('<i class="fa fa-list"></i> Print table data', ['/orderElement/group/advanced_print'],
        [
          'class' => 'btn btn-blue-gem margin-bottom-10 InSystem_show Draft_show difUserIdHide group-print-advanced',
          'id'=>'group-print-advanced',
        ]); ?>
      <?=Html::a('<i class="fa fa-list"></i> Commercial Invoice', ['/orderElement/group/commercial_inv_print'],
        [
          'class' => 'btn btn-blue-gem InSystem_show Draft_show difUserIdHide group-print-advanced',
          'id'=>'group-print-advanced',
        ]); ?>

