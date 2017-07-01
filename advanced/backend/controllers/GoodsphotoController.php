<?php

namespace backend\controllers;

use backend\models\GoodsPhoto;
use xj\uploadify\UploadAction;

class GoodsphotoController extends BackendController
{
    public function actionIndex($id)
    {
        $models=GoodsPhoto::find()->where(['and','status=1',['goods_id'=>$id]])->all();

        return $this->render('index',['models'=>$models,'goods_id'=>$id]);
    }

    //添加
    public function actionAdd($goods_id){
        $photo=new GoodsPhoto();
        $request=\Yii::$app->request;
        if($request->isPost){
            $photo->load($request->post());
            if($photo->validate()){
                $photo->status=1;
                $photo->goods_id=$goods_id;
                $photo->save();
                \Yii::$app->session->setFlash('success','添加成功');
                return $this->redirect(['goodsphoto/index','id'=>$goods_id]);
            }
        }

        return $this->render('add',['photo'=>$photo]);
    }

    //AJAX删除图片
    public function actionDel(){

            $id = \Yii::$app->request->post('id');
            $model = GoodsPhoto::findOne(['id'=>$id]);
            if($model && $model->delete()){
                return 'success';
            }else{
                return 'fail';
            }

    }

    //修改
    public function actionEdit($id){
        //获取数据
        $photo=GoodsPhoto::findOne(['id'=>$id]);

        //判断传参方式
        $request=\Yii::$app->request;
        if($request->isPost){
            //接收
            $photo->load($request->post());
            //验证
            if($photo->validate()){
                //保存
                $photo->save();

                //提示、跳转
                \Yii::$app->session->setFlash('success','修改成功');
                return $this->redirect(['goodsphoto/index','id'=>$photo->goods_id]);
            }
        }



        return $this->render('add',['photo'=>$photo]);
    }



//添加uploadedfiy插件
    public function actions() {
        return [
            'upload' => [
                'class' => 'kucha\ueditor\UEditorAction',
                'config' => [
                    "imageUrlPrefix"  => "",//图片访问路径前缀
                    "imagePathFormat" => "/upload/{yyyy}{mm}{dd}/{time}{rand:6}" ,//上传保存路径
                    "imageRoot" => \Yii::getAlias("@webroot"),
                ],
            ],

            's-upload' => [
                'class' => UploadAction::className(),
                'basePath' => '@webroot/upload/logo',
                'baseUrl' => '@web/upload/logo',
                'enableCsrf' => true, // default
                'postFieldName' => 'Filedata', // default
                //BEGIN METHOD
                //'format' => [$this, 'methodName'],
                //END METHOD
                //BEGIN CLOSURE BY-HASH
                'overwriteIfExist' => true,
                /*'format' => function (UploadAction $action) {
                    $fileext = $action->uploadfile->getExtension();
                    $filename = sha1_file($action->uploadfile->tempName);
                    return "{$filename}.{$fileext}";
                },*/
                //END CLOSURE BY-HASH
                //BEGIN CLOSURE BY TIME
                'format' => function (UploadAction $action) {
                    $fileext = $action->uploadfile->getExtension();
                    $filehash = sha1(uniqid() . time());
                    $p1 = substr($filehash, 0, 2);
                    $p2 = substr($filehash, 2, 2);
                    return "/{$p1}/{$p2}/{$filehash}.{$fileext}";
                },
                //END CLOSURE BY TIME
                'validateOptions' => [
                    'extensions' => ['jpg', 'png','gif'],
                    'maxSize' => 1 * 1024 * 1024, //file size
                ],
                'beforeValidate' => function (UploadAction $action) {
                    //throw new Exception('test error');
                },
                'afterValidate' => function (UploadAction $action) {},
                'beforeSave' => function (UploadAction $action) {},
                'afterSave' => function (UploadAction $action) {
                    //图片上传成功的同时，将图片和商品关联起来
                    $model = new GoodsPhoto();
                    $model->goods_id = \Yii::$app->request->post('goods_id');
                    $model->img = $action->getWebUrl();
                    $model->status=1;
                    $model->save();
                    $action->output['fileUrl'] = $model->img;
                    //$action->output['goods_id'] = $model->goods_id;

//                    $action->getFilename(); // "image/yyyymmddtimerand.jpg"
//                    $action->getWebUrl(); //  "baseUrl + filename, /upload/image/yyyymmddtimerand.jpg"
//                    $action->getSavePath(); // "/var/www/htdocs/upload/image/yyyymmddtimerand.jpg"
                    //$action->output['Path'] = $action->getSavePath();
                    /*
                     * 将图片上传到七牛云
                     */
                    /* $qiniu = \Yii::$app->qiniu;//实例化七牛云组件
                     $qiniu->uploadFile($action->getSavePath(),$action->getFilename());//将本地图片上传到七牛云
                     $url = $qiniu->getLink($action->getFilename());//获取图片在七牛云上的url地址
                     $action->output['fileUrl'] = $url;//将七牛云图片地址返回给前端js
                    */
                },
            ],
        ];

        //添加ueditor插件
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
