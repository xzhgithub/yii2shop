<?php

namespace backend\controllers;

use backend\models\GoodsPhoto;
use xj\uploadify\UploadAction;

class GoodsphotoController extends \yii\web\Controller
{
    public function actionIndex($id)
    {
//        $goods_id=$id;
//        var_dump($id);exit;
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

    //删除
    public function actionDel($id){
        //获取该数据
        $goodsphoto=GoodsPhoto::findOne(['id'=>$id]);
        //逻辑删除
        $goodsphoto->status=0;
        $goodsphoto->save();
        //提示、跳转
        \Yii::$app->session->setFlash('success','删除成功');
        return $this->redirect(['goodsphoto/index','id'=>$goodsphoto->goods_id]);
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

                    $imgUrl=$action->getWebUrl();//获取点击上传图片时图片保存到的相对路径
                    //将图片上传到七牛云
                    $qiniu=\Yii::$app->qiniu;
                    $qiniu->uploadFile(\Yii::getAlias('@webroot').$imgUrl,$imgUrl);
                    //获取图片在七牛云上的地址
                    $url=$qiniu->getLink($imgUrl);
                    //将回显图片地址设置成七牛云上的地址
                    $action->output['fileUrl'] = $url;

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
