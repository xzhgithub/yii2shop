<?php
namespace backend\models;

use yii\base\Model;

class LoginForm extends Model
{
    //定义表单字段
    public $username;
    public $password;
    public $code;
    public $remember;

    //验证数据
    public function rules(){
        return[
            [['username','password'],'required'],
            ['code','captcha','captchaAction'=>'user/captcha'],
            //验证用户名
            ['username','checkUsername'],//自定义验证方法
            ['remember','integer'],
        ];
    }

    //将英文标签改成中文
    public function attributeLabels(){
        return[
            'username'=>'用户名',
            'password'=>'密码',
            'code'=>'验证码',
            'remember'=>'记住本次登陆',

        ];
    }

    //自定义验证方法
    public function checkUsername(){
        //获取该用户信息
        $usr=User::findOne(['username'=>$this->username]);
        //判断该用户是否存在
        if($usr){
            //验证密码是否正确
            if(\Yii::$app->security->validatePassword($this->password,$usr->password_hash)){
                //验证通过，登陆 注入认证
                //存入自动登陆的验证AuthKey
                $usr->auth_key =\Yii::$app->security->generateRandomString();
                $usr->save(false);

//                var_dump($this->remember);exit;
                //判断用户是否勾选了自动登陆
                if($this->remember){

                    //认证，保存cookie，设置有效时间
                    \Yii::$app->user->login($usr,3600);
//                    //添加一个任意的数据，作为cookie值
//                    $usr->auth_key=\Yii::$app->security->generateRandomString();
//                    $usr->save(false);
                }else{
                    \Yii::$app->user->login($usr);
                }

            }else{
                //密码错误
                $this->addError('password','密码错误');
            }

        }else{
            //账户不存在
            $this->addError('username','账户不存在');
        }
    }

}