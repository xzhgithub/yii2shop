<?php
namespace frontend\widgets;

use frontend\models\GoodsCategory;
use yii\base\Widget;
use yii\helpers\Html;

class CategoryWidget extends Widget{

    public function init(){
        parent::init();
    }

    public function run(){
        //利用redis缓存
        $redis=new \Redis();
        $redis->connect('127.0.0.1');
        $category_html=$redis->get('category_html');
        if($category_html==null) {
            //从数据库获取
            $models = GoodsCategory::find()->all();

            $category_html = $this->renderFile('@app/widgets/GoodsCategory.php', ['models' => $models]);
            $redis->set('category_html', $category_html,3600*24);

        }

        return $category_html;


    }
}