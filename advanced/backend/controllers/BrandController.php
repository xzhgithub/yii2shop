<?php

namespace backend\controllers;

use backend\models\Brand;
use yii\web\UploadedFile;
use xj\uploadify\UploadAction;

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
//            var_dump($model);exit;
            //实例化上传文件
//            $model->imgFile=UploadedFile::getInstance($model,'imgFile');

            //验证
            if($model->validate()){
                //判断是否上次了文件
//                if($model->imgFile){
//                    //获取图片地址，相对路径
//                    $imgpath='/images/brand/'.uniqid().'.'.$model->imgFile->extension;
//                    //保存图片
//                    $model->imgFile->saveAs(\Yii::getAlias('@webroot').$imgpath,false);
//                    $model->logo=$imgpath;
//                }

                //保存
                if($model->save(false)){
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
        $img=$model->logo;

        $request=\Yii::$app->request;
        if($request->isPost){
            //接收数据
            $model->load($request->post());

            //实例化上次文件类
//            $model->imgFile=UploadedFile::getInstance($model,'imgFile');

            //验证
            if($model->validate()){
                //判断是否修改了图片
                if(!$model->logo){

                    //给数据库里面的图片地址赋值
                    $model->logo=$img;
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

    public function actions() {
        return [
            's-upload' => [
                'class' => UploadAction::className(),
                'basePath' => '@webroot/upload',
                'baseUrl' => '@web/upload',
                'enableCsrf' => true, // default
                'postFieldName' => 'Filedata', // default
                //BEGIN METHOD
                'format' => [$this, 'methodName'],
                //END METHOD
                //BEGIN CLOSURE BY-HASH
                'overwriteIfExist' => true,
//                'format' => function (UploadAction $action) {
//                    $fileext = $action->uploadfile->getExtension();
//                    $filename = sha1_file($action->uploadfile->tempName);
//                    return "{$filename}.{$fileext}";
//                },
                //END CLOSURE BY-HASH
                //BEGIN CLOSURE BY TIME
                'format' => function (UploadAction $action) {
                    $fileext = $action->uploadfile->getExtension();
                    $filehash = sha1(uniqid() . time());
                    $p1 = substr($filehash, 0, 2);
                    $p2 = substr($filehash, 2, 2);
                    return "{$p1}/{$p2}/{$filehash}.{$fileext}";
                },
                //END CLOSURE BY TIME
                'validateOptions' => [
                    'extensions' => ['jpg', 'png'],
                    'maxSize' => 1 * 1024 * 1024, //file size
                ],
                'beforeValidate' => function (UploadAction $action) {
                    //throw new Exception('test error');
                },
                'afterValidate' => function (UploadAction $action) {},
                'beforeSave' => function (UploadAction $action) {},
                'afterSave' => function (UploadAction $action) {
                    $action->output['fileUrl'] = $action->getWebUrl();
                    $action->getFilename(); // "image/yyyymmddtimerand.jpg"
                    $action->getWebUrl(); //  "baseUrl + filename, /upload/image/yyyymmddtimerand.jpg"
                    $action->getSavePath(); // "/var/www/htdocs/upload/image/yyyymmddtimerand.jpg"
                },
            ],
        ];
    }


}
