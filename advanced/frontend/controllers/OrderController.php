<?php

namespace frontend\controllers;

use frontend\models\Order;
use frontend\models\OrderGoods;

class OrderController extends \yii\web\Controller
{
    public $layout='list';
    public function actionIndex()
    {
        if(\Yii::$app->user->isGuest){
            return $this->redirect(['user/login']);
        }
        $status=[0=>'已取消',1=>'待付款',2=>'待发货',3=>'待收货',4=>'完成'];

        $models=Order::findAll(['member_id'=>\Yii::$app->user->identity->getId()]);

        return $this->render('index',['models'=>$models,'status'=>$status]);
    }



}
