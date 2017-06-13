<?=\yii\bootstrap\Html::a('添加',['goodsphoto/add','goods_id'=>$goods_id],['class'=>'btn btn-primary'])?>

<table class="table table-bordered table-hover table-striped">
    <tr>
        <th>ID</th>
        <th>图片</th>
        <th>操作</th>
    </tr>
    <?php foreach($models as $model):?>
        <tr>
            <td><?=$model->id?></td>
            <td><img src="<?=$model->img?>" height="50"></td>
            <td><?=\yii\bootstrap\Html::a('修改',['goodsphoto/edit','id'=>$model->id],['class'=>'btn btn-warning btn-xs'])?>
                <?=\yii\bootstrap\Html::a('删除',['goodsphoto/del','id'=>$model->id],['class'=>'btn btn-danger btn-xs'])?>
            </td>
        </tr>
    <?php endforeach;?>
</table>
