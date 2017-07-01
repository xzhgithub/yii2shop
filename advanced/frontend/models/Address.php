<?php
namespace frontend\models;

use yii\db\ActiveRecord;

class Address extends ActiveRecord{

    public $remember;
    public function rules(){
        return[
            [['username','province','city','county','address','tel'],'required'],
            ['username','unique'],
            ['remember','safe'],
        ];
    }

    public function attributeLabels(){
        return[
            'username'=>'收货人',
            'province'=>'省份',
            'city'=>'城市',
            'county'=>'县',
            'address'=>'详细地址',
            'tel'=>'电话',
            'remember'=>'设为默认收获地址',
        ];

    }

    public function getProvinceaddress(){
        return $this->hasOne(Locations::className(),['id'=>'province']);
        }

    public function getCityaddress(){
        return $this->hasOne(Locations::className(),['id'=>'city']);
    }

    public function getCountyaddress(){
        return $this->hasOne(Locations::className(),['id'=>'county']);
    }
}