<?php
namespace frontend\controllers;

use EasyWeChat\Message\News;
use frontend\models\Goods;
use frontend\models\Member;
use frontend\models\Order;
use yii\helpers\Url;
use yii\web\Controller;
use EasyWeChat\Foundation\Application;
use frontend\models\Address;

class WechatController extends Controller{
    //关闭csrf验证
    public $enableCsrfValidation = false;

    public function actionIndex(){
        //验证
//        $app = new Application(\Yii::$app->params['wechat']);
//        $response = $app->server->serve();
//        //将响应输出
//        $response->send(); // Laravel 里请使用：return $response;

        $app = new Application(\Yii::$app->params['wechat']);
        $app->server->setMessageHandler(function ($message) {
            if($message->MsgType=='event'){
                    if ($message->Event == 'CLICK') {
                        if ($message->EventKey == 'zxhd') {
                            $goods = Goods::find()->limit(5)->all();
                            $models = [];
                            foreach ($goods as $good) {
                                $models[] = new News([
                                    'title' => $good->name,
                                    'description' => '图文信息的描述...',
                                    'url' => 'http://xzh.fan-0.cn/goods/goodsintro?goods_id='.$good->id,
                                    'image' => $good->logo,
                                ]);

                            }
                            return $models;

                        }
                    }
            }

            if($message->MsgType=='text'){
                if($message->Content='帮助'){
                    return "您可以发送 优惠、解除绑定 等信息";
                }elseif($message->Content='优惠'){
                    $goods = Goods::find()->limit(5)->all();
                    $models = [];
                    foreach ($goods as $good) {
                        $models[] = new News([
                            'title' => $good->name,
                            'description' => '图文信息的描述...',
                            'url' => 'http://xzh.fan-0.cn/goods/goodsintro?goods_id='.$good->id,
                            'image' => $good->logo,
                        ]);

                    }
                    return $models;
                }elseif($message->Content='解除绑定'){

                }
            }

        });
        $response = $app->server->serve();
// 将响应输出
        $response->send(); // Laravel 里请使用：return $response;



   }

    //设置菜单
    public function actionSetMenu()
    {
        $app = new Application(\Yii::$app->params['wechat']);
        $menu = $app->menu;
        $buttons = [
            [
                "type" => "click",
                "name" => "促销商品",
                "key"  => "zxhd"
            ],
            [
                "type" => "view",
                "name" => "在线商城",
                "url"  => 'http://xzh.fan-0.cn/goodscategory/index',
            ],
            [
                "name" => "个人中心",
                "sub_button" => [
                    [
                        "type" => "view",
                        "name" => "绑定账户",
                        "url"  => Url::to(['wechat/login'],true)
                    ],
                    [
                        "type" => "view",
                        "name" => "我的订单",
                        "url"  => Url::to(['wechat/order'],true)
                    ],
                    [
                        "type" => "view",
                        "name" => "收货地址",
                        "url" => Url::to(['wechat/address'],true)
                    ],
                    [
                        "type" => "view",
                        "name" => "修改密码",
                        "url" => Url::to(['wechat/edit'],true)
                    ],
                ],
            ],
        ];
        $menu->add($buttons);
        //获取已设置的菜单（查询菜单）
        $menus = $menu->all();
        var_dump($menus);
    }

    public function actionTest(){
        $goods = Goods::find()->limit(5)->all();
        $models = [];
        foreach ($goods as $good) {
            $models[] = new News([
                'title' => $good->name,
                'description' => '图文信息的描述...',
                'url' => 'http://xzh.fan-0.cn/goods/goodsintro?goods_id='.$good->id,
                'image' => $good->logo,
            ]);

        }
        var_dump($models);
    }



    //我的订单
    public function actionOrder()
    {
        //openid
        $openid = \Yii::$app->session->get('openid');
        if($openid == null){
            //获取用户的基本信息（openid），需要通过微信网页授权
            \Yii::$app->session->set('redirect',\Yii::$app->controller->action->uniqueId);
            //echo 'wechat-user';
            $app = new Application(\Yii::$app->params['wechat']);
            //发起网页授权
            $response = $app->oauth->scopes(['snsapi_base'])
                ->redirect();
            $response->send();
        }
        //var_dump($openid);
        //通过openid获取账号
        $member = Member::findOne(['openid'=>$openid]);
        if($member == null){
            //该openid没有绑定任何账户
            //引导用户绑定账户
            return $this->redirect(['wechat/login']);
        }else{
            //已绑定账户
            $orders = Order::find()->where(['member_id'=>$member->id])->andWhere(['!=','status',0])->all();
            return $this->render('order',['orders'=>$orders]);
        }
    }


