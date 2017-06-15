<?php
namespace backend\models;

use yii\base\Model;

class GoodssearchForm extends Model{
    public $keywords;
    public $sn;
    public $minprice;
    public $maxprice;

    public function rules(){
        return[
            ['keywords','string'],
            ['sn','string'],
            ['minprice','double'],
            ['maxprice','double'],
        ];
    }

    public function attributeLabels(){
        return[
            'keywords'=>'',
            'sn'=>'',
            'minprice'=>'',
            'maxprice'=>'',
        ];
    }
}