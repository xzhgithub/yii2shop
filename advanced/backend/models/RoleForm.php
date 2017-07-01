<?php
namespace backend\models;

use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\rbac\Role;

class RoleForm extends Model{
    public $name;
    public $description;
    public $permissions=[];

    public function rules(){
        return[
            [['name','description'],'required'],
            ['permissions','safe'],
        ];
    }

    public function attributeLabels(){
        return[
            'name'=>'角色名称',
            'description'=>'角色描述',
            'permissions'=>'权限',
        ];
    }

    //实现添加角色方法
    public function addRole(){

        $authManager=\Yii::$app->authManager;
        //判断该角色是否已经存在
        if($authManager->getRole($this->name)){
            $this->addError('该角色已经存在');
        }else{
            //创建角色
            $role=$authManager->createRole($this->name);
            $role->description=$this->description;
            //将角色添加到数据表
            if($authManager->add($role)){
                //将角色与权限关联
                //遍历出name值
                foreach($this->permissions as $permissionName){
                    //通过name值获取权限
                    $permission=$authManager->getPermission($permissionName);
                    //将权限关联给该角色
                    if($permission){//如果该权限存在
                        $authManager->addChild($role,$permission);
                    }
                }
                return true;
            }
        }
        return false;
    }

    //实现获取所有权限
    public static function getPermissions(){
        $permissions=\Yii::$app->authManager->getPermissions();
        return ArrayHelper::map($permissions,'name','description');
    }

    //实现将角色的值赋值到修改表单模型
    public function loadData(Role $role){
        $this->name=$role->name;
        $this->description=$role->description;
        //通过name获取该角色的权限
        $permissions=\Yii::$app->authManager->getPermissionsByRole($role->name);
        if($permissions){
            //将权限遍历出来
            foreach($permissions as $permission){
                $this->permissions[]=$permission->name;
            }
        }
    }

    //实现更新角色
    public function updateRole($name){
        $authManager=\Yii::$app->authManager;
        $role=$authManager->getRole($this->name);
        //判断角色是否被修改
        if($this->name!=$name){//修改了角色
            //判断修改后的角色是否已经存在
            if($role){//存在
                $this->addError('name','该角色已经存在');
                return false;
            }
        }
        $role->name=$this->name;
        $role->description=$this->description;
        //更新
        if($authManager->update($name,$role)){//更新成功
            //将角色与修改后的权限关联(先删除旧的权限，再添加新的权限)
            //删除旧的权限
            $authManager->removeChildren($role);
            //添加新的权限
            foreach($this->permissions as $permissionName){
                //根据name获取权限
                $permission=$authManager->getPermission($permissionName);
                if($permission){
                    $authManager->addChild($role,$permission);
                }
            }
            return true;
        }
    }

}