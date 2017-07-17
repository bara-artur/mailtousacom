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

$param_name='receive_max_time'.(Yii::$app->user->identity->isManager() ? '_admin' : '');
$day_delta=24-Yii::$app->config->get($param_name);
?>
<?php
if(Yii::$app->user->identity->isManager()){
    ?>
<label>Choose date which will be specified in documents</label>
<?php echo DatePicker::widget([
  'id'=>'transport_date_group',
  'name' => 'transport_date_for_print',
  'removeButton' => false,
  'value' => date('d-M-Y', $model->transport_data),
  'options' => ['placeholder' => date('d-M-Y')],
  'pluginOptions' => [
    'class' => 'qwerty',
    'startDate' =>
        date(
          "d-M-Y",
            ($min_border?(strtotime("-1 year")):strtotime('+'.$day_delta.' hours'))),
    'format' => Yii::$app->config->get('data_format_js'),
    'todayHighlight' => true,
    'autoclose'=>true,
  ]
]);?>
<hr>
<?php }?>
<div class="row">
    <div class="col-md-12 text-center">
      <?=Html::a('<i class="fa fa-print push-up-tiny"></i><br>Cargo manifest', ['/orderElement/group/print'], [
        'class' => 'tile2 agreement_dis',
        'id'=>'group-print',
        'target' => '_blank',
      ]); ?>

      <?=Html::a('<i class="fa fa-print"></i><br>Cargo manifest<div>for each parcel</div>', ['/orderElement/group/print_for_each'], [
        'class' => 'tile2 agreement_dis',
        'target' => '_blank',
      ]); ?>
      <?php
      if(Yii::$app->user->identity->isManager()){
          ?>
      <?=Html::a('<i class="fa fa-print push-up-tiny"></i><br>Table data', ['/orderElement/group/advanced_print'],
        [
          'class' => 'tile2 InSystem_show Draft_show difUserIdHide group-print-advanced',
          'id'=>'group-print-advanced',
          'target' => '_blank',
        ]); ?>

      <?=Html::a('<i class="fa fa-print push-up-tiny"></i><br>Commercial Invoice', ['/orderElement/group/commercial_inv_print'],
        [
          'class' => 'tile2 InSystem_show Draft_show difUserIdHide group-print-advanced',
          'id'=>'group-print-advanced',
          'target' => '_blank',
        ]); ?>
      <?php }?>

</div>
</div>
<?php
echo "
    <script>
      $(document).ready(function() {
         $('#transport_date_group').on('change',function() {
           $.ajax({
             type: 'POST',
             url: 'orderElement/group-print',
             data: {transport_data: $('#transport_date_group').val()},// payment_id'+$(this).attr('payment_id'),
             success: function(data) {
             },
             error:  function(xhr, str){
               gritterAdd('Error','Error: '+xhr.responseCode,'gritter-danger');
             }
           });
         })
      });
    </script>
    ";
?>

