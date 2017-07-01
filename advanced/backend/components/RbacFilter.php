<?php
namespace backend\components;

use yii\base\ActionFilter;
use yii\web\HttpException;

class RbacFilter extends ActionFilter{

    public function beforeAction($action){
        //判断是否有权限
        $user=\Yii::$app->user;
//        var_dump($user->can($action->uniqueId));exit;
        if(!$user->can($action->uniqueId)){

            //判断是否登陆
            if($user->isGuest){//游客，未登录
                //跳转到登陆页面
                return $action->controller->redirect(['user/login']);
            }

            //抛出异常
            throw new HttpException(403,'您没有权限访问该页面');
            return false;

        }

        return parent::beforeAction($action);
    }


}