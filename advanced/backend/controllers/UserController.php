<?php

namespace backend\controllers;

use backend\models\LoginForm;
use backend\models\User;
use backend\models\UsereditForm;
use xj\uploadify\UploadAction;
use yii\filters\AccessControl;
use yii\web\Cookie;
use yii\web\Request;

class UserController extends \yii\web\Controller
{
    public function actionIndex()
    {
        $models=User::find()->where('status=1')->all();
        return $this->render('index',['models'=>$models]);
    }

    //添加
    public function actionAdd(){
        $model=new User();
//        var_dump($model);exit;
        $request=\Yii::$app->request;
        if($request->isPost){
            //加载
            $model->load($request->post());
//            var_dump($model);exit;
            //验证
            if($model->validate()) {

//                var_dump($model);exit;
                //创建时间
                $model->created_at = time();
                //密码加盐加密
                $model->password_hash = \Yii::$app->security->generatePasswordHash($model->password_hash);

                //保存
                if ($model->save()) {
                    //提示、跳转
                    \Yii::$app->session->setFlash('success', '添加成功');
                    return $this->redirect(['user/index']);
                }else{
                    var_dump($model->getErrors());
                    exit;
                }


            }else{
                var_dump($model->getErrors());
                exit;
            }

        }

        return $this->render('add',['model'=>$model]);
    }

    //删除
    public function actionDel($id){
        $model=User::findOne(['id'=>$id]);
        $model->status=0;
        //保存
        if ($model->save(false)) {
            //提示、跳转
            \Yii::$app->session->setFlash('success', '删除成功');
            return $this->redirect(['user/index']);
        }else{
            var_dump($model->getErrors());
            exit;
        }
    }

    //修改基本信息
    public function actionUpdate($id){
        $model=User::findOne(['id'=>$id]);
        $img=$model->img;

        $request=\Yii::$app->request;
        if($request->isPost){
            //接收数据
            $model->load($request->post());
//            var_dump($model,$img);exit;
            //验证
            if($model->validate()){
                //判断是否修改了图片
                if(!$model->img){
                    //保存旧图片地址
                    $model->img=$img;
                }

                //保存
                if ($model->save()) {
                    //提示、跳转
                    \Yii::$app->session->setFlash('success', '信息修改成功');
                    return $this->redirect(['user/index']);
                }else{
                    var_dump($model->getErrors());
                    exit;
                }
            }else{
                var_dump($model->getErrors());
                exit;
            }
        }

        return $this->render('update',['model'=>$model]);
    }


    //修改密码
    public function actionEdit($id){
        $model=new UsereditForm();
        $request=\Yii::$app->request;
        if($request->isPost){
            //接收数据
            $model->load($request->post());
            //验证
            if($model->validate()){
                $user=User::findOne(['id'=>$id]);
                $user->password_hash=\Yii::$app->security->generatePasswordHash($model->password);
                //保存
                if ($user->save(false)) {
                    //提示、跳转
                    \Yii::$app->session->setFlash('success', '密码修改成功');
                    return $this->redirect(['user/index']);
                }else{
                    var_dump($model->getErrors());
                    exit;
                }
            }else{
                var_dump($model->getErrors());
                exit;
            }
        }

        return $this->render('edit',['model'=>$model]);
    }

