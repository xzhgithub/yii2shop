<?php

namespace backend\controllers;

use backend\models\Article;
use backend\models\ArticleDetail;

class Article_detailController extends \yii\web\Controller
{
    public function actionIndex($id)
    {
        //��ȡ��������
        $model=ArticleDetail::findOne(['article_id'=>$id]);
        //��ȡ������Ϣ
        $data=Article::findOne(['id'=>$id]);

        return $this->render('index',['model'=>$model,'data'=>$data]);
    }

}
