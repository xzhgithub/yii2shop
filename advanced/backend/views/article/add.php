<?php
$form=\yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'name');
echo $form->field($model,'intro')->textarea();
echo $form->field($model,'article_category_id')->dropDownList(\yii\helpers\ArrayHelper::map($data,'id','name'),['prompt'=>'请选择文章分类']);
echo $form->field($model,'sort');
echo $form->field($model,'status',['inline'=>true])->radioList([1=>'正常',0=>'隐藏']);
echo \yii\bootstrap\Html::submitInput('提交',['class'=>'btn btn-primary']);
\yii\bootstrap\ActiveForm::end();