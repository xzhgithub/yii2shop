<?php

namespace frontend\controllers;

use frontend\models\GoodsCategory;

class GoodscategoryController extends \yii\web\Controller
{
    public $layout='index';

    public function actionIndex()
    {
        //获取分类数据
//        $models=GoodsCategory::find()->all();

        return $this->render('index');
    }

}
