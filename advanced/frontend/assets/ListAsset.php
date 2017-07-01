<?php
namespace frontend\assets;

use yii\web\AssetBundle;

class ListAsset extends AssetBundle
{
    public $basePath = '@webroot';//静态资源的硬盘路径
    public $baseUrl = '@web';//静态资源的url路径
    //需要加载的css文件
    public $css = [
        'style/base.css',
        'style/global.css',
        'style/header.css',
        'style/list.css',
        'style/common.css',
        'style/bottomnav.css',
        'style/footer.css',
        'style/home.css',
        'style/address.css',
        'style/goods.css',
        'style/jqzoom.css',
        'style/order.css',

//
//<link rel="stylesheet" href="style/base.css" type="text/css">
//	<link rel="stylesheet" href="style/global.css" type="text/css">
//	<link rel="stylesheet" href="style/header.css" type="text/css">
//	<link rel="stylesheet" href="style/home.css" type="text/css">
//	<link rel="stylesheet" href="style/order.css" type="text/css">
//	<link rel="stylesheet" href="style/bottomnav.css" type="text/css">
//	<link rel="stylesheet" href="style/footer.css" type="text/css">
//
//	<script type="text/javascript" src="js/jquery-1.8.3.min.js"></script>
//	<script type="text/javascript" src="js/header.js"></script>
//	<script type="text/javascript" src="js/home.js"></script>

    ];
    //需要加载的js文件
    public $js = [
        'js/jquery-1.8.3.min.js',
        'js/header.js',
        'js/list.js',
        'js/jqzoom-core.js',
        'js/goods.js',
        'js/home.js',
    ];
    //和其他静态资源管理器的依赖关系
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}