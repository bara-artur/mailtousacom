<?php

namespace app\modules\cron\controllers;

use Yii;
use yii\web\Controller;
use app\modules\orderElement\models\OrderElement;
use app\modules\orderElement\models\OrderElementSearch;
use keltstr\simplehtmldom\SimpleHTMLDom as SHD;

/**
 * Default controller for the `cron` module
 */
class DefaultController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
      $data=OrderElement::find()->orderBy(['cron_refresh' => SORT_ASC])->limit(10)->all();
      foreach ($data as $parcel){
        $parcel->cron_refresh = time();
        $parcel->save();
      }


      $html = SHD::file_get_html('https://www.fedex.com/apps/fedextrack/?action=track&tracknumbers=786115185080',null,null,1,1);
      $str = $html->save();
      //var_dump($html->find('div[id=content]',0)->innertext);
      var_dump($str);
      foreach($html->find('.statusChevron_key_status') as $element)
        var_dump('value = '.$element->value);

/*      fore0ach ($data as $i=>$parcel){
        $html = SHD::file_get_html('https://www.fedex.com/apps/fedextrack/?action=track&tracknumbers='.$parcel->track_number,null,null,1,1);
        if ($i==0) var_dump();

      //$html = file_get_contents('http://www.google.com/');
        var_dump(count($html->find('.statusChevron_key_status')));
      foreach($html->find('.statusChevron_key_status') as $element)
        var_dump('value = '.$element->value);
      }*/
      return $this->render('index', [
       'data' => $data,
      ]);
    }
}
