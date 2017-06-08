<?php

namespace backend\controllers;

use backend\models\Article;
use backend\models\ArticleCategory;
use backend\models\ArticleDetail;

class ArticleController extends \yii\web\Controller
{
    public function actionIndex()
    {
        $model=Article::find()->where('status=1')->orderBy(['sort'=>'asc'])->all();

        return $this->render('index',['model'=>$model]);
    }


    //添加
    public function actionAdd(){
        //实例化模型
        $model=new Article();
        //获取分类数据
        $data=ArticleCategory::find()->where('status=1')->orderBy(['sort'=>'asc'])->all();
        $request=\Yii::$app->request;
        if($request->isPost){
            //接收
            $model->load($request->post());

            //验证
            if($model->validate()){

                //添加创建时间
                $model->create_time=time();
                //保存
                if($model->save()){
                    //将文字内容保存到文章详情表
                    $article_detail=new ArticleDetail();
                    $article_detail->article_id=$model->id;
                    $article_detail->content=$model->intro;
                    $article_detail->save();

                    \Yii::$app->session->setFlash('success','添加成功');
                    return $this->redirect(['article/index']);
                }
            }

        }

        //选择视图，分配数据
        return $this->render('add',['model'=>$model,'data'=>$data]);
    }

    //删除
    public function actionDel($id){
        $article=Article::findOne(['id'=>$id]);
        $article->status=-1;
        //保存
        $article->save();
        \Yii::$app->session->setFlash('success','删除成功');
        return $this->redirect(['article/index']);

    }

    //修改
    public function actionEdit($id){
        //实例化表单模型
        $model=Article::findOne(['id'=>$id]);
        //获取分类数据
        $data=ArticleCategory::find()->where('status=1')->orderBy(['sort'=>'asc'])->all();
        $request=\Yii::$app->request;
        if($request->isPost){
            //接收数据
            $model->load($request->post());

            //验证
            if($model->validate()){

                //保存
                if($model->save()){
                    \Yii::$app->session->setFlash('success','修改成功');
                    //跳转
                    return $this->redirect(['article/index']);
                }

            }else{
                //打印错误
                var_dump($model->getErrors());
                exit;
            }
        }

        return $this->render('add',['model'=>$model,'data'=>$data]);
    }


}
