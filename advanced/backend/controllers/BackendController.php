<?php
namespace backend\controllers;

use backend\components\RbacFilter;
use yii\web\Controller;

class BackendController extends Controller{

    //过滤器
    public function behaviors(){
        return[
            'rbac'=>[
                'class'=>RbacFilter::className(),
            ]
        ];
    }
}
