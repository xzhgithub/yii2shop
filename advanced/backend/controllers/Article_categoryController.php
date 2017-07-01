<?php

namespace backend\controllers;

use backend\models\ArticleCategory;
use backend\models\BackendController;
use yii\data\Pagination;

class Article_categoryController extends \backend\controllers\BackendController
{
    public function actionIndex()
    {
        $query=ArticleCategory::find();
        //获取数据总条数
        $count=$query->count();
        $page=new Pagination([
            'defaultPageSize'=>4,
            'totalCount'=>$count,
        ]);
        $model=$query->where('status=1')->orderBy(['sort'=>'asc'])->offset($page->offset)->limit($page->limit)->all();

        return $this->render('index',['model'=>$model,'page'=>$page]);
    }

    //添加
    public function actionAdd(){
        //实例化模型
        $model=new ArticleCategory();
        $request=\Yii::$app->request;
        if($request->isPost){
            //接收
            $model->load($request->post());

            //验证
            if($model->validate()){

                //保存
                if($model->save()){
                    \Yii::$app->session->setFlash('success','添加成功');
                    return $this->redirect(['article_category/index']);
                }
            }

        }

        //选择视图，分配数据
        return $this->render('add',['model'=>$model]);
    }

    //删除
    public function actionDel($id){
        $article_category=ArticleCategory::findOne(['id'=>$id]);
        $article_category->status=-1;
        //保存
        $article_category->save();
        \Yii::$app->session->setFlash('success','删除成功');
        return $this->redirect(['article_category/index']);

    }

    //修改
    public function actionEdit($id){
        //实例化表单模型
        $model=ArticleCategory::findOne(['id'=>$id]);

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
                    return $this->redirect(['article_category/index']);
                }

            }else{
                //打印错误
                var_dump($model->getErrors());
                exit;
            }
        }

        return $this->render('add',['model'=>$model]);
    }


}
