<?php

namespace backend\controllers;

use backend\models\Menu;
use yii\helpers\ArrayHelper;
use yii\web\Controller;

class MenuController extends BackendController
{
    public function actionIndex()
    {
        $models=Menu::find()->all();
        return $this->render('index',['models'=>$models]);
    }


    //添加菜单
    public function actionAdd(){
        //实例化模型
        $model=new Menu();
        //判断
        $request=\Yii::$app->request;
        if($request->isPost){
            //接收
            $model->load($request->post());

            //验证
            if($model->validate()){

                if($model->save()){

                    //提示并跳转
                    \Yii::$app->session->setFlash('success','添加成功');
                    return $this->redirect(['menu/index']);
                }
            }

        }

        //获取菜单数据,同时增加一个一级菜单
        $data=ArrayHelper::merge([['id'=>0,'label'=>'一级菜单','parent_id'=>0]],$model->find()->where('parent_id=0')->all());

        //选择视图，分配数据
        return $this->render('add',['model'=>$model,'data'=>$data]);
    }

    //修改
    public function actionEdit($id){
        $model=Menu::findOne(['id'=>$id]);
        //判断
        $request=\Yii::$app->request;
        if($request->isPost){
            //接收
            $model->load($request->post());

            //验证
            if($model->validate()){

                if($model->save()){

                    //提示并跳转
                    \Yii::$app->session->setFlash('success','修改成功');
                    return $this->redirect(['menu/index']);
                }
            }

        }

        //获取菜单数据,同时增加一个一级菜单
        $data=ArrayHelper::merge([['id'=>0,'label'=>'一级菜单','parent_id'=>0]],$model->find()->where('parent_id=0')->all());

        //选择视图，分配数据
        return $this->render('add',['model'=>$model,'data'=>$data]);

    }

    //删除
    public function actionDel($id){
        //判断是否有下级分类
        $menu=Menu::findOne(['id'=>$id]);
        if(Menu::findOne(['parent_id'=>$id])){
            var_dump('该分类下有子分类，不能直接删除');
            exit;
        }

        if($menu->delete()){
            //提示并跳转
            \Yii::$app->session->setFlash('success','删除成功');
            return $this->redirect(['menu/index']);
        }
    }

}
