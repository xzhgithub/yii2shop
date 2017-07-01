<?php

namespace backend\controllers;

use backend\models\Order;

class OrderController extends \yii\web\Controller
{
    //订单列表
    public function actionIndex()
    {
        $status=[0=>'已取消',1=>'待付款',2=>'待发货',3=>'已发货',4=>'完成'];
        $orders=Order::find()->all();
        return $this->render('index',['orders'=>$orders,'status'=>$status]);
    }

    //发货 0已取消1待付款2待发货3待收货4完成
    public function actionDeliver(){
        $order_id=\Yii::$app->request->post('order_id');
        $order=Order::findOne(['id'=>$order_id]);
        $order->status=3;
        $order->save();
    }

}