    //登陆
    public function actionLogin()
    {
        //实例化登陆模型
        $model = new LoginForm();
        //实例化接收方式
        $request = new Request();
        if ($request->isPost) {
            //接收
            $model->load($request->post());
            //验证(表单模型里面已实现验证数据)
            if ($model->validate()) {

//                //判断是否勾选了记住登陆
//                if($model->remember){
//                    //实例化可写的cookie
//                    $cookies = \Yii::$app->response->cookies;
//                    // 将用户信息 放入cookie
//                    $cookie = new Cookie([
//                        'name'=>'username',
//                        'value'=>$model->username,
//                    ]);
//                    $cookies->add($cookie);
//                }

                //保存登陆时间
                $user = User::findOne(['username' => $model->username]);
                $user->last_time = time();
                //保存登陆ip
                $user->last_ip=$_SERVER["REMOTE_ADDR"];
                //保存
                if ($user->save(false)) {
                    //提示、跳转
                    \Yii::$app->session->setFlash('success', '登陆成功');
//                    var_dump(\Yii::$app->user->identity->password_hash);exit;
                    return $this->redirect(['user/index']);
                }else{
                    var_dump($model->getErrors());
                    exit;
                }


            } else {
                //打印错误
                var_dump($model->getErrors());
                exit;
            }
        }

        return $this->render('login', ['model' => $model]);
    }

    //注销
    public function actionLogout(){
        \Yii::$app->user->logout();
        return $this->redirect(['user/login']);
    }


    //过滤器
    public function behaviors(){
        return [
            'acf'=>[
                'class'=>AccessControl::className(),
                'only'=>['add','edit','del','index'],
                'rules'=>[
                    [
                        //已认证用户才可以执行增、删、改、查操作
                        'allow'=>true,
                        'actions'=>['add','edit','del','index'],
                        'roles'=>['@'],
                    ],
                ]

            ],

        ];
    }

    public function actions(){
        return[
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
                'backColor'=>0x000000,//背景颜色
                'maxLength' => 4, //最大显示个数
                'minLength' => 4,//最少显示个数
                'padding' => 5,//间距
                'height'=>40,//高度
                'width' => 130,  //宽度
                'foreColor'=>0xffffff,     //字体颜色
                //设置字符偏移量 有效果
                //'controller'=>'login',        //拥有这个动作的controller
            ],



            's-upload' => [
                'class' =>UploadAction ::className(),
                'basePath' => '@webroot/upload',
                'baseUrl' => '@web/upload',
                'enableCsrf' => true, // default
                'postFieldName' => 'Filedata', // default
                //BEGIN METHOD
                'format' => [$this, 'methodName'],
                //END METHOD
                //BEGIN CLOSURE BY-HASH
                'overwriteIfExist' => true,
//                'format' => function (UploadAction $action) {
//                    $fileext = $action->uploadfile->getExtension();
//                    $filename = sha1_file($action->uploadfile->tempName);
//                    return "{$filename}.{$fileext}";
//                },
                //END CLOSURE BY-HASH
                //BEGIN CLOSURE BY TIME
                'format' => function (UploadAction $action) {
                    $fileext = $action->uploadfile->getExtension();
                    $filehash = sha1(uniqid() . time());
                    $p1 = substr($filehash, 0, 2);
                    $p2 = substr($filehash, 2, 2);
                    return "{$p1}/{$p2}/{$filehash}.{$fileext}";
                },
                //END CLOSURE BY TIME
                'validateOptions' => [
                    'extensions' => ['jpg', 'png'],
                    'maxSize' => 1 * 1024 * 1024, //file size
                ],
                'beforeValidate' => function (UploadAction $action) {
                    //throw new Exception('test error');
                },
                'afterValidate' => function (UploadAction $action) {},
                'beforeSave' => function (UploadAction $action) {},
                'afterSave' => function (UploadAction $action) {
//                    $action->output['fileUrl'] = $action->getWebUrl();

                    $imgUrl=$action->getWebUrl();//获取点击上传图片时图片保存到的相对路径
                    //将图片上传到七牛云
                    $qiniu=\Yii::$app->qiniu;
                    $qiniu->uploadFile(\Yii::getAlias('@webroot').$imgUrl,$imgUrl);
                    //获取图片在七牛云上的地址
                    $url=$qiniu->getLink($imgUrl);
                    //将回显图片地址设置成七牛云上的地址
                    $action->output['fileUrl'] = $url;

                },
            ],


        ];
    }
}
