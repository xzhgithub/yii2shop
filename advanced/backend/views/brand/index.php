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
            <td><img src="<?=Yii::getAlias('@web').$row->logo?>" width="80" class="img-circle"></td>
            <td><?=$row->name?></td>
            <td><?=$row->intro?></td>
            <td><?=\backend\models\Brand::$status[$row->status]?></td>
            <td><?=$row->sort?></td>
            <td><?=\yii\bootstrap\Html::a('',['brand/del','id'=>$row->id],['class'=>'glyphicon glyphicon-trash btn btn-danger btn-xs'])?>
                <?=\yii\bootstrap\Html::a('',['brand/edit','id'=>$row->id],['class'=>'glyphicon glyphicon-pencil btn btn-warning btn-xs'])?>
            </td>
        </tr>
    <?php endforeach;?>
</table>
<?php
echo \yii\widgets\LinkPager::widget([
    'pagination'=>$page,
    'prevPageLabel'=>'上一页',
    'nextPageLabel'=>'下一页',
    'firstPageLabel'=>true,
    'lastPageLabel'=>true,
    'options' => ['class' => 'pagination','style'=>'margin-left:35%']
]);
