<?php

namespace app\modules\orderInclude\models;

use app\modules\order\models\Order;
use app\modules\address\models\Address;
use \yii\web\Response;
use yii\helpers\Html;
use Yii;
use app\modules\logs\models\Log;

/**
 * This is the model class for table "order_include".
 *
 * @property integer $id
 * @property integer $order_id
 * @property string $name
 * @property double $price
 * @property integer $weight
 * @property integer $quantity
 */
class OrderInclude extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'order_include';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id', 'name', 'price', 'country', 'quantity'], 'required'],
            [['order_id', 'quantity'], 'integer'],
            [['price'], 'number'],
            [['name'], 'string', 'max' => 60],
            [['country'], 'string', 'max' => 64],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_id' => 'Order ID',
            'name' => 'Name',
            'price' => 'Price',
            'country' => 'Country',
            'quantity' => 'Quantity',
        ];
    }

  /**
   * Lists all OrderInclude models.
   * @return mixed
   */
  public static function createOrder($user,$this_){
    if (Yii::$app->user->can('userManager')&&($user!=0)) {
      $user_id = $user;
    }
    else {
      $user_id = Yii::$app->user->id;
    }
    $request = Yii::$app->request;
    $emptyOrder = Order::find()->andWhere(['user_id'=>$user_id])->andWhere(['el_group'=>null])->one();
    if ($emptyOrder!=null) {
      return $this_->redirect('/orderInclude/create-order/'.$emptyOrder['id']);
    }
    // actionCreateOrder2
     /* ->select(['`order_element`.`order_id`','`order`.`id`'])
      ->leftJoin('order_element', '`order`.`id` = `order_element`.`order_id`')
      ->where(['user_id'=>$user_id,'order_id'=>null])
      ->asArray()
      ->all();

    if(count($orderTable)>0){
      foreach ($orderTable as $order){
        if ($order['id']!=null){
          if($request->isAjax){
            $this_->redirect('/orderInclude/create-order/' . $order['id']);
            return "Redirecting to create an order";
          }else {
            return $this_->redirect('/orderInclude/create-order/' . $order['id']);
          }
        }
      }
    }
*/
    $request = Yii::$app->request;
    if(!Yii::$app->user->isGuest) {
      $address = Address::find()->where('user_id = :id', [':id' => $user_id])->one();
      if($address) {
        $last_order = Order::find()->where('user_id = :id', [':id' => $user_id])->orderBy('created_at DESC')->one();
        $address_id = $address->id;
        $model = new Order();
        $model->user_id = $user_id;
        $model->created_at = time();
        if ($model->save()) {
          $log = new Log;
          $log->createLog($model->user_id, $model->id, "Draft");

          if ($request->isAjax) {
            $this_->redirect('/orderInclude/create-order/' . $model->id);
            return "Redirecting to create an order";
          } else {
            return $this_->redirect('/orderInclude/create-order/' . $model->id);
          }
        }
      }
      //return ddd($model);
    }

    if(Yii::$app->user->isGuest){
      Yii::$app
        ->getSession()
        ->setFlash(
          'error',
          'You must login.'
        );
      return $this_->redirect('/');
    }else {
      Yii::$app
        ->getSession()
        ->setFlash(
          'error',
          'An error has occurred. Try to create order again.'
        );

      if($request->isAjax){
        Yii::$app->response->format = Response::FORMAT_JSON;
        //$this_->redirect('address/create-order-billing');
        return [
          'title' => "This user does not have a billing address",
          'content' => "Do you want to create a billing address for the user?",
          'footer'=>
            Html::button('Close',['class'=>'btn btn-default pull-left reload_on_click','data-dismiss'=>"modal"]).
            Html::a('Billing address', '/user/admin/billing?id='.$user, [
              'title' => '',
              'class'=>'btn btn-success',
              'role'=>'modal-remote',
              'data-pjax'=>0,
            ])
        ];

      }else {
        return $this_->redirect('address/create-order-billing');
      }
    }
  }
}
