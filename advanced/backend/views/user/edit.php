<?php
$form=\yii\bootstrap\ActiveForm::begin();
//echo $form->field($model,'oldpassword')->passwordInput();
echo $form->field($model,'password')->passwordInput();
echo $form->field($model,'repassword')->passwordInput();
echo \yii\bootstrap\Html::submitInput('提交',['class'=>'btn btn-primary']);
\yii\bootstrap\ActiveForm::end();