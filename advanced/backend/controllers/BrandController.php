<?php

namespace backend\controllers;

use backend\models\Brand;
use yii\web\UploadedFile;

class BrandController extends \yii\web\Controller
{
    public function actionIndex()
    {
        $model=Brand::find()->where('status=1')->orderBy(['sort'=>'asc'])->all();


        return $this->render('index',['model'=>$model]);
    }

    //添加
    public function actionAdd(){
        //实例化模型
        $model=new Brand();
        $request=\Yii::$app->request;
        if($request->isPost){
            //接收
            $model->load($request->post());

            //实例化上传文件
            $model->imgFile=UploadedFile::getInstance($model,'imgFile');

            //验证
            if($model->validate()){
                //判断是否上次了文件
                if($model->imgFile){
                    //获取图片地址，相对路径
                    $imgpath='/images/brand/'.uniqid().'.'.$model->imgFile->extension;
                    //保存图片
                    $model->imgFile->saveAs(\Yii::getAlias('@webroot').$imgpath,false);
                    $model->logo=$imgpath;
                }

                //保存
                if($model->save()){
                    \Yii::$app->session->setFlash('success','添加成功');
                    return $this->redirect(['brand/index']);
                }
            }

        }

        //选择视图，分配数据
        return $this->render('add',['model'=>$model]);
    }

    //删除
    public function actionDel($id){
        $brand=Brand::findOne(['id'=>$id]);
        $brand->status=-1;
        //保存
        $brand->save();
        \Yii::$app->session->setFlash('success','删除成功');
        return $this->redirect(['brand/index']);


    }

    //修改
    public function actionEdit($id){
        //实例化表单模型
        $model=Brand::findOne(['id'=>$id]);
        $model->imgFile=$model->logo;

        $request=\Yii::$app->request;
        if($request->isPost){
            //接收数据
            $model->load($request->post());

            //实例化上次文件类
            $model->imgFile=UploadedFile::getInstance($model,'imgFile');

            //验证
            if($model->validate()){
                //判断是否修改了图片
                if($model->imgFile){

                    //获取图片相对路径,好保存到数据库
                    $imgpath='/images/'.uniqid().'.'.$model->imgFile->extension;
                    //保存图片到绝对路径
                    $model->imgFile->saveAs(\yii::getAlias('@webroot').$imgpath,false);
                    //给数据库里面的图片地址赋值
                    $model->logo=$imgpath;
                }

                //保存
                if($model->save()){
                    \Yii::$app->session->setFlash('success','修改成功');
                    //跳转
                    return $this->redirect(['brand/index']);
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
