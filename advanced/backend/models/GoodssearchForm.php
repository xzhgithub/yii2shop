<?php
namespace backend\models;

use yii\base\Model;

class GoodssearchForm extends Model{
    public $search;

    public function rules(){
        return[
            ['search','string'],
        ];
    }

    public function attributeLabels(){
        return[
            'search'=>'搜索内容',
        ];
    }
}