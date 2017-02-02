<?php

namespace app\modules\user\components;

use app\modules\user\models\forms\LoginForm;
use app\modules\user\models\User;
use yii\base\Widget;
use yii\helpers\Url;
use Yii;

class UserWidget extends Widget
{
    public function run()
    {
        $isLogin=false;
        if(Yii::$app->user->isGuest) {
            $model = new LoginForm();
            if(Yii::$app->request->post()) {
                $model->load(Yii::$app->request->post());
                $isLogin = $model->login();
            }
            if(!$isLogin) {
                return $this->render('loginWidget', [
                    'model' => $model
                ]);
            }
        }

        $user_data=Yii::$app->user->identity->toArray();
        if(strlen($user_data['photo'])<10){
            $user_data['photo']='/img/avatar.jpg';
        }
        return $this->render('onlineWidget', $user_data);
    }

}