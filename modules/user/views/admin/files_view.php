<?php
use kartik\file\FileInput;
use yii\helpers\Url;
use yii\helpers\Html;

$user_files=$model->fileList();


echo FileInput::widget([
  'model' => $model,
  'attribute' => 'files['.$model->id.']',
  'options' => [
    'multiple' => false,
    'accept' => 'application/pdf,image/jpeg,image/pjpeg,application/msword,application/rtf,application/x-rtf,text/richtext'
  ],
  'pluginOptions' => [
    'uploadUrl' => Url::to(['/user/admin/file-upload?id='.$model->id]),
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
      "actions"=>"{delete}".
        Html::a('<i class="glyphicon glyphicon-download-alt"></i>', "{data}",[
          "target"=>"_blank",
          "data-pjax"=>false,
          "class"=>"file-download btn btn-sm fg-white bg-primary text-center",
          'title'=> 'Download document'

        ]).
        '{zoom}',
        "actionDelete"=>
          '<a
                href="'.'/user/admin/file-delete?id='.$model->id.'"
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
    "allowedFileExtensions"=> ["pdf", "jpg", "jepg", "doc", "docx", "rtf"],
    'initialPreview'=>$user_files['initialPreview'],
    'initialPreviewConfig'=>$user_files['initialPreviewConfig'],
    'append'=>$user_files['append'],
    'initialPreviewAsData'=> true,
  ],
  "pluginEvents"=>[
    'filebatchuploadcomplete' => "function(event, files, extra) {
                  $('.kv-upload-progress .progress').hide()
                  k=$('.file-preview-thumbnails>.file-preview-frame').length
                  $('#user_file_".$model->id." span[col_file]').attr('col_file',k)
                 }",
    "filebatchselected"=>'function(event, files) {
                  k=$(\'.file-preview-thumbnails>.file-preview-frame\').length
                  $(\'#user_file_'.$model->id.' span[col_file]\').attr(\'col_file\',k)
                  $this=$(this).fileinput("upload");
                }',
    "filebatchuploadsuccess"=>'function(event, data, previewId, index) {
                  $this=$(this)
                  col_file=data.response.initialPreview.length
                  $(\'#user_file_'.$model->id.' span[col_file]\').attr(\'col_file\',col_file)
                  $this.closest(\'.order-include-index\').find(\'[col_file]\').attr("col_file",col_file)
                }',
    "filebatchuploaderror"=>"
                  function(event, data, msg) {
                      var form = data.form, files = data.files, extra = data.extra,
                          response = data.response, reader = data.reader;
                      gritterAdd('Upload error', msg, 'gritter-danger');
                      $('.file-error-message').remove();
                      
                      event.preventDefault();
                      k=$('.file-preview-thumbnails>.file-preview-frame').length
                      $('#user_file_".$model->id." span[col_file]').attr('col_file',k)
                      return false;
                  }
                "
  ]
]);
?>