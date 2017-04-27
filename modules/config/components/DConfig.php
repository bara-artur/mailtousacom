<?php
/*
 * Ознакомится с https://github.com/johnitvn/yii2-settings/blob/master/src/components/Settings.php
 */
namespace app\modules\config\components;

use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;
use app\modules\config\models\Config;

class DConfig extends Component
{
  public $cache = 0;
  public $dependency = null;

  protected $data = array();

  public function init()
  {
    $db = $this->getDbConnection();

    $items = $db->createCommand('SELECT * FROM {{config}}')->queryAll();

    foreach ($items as $item)
    {
      if ($item['param'])
        $this->data[$item['param']] = $item['value'] === '' ?  $item['default'] : $item['value'];
    }

    parent::init();
  }

  public function get($key)
  {
    if (array_key_exists($key, $this->data))
      return $this->data[$key];
    else
      throw new \yii\web\ForbiddenHttpException('Undefined parameter ' . $key);
  }

  public function set($key, $value)
  {
    $model = Config::find()->where(['param'=>$key])->one();
    if (!$model)
      throw new \yii\web\ForbiddenHttpException('Undefined parameter ' . $key);

    $model->value = $value;

    if ($model->save(false)) // для работы из консоли
      $this->data[$key] = $value;
    Yii::$app->cache->flush();

  }

  public function add($params)
  {
    if (isset($params[0]) && is_array($params[0]))
    {
      foreach ($params as $item)
        $this->createParameter($item);
    }
    elseif ($params)
      $this->createParameter($params);
    Yii::$app->cache->flush();
  }

  public function delete($key)
  {
    if (is_array($key))
    {
      foreach ($key as $item)
        $this->removeParameter($item);
    }
    elseif ($key)
      $this->removeParameter($key);
    Yii::$app->cache->flush();
  }

  protected function getDbConnection()
  {
    //if ($this->cache)
    //  $db = Yii::$app->db->cache($this->cache);
    //else
      $db = Yii::$app->db;

    return $db;
  }

  protected function createParameter($param)
  {
    if (!empty($param['param']))
    {
      $model = Config::find()->where(['param'=>$param['param']])->one();
      if ($model === null)
        $model = new Config();

      $model->param = $param['param'];
      $model->label = isset($param['label']) ? $param['label'] : $param['param'];
      $model->value = isset($param['value']) ? $param['value'] : '';
      $model->default = isset($param['default']) ? $param['default'] : '';
      $model->type = isset($param['type']) ? $param['type'] : 'string';

      $model->save();
      Yii::$app->cache->flush();
    }
  }

  protected function removeParameter($key)
  {
    if (!empty($key))
    {
      $model = Config::find()->where(['param'=>$key])->one();
      if ($model)
        $model->delete();
      Yii::$app->cache->flush();
    }
  }
}