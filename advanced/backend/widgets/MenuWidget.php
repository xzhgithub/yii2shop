<?php
namespace backend\widgets;

use backend\models\Menu;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\bootstrap\Widget;

class MenuWidget extends Widget{
    //实例化后执行的方法（可写）
    public function init(){
        parent::init();
    }

    //调用widget方法时执行的方法
    public function run(){
        NavBar::begin([
            'brandLabel' => 'yii2框架学习',
            'brandUrl' => \Yii::$app->homeUrl,
            'options' => [
                'class' => 'navbar-inverse navbar-fixed-top',
            ],
        ]);


        $menuItems = [
//            ['label' => '品牌分类',  'url' => ['brand/index']],
//            ['label'=>'文章管理','items'=>
//                [
//                    ['label' => '文章分类', 'url' => ['article_category/index']],
//                    ['label' => '文章列表', 'url' => ['article/index']],
//                ]
//
//            ],
//            ['label'=>'商品管理','items'=>
//                [
//                    ['label' => '商品列表', 'url' => ['goods/index']],
//                    ['label' => '商品分类', 'url' => ['goodscategory/index']],
//                ]
//            ],
//            ['label' => '会员列表', 'url' => ['user/index']],
//        ['label' => '注销登陆', 'url' => ['user/logout']],
//        ['label' => '首页', 'url' => ['/site/index']],

        ];
        //判断是否登陆，显示对应菜单
        if (\Yii::$app->user->isGuest) {
            $menuItems[] = ['label' => '登陆', 'url' => ['user/login']];
            $menuItems[] = ['label' => '首页', 'url' => ['/site/index']];
        } else {
            //从数据库取出菜单数
            $menus=Menu::findAll(['parent_id'=>0]);//获取所有的一级菜单
            foreach($menus as $menu){
                //获取一级菜单下的所有二级菜单
                $tomenus=Menu::findAll(['parent_id'=>$menu->id]);

                $Items=['label'=>$menu->label,'items'=>[]];
                foreach($tomenus as $tomenu){

                    //根据权限添加菜单
                    if(\Yii::$app->user->can($tomenu->url)){
                        $Items['items'][]=['label'=>$tomenu->label,'url'=>[$tomenu->url]];
                    }
                }
                //判断一级菜单下是否有子菜单
                if(!empty($Items['items'])){
                    $menuItems[]=$Items;
                }
            }


            $menuItems[] = ['label'=>'注销   (' . \Yii::$app->user->identity->username . ')','url'=>['user/logout']];
        }


        echo Nav::widget([
            'options' => ['class' => 'navbar-nav navbar-right'],
            'items' => $menuItems,
        ]);
        NavBar::end();
    }
}