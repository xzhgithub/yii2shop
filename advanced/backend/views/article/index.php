<?=\yii\bootstrap\Html::a('添加',['article/add'],['class'=>'btn btn-primary'])?>
<table class="table table-bordered table-striped table-hover">
    <tr>
        <td>ID</td>
        <td>名称</td>
        <td>简介</td>
        <td>分类</td>
        <td>状态</td>
        <td>排序</td>
        <td>创建时间</td>
        <td>操作</td>
    </tr>
    <?php foreach($model as $row):?>
        <tr>
            <td><?=$row->id?></td>
            <td><?=$row->name?></td>
            <td><?=\yii\bootstrap\Html::a($row->intro,['article_detail/index','id'=>$row->id])?></td>
            <td><?=$row->category->name?></td>
            <td><?=\backend\models\Article::$setStatus[$row->status]?></td>
            <td><?=$row->sort?></td>
            <td><?=date('Y-m-d H:i:s',$row->create_time)?></td>
            <td><?=\yii\bootstrap\Html::a('',['article/del','id'=>$row->id],['class'=>'glyphicon glyphicon-trash btn btn-danger btn-xs'])?>
                <?=\yii\bootstrap\Html::a('',['article/edit','id'=>$row->id],['class'=>'glyphicon glyphicon-pencil btn btn-warning btn-xs'])?>
            </td>
        </tr>
    <?php endforeach;?>
</table>
