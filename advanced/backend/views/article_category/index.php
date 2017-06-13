<?=\yii\bootstrap\Html::a('添加',['article_category/add'],['class'=>'btn btn-primary'])?>
<table class="table table-bordered table-striped table-hover">
    <tr>
        <td>ID</td>
        <td>名称</td>
        <td>简介</td>
        <td>状态</td>
        <td>排序</td>
        <td>类型</td>
        <td>操作</td>
    </tr>
    <?php foreach($model as $row):?>
        <tr>
            <td><?=$row->id?></td>
            <td><?=$row->name?></td>
            <td><?=$row->intro?></td>
            <td><?=\backend\models\ArticleCategory::$status[$row->status]?></td>
            <td><?=$row->sort?></td>
            <td><?=$row->is_help==1?'帮助文档':'普通文档'?></td>
            <td><?=\yii\bootstrap\Html::a('',['article_category/del','id'=>$row->id],['class'=>'glyphicon glyphicon-trash btn btn-danger btn-xs'])?>
                <?=\yii\bootstrap\Html::a('',['article_category/edit','id'=>$row->id],['class'=>'glyphicon glyphicon-pencil btn btn-warning btn-xs'])?>
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
