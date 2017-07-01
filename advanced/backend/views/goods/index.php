<?php
//判断是否有权限
if(Yii::$app->user->can('goods/add')){
    echo \yii\bootstrap\Html::a('添加',['goods/add'],['class'=>'btn btn-primary']);
}

$form=\yii\bootstrap\ActiveForm::begin(['method'=>'get','action'=>['goods/index'],'options'=>['class'=>'form-inline']]);
echo $form->field($search,'keywords')->textInput(['placeholder'=>'name'])->label(false);
echo $form->field($search,'sn')->textInput(['placeholder'=>'sn'])->label(false);
echo $form->field($search,'minprice')->textInput(['placeholder'=>'min-price'])->label(false);
echo $form->field($search,'maxprice')->textInput(['placeholder'=>'max-price'])->label('-');
echo \yii\bootstrap\Html::submitInput('搜索',['class'=>'btn btn-primary']);
\yii\bootstrap\ActiveForm::end();
?>
<table class="table table-bordered table-hover table-striped">
    <tr>
        <th>ID</th>
        <th>商品LOGO</th>
        <th>商品名称</th>
        <th>商品货号</th>
        <th>所属分类</th>
        <th>所属品牌</th>
        <th>市场价格</th>
        <th>商品售价</th>
        <th>库存</th>
        <th>状态</th>
        <th>是否上架</th>
        <th>排序</th>
        <th>添加时间</th>
        <th>操作</th>
    </tr>
    <?php foreach($models as $model):?>
    <tr>
        <td><?=$model->id?></td>
        <td><img src="<?=$model->logo?>" height="50"></td>
        <td><?=$model->name?></td>
        <td><?=$model->sn?></td>
        <td><?=$model->category->name?></td>
        <td><?=$model->brand->name?></td>
        <td><?=$model->market_price?></td>
        <td><?=$model->shop_price?></td>
        <td><?=$model->stock?></td>
        <td><?=$model->status==1?'正常':'回收站'?></td>
        <td><?=$model->is_on_sale==1?'在售':'下架'?></td>
        <td><?=$model->sort?></td>
        <td><?=date('Y-m-d H:i:s',$model->create_time)?></td>
        <td>
            <?php
                if(Yii::$app->user->can('goods/edit')) {
                    echo \yii\bootstrap\Html::a('修改', ['goods/edit','id'=>$model->id], ['class' => 'btn btn-danger btn-xs']);
                }
                if(Yii::$app->user->can('goods/del')) {
                    echo \yii\bootstrap\Html::a('删除', ['goods/del','id'=>$model->id], ['class' => 'btn btn-warning btn-xs']);
                }

                if(Yii::$app->user->can('goods/intro')) {
                    echo \yii\bootstrap\Html::a('详情',['goods/intro','id'=>$model->id], ['class'=>'btn btn-primary btn-xs']);
                }
                if(Yii::$app->user->can('goodsphoto/index')) {
                    echo \yii\bootstrap\Html::a('相册',['goodsphoto/index','id'=>$model->id], ['class'=>'btn btn-info btn-xs']);
                }
            ?>
        </td>
    </tr>
    <?php endforeach;?>
</table>
<?php
echo \yii\widgets\LinkPager::widget([
    'pagination'=>$page,
    'firstPageLabel'=>true,
    'lastPageLabel'=>true,
    'nextPageLabel'=>'下一页',
    'prevPageLabel'=>'上一页',
]);