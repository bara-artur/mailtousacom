<?php

namespace app\modules\claim\models;

use Yii;

/**
 * This is the model class for table "claim".
 *
 * @property int $id
 * @property int $user_id
 * @property int $subject
 * @property string $text
 * @property int $status
 * @property int $created
 */
class Claim extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'claim';
    }

    public function beforeSave($insert)
    {
      if (parent::beforeSave($insert)) {

        if($this->isNewRecord) {
          $this->created=time();
          $this->user_id=Yii::$app->user->getId();
        }
        return true;
      }
      return false;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'subject', 'status', 'created'], 'integer'],
            [['subject', 'text'], 'required'],
            [['text'], 'string', 'max' => 500],
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
            'subject' => 'Subject',
            'text' => 'Text',
            'status' => 'Status',
            'created' => 'Created',
        ];
    }
}
