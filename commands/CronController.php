<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use Yii;
use yii\console\Controller;
use app\modules\orderElement\models\OrderElement;
use keltstr\simplehtmldom\SimpleHTMLDom as SHD;
use app\modules\config\components\DConfig;

/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class CronController extends Controller
{
  /**
   * This command echoes what you have entered as the message.
   * @param string $message the message to be echoed.
   */
  public function actionIndex($message = 'hello world')
  {
    echo $message . "\n";
  }

  public function actionRefresh($message = 'hello world')
  {
    $data=OrderElement::find()->orderBy(['cron_refresh' => SORT_ASC])->limit(10)->all(); // берем 10 посылок (надо будет исключить доставленные)

    foreach ($data as $parcel){
      $parcel->cron_refresh = time();          // записываем последнее время обновления
      $company = OrderElement::GetShippingCarrier($parcel->track_number);
      if (($company != '')&&($parcel->status!=5)) {   // если определили транспортную компанию и ещё не доставлена
        $html = SHD::file_get_html('https://trackingshipment.net/' .$company.'/' . $parcel->track_number, null, null, 1, 1); // дружественный сервис просмотра состояний посылок
        $str = $html->find('.output-info p', 0)->innertext; // берем содержимое первого абзаца у тэга с классом output_info
        if ((strripos($str, 'ummary:') != false) && (strripos($str, 'eliver') != false)) {   // Если есть включение S-ummary И D-eliver-ed
          echo "Parcel " . $parcel->id . " was delivered".PHP_EOL;
          $parcel->status = 5;
        }
        else{
          echo $parcel->id . " not delivered";
        }
      }else{
        if ($company == '') echo $parcel->id . " has unknown shipping company".PHP_EOL;
        else echo $parcel->id . " has status = 5".PHP_EOL;
      }
      $parcel->save();
    }
    //   $arr = array ('USPS/9405509699937475900484','USPS/9405509699938333870260','USPS/9407809699939814166833',
    //                  'UPS/1Z4008YY4291160859','UPS/1ZW258314248802240','UPS/1Z2A37W90324146148',
    //                'fedex/786083077470','fedex/786061718512','fedex/786043744820');
  }

  public function actionExchange($message = 'hello world')
  {
    $html = SHD::file_get_html('https://openexchangerates.org/api/latest.json?app_id=a405ef00381748dd895923fb7008ea34', null, null, 1, 1);
    $rate = ((array)((array)json_decode('{'.$html))['rates'])['CAD'];
    echo 'Exange rate      : 1[USD]= '.$rate.PHP_EOL;
    $rate = $rate + (($rate*5)/100);
    echo 'Exange rate + 5% : 1[USD]= '.$rate.PHP_EOL;
    $a = new DConfig();
    $a->set('USD_CAD',$rate);
    //Yii::$app->config->set('USD_CAD',$rate);
  }
}
