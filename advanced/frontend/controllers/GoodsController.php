<?php

namespace frontend\controllers;

use frontend\models\Brand;
use frontend\models\Goods;
use frontend\models\GoodsCategory;
use frontend\models\GoodsIntro;
use yii\web\Cookie;
use yii\web\NotFoundHttpException;
use backend\components\SphinxClient;
use yii\helpers\ArrayHelper;
class GoodsController extends \yii\web\Controller
{
    public $layout='list';

    //根据分类，列出商品列表
    public function actionList($category_id=0,$brandid=0){
//        var_dump(\Yii::$app->request->get('search_content'));exit;
        $query=Goods::find();

        //搜索
        if($search=\Yii::$app->request->get('search_content')){

            $cl = new SphinxClient();
            $cl->SetServer ( '127.0.0.1', 9312);
            $cl->SetConnectTimeout ( 10 );
            $cl->SetArrayResult ( true );
            $cl->SetMatchMode ( SPH_MATCH_ALL);
            $cl->SetLimits(0, 1000);
            $res = $cl->Query($search, 'goods');//shopstore_search
//            var_dump($res);exit;
            if(!isset($res['matches'])){
//                throw new NotFoundHttpException('没有找到xxx商品');
                $query->where(['id'=>0]);
            }else{

                //获取商品id
//                var_dump($res);exit;
                $ids = ArrayHelper::map($res['matches'],'id','id');
                $query->where(['in','id',$ids]);
            }
        }



        //获取该分类的所有商品,以及该分类下的所有分类的商品
        //1、获取该分类的所有商品
//        if($category_id) {
//            $goods = Goods::find()->Where(['goods_category_id' => $category_id])->andWhere(['status' => 1])->all();
//        }else{
//            $goods=Goods::findAll(['status' => 1]);
////            var_dump($goods);exit;
//        }

        $goods=$query->andwhere(['status' => 1])->all();
//        var_dump($goods);exit;

        //获取该商品的所以品牌id
        $brand_ids=[];
        if($goods){
            foreach($goods as $good ){

                $brand_ids[]=$good->brand_id;

            }
        }


        //去除重复品牌id
        $brand_ids=array_unique($brand_ids);
        //获取该商品的所有品牌
        $brands=[];
        foreach($brand_ids as $brand_id){
            $brands[]=Brand::findOne(['id'=>$brand_id]);
        }
//        var_dump($brands);exit;
        //根据品牌列出对应商品
        if($brandid!=0&&$category_id!=0){
            $goods=Goods::find()->andWhere(['brand_id'=>$brandid])->andWhere(['goods_category_id'=>$category_id])->all();
        }
        if($category_id!=0&&$brandid==0){
            //获取该分类的所有商品
            $goods=Goods::findAll(['goods_category_id'=>$category_id]);
        }

        if($search = \Yii::$app->request->get('search_content')) {
            $keywords = array_keys($res['words']);

            $options = array(
                'before_match' => '<span style="color:red;">',
                'after_match' => '</span>',
                'chunk_separator' => '...',
                'limit' => 80, //如果内容超过80个字符，就使用...隐藏多余的的内容
            );

//关键字高亮
//        var_dump($models);exit;
            foreach ($goods as $index => $item) {
                $name = $cl->BuildExcerpts([$item->name], 'goods', implode(',', $keywords), $options); //使用的索引不能写*，关键字可以使用空格、逗号等符号做分隔，放心，sphinx很智能，会给你拆分的
                $goods[$index]->name = $name[0];
//            var_dump($name);
            }
//            var_dump($goods);exit;

        }


        return $this->render('list',['brands'=>$brands,'goods'=>$goods,'category_id'=>$category_id]);
    }

    //商品详情
    public function actionGoodsintro($goods_id){

        //获取商品详情
        $goods=Goods::findOne(['id'=>$goods_id]);
//        var_dump($goods);exit;
        $goodsintro=GoodsIntro::findOne(['goods_id'=>$goods_id]);
        return $this->render('intro',['goods'=>$goods,'goodsintro'=>$goodsintro]);
    }




}
