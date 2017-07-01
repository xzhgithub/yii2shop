<?php
namespace frontend\models;

use yii\base\Model;

class LoginForm extends Model{
    public $username;
    public $password;
    public $code;
    public $remember;

    public function rules(){
        return[
            [['username','password','code'],'required'],
            ['code','captcha','captchaAction'=>'user/captcha'],
            ['remember','safe'],
            ['username','checkUsername'],
        ];
    }

    public function attributeLabels(){
        return[
            'username'=>'用户名',
            'password'=>'密码',
            'code'=>'验证码',
            'remember'=>'记住密码',
        ];
    }


    //自定义验证用户方法
    public function checkUsername()
    {
        //从数据库读取该用户
        $member=Member::findOne(['username'=>$this->username]);
        //判断该用户名是否存在
        if($member){

            //判断密码是否正确
            if(\Yii::$app->security->validatePassword($this->password,$member->password_hash)){
                //允许登陆
                //随机生成一个字符串，保存到数据包的auth_key字段
                $member->auth_key=\Yii::$app->security->generateRandomString();
                $member->save(false);
                //是否记住登陆
                $duration=0;
                if($this->remember){
                    //设置cookie保存时间
                    $duration=3600*24;
                }

                \Yii::$app->user->login($member,$duration);

            }else{
                //密码错误
                $this->addError('password','密码错误');
            }
        }else{
            //用户名不存在
            $this->addError('username','用户名不存在');
        }

    }
}