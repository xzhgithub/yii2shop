<?php
namespace frontend\controllers;

use frontend\models\Address;
use frontend\models\Cart;
use frontend\models\Goods;
use frontend\models\Locations;
use frontend\models\Member;
use frontend\models\Order;
use frontend\models\OrderGoods;
use yii\db\Exception;
use yii\web\Controller;

class Cart2Controller extends Controller{

    public $layout='cart2';

    //送货方式
    private static $deliveris=[['delivery_id'=>1,'delivery_name'=>'普通快递送货上门','delivery_price'=>'10.00'],
                              ['delivery_id'=>2,'delivery_name'=>'特快专递','delivery_price'=>'20.00'],
                              ['delivery_id'=>3,'delivery_name'=>'加急快递送货上门','delivery_price'=>'40.00'],
                              ['delivery_id'=>4,'delivery_name'=>'平邮','delivery_price'=>'5.00'],
                                ];
    //支付方式
    private static $payments=[
        ['payment_id'=>1,'payment_name'=>'货到付款','payment_intro'=>'送货上门后再收款，支持现金、POS机刷卡、支票支付'],
        ['payment_id'=>2,'payment_name'=>'在线支付','payment_intro'=>'即时到帐，支持绝大数银行借记卡及部分银行信用卡'],
        ['payment_id'=>3,'payment_name'=>'上门自提','payment_intro'=>'自提时付款，支持现金、POS刷卡、支票支付'],
        ['payment_id'=>4,'payment_name'=>'邮局汇款','payment_intro'=>'通过快钱平台收款 汇款后1-3个工作日到账'],
    ];

    //订单信息
    public function actionFlow(){
        //获取该用户的id
        $member_id=\Yii::$app->user->identity->getId();
        //从数据库地址表读取该用户的地址信息
        $addresses=Address::find()->andWhere(['member_id'=>$member_id,'status'=>[1,3]])->all();
//        var_dump($addresses);exit;
        $address=[];
        //获取地址信息
        foreach($addresses as $addre){
            $addre->province=Locations::findOne(['id'=>$addre->province])->name;
            $addre->city=Locations::findOne(['id'=>$addre->city])->name;
            $addre->county=Locations::findOne(['id'=>$addre->county])->name;
            $address[]=$addre;

        }
        //获取该用户的订单信息
        $carts=Cart::find()->andWhere(['member_id'=>$member_id])->andWhere(['status'=>1])->all();
        //根据订单信息，获取商品的详细信息
        $models=[];
        foreach($carts as $cart){
            $goods=Goods::findOne(['id'=>$cart->goods_id,'status'=>1]);
            //判断该商品是否存在
            if(!$goods){
                continue;
            }
            $goods=$goods->attributes;//获取对象的属性（转换成数组）
            $goods['amount']=$cart->amount;
            $models[]=$goods;

        }


        return $this->render('flow',['address'=>$address,'deliveris'=>self::$deliveris,'payments'=>self::$payments,'models'=>$models]);
    }


    //根据送货方式id获取费用
    public function actionDelivery(){
        $delivery_id=\Yii::$app->request->post('delivery_id');
        foreach(self::$deliveris as $delivery){
            if($delivery['delivery_id']==$delivery_id){
                $money=$delivery['delivery_price'];
            }
        }
        echo $money;

    }

    //添加订单数据
    public function actionAdd(){

        //获取参数
        //地址id
        $address_id=\Yii::$app->request->post('address_id');
        //送货方式id
        $delivery_id=\Yii::$app->request->post('delivery_id');
        //支付方式id
        $payment_id=\Yii::$app->request->post('pay');
        //总金额
        $total=\Yii::$app->request->post('money');
        //用户id
        $member_id=\Yii::$app->user->identity->getId();

        //获取地址信息
        $address=Address::findOne(['id'=>$address_id]);
        //将地址的省、市、县，转换成字符串
        $address->province=Locations::findOne(['id'=>$address->province])->name;
        $address->city=Locations::findOne(['id'=>$address->city])->name;
        $address->county=Locations::findOne(['id'=>$address->county])->name;

        //获取配送方式信息
        $delivery2='';
        foreach(self::$deliveris as $k=>$delivery){
            if($delivery['delivery_id']==$delivery_id){
                $delivery2=self::$deliveris[$k];
                break;
            }
        }

        //获取支付方式信息
        $payment2='';
        foreach(self::$payments as $k=>$payment){

            if($payment['payment_id']==$payment_id){

                $payment2=self::$payments[$k];
                break;
            }
        }

//        var_dump($payment2,$delivery2);exit;

        //实例化，保存
        $order=new Order();
        $order->member_id=$member_id;
        $order->name=$address->username;
        $order->province=$address->province;
        $order->city=$address->city;
        $order->area=$address->county;
        $order->address=$address->address;
        $order->tel=$address->tel;
        $order->delivery_id=$delivery_id;
        $order->delivery_name=$delivery2['delivery_name'];
        $order->delivery_price=$delivery2['delivery_price'];
        $order->payment_id=$payment_id;
        $order->payment_name=$payment2['payment_name'];
        $order->total=$total;
        $order->status=1;
        $order->create_time=time();


        //开启事务
        $transaction=\Yii::$app->db->beginTransaction();
        try{

            //保存
            if(!$order->save()){
                throw new Exception('订单保存失败!');
            }

            //将订单商品信息保存
            //从购物车获取后，清空购物车
            //获取该用户的订单信息
            $carts=Cart::find()->andWhere(['member_id'=>$member_id])->andWhere(['status'=>1])->all();
            //根据订单信息，获取商品的详细信息
            $models=[];
            foreach($carts as $cart){
                $goods=Goods::findOne(['id'=>$cart->goods_id,'status'=>1]);
//                var_dump($goods,$cart->goods_id);
                //判断该商品是否存在
                if(!$goods){
                    throw new Exception('该商品不存在');
                }

                //判断库存
                if($goods->stock<$cart->amount){
                    throw new Exception($goods->name.'库存不足');
                }

                //减少商品库存
                $goods->stock-=$cart->amount;
                //保存
                if(!$goods->save()){
                    throw new Exception($goods->name.'减少商品库存保存失败');
                }

                //获取该商品的信息，保存到订单商品详情表
                $goods=$goods->attributes;//获取对象的属性（转换成数组）
                $goods['amount']=$cart->amount;
                $models[]=$goods;

                //删除购物车的该商品
                $cart->status=0;
                if(!$cart->save()){
                    throw new Exception('删除购物车的该商品保存失败！');
                }

            }

            //添加订单商品
            foreach($models as $good){
                $ordergoods=new OrderGoods();
                $ordergoods->order_id=$order->id;
                $ordergoods->goods_id=$good['id'];
                $ordergoods->goods_name=$good['name'];
                $ordergoods->logo=$good['logo'];
                $ordergoods->price=$good['shop_price'];
                $ordergoods->amount=$good['amount'];
                $ordergoods->total=$good['amount']*$good['shop_price'];
                //保存
                if(!$ordergoods->save()){
                    throw new Exception('订单商品保存失败！');
                }
            }
            //提交
            $transaction->commit();

        }catch (Exception $e){
            //回滚
            $transaction->rollBack();
            echo $e->getMessage();

        }
      return $this->redirect(['order/index']);

    }


}
