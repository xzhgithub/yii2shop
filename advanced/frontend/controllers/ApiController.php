<?php
namespace frontend\controllers;

use backend\models\Article;
use backend\models\ArticleCategory;
use frontend\models\Address;
use frontend\models\Brand;
use frontend\models\Cart;
use frontend\models\Goods;
use frontend\models\GoodsCategory;
use frontend\models\LoginForm;
use frontend\models\Member;
use frontend\models\Order;
use frontend\models\OrderGoods;
use yii\db\Exception;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\Response;
use yii\web\UploadedFile;

class ApiController extends Controller{


    public $enableCsrfValidation = false;

    //定义所有返回数据的格式为json格式
    public function init(){
        \Yii::$app->response->format=Response::FORMAT_JSON;
        parent::init();
    }


    //会员注册接口
    public function actionUserRegister(){
        //接收数据
        $request=\Yii::$app->request;
        if($request->isPost){
            $model=new Member();
            $model->username=$request->post('username');//用户名
            $model->password=$request->post('password');//密码
            $model->repassword=$request->post('repassword');//确认密码
            $model->email=$request->post('email');//邮箱
            $model->tel=$request->post('tel');//电话
            $model->code=$request->post('code');//图片验证码
            $model->tel_code=$request->post('tel_code');//短信验证码
            //验证
            if($model->validate()){
                $model->password_hash=\Yii::$app->security->generatePasswordHash($model->password);
                $model->status=1;
                $model->created_at=time();
                $model->save();
                return ['status'=>1,'errormsge'=>'','data'=>['success'=>true]];
            }else{
                return ['status'=>-1,'errormsge'=>$model->getErrors(),'data'=>''];
            }


        }

        return ['status'=>-1,'errormsge'=>'请求方式错误','data'=>''];
    }


    //-发送手机验证码
    public function actionSendSms()
    {
        //确保上一次发送短信间隔超过1分钟
        $tel = \Yii::$app->request->post('tel');
        if(!preg_match('/^1[34578]\d{9}$/',$tel)){
            return ['status'=>'-1','errormsge'=>'电话号码不正确'];
        }
        //检查上次发送时间是否超过1分钟
        $value = \Yii::$app->cache->get('time_tel_'.$tel);
        $s = time()-$value;
        if($s <60){
            return ['status'=>'-1','errormsge'=>'请'.(60-$s).'秒后再试'];
        }

        $code = rand(1000,9999);
        $result = \Yii::$app->sms->setNum($tel)->setParam(['code' => $code])->send();
//        $result = 1;
        if($result){
            //保存当前验证码 session  mysql  redis  不能保存到cookie
//            \Yii::$app->session->set('code',$code);
//            \Yii::$app->session->set('tel_'.$tel,$code);
            \Yii::$app->cache->set('tel_'.$tel,$code,5*60);
            \Yii::$app->cache->set('time_tel_'.$tel,time(),5*60);
            //echo 'success'.$code;
            return ['status'=>'1','errormsge'=>''];
        }else{
            return ['status'=>'-1','errormsge'=>'短信发送失败'];
        }
    }



