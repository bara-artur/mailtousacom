<?php
use kartik\file\FileInput;
use yii\helpers\Url;
use yii\helpers\Html;

$percel_files=$percel->fileList();

echo FileInput::widget([
  'model' => $percel,
  'attribute' => 'files['.$percel->id.']',
  'options' => [
    'multiple' => false,
    'accept' => 'application/pdf,image/jpeg,image/pjpeg,application/msword,application/rtf,application/x-rtf,text/richtext'
  ],
  'pluginOptions' => [
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
      "actions"=>
        Html::a('<i class="glyphicon glyphicon-download-alt"></i>', "{data}",[
          "target"=>"_blank",
          "data-pjax"=>false,
          "class"=>"file-download btn btn-sm fg-white bg-primary text-center",
          'title'=> 'Download document'

        ]).
        '{zoom}'
    ],
    'removeFromPreviewOnError'=>false,
    'maxFileCount' => 5,
    'minFileCount' => 1,
    "uploadAsync"=>false,
    'showRemove' => false,
    'showUpload' => false,
    'showBrowse'=> false,
    'showCaption' => false,
    'showUploadedThumbs' => false,
    'showCancel' => false,
    'browseOnZoneClick'=> false,
    'maxFileSize'=>2800,
    "allowedFileExtensions"=> ["pdf", "jpg", "jepg", "doc", "docx", "rtf"],
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