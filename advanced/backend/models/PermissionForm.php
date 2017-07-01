<?php
namespace backend\models;

use yii\base\Model;

class PermissionForm extends Model{

    public $name;
    public $description;

    public function rules(){
        return[
            [['name','description'],'required'],
        ];
    }
    public function attributeLabels(){
        return[
            'name'=>'权限名称',
            'description'=>'描述',
        ];
    }

    //实现添加权限的方法
    public function addPermission(){
        $authManager=\Yii::$app->authManager;
        //判断该权限是否存在
        if($authManager->getPermission($this->name)){
            $this->addError('name','该权限已经存在');
        }else{
            //创建权限
            $permission=$authManager->createPermission($this->name);
            //添加权限描述
            $permission->description=$this->description;
            //将权限添加到数据表
            return $authManager->add($permission);
        }
        return false;

    }

    //实现将数据保存到模型数据里面
    public function loadData($permission){
        $this->name=$permission->name;
        $this->description=$permission->description;
    }

    //实现修改权限的方法
    public function updatePermission($permission){
        //获取原来权限的name
        $name=$permission->name;
        $authManager=\Yii::$app->authManager;
        //判断修改后的权限是否存在
        if($name!=$this->name){//表示修改了
            //判断修改后的权限是否存在
            if($authManager->getPermission($this->name)){
                $this->addError('name','该权限已经存在');
                return false;
            }
        }
            //权限修改后重新赋值
            $permission->name=$this->name;
            $permission->description=$this->description;
            //修改
            return $authManager->update($name,$permission);


    }

}