    //获取验证码
    //http://www.yii2shop.com/api/captcha 显示验证码
    //http://www.yii2shop.com/api/captcha?refresh=1 获取新验证码图片地址(加参数refresh=1)
    //http://www.yii2shop.com/api/captcha?v=59573cbe28c58 新验证码图片地址
    public function actions()
    {
        return [
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
                'minLength'=>3,
                'maxLength'=>3,
            ],
        ];
    }

    //-文件上传
    public function actionUpload()
    {
        $img = UploadedFile::getInstanceByName('img');
        if($img){
            $fileName = '/upload/'.uniqid().'.'.$img->extension;
            $result = $img->saveAs(\Yii::getAlias('@webroot').$fileName,0);
            if($result){
                return ['status'=>'1','errormsge'=>'','data'=>$fileName];
            }
            return ['status'=>'-1','errormsge'=>$img->error];
        }
        return ['status'=>'-1','errormsge'=>'没有文件上传'];
    }



    //会员登陆接口
    public function actionLogin(){
        //接收数据
        $request=\Yii::$app->request;
        if($request->isPost){
            $model=new LoginForm();
            $model->username=$request->post('username');//用户名
            $model->password=$request->post('password');//密码
            $model->remember=$request->post('remember');//记住登陆
            //验证
            if($model->validate()){
//                var_dump(\Yii::$app->user->identity);exit;
                return ['status'=>1,'errormsge'=>'','data'=>['success'=>true]];
            }else{
                return ['status'=>-1,'errormsge'=>$model->getErrors(),'data'=>''];
            }


        }

        return ['status'=>-1,'errormsge'=>'请求方式错误','data'=>''];
    }


    //会员修改密码接口
    public function actionEditPassword(){
        //接收数据
        $request=\Yii::$app->request;
        if($request->isPost){
            $model=Member::findOne(['id'=>\Yii::$app->user->id]);
//            var_dump($model);exit;
            $old_password=$request->post('old_password');//旧密码
            $password=$request->post('password');//新密码
            //验证
            if(\Yii::$app->security->validatePassword($old_password,$model->password_hash)){

                $model->password_hash=\Yii::$app->security->generatePasswordHash($password);
                $model->save(false);
                return ['status'=>1,'errormsge'=>'','data'=>['success'=>true]];
            }else{
                return ['status'=>-1,'errormsge'=>'旧密码错误','data'=>''];
            }


        }

        return ['status'=>-1,'errormsge'=>'请求方式错误','data'=>''];
    }


    //获取当前登陆用户的登陆信息接口
    public function actionGetUserinfo(){

            $model=Member::findOne(['id'=>\Yii::$app->user->getId()]);
            return ['status'=>1,'errormsge'=>'','data'=>$model->toArray()];
    }

    //添加地址接口
    public function actionAddAddress(){
        //接收数据
        $request=\Yii::$app->request;
        if($request->isPost){

            $model=new Address();
            $model->username=$request->post('username');//收货人
            $model->province=$request->post('province');//省id
            $model->city=$request->post('city');//市id
            $model->county=$request->post('county');//区id
            $model->address=$request->post('address');//详细地址
            $model->tel=$request->post('tel');//电话
            $model->status=1;
            $model->member_id=\Yii::$app->user->getId();
           if($model->save()){
               return ['status'=>1,'errormsge'=>'','data'=>['success'=>true]];
           }


        }

        return ['status'=>-1,'errormsge'=>'请求方式错误','data'=>''];
    }

    //修改地址接口
    public function actionEditAddress(){
        //接收数据
        $request=\Yii::$app->request;
        if($request->isPost){
            $model=Address::findOne(['member_id'=>\Yii::$app->user->getId()]);
            $model->username=$request->post('username');//收货人
            $model->province=$request->post('province');//省id
            $model->city=$request->post('city');//市id
            $model->county=$request->post('county');//区id
            $model->address=$request->post('address');//详细地址
            $model->tel=$request->post('tel');//电话
            if($model->save()){
                return ['status'=>1,'errormsge'=>'','data'=>['success'=>true]];
            }


        }

        return ['status'=>-1,'errormsge'=>'请求方式错误','data'=>''];
    }

    //删除地址接口
    public function actionDelAddress(){
        //接收数据
        $request=\Yii::$app->request;
        if($request->isGet){
            $address_id=$request->get('address_id');
            $model=Address::findOne(['id'=>$address_id]);
            $model->status=0;
            if($model->save()){
                return ['status'=>1,'errormsge'=>'','data'=>['success'=>true]];
            }


        }

        return ['status'=>-1,'errormsge'=>'请求方式错误','data'=>''];
    }

    //地址列表接口
    public function actionAddressList(){

            $model=Address::find()->Where(['!=','status',0])->andWhere(['member_id'=>\Yii::$app->user->getId()])->all();

                return ['status'=>1,'errormsge'=>'','data'=>$model];
    }


    //获取所有商品分类接口
    public function actionGetAllGoodsCategory(){


        //每页显示条数
        $per_page = \Yii::$app->request->get('per_page',1);//默认2条
        //当前第几页
        $page = \Yii::$app->request->get('page',1);

        $keywords = \Yii::$app->request->get('keywords');//搜索的关键词

        $page = $page < 1?1:$page;
        $query = GoodsCategory::find();

        if($keywords){
            $query->andWhere(['like','name',$keywords]);
        }

        //总条数
        $total = $query->count();
        //获取当前页的商品数据
        $goods = $query->offset($per_page*($page-1))->limit($per_page)->asArray()->all();
        return ['status'=>'1','errormsge'=>'','data'=>[
            'total'=>$total,
            'per_page'=>$per_page,
            'page'=>$page,
            'goods'=>$goods
        ]];

    }

    //获取某分类的所有子分类
    public function actionGetSonCategory(){
        //接收数据
        $request=\Yii::$app->request;
        if($request->isGet){
            $id=$request->get('id');
            $model=GoodsCategory::find()->where(['parent_id'=>$id])->all();

            return ['status'=>1,'errormsge'=>'','data'=>$model];
        }

        return ['status'=>-1,'errormsge'=>'请求方式错误','data'=>''];
    }


    //获取某分类的父分类接口
    public function actionGetParentCategory(){
        //接收数据
        $request=\Yii::$app->request;
        if($request->isGet){
            $id=$request->get('parent_id');
            $model=GoodsCategory::findOne(['id'=>$id]);

            return ['status'=>1,'errormsge'=>'','data'=>$model];
        }

        return ['status'=>-1,'errormsge'=>'请求方式错误','data'=>''];
    }


    //获取某分类下面的所有商品接口
    public function actionGetGoodsByCategory(){
        //接收数据
        $request=\Yii::$app->request;
        if($request->isGet){
            //每页显示条数
            $per_page = \Yii::$app->request->get('per_page',10);//默认2条
            //当前第几页
            $page = \Yii::$app->request->get('page',1);

            $keywords = \Yii::$app->request->get('keywords');//搜索的关键词

            $page = $page < 1?1:$page;
            $query = Goods::find();


            $cate_id = \Yii::$app->request->get('cate_id');
            $cate = \backend\models\GoodsCategory::findOne(['id'=>$cate_id]);
            if($cate==null){
                return ['status'=>-1,'errormsge'=>'该分类不存在','data'=>''];
            }
            switch ($cate->depth){
                case 2://三级分类
                    $query->andWhere(['goods_category_id'=>$cate_id]);
                    break;
                case 1://二级分类
                    $ids = ArrayHelper::map($cate->children,'id','id');
                    $ids[$cate_id]=$cate_id;
//                    var_dump($ids);exit;
                    $query->andWhere(['in','goods_category_id',$ids]);
                    break;
                case 0;//一级分类
                    $ids = ArrayHelper::map($cate->leaves()->asArray()->all(),'id','id');
                    $ids[$cate_id]=$cate_id;
                    $query->andWhere(['in','goods_category_id',$ids]);
                    break;

            }

            if($keywords){
                $query->andWhere(['like','name',$keywords]);
            }

            //总条数
            $total = $query->count();
            //获取当前页的商品数据
            $goods = $query->offset($per_page*($page-1))->limit($per_page)->asArray()->all();
            return ['status'=>'1','errormsge'=>'','data'=>[
                'total'=>$total,
                'per_page'=>$per_page,
                'page'=>$page,
                'goods'=>$goods
            ]];
        }

        return ['status'=>-1,'errormsge'=>'请求方式错误','data'=>''];
    }


    //获取某品牌下面的所有商品接口
    public function actionGetGoodsByBrand(){
        //接收数据
        $request=\Yii::$app->request;
        if($request->isGet){
            $brand_id=$request->get('brand_id');
            //每页显示条数
            $per_page = \Yii::$app->request->get('per_page',10);//默认2条
            //当前第几页
            $page = \Yii::$app->request->get('page',1);

            $keywords = \Yii::$app->request->get('keywords');//搜索的关键词

            $page = $page < 1?1:$page;
            $query = Goods::find();

            if($keywords){
                $query->andWhere(['like','name',$keywords]);
            }

            //总条数
            $total = $query->count();
            //获取当前页的商品数据
            $goods = $query->andwhere(['brand_id'=>$brand_id])->offset($per_page*($page-1))->limit($per_page)->asArray()->all();
            return ['status'=>'1','errormsge'=>'','data'=>[
                'total'=>$total,
                'per_page'=>$per_page,
                'page'=>$page,
                'goods'=>$goods
            ]];

        }

        return ['status'=>-1,'errormsge'=>'请求方式错误','data'=>''];
    }

    //获取所有文章分类
    public function actionGetArticleCategory(){
        $request=\Yii::$app->request;
        if($request->isGet){

            $model=ArticleCategory::find()->where(['status'=>1])->all();

            return ['status'=>1,'errormsge'=>'','data'=>$model];
        }

        return ['status'=>-1,'errormsge'=>'请求方式错误','data'=>''];
    }

    //获取某分类下面的所有文章
    public function actionGetArticleByCategory(){
        $request=\Yii::$app->request;
        if($request->isGet){
            $id=$request->get('article_category_id');
            $model=Article::find()->where(['status'=>1])->andWhere(['article_category_id'=>$id])->all();

            return ['status'=>1,'errormsge'=>'','data'=>$model];
        }

        return ['status'=>-1,'errormsge'=>'请求方式错误','data'=>''];
    }


    //获取某文章所属分类
    public function actionGetCategory(){
        $request=\Yii::$app->request;
        if($request->isGet){
            $id=$request->get('article_category_id');
            $model=ArticleCategory::find()->where(['status'=>1])->andWhere(['id'=>$id])->all();

            return ['status'=>1,'errormsge'=>'','data'=>$model];
        }

        return ['status'=>-1,'errormsge'=>'请求方式错误','data'=>''];
    }


    //添加商品到购物车
    public function actionAddGoodsToCart(){
        //接收数据
        $request=\Yii::$app->request;
        if($request->isPost){

            $model=new Cart();
            $model->goods_id=$request->post('goods_id');
            $model->amount+=$request->post('amount');
            $model->member_id=\Yii::$app->user->getId();
            $model->status=1;
            if($model->validate()){
                $model->save();
                return ['status'=>1,'errormsge'=>'','data'=>['success'=>true]];

            }
            return ['status'=>-1,'errormsge'=>$model->getErrors(),'data'=>''];


        }

        return ['status'=>-1,'errormsge'=>'请求方式错误','data'=>''];
    }


    //修改购物车某商品数量
    public function actionEditGoodsAmount(){
        $request=\Yii::$app->request;
        if($request->isGet){
            $goods_id=$request->get('goods_id');
            $amount=$request->get('amount');
            $model=Cart::findOne(['status'=>1,'goods_id'=>$goods_id,'member_id'=>\Yii::$app->user->getId()]);
            if(!$model){
                return ['status'=>-1,'errormsge'=>'数据不存在','data'=>''];
            }
            $model->amount+=$amount;
            $model->save();
            return ['status'=>1,'errormsge'=>'','data'=>['success'=>true]];
        }

        return ['status'=>-1,'errormsge'=>'请求方式错误','data'=>''];
    }


    //删除购物车某商品
    public function actionDelCartOne(){
        //接收数据
        $request=\Yii::$app->request;
        if($request->isGet){
            $goods_id=$request->get('goods_id');
            $goods=Cart::findOne(['goods_id'=>$goods_id,'status'=>1,'member_id'=>\Yii::$app->user->getId()]);

            if($goods){
                $goods->status=0;
                $goods->save();
                return ['status'=>1,'errormsge'=>'','data'=>['success'=>true]];

            }
            return ['status'=>-1,'errormsge'=>'商品不存在','data'=>''];


        }

        return ['status'=>-1,'errormsge'=>'请求方式错误','data'=>''];
    }


    //清空购物车
    public function actionDelCartAll(){
        //接收数据
        $request=\Yii::$app->request;
        if($request->isGet){
            $goods=Cart::find()->where(['status'=>1])->andWhere(['member_id'=>\Yii::$app->user->getId()])->all();
            if(!$goods){

                return ['status'=>-1,'errormsge'=>'购物车没有数据','data'=>''];

            }

            foreach($goods as $good){
                $good->status=0;
                $good->save();
            }

            return ['status'=>1,'errormsge'=>'','data'=>['success'=>true]];


        }

        return ['status'=>-1,'errormsge'=>'请求方式错误','data'=>''];
    }


    //获取购物车所有商品
    public function actionGetCartAll(){
        $request=\Yii::$app->request;
        if($request->isGet){
            $goods=Cart::find()->where(['status'=>1])->andWhere(['member_id'=>\Yii::$app->user->getId()])->all();

            if(!$goods){
                return ['status'=>-1,'errormsge'=>'购物车没有商品','data'=>''];
            }

            return ['status'=>1,'errormsge'=>'','data'=>$goods];
        }

        return ['status'=>-1,'errormsge'=>'请求方式错误','data'=>''];
    }

    //提交订单接口
    public function actionSubmitOrder(){
        $request=\Yii::$app->request;
        if($request->isPost){
            $order=new Order();
            $order->member_id=\Yii::$app->user->getId();
            $order->name=$request->post('name');
            $order->province=$request->post('province');
            $order->city=$request->post('city');
            $order->area=$request->post('area');
            $order->address=$request->post('address');
            $order->tel=$request->post('tel');
            $order->delivery_id=$request->post('delivery_id');
            $order->delivery_name=$request->post('delivery_name');
            $order->delivery_price=$request->post('delivery_price');
            $order->payment_id=$request->post('payment_id');
            $order->payment_name=$request->post('payment_name');
            $order->total=$request->post('total');
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
                $carts=Cart::find()->andWhere(['member_id'=>\Yii::$app->user->getId()])->andWhere(['status'=>1])->all();
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
                return ['status'=>1,'errormsge'=>'','data'=>['success'=>true]];

            }catch (Exception $e){
                //回滚
                $transaction->rollBack();
                return ['status'=>-1,'errormsge'=>$e->getMessage(),'data'=>''];
            }
        }

        return ['status'=>-1,'errormsge'=>'请求方式错误','data'=>''];
    }

    //获取当前用户订单列表接口
    public function actionGetOrderList(){
        //接收数据
        $request=\Yii::$app->request;
        if($request->isGet){
            $orders=Order::find()->Where(['member_id'=>\Yii::$app->user->getId()])->all();
            if(!$orders){

                return ['status'=>-1,'errormsge'=>'没有订单','data'=>''];

            }

            return ['status'=>1,'errormsge'=>'','data'=>$orders];


        }

        return ['status'=>-1,'errormsge'=>'请求方式错误','data'=>''];
    }

    //取消订单
    public function actionCancelOrder(){
        //接收数据
        $request=\Yii::$app->request;
        if($request->isGet){
            $order_id=$request->get('order_id');
            $order=Order::findOne(['member_id'=>\Yii::$app->user->getId(),'id'=>$order_id]);
            if(!$order){

                return ['status'=>-1,'errormsge'=>'该订单不存在订单','data'=>''];

            }
            $order->status=0;
            $order->save();

            return ['status'=>1,'errormsge'=>'','data'=>['success'=>true]];


        }

        return ['status'=>-1,'errormsge'=>'请求方式错误','data'=>''];
    }



}