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
            <td><?=$row->status==1?'正常':'异常'?></td>
            <td><?=$row->sort?></td>
            <td><?=$row->is_help==1?'帮助文档':'普通文档'?></td>
            <td><?=\yii\bootstrap\Html::a('删除',['article_category/del','id'=>$row->id],['class'=>'btn btn-danger btn-xs'])?>
                <?=\yii\bootstrap\Html::a('修改',['article_category/edit','id'=>$row->id],['class'=>'btn btn-warning btn-xs'])?>
            </td>
        </tr>
    <?php endforeach;?>
</table>
