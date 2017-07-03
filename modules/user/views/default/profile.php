<?php
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
//use app\components\fileImageInput\FileInput;
use kartik\file\FileInput;
use yii\helpers\Url;
use yii\bootstrap\Modal;

$user_files=$model->fileList();

$submitOption = [
  'class' => 'btn btn-lg btn-success'
];

$form = ActiveForm::begin([
  'layout' => 'horizontal',
  'enableAjaxValidation' => false,
  'enableClientValidation' => true,
  'options' => ['enctype'=>'multipart/form-data']
]); ?>

<?= skinka\widgets\gritter\AlertGritterWidget::widget() ?>
<div class="col-md-4 col-sm-12 padding-off-left">
    <?=Html::a('<i class="icon-metro-arrow-left-3"></i> Back', ['/parcels'],
        [
            'class'=>'btn btn-md btn-neutral-border pull-left hidden-xs',
        ])?>
</div>
<div class="col-md-4 col-sm-12 text-center">
    <h4 class="modernui-neutral5">Profile <i class="icon-metro-user-2"></i></h4>
</div>
<hr class="bottom_line3">
<div class="container col-md-offset-1 col-md-10">
<?= $form->field($model, 'username') ?>
<?= $form->field($model, 'first_name') ?>
<?= $form->field($model, 'last_name') ?>
<?= $form->field($model, 'phone');?>
<div class="form-group field-user-doc0">
  <label class="control-label col-sm-3" for="user-doc0">Document</label>
  <div class="col-sm-6">
<?= FileInput::widget([
  'model' => $model,
  'attribute' => 'files['.$model->id.']',
  'options' => [
    'multiple' => false,
    'accept' => 'application/pdf,image/jpeg,image/pjpeg,application/msword,application/rtf,application/x-rtf,text/richtext'
  ],
  'pluginOptions' => [
    'uploadUrl' => Url::to(['/user/file-upload/']),
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
        //'    <div class="{dropClass}">'.
        '    <div class="file-preview-thumbnails">'.
        '    </div>'.
        //'    <div class="clearfix"></div>'.
        //'    <div class="file-preview-status text-center text-success"></div>'.
        //'    <div class="kv-fileinput-error"></div>'.
        //'    </div>'.
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
                href="'.'/user/file-delete"
                title= "Delete document"
                class="btn btn-sm file-remove_ bg-danger pull-right popup-modal" 
                confirm-message="Are you sure to delete this document?"
                data-toggle = "modal",
                data-target = "#modal-delete"
                data-id = "0"
                data-name = "0"
                style="background: #fff;"
                confirm-title="Delete"
                {dataKey}
                data-upd="#user_file_'.$model->id.'"
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
    "allowedFileExtensions"=> ["pdf", "jpg", "jpeg", "doc", "docx", "rtf"],
    /*'previewFileIconSettings'=>[
      'doc'=> '<i class="fa fa-file-word-o text-primary"></i>',
      'xls'=> '<i class="fa fa-file-excel-o text-success"></i>',
      'ppt'=> '<i class="fa fa-file-powerpoint-o text-danger"></i>',
      'jpg'=> '<i class="fa fa-file-photo-o text-warning"></i>',
      'pdf'=> '<i class="fa fa-file-pdf-o text-danger"></i>',
      'zip'=> '<i class="fa fa-file-archive-o text-muted"></i>',
    ],*/
    'initialPreview'=>$user_files['initialPreview'],
    'initialPreviewConfig'=>$user_files['initialPreviewConfig'],
    'append'=>$user_files['append'],
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


<?= $form->field($model, 'password')->passwordInput(['placeholder' => 'Password']);?>



  <div class="form-group">
    <div class="col-xs-offset-3 col-xs-9">
      <?= Html::submitButton('UPDATE PROFILE', $submitOption) ?>
    </div>
  </div>
</div>
<?php ActiveForm::end(); ?>


<?php

Modal::begin([
  "id"=>"modal-delete",
  'header' => '<h4 class="modal-title"></h4>',
  'footer' => Html::a('YES', '', ['class' => 'btn btn-danger', 'id' => 'delete-confirm']).
    Html::a('NO', '', ['class' => 'btn']),
]);
echo 'Are you sure to delete this document?';
Modal::end();
?>

<?php
$this->registerJs("
    $(function() {
        $('body').on('click','.popup-modal',function(e) {
            e.preventDefault();
            var modal = $('#modal-delete')//.modal('show');
            modal.find('.modal-body').load($('.modal-dialog'));
            var that = $(this);
            var url = that.attr('href');
            var upd = that.data('upd');
            var name = that.data('key');
            modal.find('.modal-title').text('Delete \"' + name + '\"');

            $('#delete-confirm')
            .unbind('click')
            .click(function(e) {
                e.preventDefault();
                $('#modal-delete').modal('hide');
                $.post(url,{key:name});
                $('[data-key=\"'+name+'\"]').closest(\".file-preview-frame\").remove()
                k=$('.file-preview-thumbnails>.file-preview-frame').length
                $(upd+' span[col_file]').attr('col_file',k)
            });
        });
        
        $('#modal-delete .btn').click(function(e) {
            $('#modal-delete').modal('hide');
            return false;
        });
    });"
);