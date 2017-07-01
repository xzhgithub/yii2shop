<?php
namespace backend\models;

use yii\base\Model;

class UsereditForm extends Model{
    public $password;
    public $repassword;
    public function rules(){
        return[
            [['password','repassword'],'required'],
            ['repassword','compare', 'compareAttribute'=>'password','message'=>'两次输入密码必须一致'],
            //验证旧密码是否正确
//            ['oldpassword','check'],
        ];
    }

    public function attributeLabels(){
        return[
            'oldpassword'=>'旧密码',
            'password'=>'新密码',
            'repassword'=>'确认新密码',
        ];
    }

    //自定义验证旧密码
    public function check(){
        //从数据表获取旧密码
       $user=\Yii::$app->user->identity;
        $oldpassword=$user->password_hash;

        if(!\Yii::$app->security->validatePassword($this->oldpassword,$oldpassword)){
            $this->addError('oldpassword','旧密码错误');
        }
    }
}