<?php

namespace backend\controllers;

use backend\models\Article;
use backend\models\ArticleDetail;
use backend\models\BackendController;
use yii\web\Controller;

class Article_detailController extends Controller
{
    public function actionIndex($id)
    {

        $model=ArticleDetail::findOne(['article_id'=>$id]);

        $data=Article::findOne(['id'=>$id]);

        return $this->render('index',['model'=>$model,'data'=>$data]);
    }

}
