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

      <?=Html::a('<i class="icon-metro-clipboard-2"></i> Print cargo manifest', ['/orderElement/group/print'], [
        'class' => 'btn btn-blue-gem margin-bottom-10 agreement_dis',
        'id'=>'group-print',
      ]); ?>
      <?=Html::a('<i class="icon-metro-clipboard-2"></i> Print cargo manifest(for each)', ['/orderElement/group/print_for_each'], [
        'class' => 'btn btn-blue-gem margin-bottom-10 agreement_dis',
      ]); ?>

      <?php echo DatePicker::widget([
       'id'=>'transport_date_group',
      'name' => 'transport_date_for_print',
      'removeButton' => false,
      'value' => date('d-M-Y', $model->transport_data),
      'options' => ['placeholder' => date('d-M-Y')],
      'pluginOptions' => [
        'class' => 'qwerty',
        'startDate' => (($min_border)?(0):(date(Yii::$app->config->get('data_format_php'),strtotime('+'.$day_delta.' hours')))),
        'format' => Yii::$app->config->get('data_format_js'),
        'todayHighlight' => true,
        'autoclose'=>true,
      ]
      ]);?>
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

<label id="debug"></label>
<?php
echo "
    <script>
      $(document).ready(function() {
         $('#transport_date_group').on('change',function() {
           $('#debug').text('Saving');
           $.ajax({
             type: 'POST',
             url: 'orderElement/group-print',
             data: {transport_data: $('#transport_date_group').val()},// payment_id'+$(this).attr('payment_id'),
             success: function(data) {
               $('#debug').text('Success');
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

