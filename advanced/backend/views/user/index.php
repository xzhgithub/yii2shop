<?=\yii\bootstrap\Html::a('添加',['user/add'],['class'=>'btn btn-primary'])?>
<table class="table table-bordered table-striped table-hover">
    <tr>
        <th>ID</th>
<!--        <th>头像</th>-->
        <th>用户名</th>
        <th>邮箱</th>
        <th>创建时间</th>
        <th>上次登陆时间</th>
        <th>上次登陆IP</th>
        <th>操作</th>
    </tr>
    <?php foreach($models as $model):?>
    <tr>
        <td><?=$model->id?></td>

        <td><?=$model->username?></td>
        <td><?=$model->email?></td>
        <td><?=$model->created_at==0?'从未登陆':date('Y-d-m H:i:s',$model->created_at)?></td>
        <td><?=date('Y-d-m H:i:s',$model->last_time)?></td>
        <td><?=$model->last_ip?$model->last_ip:'从未登陆'?></td>
        <td><?=\yii\bootstrap\Html::a('修改密码',['user/edit','id'=>$model->id],['class'=>'btn btn-warning btn-xs'])?>
            <?=\yii\bootstrap\Html::a('修改基本信息',['user/update','id'=>$model->id],['class'=>'btn btn-info btn-xs'])?>
            <?=\yii\bootstrap\Html::a('删除',['user/del','id'=>$model->id],['class'=>'btn btn-danger btn-xs'])?>

        </td>
    </tr>
    <?php endforeach;?>
</table>