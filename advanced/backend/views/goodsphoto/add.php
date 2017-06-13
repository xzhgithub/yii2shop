<?php
$form=\yii\bootstrap\ActiveForm::begin();

echo $form->field($photo,'img')->hiddenInput(['id'=>'logo_id']);
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

if($photo->img){
    echo \yii\bootstrap\Html::img($photo->img,['class'=>'img','height'=>'200']);
}else{
    echo \yii\bootstrap\Html::img('',['style'=>'display:none','class'=>'img','height'=>'200']);
}



echo \yii\bootstrap\Html::submitInput('提交',['class'=>'btn btn-primary']);
\yii\bootstrap\ActiveForm::end();