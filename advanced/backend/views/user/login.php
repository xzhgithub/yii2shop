<?php
$form=\yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'username')->textInput();
echo $form->field($model,'password')->passwordInput();
echo $form->field($model,'code')->widget(\yii\captcha\Captcha::className(),[
    'captchaAction'=>'user/captcha',
    'template'=>'<div class="row"><div class="col-lg-2">{input}</div><div class="col-lg-2">{image}</div></div>'
]);
echo $form->field($model,'remember')->checkbox([1=>'记住本次登陆']);
echo '<br />';
echo \yii\bootstrap\Html::submitInput('登陆',['class'=>'btn btn-primary']);
\yii\bootstrap\ActiveForm::end();