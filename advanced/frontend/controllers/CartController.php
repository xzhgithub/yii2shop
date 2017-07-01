<?php
namespace frontend\controllers;

use frontend\models\Cart;
use frontend\models\Goods;
use yii\web\Controller;
use yii\web\Cookie;
use yii\web\NotFoundHttpException;

class CartController extends Controller{

    public $layout='cart';
    //添加商品到购物车
    public function actionAdd(){
        //获取商品的id和数量
        $goods_id=\Yii::$app->request->post('goods_id');
        $amount=\Yii::$app->request->post('amount');

        //判断该商品是否存在
        if(!Goods::findOne(['id'=>$goods_id])){
            throw new NotFoundHttpException('该商品不存在');
        }

        //判断是否登陆
        if(\Yii::$app->user->isGuest){//未登录

            //先获取cookie中已有的商品数据
            $cookies=\Yii::$app->request->cookies;//实例化可读的cookie
            $cookie=$cookies->get('cart');//获取
            //判断cookie中是否有数据
            if($cookie==null){//没有数据
                $cart=[];
            }else{
                //有数据,反序列化
                $cart=unserialize($cookie->value);
            }

            //将商品保存到cookie
            $cookies=\Yii::$app->response->cookies;//实例化可写的cookie

            //判断cookie中是否有该商品
            if(array_key_exists($goods_id,$cart)){
                //cookie中有该商品，只增加数量
                $cart[$goods_id]+=$amount;
            }else{
                //没有该商品，添加一条商品数据
                $cart[$goods_id]=$amount;
            }
//            $cart=[$goods_id=>$amount];//将商品id和数量以键值对的形式保存到数组
            $cookie=new Cookie([//实例化要添加的cookie对象
                'name'=>'cart',
                'value'=>serialize($cart),//序列化value值
                'expire'=>time()+3600,
            ]);

            $cookies->add($cookie);//添加到cookie
        }else{
            //已登陆

            //获取用户id
            $member_id=\Yii::$app->user->identity->getId();
            //添加时如果有该商品，就只增加数量
            $goods=Cart::findOne(['goods_id'=>$goods_id,'member_id'=>$member_id,'status'=>1]);

            if($goods){
                $goods->amount+=$amount;
                $goods->save();
            }else{
                //将提交过来的数据添加到数据表
                $model=new Cart();
                $model->goods_id=$goods_id;
                $model->amount=$amount;
                $model->status=1;
                $model->member_id=$member_id;
                $model->save();
            }
        }
        return $this->redirect(['cart/cart']);
    }

    //展示购物车商品
    public function actionCart(){
        $models=[];
        //判断是否已登陆
        if(\Yii::$app->user->isGuest){
            //未登陆
            //从cookie中获取商品信息
            $cookies=\Yii::$app->request->cookies;//实例化可读的cookie
            $cookie=$cookies->get('cart');//获取
            //判断cookie中是否有数据
            if($cookie==null){
                //没有数据
                $cart=[];
            }else{
                //有数据
                $cart=unserialize($cookie->value);
            }

            //根据cookie中的商品id和数量

            //循环获取商品数据
            foreach($cart as $goods_id=>$amount){
                $goods=Goods::findOne(['id'=>$goods_id])->attributes;
                $goods['amount']=$amount;
                $models[]=$goods;
            }


        }else{

            //已登陆,从数据库读取
            $member_id=\Yii::$app->user->identity->getId();
            $carts=Cart::find()->andWhere(['status'=>1])->andWhere(['member_id'=>$member_id])->all();

            //循环获取商品数据
            foreach($carts as $cart){
                $goods=Goods::findOne(['id'=>$cart->goods_id,'status'=>1]);
                if(!$goods){
                    //商品不存在，跳出本次循环
                    continue;
                }
                $goods=$goods->attributes;
                $goods['amount']=$cart->amount;
                $models[]=$goods;
            }

        }

        return $this->render('cart',['models'=>$models]);

    }

    //修改商品数据，和删除
    public function actionUpdate(){
        //获取商品的id和数量
        $goods_id=\Yii::$app->request->post('goods_id');
        $amount=\Yii::$app->request->post('amount');

        //判断该商品是否存在
        if(!Goods::findOne(['id'=>$goods_id])){
            throw new NotFoundHttpException('该商品不存在');
        }


        //判断是否登陆
        if(\Yii::$app->user->isGuest){//未登录

            //先获取cookie中已有的商品数据
            $cookies=\Yii::$app->request->cookies;//实例化可读的cookie
            $cookie=$cookies->get('cart');//获取
            //判断cookie中是否有数据
            if($cookie==null){//没有数据
                $cart=[];
            }else{
                //有数据,反序列化
                $cart=unserialize($cookie->value);
            }

            $cookies=\Yii::$app->response->cookies;//实例化可写的cookie

            //判断amount(amount=0时删除该商品)
            if($amount){
                //cookie中有该商品，只增加数量
                $cart[$goods_id]=$amount;
            }else{
                if(array_key_exists($goods_id,$cart)) unset($cart[$goods_id]);
            }

//            $cart=[$goods_id=>$amount];//将商品id和数量以键值对的形式保存到数组
            $cookie=new Cookie([//实例化要添加的cookie对象
                'name'=>'cart',
                'value'=>serialize($cart),//序列化value值
            ]);

            $cookies->add($cookie);//添加到cookie
        }else{
            //已登陆
            //获取用户id
            $member_id=\Yii::$app->user->identity->getId();
            //找到该条商品
            $goods=Cart::findOne(['goods_id'=>$goods_id,'member_id'=>$member_id,'status'=>1]);
            if($goods){//判断购物车有该商品
                if($amount){//判断修改，还是删除（amount=0时删除）
                    //修改数量
                    $goods->amount=$amount;
                    $goods->save();
                }else{
                    //删除该商品
                    $goods->status=0;
                    $goods->save();
                }
            }
        }
       return $this->redirect(['cart/cart']);

    }


}
