<?php
//判断是否有权限
if(Yii::$app->user->can('menu/add')){
    echo \yii\bootstrap\Html::a('添加',['menu/add'],['class'=>'btn btn-primary']);
}
?>

<table class="table table-bordered table-striped table-hover">
    <thead>
    <tr>
        <th>ID</th>
        <th>菜单名称</th>
        <th>地址/路由</th>
        <th>上级菜单</th>
        <th>排序</th>
        <th>操作</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach($models as $model):?>
    <tr>
        <td><?=$model->id?></td>
        <td><?=$model->label?></td>
        <td><?=$model->url?></td>
        <td><?=$model->parent_id==0?'一级分类':$model->parent->label?></td>
        <td><?=$model->sort?></td>
        <td>
            <?php
            if(Yii::$app->user->can('menu/edit')) {
                echo \yii\bootstrap\Html::a('修改',['menu/edit','id'=>$model->id],['class'=>'btn btn-warning btn-xs']);
            }
            if(Yii::$app->user->can('menu/del')) {
                echo \yii\bootstrap\Html::a('删除',['menu/del','id'=>$model->id],['class'=>'btn btn-danger btn-xs']);
            }
            ?>
        </td>
    </tr>
    <?php endforeach;?>
    </tbody>
</table>

<?php
/**
 * @var $this \yii\web\View
 */
$this->registerCssFile('//cdn.datatables.net/1.10.15/css/jquery.dataTables.min.css');
$this->registerJsFile('//cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js',['depends'=>\yii\web\JqueryAsset::className()]);
$this->registerJs('$(".table").DataTable({

});');