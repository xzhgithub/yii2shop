<?php


namespace backend\models;
use Yii;
/**
 * This is the model class for table "brand".
 *
 * @property integer $id
 * @property string $name
 * @property string $intro
 * @property string $logo
 * @property integer $sort
 * @property integer $status
 */
class Brand extends \yii\db\ActiveRecord
{
    public static $status=[-1=>'删除',0=>'隐藏',1=>'正常'];
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'brand';
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name','intro','sort','status'],'required'],
            [['intro'], 'string'],
            [['logo'], 'string'],
            [['sort', 'status'], 'integer'],
            [['name'], 'string', 'max' => 20],
//            [['imgFile'], 'file', 'extensions'=>['jpg','gif','png']],
        ];
    }
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '品牌名称',
            'intro' => '简介',
            'sort' => '排序',
            'status' => '状态',
            'logo' => 'LOGO',
        ];
    }



}
