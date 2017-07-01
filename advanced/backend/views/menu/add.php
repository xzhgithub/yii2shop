<?php
$form=\yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'label');

echo $form->field($model,'url')->textInput(['placeholder'=>'添加一级菜单时，此处不用填写！']);
echo $form->field($model,'sort');
echo $form->field($model,'parent_id')->dropDownList(\yii\helpers\ArrayHelper::map($data,'id','label'),['prompt'=>'请选择']);
echo \yii\bootstrap\Html::submitInput('提交',['class'=>'btn btn-primary']);
\yii\bootstrap\ActiveForm::end();