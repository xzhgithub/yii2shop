<?php

namespace backend\controllers;



use backend\models\PermissionForm;
use backend\models\RoleForm;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class RbacController extends BackendController
{
   //添加权限
    public function actionAddpermission(){

        //实例化添加权限表单模型
        $model=new PermissionForm();
        $request=\Yii::$app->request;
        if($request->isPost){
            $model->load(\Yii::$app->request->post());
            if($model->validate()){
                //添加权限
                if($model->addPermission()){
                    //添加成功。提示并跳转
                    \Yii::$app->session->setFlash('success','权限添加成功');
                    return $this->redirect(['rbac/indexpermission']);
                }

            }else {
                //打印错误
                var_dump($model->getErrors());
                exit;
            }
        }
        //分配数据，显示视图
        return $this->render('addpermission',['model'=>$model]);
    }

    //显示权限列表
    public function actionIndexpermission(){
        //获取所有权限
       $models= \Yii::$app->authManager->getPermissions();

        return $this->render('indexpermission',['models'=>$models]);
    }

    //删除权限
    public function actionDelpermission($name){
        //获取该数据
        $authManager=\Yii::$app->authManager;
        $permission=$authManager->getPermission($name);
        //删除
        if($authManager->remove($permission)){
            //删除成功。提示并跳转
            \Yii::$app->session->setFlash('success','权限删除成功');
            return $this->redirect(['rbac/indexpermission']);
        }
    }

    //修改权限
    public function actionEditpermission($name){
        $authManager=\Yii::$app->authManager;
        //判断该权限是否存在
        if($authManager->getPermission($name)==null){
            throw new NotFoundHttpException('该权限不存在');
        }

        //实例化权限表单模型
        $model=new PermissionForm();

        //获取该数据
        $permission=$authManager->getPermission($name);

        //调用方法将permmission内的数据保存到model模型里面
        $model->loadData($permission);

        //判断传出方式
        $request=\Yii::$app->request;
        if($request->isPost){
            //加载数据
            $model->load(\Yii::$app->request->post());
            if($model->validate()){//验证
                //修改权限
                if($model->updatePermission($permission)){
                    //添加成功。提示并跳转
                    \Yii::$app->session->setFlash('success','权限修改成功');
                    return $this->redirect(['rbac/indexpermission']);
                }

            }else {
                //打印错误
                var_dump($model->getErrors());
                exit;
            }
        }
        //分配数据，显示视图
        return $this->render('addpermission',['model'=>$model]);
    }


    //添加角色
    public function actionAddrole(){
        //实例化角色表单模型
        $model=new RoleForm();
        //判断传参方式,验证
        if($model->load(\Yii::$app->request->post())&&$model->validate()){
            //添加角色
            if($model->addRole()){
                //添加成功。提示并跳转
                \Yii::$app->session->setFlash('success','角色添加成功');
                return $this->redirect(['rbac/indexrole']);
            }
        }

        //获取权限数据
        $permission=\Yii::$app->authManager->getPermissions();
        $options=ArrayHelper::map($permission,'name','description');
        //分配数据，显示视图
        return $this->render('addrole',['model'=>$model,'options'=>$options]);
    }

    //展示角色列表
    public function actionIndexrole(){
        //获取所有角色数据
        $models=\Yii::$app->authManager->getRoles();

        //分配数据，显示视图
        return $this->render('indexrole',['models'=>$models]);
    }

    //删除角色
    public function actionDelrole($name){
        //通过name值获取角色
        $authManager=\Yii::$app->authManager;
        $role=$authManager->getRole($name);
        //删除
       if($authManager->remove($role)){
           //删除成功。提示并跳转
           \Yii::$app->session->setFlash('success','角色删除成功');
           return $this->redirect(['rbac/indexrole']);
       }
    }


    //修改角色
    public function actionEditrole($name){
        //通过name值获取角色
        $authManager=\Yii::$app->authManager;
        $role=$authManager->getRole($name);
        //判断该角色是否存在
        if($role==null){
            throw new NotFoundHttpException('该角色不存在');
        }
        //实例化表单模型
        $model=new RoleForm();
        //将该角色的数据赋值到表单模型
        $model->loadData($role);

        //判断传参方式、验证
        if($model->load(\Yii::$app->request->post())&&$model->validate()){
            //修改角色
            if($model->updateRole($name)){
                //修改成功。提示并跳转
                \Yii::$app->session->setFlash('success','角色修改成功');
                return $this->redirect(['rbac/indexrole']);
            }
        }
            //分配数据，调用视图
            return $this->render('addrole',['model'=>$model]);
    }
}

