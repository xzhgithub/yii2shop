<?php
//判断是否有权限
if(Yii::$app->user->can('goodscategory/add')){
    echo \yii\bootstrap\Html::a('添加',['goodscategory/add'],['class'=>'btn btn-primary']);
}
?>

<table class="table table-bordered table-striped table-hover">
 <tr>
     <td>ID</td>
     <td>分类名称</td>
     <td>上级分类名称</td>
     <td>简介</td>
     <td>操作</td>
    </tr>
<?php foreach($model as $row):?>
    <tr data-tree="<?=$row->tree?>" data-lft="<?=$row->lft?>" data-rgt="<?=$row->rgt?>">
        <td><?=$row->id?></td>
        <td><?=str_repeat('- ',$row->depth).$row->name?><span class="cate glyphicon glyphicon-chevron-down" style="float: right;"></span></td>
        <td><?=$row->parent_id?$row->parent->name:'顶级分类'?></td>
        <td><?=$row->intro?></td>
        <td>
            <?php
            if(Yii::$app->user->can('goodscategory/edit')) {
                echo \yii\helpers\Html::a('修改',['goodscategory/edit','id'=>$row->id],['class'=>'btn btn-info']);
            }
            ?>
        </td>
        </tr>
    <?php endforeach;?>

</table>
<?php
$js=new \yii\web\JsExpression(
    <<<js
    //点击图标时,隐藏该分类下的分类
    $('.cate').on('click',function(){

        //找到该图标所在行
        var tr=$(this).closest('tr');
        //获取该行的属性值,转换成整数
        var tree=parseInt(tr.attr('data-tree'));
        var lft=parseInt(tr.attr('data-lft'));
        var rgt=parseInt(tr.attr('data-rgt'));

        //获取该行的类属性
        var show=$(this).hasClass('glyphicon glyphicon-chevron-down');

        //判断显示哪个图标
        $(this).toggleClass('glyphicon glyphicon-chevron-up');
        $(this).toggleClass('glyphicon glyphicon-chevron-down');

        //遍历，找到该分离下的分类行
        $('.table tr').each(function(){
        //判断是否是该分类下的
        if(parseInt($(this).attr('data-tree'))==tree&&parseInt($(this).attr('data-lft'))>lft&&parseInt($(this).attr('data-rgt'))<rgt){
            console.log(show)
            //根据类的属性判断是隐藏，还是显示
            show?$(this).hide():$(this).show();
        }
        })
    })

js

);
$this->registerJs($js);
