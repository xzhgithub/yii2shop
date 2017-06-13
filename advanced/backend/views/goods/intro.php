<div style="border: 1px solid blue; margin: auto;background: gainsboro;width: 800px; min-height: 600px;">
    <div style="margin: 10px 50px;">当前位置><?=\yii\bootstrap\Html::a('商品列表',['goods/index'])?>>商品详情</div>
    <h2 style="text-align: center; margin-top: 30px;"><?=$data->name?></h2>
    <p style="text-align: center;">
        添加时间：<?=date('Y-m-d H:i:s',$data->create_time)?>
    </p>
    <div style="margin: 10px 50px;font: 16px/24px 微软雅黑;"><?=$intro->content?></div>
</div>