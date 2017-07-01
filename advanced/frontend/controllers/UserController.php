<?php

namespace frontend\controllers;
use frontend\models\Cart;
use frontend\models\LoginForm;
use frontend\models\Member;
use yii\base\Model;
use yii\web\Response;


class UserController extends \yii\web\Controller
{
    public $layout='login';



    //注册
    public function actionRegister()
    {
        $model=new Member();
        $request=\Yii::$app->request;
        if($request->isPost){
            $model->load($request->post());
            if($model->validate()){
                //密码加盐加密
                $model->password_hash=\Yii::$app->security->generatePasswordHash($model->password);
                //创建时间
                $model->created_at=time();
                //状态
                $model->status=1;
                if($model->save(false)){
                    //提示、跳转
                    \Yii::$app->session->setFlash('success','注册成功');
                    return $this->redirect(['user/login']);
                }
            }else{
                var_dump($model->getErrors());
                exit;
            }
        }

        return $this->render('register',['model'=>$model]);
    }

    //登陆
    public function actionLogin(){

        $model=new LoginForm();

        $request=\Yii::$app->request;
        if($request->isPost){

            $model->load($request->post());

            if($model->validate()){
                //登陆成功，判断cookie里面是否有商品信息
                $cookies=\Yii::$app->request->cookies;
                $cookie=$cookies->get('cart');
                //判断cookie中是否有数据
                if($cookie!=null){
                    //cookie中有商品信息，将其保存到数据库，并将cookie中的商品删除
                    $cart=unserialize($cookie->value);
                    $cart_model=new Cart();
                    //自定义方法添加到数据表
                    if($cart_model->addCart($cart)){//添加成功
                        //将cookie中的商品信息删除
                        $cookies=\Yii::$app->response->cookies;
                        $cookies->remove('cart');
                    }
                }


                //保存登陆时间
                $user = Member::findOne(['username' => $model->username]);
                $user->last_login_time = time();
                //保存登陆ip
                $user->last_login_ip=$_SERVER["REMOTE_ADDR"];

                //保存
                if ($user->save(false)) {
                    //提示、跳转
                    \Yii::$app->session->setFlash('success', '登陆成功');
                    return $this->redirect(['goodscategory/index']);
                }else{
                    var_dump($user->getErrors());
                    exit;
                }
            }else{
                var_dump($model->getErrors());
                exit;
            }
        }

        return $this->render('login',['model'=>$model]);

    }

    //注销
    public function actionLogout(){
        \Yii::$app->user->logout();
        return $this->redirect(['user/login']);

    }


    //验证码
    public function actions()
    {
        return [
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
                'minLength'=>4,
                'maxLength'=>4,
            ],
        ];
    }

    //发送短信
    public function actionSend(){

        $tel=\Yii::$app->request->post('tel');
        //判断
        if(!preg_match('/^1[34578]\d{9}$/',$tel)){
            echo '电话号码不正确';
            exit;
        }
        //使用封装好的主键
        $code=rand(1000,9999);
        $result=\Yii::$app->sms->setNum($tel)->setParam(['code'=>$code])->send();
        if($result){
            //将验证码保存到缓存 //保存当前验证码 session  mysql  redis  不能保存到cookie
            \Yii::$app->cache->set('tel'.$tel,$code,5*60);//保存5分钟

            echo 'success';
        }else{
            echo '短信发送失败';
        }
    }

    //发送邮件
    public function actionMailer(){
        $email=\Yii::$app->request->post('email');
        //判断
        if(!preg_match('/^\w+([-.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/',$email)){
            echo '邮箱地址不正确';
            exit;
        }
        $code=rand(1000,9999);
        $result=\Yii::$app->mailer->compose()
            ->setFrom('15228110515@163.com')//从谁哪里发的
            ->setTo($email)//发给谁
            ->setSubject('注册验证码')//主题
//            ->setTextBody('Plain text content')//内容text
            ->setHtmlBody('<b style="font: 24px/48px 微软雅黑">您的验证码是</b><strong style="color: red ;font-size:48px">'.$code.'</strong>')//内容html
            ->send();
        if($result){
            //保存验证码到缓存,有效期5分钟
            \Yii::$app->cache->set('email'.$email,$code,5*60);

            echo 'success';
        }else{
            echo '邮件发送失败';
        }
    }

    public function actionUser()
    {
        var_dump(\Yii::$app->user->identity);
    }



}
