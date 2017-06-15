<?php

$goods_id=$_GET['id'];


echo \yii\bootstrap\Html::fileInput('test', NULL, ['id' => 'test']);
echo xj\uploadify\Uploadify::widget([
    'url' => yii\helpers\Url::to(['s-upload']),
    'id' => 'test',
    'csrf' => true,
    'renderTag' => false,
    'jsOptions' => [
    'formData'=>['goods_id'=>$goods_id],//上传文件的同时传参goods_id
    'width' => 120,
    'height' => 40,
    'onUploadError' => new \yii\web\JsExpression(<<<EOF
        function(file, errorCode, errorMsg, errorString) {
        console.log('The file ' + file.name + ' could not be uploaded: ' + errorString + errorCode + errorMsg);
        }
EOF
    ),
    'onUploadSuccess' => new \yii\web\JsExpression(<<<EOF
        function(file, data, response) {
        data = JSON.parse(data);
        if (data.error) {
            console.log(data.msg);
            } else {
                console.log(data.fileUrl);
                //上传成功之后，将图片回显添加一行tr
                var html='<tr data-id="'+data.id+'" id="gallery_'+data.id+'">';
                html += '<td><img src="'+data.fileUrl+'" height="50"/></td>';
                html += '<td><button type="button" class="btn btn-danger del_btn">删除</button></td>';
                html += '</tr>';
                $("table").append(html);

            }
        }
EOF
    ),
    ]
    ]);

?>

<table class="table table-bordered table-hover table-striped">
    <tr>
        <th>图片</th>
        <th>操作</th>
    </tr>
    <?php foreach($models as $model):?>
        <tr data-id=<?=$model->id?> id="gallery_<?=$model->id?>">
            <td><?=\yii\helpers\Html::img($model->img,['height'=>'50'])?></td>
            <td><?=\yii\helpers\Html::button('删除',['class'=>'btn btn-danger del_btn'])?></td>
            </td>
        </tr>
    <?php endforeach;?>
</table>
<?php
$url = \yii\helpers\Url::to(['goodsphoto/del']);
$this->registerJs(new \yii\web\JsExpression(
    <<<EOT
    $("table").on('click',".del_btn",function(){
        if(confirm("确定删除该图片吗?")){
        var id = $(this).closest("tr").attr("data-id");
            $.post("{$url}",{id:id},function(data){
                if(data=="success"){
                    //alert("删除成功");
                    $("#gallery_"+id).remove();
                }
            });
        }
    });
EOT

));