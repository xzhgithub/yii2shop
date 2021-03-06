<?php

namespace backend\controllers;

use backend\components\RbacFilter;
use backend\models\BackendController;
use backend\models\Brand;
use backend\models\Goods;
use backend\models\GoodsCategory;
use backend\models\Goodsdaycount;
use backend\models\Goodsintro;
use backend\models\GoodssearchForm;
use xj\uploadify\UploadAction;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use backend\components\SphinxClient;

class GoodsController extends Controller
{
     //过滤器
    public function behaviors(){
        return[
            'rbac'=>[
                'class'=>RbacFilter::className(),
                'only'=>['index','add','edit','del','intro'],
            ]
        ];
    }


    public function actionIndex()
    {
        //实现分页
        $query=Goods::find();

        //实例化搜索模型
        $search=new GoodssearchForm();

        if($keyword = \Yii::$app->request->get('GoodssearchForm')["keywords"]){
            $cl = new SphinxClient();
            $cl->SetServer ( '127.0.0.1', 9312);
            $cl->SetConnectTimeout ( 10 );
            $cl->SetArrayResult ( true );
            $cl->SetMatchMode ( SPH_MATCH_ALL);
            $cl->SetLimits(0, 1000);
            $res = $cl->Query($keyword, 'goods');//shopstore_search
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
        /*if($keyword = \Yii::$app->request->get('keyword')){
            $query->andWhere(['like','name',$keyword]);
        }
        if($sn = \Yii::$app->request->get('sn')){
            $query->andWhere(['like','sn',$sn]);
        }*/

        //接收表单提交的查询参数
        //$model->search($query);


        //商品名称含有"耳机"的  name like "%耳机%"
//        $query = Goods::find()->where(['like','name','耳机']);
        $count=$query->andwhere('status=1')->count();
            $page=new Pagination([
                'defaultPageSize'=>2,
                'totalCount'=>$count,
            ]);

        $models=$query->andWhere('status=1')->orderBy('sort')->offset($page->offset)->limit($page->limit)->all();
        if($keyword = \Yii::$app->request->get('GoodssearchForm')["keywords"]) {
            $keywords = array_keys($res['words']);
            $options = array(
                'before_match' => '<span style="color:red;">',
                'after_match' => '</span>',
                'chunk_separator' => '...',
                'limit' => 80, //如果内容超过80个字符，就使用...隐藏多余的的内容
            );
//关键字高亮
//        var_dump($models);exit;
            foreach ($models as $index => $item) {
                $name = $cl->BuildExcerpts([$item->name], 'goods', implode(',', $keywords), $options); //使用的索引不能写*，关键字可以使用空格、逗号等符号做分隔，放心，sphinx很智能，会给你拆分的
                $models[$index]->name = $name[0];
//            var_dump($name);
            }


        }
//        $request=\Yii::$app->request;
//        if($request->isGet) {
//            //接收
//            $search->load($request->get());
//
//            if ($search->keywords) {
//                $query = $query->andwhere( ['like', 'name', $search->keywords]);
//            }
//            if ($search->sn) {
//                $query = $query->andwhere( ['like', 'sn', $search->sn]);
//            }
//            if ($search->minprice) {
//                $query = $query->andwhere( ['>=', 'shop_price', $search->minprice]);
//            }
//            if ($search->maxprice) {
//                $query = $query->andwhere( ['<=', 'shop_price', $search->maxprice]);
//            }
//
//        }

            //总条数
//            $count=$query->andwhere('status=1')->count();
//            $page=new Pagination([
//                'defaultPageSize'=>2,
//                'totalCount'=>$count,
//            ]);
//            $models=$query->andWhere('status=1')->orderBy('sort')->offset($page->offset)->limit($page->limit)->all();


        return $this->render('index',['models'=>$models,'search'=>$search,'page'=>$page]);
    }


    //添加商品
    public function actionAdd(){
        //实例化模型
        $model=new Goods();
        //实例化商品详情模型
        $goodsintro=new Goodsintro();
        //获取品牌数据
        $brand=Brand::find()->where('status=1')->all();
        //获取商品分类数据
        $categories=GoodsCategory::find()->all();
        //新增商品自动生成sn,规则为年月日+今天的第几个商品
            //判断今天是否已添加过商品，没有添加过则数据不存在
        $time=time();
        $day=date('Ymd',$time);
//        var_dump($day);exit;
        $goods=Goodsdaycount::findOne(['day'=>$day]);
        if($goods){
            //已添加过
//            $sn=$day.'000'.($goods->count+1);
            $sn = date('Ymd').sprintf("%04d",$goods->count+1);
        }else{
            //未添加过
            //添加一条数据
            $goods=new Goodsdaycount();
            $goods->day=$day;
            $goods->count=0;
            $goods->save();
            $sn = date('Ymd').sprintf("%04d",$goods->count+1);

        }

        //判断传参方式
        $request=\Yii::$app->request;
        if($request->isPost){
            //接收
            $model->load($request->post());
            $goodsintro->load($request->post());
            //验证
            if($model->validate()){
                //添加时间
                $model->create_time=time();
                //保存
                $model->save();
                //goods_day_count表今天的商品数量+1
                $goods=Goodsdaycount::findOne(['day'=>$day]);
                $goods->count=$goods->count+1;
                $goods->save();
                //将商品详情保存到goods_intro 商品详情表
                $intro=new Goodsintro();
                $intro->goods_id=$model->id;
                $intro->content=$goodsintro->content;
                $intro->save();
                //提示、跳转
                \Yii::$app->session->setFlash('success','添加成功');
                return $this->redirect(['goods/index']);
            }
        }

        return $this->render('add',['model'=>$model,'brand'=>$brand,'categories'=>$categories,'sn'=>$sn,'goodsintro'=>$goodsintro]);
    }

    //删除
    public function actionDel($id){
       //获取该数据
       $goods=Goods::findOne(['id'=>$id]);
        //逻辑删除
        $goods->status=0;
        $goods->save();
        //提示、跳转
        \Yii::$app->session->setFlash('success','删除成功');
        return $this->redirect(['goods/index']);
    }

    //修改
    public function actionEdit($id){
        //获取数据
        $model=Goods::findOne(['id'=>$id]);
//        var_dump($model);exit;
        $goodsintro=Goodsintro::findOne(['goods_id'=>$id]);
        //获取品牌数据
        $brand=Brand::find()->where('status=1')->all();
        //获取商品分类数据
        $categories=GoodsCategory::find()->all();

        //判断传参方式
        $request=\Yii::$app->request;
        if($request->isPost){
            //接收
            $model->load($request->post());
            $goodsintro->load($request->post());
            //验证
            if($model->validate()){
                //保存
                $model->save();
                //将商品详情保存到goods_intro 商品详情表

//                $goodsintro->goods_id=$model->id;
                $goodsintro->save();
                //提示、跳转
                \Yii::$app->session->setFlash('success','修改成功');
                return $this->redirect(['goods/index']);
            }
        }



        return $this->render('add',['model'=>$model,'goodsintro'=>$goodsintro,'brand'=>$brand,'categories'=>$categories]);
    }

    //详情
    public function actionIntro($id){
        //读取该数据
        $intro=Goodsintro::findOne(['goods_id'=>$id]);
        $data=Goods::findOne(['id'=>$id]);
//        var_dump($intro);exit;
        //分配数据显示
        return $this->render('intro',['intro'=>$intro,'data'=>$data]);
    }



    public function actions() {
        return [
            's-upload' => [
                'class' => UploadAction::className(),
                'basePath' => '@webroot/upload',
                'baseUrl' => '@web/upload',
                'enableCsrf' => true, // default
                'postFieldName' => 'Filedata', // default
                //BEGIN METHOD
                'format' => [$this, 'methodName'],
                //END METHOD
                //BEGIN CLOSURE BY-HASH
                'overwriteIfExist' => true,
//                'format' => function (UploadAction $action) {
//                    $fileext = $action->uploadfile->getExtension();
//                    $filename = sha1_file($action->uploadfile->tempName);
//                    return "{$filename}.{$fileext}";
//                },
                //END CLOSURE BY-HASH
                //BEGIN CLOSURE BY TIME
                'format' => function (UploadAction $action) {
                    $fileext = $action->uploadfile->getExtension();
                    $filehash = sha1(uniqid() . time());
                    $p1 = substr($filehash, 0, 2);
                    $p2 = substr($filehash, 2, 2);
                    return "{$p1}/{$p2}/{$filehash}.{$fileext}";
                },
                //END CLOSURE BY TIME
                'validateOptions' => [
                    'extensions' => ['jpg', 'png'],
                    'maxSize' => 1 * 1024 * 1024, //file size
                ],
                'beforeValidate' => function (UploadAction $action) {
                    //throw new Exception('test error');
                },
                'afterValidate' => function (UploadAction $action) {},
                'beforeSave' => function (UploadAction $action) {},
                'afterSave' => function (UploadAction $action) {
//                    $action->output['fileUrl'] = $action->getWebUrl();

                    $qiniu=\Yii::$app->qiniu;
                    $qiniu->uploadFile($action->getSavePath(),$action->getWebUrl());
                    //获取图片在七牛云上的地址
                    $url=$qiniu->getLink($action->getWebUrl());
                    //将回显图片地址设置成七牛云上的地址
                    $action->output['fileUrl'] = $url;

                },
            ],
        ];

        //添加ueditor插件
        return [

            'ueditor' => [
                'class' => 'crazyfd\ueditor\Upload',
                'config'=>[
                    'uploadDir'=>date('Y/m/d')
                ]

            ],
        ];
    }



}
