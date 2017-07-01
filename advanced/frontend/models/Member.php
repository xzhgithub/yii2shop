<?php

namespace frontend\models;

use Yii;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "member".
 *
 * @property integer $id
 * @property string $username
 * @property string $auth_key
 * @property string $password_hash
 * @property string $email
 * @property string $tel
 * @property integer $last_login_time
 * @property integer $last_login_ip
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 */
class Member extends \yii\db\ActiveRecord implements IdentityInterface
{

    public $password;//明文密码
    public $repassword;//确认密码
    public $code;//验证码
    public $tel_code;//验证电话号码
    public $email_code;//验证邮箱
    public $remember;//保存登陆信息
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'member';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username','password','code','repassword','email','tel','tel_code','email_code'],'required'],
            [['last_login_time', 'last_login_ip', 'status', 'created_at', 'updated_at'], 'integer'],
            [['username'], 'string', 'max' => 50],
            [['auth_key'], 'string', 'max' => 32],
            [['password_hash', 'email'], 'string', 'max' => 100],
            [['tel'], 'string', 'max' => 11],
            ['code','captcha'],
            //用户名唯一
            ['username','unique'],
            ['email','unique'],
            ['email','email'],
            ['tel','unique'],
            //两次输入密码一致
            ['repassword','compare','compareAttribute'=>'password','message'=>'两次输入密码必须一致'],
            //验证短信验证码
            ['tel_code','validateTel'],
            //验证邮箱验证码
            ['email_code','validateEmail'],


        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => '用户名：',
            'auth_key' => 'Auth Key',
            'password_hash' => '旧密码',
            'password' => '密码：',
            'email' => '邮箱：',
            'tel' => '电话：',
            'last_login_time' => '最后登录时间',
            'last_login_ip' => '最后登录ip',
            'status' => '状态',
            'created_at' => '添加时间',
            'updated_at' => '修改时间',
            'code'=>'验证码：',
            'tel_code'=>'短信验证码：',
            'email_code'=>'邮箱验证码：',
            'repassword'=>'确认密码：',
//            'remember'=>'保存登陆信息',
        ];
    }


//验证电话号码验证码
    public function validateTel(){
        $value=Yii::$app->cache->get('tel'.$this->tel);
        if(!$value||$this->tel_code!=$value){
            $this->addError('tel_code','短信验证码错误');
        }
    }

    //验证邮箱验证码
    public function validateEmail(){
        $value=Yii::$app->cache->get('email'.$this->email);
        if(!$value||$this->email_code!=$value){
            $this->addError('tel_code','短信验证码错误');
        }
    }


    /**
     * Finds an identity by the given ID.
     * @param string|int $id the ID to be looked for
     * @return IdentityInterface the identity object that matches the given ID.
     * Null should be returned if such an identity cannot be found
     * or the identity is not in an active state (disabled, deleted, etc.)
     */
    public static function findIdentity($id)
    {
        // 通过id获取账号
        return self::findOne(['id'=>$id]);
    }

    /**
     * Finds an identity by the given token.
     * @param mixed $token the token to be looked for
     * @param mixed $type the type of the token. The value of this parameter depends on the implementation.
     * For example, [[\yii\filters\auth\HttpBearerAuth]] will set this parameter to be `yii\filters\auth\HttpBearerAuth`.
     * @return IdentityInterface the identity object that matches the given token.
     * Null should be returned if such an identity cannot be found
     * or the identity is not in an active state (disabled, deleted, etc.)
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        // TODO: Implement findIdentityByAccessToken() method.
    }

    /**
     * Returns an ID that can uniquely identify a user identity.
     * @return string|int an ID that uniquely identifies a user identity.
     */
    public function getId()
    {
        // TODO: Implement getId() method.
        return $this->id;
    }

    /**
     * Returns a key that can be used to check the validity of a given identity ID.
     *
     * The key should be unique for each individual user, and should be persistent
     * so that it can be used to check the validity of the user identity.
     *
     * The space of such keys should be big enough to defeat potential identity attacks.
     *
     * This is required if [[User::enableAutoLogin]] is enabled.
     * @return string a key that is used to check the validity of a given identity ID.
     * @see validateAuthKey()
     */
    public function getAuthKey()
    {
        // TODO: Implement getAuthKey() method.
        return $this->auth_key;
    }

    /**
     * Validates the given auth key.
     *
     * This is required if [[User::enableAutoLogin]] is enabled.
     * @param string $authKey the given auth key
     * @return bool whether the given auth key is valid.
     * @see getAuthKey()
     */
    public function validateAuthKey($authKey)
    {
        // TODO: Implement validateAuthKey() method.
        return $this->getAuthKey()==$authKey;
    }
}
