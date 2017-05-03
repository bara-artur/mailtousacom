<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\web\JsExpression;
use yii\bootstrap\Modal;
use kartik\grid\GridView;
use johnitvn\ajaxcrud\CrudAsset;
use johnitvn\ajaxcrud\BulkButtonWidget;
use yii\widgets\Pjax;
use yii\bootstrap\ActiveForm;
use yii\widgets\DetailView;
use app\components\ParcelPrice;
use kartik\select2\Select2;
use yii\jui\AutoComplete;
use app\modules\user\models\User;
use kartik\file\FileInput;
use yii\bootstrap\Collapse;
//http://demos.krajee.com/widget-details/fileinput
//http://plugins.krajee.com/file-input-ajax-demo/10
//http://plugins.krajee.com/file-input#options
//http://plugins.krajee.com/file-advanced-usage-demo
//http://plugins.krajee.com/file-input/demo#advanced-usage

/* @var $this yii\web\View */
/* @var $searchModel app\modules\orderInclude\models\OrderIncludeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = 'Order';
$this->params['breadcrumbs'][] = $this->title;
CrudAsset::register($this);
$submitOption = [
  'class' => 'btn btn-lg btn-success'
];
?>
  <div class="row">
      <div class="col-md-2 col-sm-12">
          <?=Html::a('<i class="icon-metro-arrow-left-3"></i> Back', ['/'],
            [
              'class'=>'btn btn-md btn-neutral-border pull-left hidden-xs',
              'id' => 'updateParcelsIdCookie',
              'data-forCookie'=> $ids,
              'data-forusercookie'=> $user_ids,
              'data-forinvoicecookie'=> $track_number_types,
              'data-forscanercookie'=> $scaner_data_list,
            ])?>
      </div>
      <?php if ($admin){ ?>
        <div class="col-md-8 col-sm-12 text-center">
          <div class="scaner_buttons">
            <input type="button" class="show_scaner_button  btn btn-info btn-xs2" value="Show scaner">
          </div>
          <p class="hide_scaner"><b>Scaner data:</b><br>
            <input type="text" class="scaner_data hide_scaner" size="32">
            <input type="button" value="Find" class="scaner_find hide_scaner  btn btn-warning btn-xs2" size="32">
            <span class="dowloadTrackNumber"> Processing... </span>
          </p>
        </div>
      <?php } ?>
      <div class="col-md-8 col-sm-12 text-center">
          <h4 class="order_id" order-id=<?=$order_id?> >Order #<?=$order_id?> for Transportation</br>
          <?php if (!$edit_not_prohibited) {?>
              <span class="prohibit_editing text-danger"><span class="glyphicon glyphicon-ban-circle"></span> <?=$message_for_edit_prohibited_order?></span>
          <?php } ?>
          </h4>
      </div>
  </div>
<hr class="bottom_line2">

<?php if (Yii::$app->user->can('userManager')) { ?>
  <div class="row">
    <?php
    //echo AutoComplete::widget([
    //  'name' => 'country',
    //  'clientOptions' => [
    //    'source' => Url::to(['/order/create']),
   //   ],
   //   'options' => ['placeholder' => 'Select the country','tabindex'=>'10'],
  //  ]);
    ?>
  </div>
<?php } ?>

  <div id=crud-datatable-pjax>

    <?php
    Pjax::begin();
    if($order_elements){

      foreach ($order_elements as $k=>$percel) {
        $percel_files=$percel->fileList();
        ?>
        <div <?= (($last==$percel->id)?('id=last_anchor'):('')) ?> class="row">
          <div class="col-md-12"><h5 class="modern_border">Parcel # <?= $k+1 ?> </h5></div>
          <div class="col-md-3 marg_p">
            <h5 class="deliv_address">Delivery address</h5><p><b>First name:</b>  <?=$percel->first_name;?></p>
              <?php
              if($percel->source==1){
                  echo "<p><b>Import from : </b><span class='from_ebay'></span></p>";
              }
              ?>
            <p><b>Last name:</b>  <?=$percel->last_name;?></p>
            <?php if($percel->address_type==1){
              echo '<p><b>Company name:</b>  '.$percel->company_name.'</p>';
            };?>
            <p><b>Addres 1:</b>  <?=$percel->adress_1;?></p>
            <p><b>Addres 2:</b>  <?=$percel->adress_2;?></p>
            <p><b>City:</b>  <?=$percel->city;?></p>
            <p><b>ZIP:</b>  <?=$percel->zip;?></p>
            <p><b>Phone:</b>  <?=$percel->phone;?></p>
            <p><b>State:</b>  <?=$percel->getStateText();?></p>
            <?php if ($edit_not_prohibited) {?>
              <?=Html::a('<i class="glyphicon glyphicon-pencil"></i> Edit address', ['/orderElement/update?id='.$percel->id],
                [
                  'role'=>'modal-remote',
                  'title'=> 'Create Element of Order',
                  'class'=>'btn btn-info btn-xs2 show_modal',
                  'id' => 'open_add_order_address',
                ])?>
            <?php } ?>
          </div>

          <div class="order-include-index col-md-9 border_left">
            <h5 class="order_include">What is inside in shipping package</h5>
            <div id="ajaxCrudDatatable_<?=$percel->id;?>">
              <div class="" id="crud-datatable-pjax">
                <?php Pjax::begin(); ?>
                <div class="table-responsive">
                  <table class="table table-bordered">
                    <tr>
                      <th>#</th>
                      <th>Product Name</th>
                      <th>Item Price</th>
                      <th>Country</th>
                      <th>Quantity</th>
                      <th>Reference number</th>
                      <?php if ($edit_not_prohibited){ ?>
                        <th>Action</th>
                      <?php } ?>
                    </tr>
                    <?php $includes=$percel->getIncludes();?>
                    <?php $total_weight=0;?>
                    <?php foreach ($includes as $i => $item){
                      // $total_weight+=$item['weight']*$item['quantity'];
                      ?>
                      <tr>
                        <td><?=$i+1?></td>
                        <td><?=$item['name'];?></td>
                        <td><?=$item['price'];?></td>
                        <?php if (!isset(Yii::$app->params['country'][$item['country']])) { ?>
                          <td><div class="has-error"><div class="help-block">Enter a country</div></div></td>
                        <?php  } else {?>
                          <td><?=Yii::$app->params['country'][$item['country']]?></td>
                        <?php } ?>
                        <td><?=$item['quantity'];?></td>
                        <td><?=$item['reference_number'];?></td>
                        <?php if ($edit_not_prohibited) {?>
                          <td>
                            <div class="but_tab_style">
                                <?=Html::a('<i class="glyphicon glyphicon-pencil"></i> Edit', ['/orderInclude/update?id='.$item['id']],
                                  ['role'=>'modal-remote','title'=> 'Edit','data-pjax'=>0,'class'=>'btn btn-info btn-sm '])?>
                                <?=Html::a('<i class="glyphicon glyphicon-trash"></i> Delete', ['/orderInclude/delete?id='.$item['id']],
                                  [
                                    'role'=>'modal-remote',
                                    'title'=> 'Delete',
                                    'data-pjax'=>0,
                                    'class'=>'btn btn-danger btn-sm w0-action-del',
                                    'data-request-method'=>"post",
                                    'data-confirm-title'=>"Are you sure?",
                                    'data-confirm-message'=>"Are you sure want to delete this item",
                                  ])?>
                                 </div>
                          </td>
                        <?php } ?>
                      </tr>
                    <?php }?>
                  </table>
                </div>

              </div>
            </div>
          </div>
<div class="row">
          <div class="col-md-12">
            <h5 class="total_package padding-top-10">Total</h5>
            <?php if ($totalPriceArray[$k] > Yii::$app->config->get('parcelMaxPrice')) {?>
              <h5 class="btn-warning max_price">
                Мaximum total price of parcel is <?= Yii::$app->config->get('parcelMaxPrice') ?>$ (USD)
              </h5> <?php } ?>
            <form id="lb-oz-tn-form" title="" method="post">
              <div class="label_valid col-md-5 padding-off-left padding-off-right">
                <div class="form-control-addon-fill">
                  <div class="input-group">
                    <span class="input-group-addon fint_input padding-off-left">Weight parcel :</span>
                    <?php if ($edit_not_prohibited) {?>
                      <input size="5" type="text" id="lb" class="lb-oz-tn-onChange num form_lb form-control" name="lb" maxlength="3" max=100 value="<?=$percel->weight_lb;?>">
                    <?php }else{ ?>
                      <span class="input-group-addon fint_input"><?=$percel->weight_lb;?></span>
                    <?php } ?>
                    <span class="input-group-addon fint_input">lb</span>

                    <?php if ($edit_not_prohibited) {?>
                      <input size="5" type="text" id="oz" class="lb-oz-tn-onChange num form_oz form-control" name="oz" maxlength="2" max=16 value="<?=$percel->weight_oz;?>">
                    <?php }else{ ?>
                      <span class="input-group-addon fint_input"><?=$percel->weight_oz;?></span>
                    <?php } ?>
                    <span class="input-group-addon fint_input">oz</span>
                  </div>
                </div>
              </div>
              <div class="label_valid col-md-4 bor padding-off-left">
                <div class="form-control-addon-fill">
                  <div class="input-group" <?php if($percel->track_number_type==1) { echo 'style="display:none;"';}?>>
                    <span class="input-group-addon fint_input padding-off-left"> Track Number :</span>
                    <?php if ($edit_not_prohibited) {?>
                      <input type="text" id="track_number" class="lb-oz-tn-onChange form_tn form-control" name="track_number" value="<?=$percel->track_number;?>">
                    <?php }else{ ?>
                      <span class="input-group-addon fint_input"><?=$percel->track_number;?></span>
                    <?php } ?>
                  </div>
                </div>
              </div>
              <div class="col-md-3">
              <span class="input-group-addon fint_input">
                <label><input type="checkbox"
                              id="track_number_type"
                              class="lb-oz-tn-onChange form-control"
                              name = "track_number_type"
                              <?php if($percel->track_number_type==1) { echo 'checked';} ?>
                              <?php if($edit_not_prohibited==0) { echo 'disabled';} ?>
                       >
                <span class="fa fa-check otst"></span>I have no Track Number</label>
              </span>
              </div>
          </div>
</div>
              <hr class="bor_bottom">
              <input type="hidden" name = "percel_id" value=<?=$percel->id?>>
              <input type="hidden" name = "order_id" value=<?=$order_id?>>
            <div class="row">
              <div class="col-md-5 cost_del"><b>Cost of delivery : </b>
                <span id="results" class="resInd<?=$k?>">
                        <?php
                        if($percel->weight>0) {
                          $ParcelPrice = ParcelPrice::widget(['weight' => $percel->weight,'user'=>$percel->user_id]);
                          if ($ParcelPrice != false) {
                            $ParcelPrice = '' . $ParcelPrice . ' $ (without tax)';
                          } else {
                            $ParcelPrice = '<span style="color:#E51400;">Exceeded weight of a parcel.</span>';
                          }
                        }else{
                          $ParcelPrice="-";
                        }
                        echo $ParcelPrice;
                        ?>
                    </span>
              </div>

            </form>
                <div class="col-md-12 col-lg-7 col-sm-12 col-xs-12 bord_butt text-center">

            <?php if ($edit_not_prohibited) {?>

                <?=Html::a('<i class="glyphicon glyphicon-plus"></i>Add Item', ['create?order-id='.$percel->id],
                  ['role'=>'modal-remote','title'=> 'Add item to Parcel','data' => [
                      'toggle'=>"tooltip",
                  ],'class'=>'btn btn btn-md btn-science-blue text-center margin-bottom-10'])?>
                <?=Html::a('<i class="glyphicon glyphicon-trash"></i> Delete', ['/orderElement/delete?id='.$percel->id.'&order_id='.$order_id],
                  [
                    'role'=>'modal-remote',
                    'title'=> 'Delete Attachment',
                      'data' => [

                          'toggle'=>"tooltip",
                      ],

                    'data-pjax'=>0,
                    'class'=>'btn btn-danger text-center margin-bottom-10',
                    'data-request-method'=>"post",
                    'data-confirm-title'=>"Are you sure?",
                    'data-confirm-message'=>"Are you sure want to delete this packages",
                  ])?>
                <?= Html::a('
                      <i class="icon-metro-attachment"></i>
                      Documents
                      <span col_file='.count($percel_files['initialPreview']).'></span>
                      ', ['#parcels_'.$percel->id.''],
                    [
                        'id'=>'#parcels_'.$percel->id.'',
                        'title'=> 'Attachment documents to parcel',
                        'data-toggle' => 'collapse',
                        'class' => 'btn btn-primary text-center margin-bottom-10',
                    ]) ?>


            <?php } ?>
            <?php if (count($order_elements)>1){?>
              <?=Html::a('<i class="icon-metro-new-tab fa-flip-horizontal"></i> Unfasten',
                ['/orderInclude/group-remove/'.$order_id."/".$percel->id],
                [
                  'class' => 'btn btn-warning btn-md text-center margin-bottom-10',
                  'title'=> 'Take out from Order',
                  'data' => [
                    'confirm-message' => 'Are you sure to remove this Parcel from this order?</br>Entered data will be kept!</br>
Parcel will be moved back to the list of parcels.',
                    'confirm-title'=>"Take out from Order",
                    'pjax'=>'false',
                    'toggle'=>"tooltip",
                    'request-method'=>"post",
                  ],
                  'role'=>"modal-remote",
                ]); ?>
            <?php }?>
            <?php if ($edit_not_prohibited==0) {?>
              <?=Html::a('<i class="fa fa-credit-card"></i> Payments', ['/payment/show-parcel-includes/' . $percel->id],
                [
                'id' => 'payment-show-includes',
                'title'=> 'View payments',
                'role' => 'modal-remote',
                'class' => 'btn btn-science-blue show_modal big_model text-center margin-bottom-10',
                'data' => ['toggle'=>"tooltip"],
                ]
                )?>
                 <?=Html::a('<i class="fa fa-list"></i> History', ['/logs/' . $percel->id],
                [
                'id' => 'payment-show-includes',
                'title'=> 'View history',
                'role' => 'modal-remote',
                'class' => 'btn btn-science-blue show_modal text-center margin-bottom-10',
                'data' => ['toggle'=>"tooltip"],
                ]
                )?>
                <?= Html::a('
                    <i class="icon-metro-attachment"></i>
                    Documents
                    <span col_file='.count($percel_files['initialPreview']).'></span>
                    ',
                    ['#parcels_'.$percel->id.''],
                    [
                        'id'=>'#parcels_'.$percel->id.'',
                        'title'=> 'Attachment documents to parcel',
                        'data-toggle' => 'collapse',
                        'class' => 'btn btn-primary text-center margin-bottom-10',
                    ]) ?>

         <?php } ?>
              </div>

                <div class="collapse" id="parcels_<?=$percel->id;?>">
                    <div class="col-md-12">
            <?php
            echo '<span class="control-label">PLEASE, ADD DOCUMENTS FOR PARCEL</span>'
            . Html::a('HIDE <i class="fa fa-caret-up"></i>', ['#parcels_'.$percel->id.''],
                    [
                        'id'=>'#parcels_'.$percel->id.'',
                        'class' => 'pull-right font-weight-700',
                        'title'=> 'Hide window addition of documents',
                        'data-toggle' => 'collapse',

                    ]);
            echo FileInput::widget([
              'model' => $percel,
              'attribute' => 'files['.$percel->id.']',
              'options' => [
                'multiple' => false,
                'accept' => 'application/pdf,image/jpeg,image/pjpeg,application/msword,application/rtf,application/x-rtf,text/richtext'
              ],
              'pluginOptions' => [
                'uploadUrl' => Url::to(['/orderElement/file-upload/'.$percel->id]),
                'browseClass'=> "btn btn-success pull-right",
                'browseLabel'=> "Add documents",
                "layoutTemplates"=> [
                  "main1"=>
                    "{preview}".
                    "<div class='input-group {class}'>".
                    "   <div class='input-group-btn'>".
                    "       {browse}".
                    "       {upload}".
                    "       {remove}".
                    "   </div>".
                    "   {caption}".
                    "</div>",
                  "preview"=>
                    '<div class="file-preview {class}">'.
                    '    <div class="{dropClass}">'.
                    '    <div class="file-preview-thumbnails">'.
                    '    </div>'.
                    '    <div class="clearfix"></div>'.
                    '    <div class="file-preview-status text-center text-success"></div>'.
                    '    <div class="kv-fileinput-error"></div>'.
                    '    </div>'.
                    '</div>',
                  "modal"=>'<div class="modal-dialog" role="document">'.
                    '  <div class="modal-content">'.
                    '    <div class="modal-header">' .
                    '      <div class="kv-zoom-actions pull-right">{close}</div>' .
                    '      <h4 class="modal-title">{heading} : <small><span class="kv-zoom-title"></span></small></h4>' .
                    '    </div>' .
                    '    <div class="modal-body">' .
                    '      <div class="floating-buttons"></div>'.
                    '      <div class="kv-zoom-body file-zoom-content"></div>'.
                    '{prev} {next}'.
                    '    </div>'.
                    '  </div>'.
                    '</div>'.
                    '<script>
                      $(\'#kvFileinputModal\').addClass("modal-lg");
                      $(\'#kvFileinputModal\').css(\'padding\',0);
                    </script>',
                  //"footer"=>"123",
                  "actions"=>"{delete}".
                    Html::a('<i class="glyphicon glyphicon-download-alt"></i>', "{data}",[
                      "target"=>"_blank",
                      "data-pjax"=>false,
                      "class"=>"file-download btn btn-sm fg-white bg-primary text-center",
                      'title'=> 'Download document'

                    ]).
                    '{zoom}'
                  ,
                  "actionDelete"=>
                    '<a
                      href="'.'/orderElement/file-delete/'.$percel->id.'"
                      title= "Delete document"
                      class="btn btn-sm file-remove bg-danger pull-right" 
                      confirm-message="Are you sure to delete this document?"
                      confirm-title="Delete"
                      {dataKey}
                      >
                      {removeIcon}
                      </a>'
                ],
                'removeFromPreviewOnError'=>true,
                'maxFileCount' => 5,
                'minFileCount' => 1,
                "uploadAsync"=>false,
                'showRemove' => false,
                'showUpload' => false,
                'showBrowse'=> true,
                'showCaption' => false,
                'showUploadedThumbs' => false,
                'showCancel' => false,
                'browseOnZoneClick'=> true,
                'maxFileSize'=>2800,
                "allowedFileExtensions"=> ["pdf", "jpg", "jepg", "doc", "docx", "rtf"],
                /*'previewFileIconSettings'=>[
                  'doc'=> '<i class="fa fa-file-word-o text-primary"></i>',
                  'xls'=> '<i class="fa fa-file-excel-o text-success"></i>',
                  'ppt'=> '<i class="fa fa-file-powerpoint-o text-danger"></i>',
                  'jpg'=> '<i class="fa fa-file-photo-o text-warning"></i>',
                  'pdf'=> '<i class="fa fa-file-pdf-o text-danger"></i>',
                  'zip'=> '<i class="fa fa-file-archive-o text-muted"></i>',
                ],*/
                'initialPreview'=>$percel_files['initialPreview'],
                'initialPreviewConfig'=>$percel_files['initialPreviewConfig'],
                'append'=>$percel_files['append'],
                'initialPreviewAsData'=> true,
              ],
              "pluginEvents"=>[
                'filebatchuploadcomplete' => "function(event, files, extra) {
                  $('.kv-upload-progress .progress').hide()
                 }",
                "filebatchselected"=>'function(event, files) {
                  $this=$(this).fileinput("upload");
                }',
                "filebatchuploadsuccess"=>'function(event, data, previewId, index) {
                  $this=$(this)
                  col_file=data.response.initialPreview.length
                  $this.closest(\'.order-include-index\').find(\'[col_file]\').attr("col_file",col_file)
                }',
                "filebatchuploaderror"=>"
                  function(event, data, msg) {
                      var form = data.form, files = data.files, extra = data.extra,
                          response = data.response, reader = data.reader;
                      gritterAdd('Upload error', msg, 'gritter-danger');
                      $('.file-error-message').remove();
                      
                      event.preventDefault();
                      return false;
                  }
                "
              ]
            ]);
            ?>

                    </div>
            </div>
            </div>
        <?php Pjax::end(); ?>
        </div>
        <hr>
      <?php } ?>
    <?php } ?>
    <?php Pjax::end(); ?>
  </div>

