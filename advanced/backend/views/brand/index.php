<?=\yii\bootstrap\Html::a('添加',['brand/add'],['class'=>'btn btn-primary'])?>
<table class="table table-bordered table-striped table-hover">
    <tr>
        <td>ID</td>
        <td>LOGO</td>
        <td>名称</td>
        <td>简介</td>
        <td>状态</td>
        <td>排序</td>
        <td>操作</td>
    </tr>
    <?php foreach($model as $row):?>
        <tr>
            <td><?=$row->id?></td>
            <td><img src="<?=$row->logo?>" width="80" class="img-circle"></td>
            <td><?=$row->name?></td>
            <td><?=$row->intro?></td>
            <td><?=$row->status?></td>
            <td><?=$row->sort?></td>
            <td><?=\yii\bootstrap\Html::a('删除',['brand/del','id'=>$row->id],['class'=>'btn btn-danger btn-xs'])?>
                <?=\yii\bootstrap\Html::a('修改',['brand/edit','id'=>$row->id],['class'=>'btn btn-warning btn-xs'])?>
            </td>
        </tr>
    <?php endforeach;?>
</table>
