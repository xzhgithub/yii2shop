<?php
namespace console\controllers;

use frontend\models\Goods;
use frontend\models\Order;
use yii\console\Controller;

class CleanorderController extends Controller{
    //定时清理未支付的订单
    public function actionClean(){

        while(1){
            //获取大于一个小时还未支付的订单
            $orders=Order::find()->andwhere(['status'=>1])->andWhere(['<','create_time',time()-3600])->all();
            if($orders){
                foreach($orders as $order){
                    //更改状态为已取消
                    $order->status=0;
                    $order->save();

                    //还原商品库存
                    foreach($order->goods as $goods){//通过关联查询获得的订单里的商品
                        //从商品表里获取该商品
                        $good=Goods::findOne(['id'=>$goods->goods_id]);
                        $good->stock+=$goods->amount;
                        $good->save();
                    }
                    echo "ID".$order->id." has been clean...\n";
                }
            }
            //间隔一秒
            sleep(1);


        }

    }
}