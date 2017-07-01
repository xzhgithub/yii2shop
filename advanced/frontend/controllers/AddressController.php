<?php
namespace frontend\controllers;

use chenkby\region\Region;
use frontend\models\Address;
use frontend\models\Locations;
use yii\web\Controller;

class AddressController extends Controller{
    public $layout='list';

    public function actionAddress(){
        //获取地址列表
        $member_id=\Yii::$app->user->identity->getId();

        $models=Address::find()->andWhere(['member_id'=>$member_id])->andWhere(['>','status','0'])->all();
        $address=new Address();

        $request=\Yii::$app->request;
        if($request->isPost){
            $address->load($request->post());
            if($address->validate()){
                if($address->remember){
                    //因为默认地址只能有一个，设置前将之前的默认地址取消，status=3为默认
                    $moren=Address::findOne(['status'=>3]);
                    if($moren) {
                        $moren->status = 1;
                        $moren->save();
                    }
                    //设置成默认地址
                    $address->status=3;
                }else{
                    $address->status=1;
                }
                $address->member_id=$member_id;
                $address->save();
                return $this->redirect(['address/address']);
            }else{
                var_dump($address->getErrors());
                exit;
            }

        }

//        var_dump($models);exit;
        return $this->render('address',['models'=>$models,'address'=>$address]);
    }

    //修改
    public function actionEdit($id){
        $address=Address::findOne(['id'=>$id]);

        $request=\Yii::$app->request;
        if($request->isPost){
            $address->load($request->post());
            if($address->validate()){
                $address->save();
                return $this->redirect(['address/address']);
            }else{
                var_dump($address->getErrors());
                exit;
            }

        }

        return $this->render('edit',['address'=>$address]);
    }

    //删除
    public function actionDel($id){
        $model=Address::findOne(['id'=>$id]);
        $model->status=0;
        if($model->save()){
            \Yii::$app->session->setFlash('success','删除成功');
            return $this->redirect(['address/address']);
        }
    }

    //设置默认地址
    public function actionSet($id){
        //因为默认地址只能有一个，设置前将之前的默认地址取消，status=3为默认
        $moren=Address::findOne(['status'=>3]);
        if($moren){
            $moren->status=1;
            if($moren->save()){
                $model=Address::findOne(['id'=>$id]);
                $model->status=3;
                if($model->save()){
                    \Yii::$app->session->setFlash('success','设置默认地址成功');
                    return $this->redirect(['address/address']);
                }
            }
        }



    }


    public function actions()
    {
        $actions=parent::actions();
        $actions['get-region']=[
            'class'=>\chenkby\region\RegionAction::className(),
            'model'=>Locations::className()
        ];
        return $actions;
    }
}