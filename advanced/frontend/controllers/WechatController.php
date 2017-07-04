<?php
namespace frontend\controllers;

use yii\web\Controller;
use EasyWeChat\Foundation\Application;

class WechatController extends Controller{
    //关闭csrf验证
    public $enableCsrfValidation = false;

    public function actionIndex(){
        echo 'wechat';

//        $app = new Application(\Yii::$app->params['wechat']);
//        $response = $app->server->serve();
//// 将响应输出
//        $response->send(); // Laravel 里请使用：return $response;
    }
}