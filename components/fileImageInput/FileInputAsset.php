<?php
namespace app\components\fileImageInput;
use yii\web\AssetBundle;
class FileInputAsset extends AssetBundle
{
  //public $basePath = '@webroot';
  //public $baseUrl = '@web';
  public $sourcePath = '@app/components/fileImageInput/assets';
  public $js = ['js/FileInput.js'];
  public $css = ['css/FileInput.css'];
  /**
   * @inheritdoc
   */
  public function init()
  {
    //$this->css[] = 'css/___.css';
    //$this->js[] = 'js/_.js';
    parent::init();
  }
}