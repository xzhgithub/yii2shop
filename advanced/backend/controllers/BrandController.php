<?php

namespace backend\controllers;

use backend\components\rbacFilter;
use backend\models\Brand;
use yii\data\Pagination;
use yii\web\Controller;
use yii\web\UploadedFile;
use xj\uploadify\UploadAction;
use crazyfd\qiniu\Qiniu;

class BrandController extends Controller
{

     //过滤器
    public function behaviors(){
        return[
            'rbac'=>[
                'class'=>RbacFilter::className(),
                'only'=>['index','add','edit','del'],
            ]
        ];
    }



    public function actionIndex()
    {
        $query=Brand::find()->where('status=1');
        //获取数据总条数
        $count=$query->count();
        $page=new Pagination([
            'defaultPageSize'=>4,
            'totalCount'=>$count,
        ]);

        $model=Brand::find()->where('status=1')->orderBy(['sort'=>'asc'])->offset($page->offset)->limit($page->limit)->all();


        return $this->render('index',['model'=>$model,'page'=>$page]);
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

                    //没有修改图片，给数据库里面的图片地址赋上原来的地址
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
//                    $action->output['fileUrl'] = $action->getWebUrl();
//                    $imgUrl=$action->getWebUrl();//获取点击上传图片时图片保存到的相对路径
                    //将图片上传到七牛云
                    $qiniu=\Yii::$app->qiniu;
                    $qiniu->uploadFile($action->getSavePath(),$action->getWebUrl());
                    //获取图片在七牛云上的地址
                    $url=$qiniu->getLink($action->getWebUrl());
                    //将回显图片地址设置成七牛云上的地址
                    $action->output['fileUrl'] = $url;
                    /*
                    *下面/* 注释的方法也可以，没有封装
                    $ak = 'CDoTHCw-z8LSDyj6Rek7bBTeOcaWs71_xDoN7LU0';
                     $sk = 'BtHUrYN_lCFJ90ZZvZf1NdbFhKpoV55hDdUsermA';
                     $domain = 'http://or9rwgf8b.bkt.clouddn.com';
                     $bucket = 'yii2';
                     $qiniu = new Qiniu($ak, $sk,$domain, $bucket);
                     $qiniu->uploadFile(\Yii::getAlias('@webroot').$imgUrl,$imgUrl);
                     $url = $qiniu->getLink($imgUrl);
                     //获取图片在七牛云上的地址
                     $action->output['fileUrl'] = $url;*/
//                    $action->getFilename(); // "image/yyyymmddtimerand.jpg"
//                    $action->getWebUrl(); //  "baseUrl + filename, /upload/image/yyyymmddtimerand.jpg"
//                    $action->getSavePath(); // "/var/www/htdocs/upload/image/yyyymmddtimerand.jpg"
                },
            ],
        ];
    }
//测试七牛云
//    public function actionQiniu(){
//        $ak = 'CDoTHCw-z8LSDyj6Rek7bBTeOcaWs71_xDoN7LU0';
//        $sk = 'BtHUrYN_lCFJ90ZZvZf1NdbFhKpoV55hDdUsermA';
//        $domain = 'http://or9rwgf8b.bkt.clouddn.com';
//        $bucket = 'yii2';
//        $qiniu = new Qiniu($ak, $sk,$domain, $bucket);
//        $filename=\Yii::getAlias('@webroot').'/test.jpg';
//
//        $key = 'test.jpg';
//        $qiniu->uploadFile($filename,$key);
//        $url = $qiniu->getLink($key);
////        var_dump($url);
//    }
}
