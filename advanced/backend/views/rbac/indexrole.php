<?php
//判断是否有权限
if(Yii::$app->user->can('rbac/addrole')){
    echo \yii\bootstrap\Html::a('添加',['rbac/addrole'],['class'=>'btn btn-primary']);
}
?>

<table class="table table-bordered table-striped table-hover">
    <thead>
    <tr>
        <th>角色名称</th>
        <th>角色描述</th>
        <th>角色权限</th>
        <th>操作</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach($models as $model):?>
    <tr>
        <td><?=$model->name?></td>
        <td><?=$model->description?></td>
        <td><?php
            //遍历循环，通过角色获取权限
            foreach(Yii::$app->authManager->getPermissionsByRole($model->name) as $permission){
                //通过权限获取描述description
                echo $permission->description;
                echo '、';
            }
            ?></td>
        <td>
            <?php
            if(Yii::$app->user->can('rbac/editrole')) {
                echo \yii\bootstrap\Html::a('修改',['rbac/editrole','name'=>$model->name],['class'=>'btn btn-warning btn-xs']);
            }
            if(Yii::$app->user->can('rbac/delrole')) {
                echo \yii\bootstrap\Html::a('删除',['rbac/delrole','name'=>$model->name],['class'=>'btn btn-danger btn-xs']);
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