<?php
/*
 * @var $this \yii\web\view
 */
$form=\yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'name');
echo $form->field($model,'parent_id')->hiddenInput(['id'=>'parent_id']);
echo '<ul id="treeDemo" class="ztree"></ul>';
echo $form->field($model,'intro')->textarea();
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