<?php Modal::begin([
  "id"=>"ajaxCrudModal",
  "footer"=>"",// always need it for jquery plugin
])?>
<?php Modal::end(); ?>


<?php if ($edit_not_prohibited) {?>
  <?=Html::a('<i class="glyphicon glyphicon-plus"></i> Add another Attachment in Order', ['/orderElement/create/'.$order_id],
    [
      'role'=>'modal-remote',
      'title'=> 'Add another attached order',
      'class'=>'btn btn-default show_modal',
      'id' => 'open_add_order_address',
    ])?>
   <?php if ($hideNext==0) { ?>
    <?=Html::a('Next <i class="glyphicon glyphicon-chevron-right"></i>', ['/orderInclude/border-form/'.$order_id],
      [
        'class'=>'btn btn-success pull-right go_to_order',
        'disabled' => (count($order_elements)==0),
      ])?>
     <?php } ?>
<?php } ?>
<?php
if($createNewAddress){
  echo '
    <script>
      $(document).ready(function() {
        setTimeout( "$(\'#open_add_order_address\').click();",200);
      });
    </script>
    ';
}
?>

<script>
  //исправить!!!!!!!
  $(document).ready(function() {
   $('.hide_scaner').hide();
   $('.dowloadTrackNumber').hide();
   $( "#ajaxCrudModal" ).on( "click", ".select2", function( event ) { // делегируем событие для динамического select2
   $("#ajaxCrudModal").removeAttr("tabindex");
   });
   $( "#crud-datatable-pjax" ).on( "click", ".btn-science-blue", function( event ) { // делегируем событие для динамической кнопки добавить
   $("#ajaxCrudModal").attr("tabindex",-1);
   });
   if (getCookie('successGritter')){
     gritterAdd('Success', 'We add the new parcel to your order', 'gritter-success');
     setCookie('successGritter','',1);
   }
   function scaner_enter_button(){
     elem = $('.order_id');
     order_id = elem.attr('order-id');
     track_number = $('.scaner_data').val();
     if ((track_number.length==12) || (track_number.length==13) || (track_number.length==15) ||
       (track_number.length==16) || (track_number.length==18) || (track_number.length==20) ||
       (track_number.length==22) ||(track_number.length==26)){
       $(".dowloadTrackNumber").show();
       $.ajax({
         type: 'POST',
         url: 'order/update',
         data: {order_id: order_id, track_number: track_number},
         success: function (data) {
           if (data == 0) {
           }
           else if (data == 1) {
             gritterAdd('Success', 'We find your parcel in DB. Saving successful', 'gritter-success');
           }
           else if (data == 2) {
             gritterAdd('Error', 'We find your parcel in DB, but order saving failed', 'gritter-danger');
           }
           else if (data == 3) {
             gritterAdd('Success', 'We create new parcel. Saving successful', 'gritter-success');
           }
           else if (data == 4) {
             gritterAdd('Error', 'We create new parcel, but order saving failed ', 'gritter-danger');
           }
           else if (data == 5) {
             gritterAdd('Error', 'Track number validation failed', 'gritter-danger');
           }
           else if (data == 6) {
             gritterAdd('Error', 'We create new parcel, but parcel saving was failed', 'gritter-danger');
           }
           else if (data == 7) {
             gritterAdd('Error', 'You have this parcel in current order', 'gritter-warning');
           }
           else if (data == 9) {
             gritterAdd('Error', 'Different user parcels error', 'gritter-danger');
           }
           else {
             setCookie('successGritter','1',1);
             location.href = data;
             location.reload();
           }
           $(".dowloadTrackNumber").hide();
           // window.location.hash="last_parcel_anchor";
         },
         error: function (xhr, str) {
           $('.order_id').css("color", "red");
           $(".dowloadTrackNumber").hide();
         }
       });
     }else{
       gritterAdd('Error','Track number length validation failed','gritter-danger');
     }
   }

   $(".scaner_find").on("click",scaner_enter_button);
   $(".show_scaner_button").on("click",function(){
     if ($(".show_scaner_button").val()=="Show scaner") {
       $(".show_scaner_button").val('Hide scaner');
       $('.hide_scaner').show(500);
       $('.scaner_data').focus();
       $('.scaner_data').val('');
     }
     else {
       $(".show_scaner_button").val('Show scaner');
       $('.hide_scaner').hide(500);
     }
   });
   $("body").on('keydown', function(){
     //if ($('.scaner_data').is(':hidden')) {
       if ((event.keyCode || event.charCode) == 118) {  // f7
         $('.hide_scaner').show(500);
         $('.scaner_data').focus();
         $('.scaner_data').val('');
         $(".show_scaner_button").val('Hide scaner');
       }
     //}else{
       if ((event.keyCode || event.charCode) == 13) {
         if ($('.scaner_data').length>0) scaner_enter_button();
       }
      //}
    });
   $(".show_modal").on("click", function() {
   $("#ajaxCrudModal").attr("tabindex",-1);

   });
   $('#last_anchor').find('#lb').focus().select();
  });
</script>