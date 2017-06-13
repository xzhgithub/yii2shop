<?php

namespace backend\controllers;

use backend\models\GoodsCategory;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

class GoodscategoryController extends \yii\web\Controller
{
    public function actionIndex()
    {
        $model=GoodsCategory::find()->OrderBy('tree','lft')->all();
        return $this->render('index',['model'=>$model]);
    }

    //添加
    public function actionAdd(){
        //实例化
        $model=new GoodsCategory();
        //接收post传值
        $request=\Yii::$app->request;
        if($request->isPost){
            $model->load($request->post());
            //验证
            if($model->validate()){
                //保存,判断是否是添加顶级节点
                if($model->parent_id){
                    //添加非顶级节点
                    //获取父节点
                    $parent=GoodsCategory::findOne(['id'=>$model->parent_id]);
                    $model->prependTo($parent);

                }else{
                    //添加根节点
                    $model->makeRoot();
                }

                //提示
                \Yii::$app->session->setFlash('success','添加成功');
                //跳转
                return $this->redirect(['goodscategory/index']);
            }else{
                //打印错误
                var_dump($model->getErrors());
                exit;
            }

        }

        //获取分类数据
        $categories=ArrayHelper::merge([['id'=>0,'name'=>'顶级分类','parent_id'=>0]],GoodsCategory::find()->asArray()->all());

        return $this->render('add',['model'=>$model,'categories'=>$categories]);
    }


    //修改
    public function actionEdit($id){
        //实例化
        $model=GoodsCategory::findOne(['id'=>$id]);
        //判断该分类是否存在
        if($model==null){
            throw new NotFoundHttpException('分类不存在');
        }

        $request=\Yii::$app->request;
        if($request->isPost){
            //接收post传值
            $model->load($request->post());
            //验证
            if($model->validate()){
                //保存,判断是否是添加顶级节点
                if($model->parent_id){
                    //添加非顶级节点
                    //获取父节点
                    $parent=GoodsCategory::findOne(['id'=>$model->parent_id]);
                    $model->prependTo($parent);

                }else{
                    //判断之前是否是一级分类
                    if($model->getOldAttribute('parent_id')==0){
                        //保存
                        $model->save();
                    }else{
                        $model->makeRoot();
                    }


                }

                //提示
                \Yii::$app->session->setFlash('success','添加成功');
                //跳转
                return $this->redirect(['goodscategory/index']);
            }else{
                //打印错误
                var_dump($model->getErrors());
                exit;
            }

        }

        //获取分类数据
        $categories=ArrayHelper::merge([['id'=>0,'name'=>'顶级分类','parent_id'=>0]],GoodsCategory::find()->asArray()->all());

        return $this->render('add',['model'=>$model,'categories'=>$categories]);
    }




    public function actionTest(){
        $categories=GoodsCategory::find()->asArray()->all();

        return $this->renderPartial('test',['categories'=>$categories]);
    }

}
