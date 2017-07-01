<?php

namespace backend\models;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "user".
 *
 * @property integer $id
 * @property string $username
 * @property string $auth_key
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $last_time
 * @property string $last_ip
 */
class User extends ActiveRecord implements IdentityInterface
{
    /**
     * @inheritdoc
     */
    public $roles=[];//给用户添加角色
    public $password;
//    public $img;
//    public $imgFile;

//    //定义场景常量
    const SCENARIO_ADD = 'add';
    const SCENARIO_EDIT = 'edit';
//    //定义场景字段
    public function scenarios()
    {
        $scenarios =  parent::scenarios();
        $scenarios[self::SCENARIO_ADD] = ['username','auth_key','roles','last_ip','password_reset_token','password','password_hash','email','status'];
        $scenarios[self::SCENARIO_EDIT] = ['username','auth_key','roles','last_ip','password_reset_token', 'email','status'];
        return $scenarios;
    }


    public static function tableName()
    {
        return 'user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username',  'password_hash', 'email','status'], 'required'],
//            [['username',   'email','status'], 'required','on'=>self::SCENARIO_EDIT],
            [['status'], 'integer'],
            ['roles','safe'],
            [['username', 'password_hash', 'password_reset_token', 'email', 'last_ip'], 'string', 'max' => 255],
            [['auth_key'], 'string', 'max' => 32],
            [['username'], 'unique'],
            [['email'], 'unique'],
            [['password_reset_token'], 'unique'],
            //验证两次输入的密码是否一致
            ['password','compare','compareAttribute'=>'password_hash','on'=>self::SCENARIO_ADD],
//            ['img','file','extensions'=>['jpg','png','gif'],'skipOnEmpty'=>true,'message'=>'文件格式错误'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => '用户名',
            'auth_key' => 'Auth Key',
            'password_hash' => '密码',
            'password_reset_token' => 'Password Reset Token',
            'email' => '邮箱',
            'status' => '状态',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'last_time' => 'Last Time',
            'last_ip' => 'Last Ip',
            'password'=>'确认密码',
            'img'=>'头像',
            'roles'=>'角色',
        ];
    }


    //获取所有角色
    public static function getRoles(){
        $authManager=Yii::$app->authManager;
        $roles=$authManager->getRoles();//获取所有角色
        return ArrayHelper::map($roles,'name','description');
    }

    //将该用户的角色数据添加到model
    public function loadRole($id){
        $roles=Yii::$app->authManager->getRolesByUser($id);
        foreach($roles as $role){
            $this->roles[]=$role->name;
        }
    }



    /**
     * Finds an identity by the given ID.
     * @param string|integer $id the ID to be looked for
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
     * @return string|integer an ID that uniquely identifies a user identity.
     */
    public function getId()
    {
        // 获取当前账户的id
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
        return $this->auth_key;
        // TODO: Implement getAuthKey() method.
    }

    /**
     * Validates the given auth key.
     *
     * This is required if [[User::enableAutoLogin]] is enabled.
     * @param string $authKey the given auth key
     * @return boolean whether the given auth key is valid.
     * @see getAuthKey()
     */
    public function validateAuthKey($authKey)
    {

        return $this->getAuthKey() === $authKey;
        // TODO: Implement validateAuthKey() method.
    }


}
