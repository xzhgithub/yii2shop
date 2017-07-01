<?php
$form=\yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'name');
echo $form->field($model,'description')->textarea();
echo $form->field($model,'permissions')->checkboxList(\backend\models\RoleForm::getPermissions());
echo \yii\bootstrap\Html::submitInput('提交',['class'=>'btn btn-primary']);
\yii\bootstrap\ActiveForm::end();