<?php
//判断是否有权限
if(Yii::$app->user->can('rbac/addpermission')){
    echo \yii\bootstrap\Html::a('添加',['rbac/addpermission'],['class'=>'btn btn-primary']);
}
?>

<table class="table table-bordered table-striped table-hover">
    <thead>
    <tr>
        <th>权限名称</th>
        <th>权限描述</th>
        <th>操作</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach($models as $model):?>
    <tr>
        <td><?=$model->name?></td>
        <td><?=$model->description?></td>
        <td>
            <?php
            if(Yii::$app->user->can('rbac/editpermission')) {
                echo \yii\bootstrap\Html::a('修改',['rbac/editpermission','name'=>$model->name],['class'=>'btn btn-warning btn-xs']);
            }
            if(Yii::$app->user->can('rbac/delpermission')) {
                echo \yii\bootstrap\Html::a('删除',['rbac/delpermission','name'=>$model->name],['class'=>'btn btn-danger btn-xs']);
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