    //授权回调页
    public function actionCallback()
    {
        $app = new Application(\Yii::$app->params['wechat']);
        $user = $app->oauth->user();
        // $user 可以用的方法:
        // $user->getId();  // 对应微信的 OPENID
        // $user->getNickname(); // 对应微信的 nickname
        // $user->getName(); // 对应微信的 nickname
        // $user->getAvatar(); // 头像网址
        // $user->getOriginal(); // 原始API返回的结果
        // $user->getToken(); // access_token， 比如用于地址共享时使用
//        var_dump($user->getId());
        //将openid放入session
        \Yii::$app->session->set('openid',$user->getId());
        return $this->redirect([\Yii::$app->session->get('redirect')]);

    }


    //绑定用户账号   将openid和用户账号绑定
    public function actionLogin()
    {
        $openid = \Yii::$app->session->get('openid');
        if($openid == null){
            //获取用户的基本信息（openid），需要通过微信网页授权
            \Yii::$app->session->set('redirect',\Yii::$app->controller->action->uniqueId);
            //echo 'wechat-user';
            $app = new Application(\Yii::$app->params['wechat']);
            //发起网页授权
            $response = $app->oauth->scopes(['snsapi_base'])
                ->redirect();
            $response->send();
        }

        //让用户登录，如果登录成功，将openid写入当前登录账户
        $request = \Yii::$app->request;
        if(\Yii::$app->request->isPost){
            $user = Member::findOne(['username'=>$request->post('username')]);
            if($user && \Yii::$app->security->validatePassword($request->post('password'),$user->password_hash)){
                \Yii::$app->user->login($user);
                //如果登录成功，将openid写入当前登录账户
                Member::updateAll(['openid'=>$openid],'id='.$user->id);
                if(\Yii::$app->session->get('redirect')) return $this->redirect([\Yii::$app->session->get('redirect')]);
                echo '绑定成功';exit;
            }else{
                echo '登录失败';exit;
            }
        }

        return $this->renderPartial('login');
    }


    //收货地址
    public function actionAddress(){
        $openid = \Yii::$app->session->get('openid');
        if($openid == null){
            //获取用户的基本信息（openid），需要通过微信网页授权
            \Yii::$app->session->set('redirect',\Yii::$app->controller->action->uniqueId);
            //echo 'wechat-user';
            $app = new Application(\Yii::$app->params['wechat']);
            //发起网页授权
            $response = $app->oauth->scopes(['snsapi_base'])
                ->redirect();
            $response->send();
        }
        //var_dump($openid);
        //通过openid获取账号
        $member = Member::findOne(['openid'=>$openid]);
        if($member == null){
            //该openid没有绑定任何账户
            //引导用户绑定账户
            return $this->redirect(['wechat/login']);
        }else{
            //已绑定账户
            $address = Address::find()->where(['member_id'=>$member->id])->andWhere(['!=','status',0])->all();
            return $this->render('address',['address'=>$address]);
        }
    }

    //修改密码
    public function actionEdit(){
        $openid = \Yii::$app->session->get('openid');
        if($openid == null){
            //获取用户的基本信息（openid），需要通过微信网页授权
            \Yii::$app->session->set('redirect',\Yii::$app->controller->action->uniqueId);
            //echo 'wechat-user';
            $app = new Application(\Yii::$app->params['wechat']);
            //发起网页授权
            $response = $app->oauth->scopes(['snsapi_base'])
                ->redirect();
            $response->send();
        }
        //var_dump($openid);
        //通过openid获取账号
        $member = Member::findOne(['openid'=>$openid]);
        if($member == null){
            //该openid没有绑定任何账户
            //引导用户绑定账户
            return $this->redirect(['wechat/login']);
        }else{
            //已绑定账户
            $request=\Yii::$app->request;
            if($request->isPost){
                $old_password=$request->post('old_password');
                $password=$request->post('password');
                $repassword=$request->post('repassword');
                if($password==$repassword&&\Yii::$app->security->validatePassword($password,$member->password_hash)){
                    $member->password_hash=\Yii::$app->security->generatePasswordHash($password);
                    $member->save();
                }else{
                    return $this->redirect(['wechat/login']);
                }

            }

            return $this->redirect(['wechat/edit']);
        }
    }



}

