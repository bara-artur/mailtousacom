<?php

namespace app\modules\cron\controllers;

use Yii;
use yii\web\Controller;
use app\modules\orderElement\models\OrderElement;
use app\modules\orderElement\models\OrderElementSearch;
use keltstr\simplehtmldom\SimpleHTMLDom as SHD;
use app\modules\config\components\DConfig;

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
      $data=OrderElement::find()->orderBy(['cron_refresh' => SORT_ASC])->limit(10)->all(); // берем 10 посылок (надо будет исключить доставленные)

      $summary = [];
      foreach ($data as $parcel){
        $parcel->cron_refresh = time();          // записываем последнее время обновления
        $company = OrderElement::GetShippingCarrier($parcel->track_number);
        if (($company != '')&&($parcel->status!=5)) {   // если определили транспортную компанию и ещё не доставлена
          $html = SHD::file_get_html('https://trackingshipment.net/' .$company.'/' . $parcel->track_number, null, null, 1, 1); // дружественный сервис просмотра состояний посылок
          $str = $html->find('.output-info p', 0)->innertext; // берем содержимое первого абзаца у тэга с классом output_info
          if ((strripos($str, 'ummary:') != false) && (strripos($str, 'eliver') != false)) {   // Если есть включение S-ummary И D-eliver-ed
            $summary[] = "Parcel " . $parcel->id . " was delivered";
            $parcel->status = 5;
          }
          else{
            $summary[] = $parcel->id . " not delivered";
          }
        }else{
          if ($company == '') $summary[] = $parcel->id . " has unknown shipping company";
          else $summary[] = $parcel->id . " has status = 5";
        }
        $parcel->save();
      }
      $html = SHD::file_get_html('https://openexchangerates.org/api/latest.json?app_id=a405ef00381748dd895923fb7008ea34', null, null, 1, 1);

     // Yii::$app->config->set('USD_CAD', '3333');
    //  Yii::$app->config->add(((array)((array)json_decode('{'.$html))['rates']));
     // var_dump(Yii::$app->config->get('rates'));
      //$arr = json_decode('{'.$html);
     // var_dump(array_key_exists ('rates',$arr));
   //   $arr = array ('USPS/9405509699937475900484','USPS/9405509699938333870260','USPS/9407809699939814166833',
  //                  'UPS/1Z4008YY4291160859','UPS/1ZW258314248802240','UPS/1Z2A37W90324146148',
    //                'fedex/786083077470','fedex/786061718512','fedex/786043744820');
      return $this->render('index', [
       'summary' => $summary,
        'cash' => ((array)((array)json_decode('{'.$html))['rates']),
        'currentInBD' => Yii::$app->config->get('USD_CAD'),
      ]);
    }
}
