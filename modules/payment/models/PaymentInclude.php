<?php

namespace app\modules\payment\models;

use Yii;

/**
 * This is the model class for table "payment_include".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $element_id
 * @property integer $element_type
 * @property string $comment
 * @property integer $status
 * @property integer $create_at
 * @property double $price
 * @property double $qst
 * @property double $gst
 */
class PaymentInclude extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'payment_include';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['element_id', 'element_type', 'payment_id'], 'required'],
            [['element_id', 'element_type', 'status', 'payment_id'], 'integer'],
            [['price', 'qst', 'gst'], 'number'],
            [['comment'], 'string', 'max' => 255],
        ];
    }

    public static function getElementTypeList(){
      return [
        "parcel"
      ];
    }
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'element_id' => 'Element ID',
            'element_type' => 'Element Type',
            'comment' => 'Comment',
            'status' => 'Status',
            'payment_id' => 'payment id',
            'price' => 'Price',
            'qst' => 'Qst',
            'gst' => 'Gst',
        ];
    }

    public function generateTextStatus(){
      $lst=PaymentInclude::getElementTypeList();

      $txt='Payment for '.$lst[$this->element_type];

      if($this->status==-1){
        $txt.="<span style='color:red;'> Refusal</span>";
      }
      return $txt;
    }
}
