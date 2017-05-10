<?php

namespace app\modules\additional_services\models;

use Yii;

/**
 * This is the model class for table "additional_services_list".
 *
 * @property integer $id
 * @property string $name
 * @property integer $type
 * @property double $base_price
 * @property integer $dop_connection
 * @property integer $only_one
 * @property integer $active
 */
class AdditionalServicesList extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'additional_services_list';
    }

  public function typeList()
  {
    return [
      1=>'For a single parcel',
      2=>'For multiple packages',
    ];
  }

  static function connectionList()
  {
    return [
      0=>"none",
      1=>'Track number',
    ];
  }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['type', 'dop_connection', 'only_one', 'active'], 'integer'],
            [['base_price'], 'number'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'type' => 'Type',
            'base_price' => 'Base Price',
            'dop_connection' => 'Additional communication',
            'only_one' => 'Count in one parcel',
            'active' => 'Active',
        ];
    }

  /**
   * @return int
   */
  public function getTypeText(){
    $list=$this->typeList();
    if(!isset($list[$this->type])){
      return "";
    }
    return $list[$this->type];
  }
}
