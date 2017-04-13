<?php

namespace app\modules\config\models;

use Yii;

/**
 * This is the model class for table "config".
 *
 * @property integer $id
 * @property string $param
 * @property string $value
 * @property string $default
 * @property string $label
 * @property string $type
 * @property integer $updated
 */
class Config extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'config';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['param', 'value', 'default', 'label', 'type'], 'required'],
            [['updated'], 'integer'],
            [['param'], 'string', 'max' => 128],
            [['value', 'default', 'label'], 'string', 'max' => 255],
            [['type'], 'string', 'max' => 20],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'param' => 'Param',
            'value' => 'Value',
            'default' => 'Default',
            'label' => 'Label',
            'type' => 'Type',
            'updated' => 'Updated',
        ];
    }

  public function beforeSave($insert){
    $this->updated=time();
    return true;
  }
}
