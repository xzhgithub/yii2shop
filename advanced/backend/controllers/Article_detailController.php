<?php

namespace backend\controllers;

use backend\models\Article;
use backend\models\ArticleDetail;

class Article_detailController extends \yii\web\Controller
{
    public function actionIndex($id)
    {
        //获取文章详情
        $model=ArticleDetail::findOne(['article_id'=>$id]);
        //获取文章信息
        $data=Article::findOne(['id'=>$id]);

        return $this->render('index',['model'=>$model,'data'=>$data]);
    }

}
