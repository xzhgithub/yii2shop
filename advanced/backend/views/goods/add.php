<?php
$form=\yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'name');
if($model->sn){echo $form->field($model,'sn')->textInput(['readonly'=>true]);}else{echo $form->field($model,'sn')->textInput(['value'=>$sn,'readonly'=>true]);}
echo $form->field($model,'logo')->hiddenInput(['id'=>'logo_id']);
echo \yii\bootstrap\Html::fileInput('test', NULL, ['id' => 'test']);
echo xj\uploadify\Uploadify::widget([
    'url' => yii\helpers\Url::to(['s-upload']),
    'id' => 'test',
    'csrf' => true,
    'renderTag' => false,
    'jsOptions' => [
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
        //上传成功之后，将图片回显为img标签条件src属性
        $('.img').attr('src',data.fileUrl).show();
        //将文件地址保存到隐藏域
        $('#logo_id').val(data.fileUrl);
    }
}
EOF
        ),
    ]
]);

if($model->logo){
    echo \yii\bootstrap\Html::img($model->logo,['class'=>'img','height'=>'80']);
}else{
    echo \yii\bootstrap\Html::img('',['style'=>'display:none','class'=>'img','height'=>'80']);
}


echo $form->field($model,'goods_category_id')->hiddenInput(['id'=>'parent_id']);;
echo '<ul id="treeDemo" class="ztree"></ul>';
echo $form->field($model,'brand_id')->dropDownList(\yii\helpers\ArrayHelper::map($brand,'id','name'));
echo $form->field($model,'market_price');
echo $form->field($model,'shop_price');
echo $form->field($model,'stock');
echo $form->field($model,'is_on_sale',['inline'=>true])->radioList([1=>'上架',0=>'下架']);
echo $form->field($model,'status',['inline'=>true])->radioList([1=>'正常',0=>'回收站']);
echo $form->field($model,'sort');
if($goodsintro->content){echo $form->field($goodsintro,'content')->widget(\crazyfd\ueditor\Ueditor::className(),['value'=>$goodsintro->content]);}else{echo $form->field($goodsintro,'content')->widget(\crazyfd\ueditor\Ueditor::className());}
echo \yii\bootstrap\Html::submitInput('提交',['class'=>'btn btn-primary']);
\yii\bootstrap\ActiveForm::end();

//加载静态资源
$this->registerCssFile('@web/zTree/css/zTreeStyle/zTreeStyle.css');
$this->registerJsFile('@web/zTree/js/jquery.ztree.core.js',['depends'=>\yii\web\JqueryAsset::className()]);

$node=yii\helpers\Json::encode($categories);
$js=new \yii\web\JsExpression(
    <<<JS
 var zTreeObj;
    // zTree 的参数配置，深入使用请参考 API 文档（setting 配置详解）
    var setting = {
        data: {
            simpleData: {
                enable: true,
                idKey: "id",
                pIdKey: "parent_id",
                rootPId: 0
                }
	    },
        callback: {
		        onClick: function(event, treeId, treeNode) {
                    //alert(treeNode.tId + ", " + treeNode.name);
                    $('#parent_id').val(treeNode.id);
                 }
	    }
    };
    // zTree 的数据属性，深入使用请参考 API 文档（zTreeNode 节点数据详解）
    var zNodes = {$node};

    zTreeObj = $.fn.zTree.init($("#treeDemo"), setting, zNodes);

     zTreeObj.expandAll(true);//展开所有节点
    //获取当前节点的父节点（根据id查找）
    var node = zTreeObj.getNodeByParam("id", $("#parent_id").val(), null);
    zTreeObj.selectNode(node);//选中当前节点的父节点

JS

);
$this->registerJs($js);
?>