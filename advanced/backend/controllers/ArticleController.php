<?php

namespace backend\controllers;

use backend\models\Article;
use backend\models\ArticleCategory;
use backend\models\ArticleDetail;
use yii\data\Pagination;

class ArticleController extends \yii\web\Controller
{

    public function actionIndex()
    {
        $query=Article::find();
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
                //将文字内容保存到文章详情表
                $article_detail=new ArticleDetail();
                $article_detail->content=$model->intro;

                //文字信息表只保存简介,截取内容的一部分保存
                $model->intro=substr($model->intro,0,45);//截取15个字符
                //保存
                if($model->save()){
                    //文章信息保存后才能获取到文字id
                    $article_detail->article_id=$model->id;
                   //将文章id和内容保存到文章详情表
                    $article_detail->save();
                    //提示并跳转
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
        //获取文章内容
        $article=ArticleDetail::findOne(['article_id'=>$id]);
        //获取分类数据
        $data=ArticleCategory::find()->where('status=1')->orderBy(['sort'=>'asc'])->all();
        $request=\Yii::$app->request;
        if($request->isPost){
            //接收数据
            $model->load($request->post());

            //验证
            if($model->validate()){
                //将文字内容保存到文章详情表
                $article_detail=ArticleDetail::findOne(['article_id'=>$model->id]);
                $article_detail->content=$model->intro;
                //将文章id和内容保存到文章详情表
                $article_detail->save();

                //文字信息表只保存简介,截取内容的一部分保存
                $model->intro=substr($model->intro,0,45);//截取15个字符
                //保存
                if($model->save()){

                    //提示并跳转
                    \Yii::$app->session->setFlash('success','添加成功');
                    return $this->redirect(['article/index']);
                }

            }else{
                //打印错误
                var_dump($model->getErrors());
                exit;
            }
        }

        return $this->render('add',['model'=>$model,'data'=>$data,'article'=>$article]);
    }

    //添加ueditor插件
    public function actions()
    {
        return [

            'ueditor' => [
                'class' => 'crazyfd\ueditor\Upload',
                'config'=>[
                    'uploadDir'=>date('Y/m/d')
                ]

            ],
        ];
    }


}
