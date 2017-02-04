<?php
namespace app\components\fileImageInput;
use Yii;
use yii\base\Widget;
use yii\base\Component;
use yii\helpers\Html;
use yii\widgets\InputWidget;
class FileInput extends InputWidget
{
  public $option;
  public $hasDelate;
  public $type='image';
  public function init()
  {
    parent::init();
    //$this->initDestroyJs();
    //$this->initInputWidget();
  }
  public function run()
  {
    if ($this->hasModel()) {
      $this->name = !isset($this->options['name']) ? Html::getInputName($this->model, $this->attribute) : $this->options['name'];
      $this->value = !isset($this->options['value']) ? Html::getAttributeValue($this->model, $this->attribute) : $this->options['value'];
      $this->id= Html::getInputId($this->model, $this->attribute);
    }else{
      $this->name=!isset($this->options['name']) ? $this->attribute : $this->options['name'];
      $this->value = !isset($this->options['value']) ? '' : $this->options['value'];
    }
    $out='<div class="input_file input_file-'.$this->type.'">';
    if(strlen($this->value) > 10) {
      $out .= HTML::activeHiddenInput($this->model, $this->attribute,['value'=>$this->value,'id'=>'']);
    }
    if($this->type=='image') {
      $out .= HTML::FileInput($this->name, '', ['accept' => 'image/jpeg','id'=>$this->options['id']]);
      $out .= '<div class="crop-image-upload-container crop_medium">';
      if (strlen($this->value) > 10) {
        $out .= '<img src="' . $this->value . '"/>';
      }
      if ($this->hasDelate == true) {
        $css = (strlen($this->value) < 10) ? ' style="display:none;" ' : '';
        $out .= '<a class="clear_photo"' . $css . '>×</a>';
      }
      $out .= '</div>';
    }
    if($this->type=='video') {
      $out .= HTML::FileInput($this->name, '', ['accept' => 'video/mp4,video/avi']);
      if (strlen($this->value) > 10) {
        $out .= '
                    <object id = "videoplayer6381" type = "application/x-shockwave-flash" data = "/player/uppod.swf"
                            width = "320" height = "240" >
                        <param name = "bgcolor" value = "#ffffff" />
                        <param name = "allowFullScreen" value = "true" />
                        <param name = "allowScriptAccess" value = "always" />
                        <param name = "movie" value = "/player/uppod.swf" />
                        <param name = "flashvars" value = "comment=test&amp;st=/player/video209-1292.txt&amp;file=/'.$this->value.'" />
                  </object >';
      }
    }
    $this->registerPlugin();
    $out.='</div>';
    return $out;
  }
  public function registerPlugin()
  {
    $view = $this->getView();
    FileInputAsset::register($view);
    $view->registerJs("init_file_prev(jQuery('[name=\"{$this->name}\"][type=file]'));");
    /*$id = $this->options['id'];
    $view->registerJs("jQuery('#{$id}').cropImageUpload(".json_encode($options).");");*/
  }
}