<?php
use \yii\helpers\Html;
?>
	<!-- 登录主体部分start -->
	<div class="login w990 bc mt10 regist">
		<div class="login_hd">
			<h2>用户注册</h2>
			<b></b>
		</div>
		<div class="login_bd">
			<div class="login_form fl">

				<?php
				$form=\yii\widgets\ActiveForm::begin(
					['fieldConfig'=>[
						'options'=>[
							'tag'=>'li'
						],
						'errorOptions'=>[
							'tag'=>'p'
						],
					]
					]
				);
				echo '<ul>';
				echo $form->field($model,'username')->textInput(['class'=>'txt']);
				echo $form->field($model,'password')->passwordInput(['class'=>'txt']);
				echo $form->field($model,'repassword')->passwordInput(['class'=>'txt']);
				$button1=Html::button('发送邮件',['id'=>'email_button']);
				echo $form->field($model,'email')->textInput(['class'=>'txt']);
				echo $form->field($model,'email_code',['options'=>['class'=>'checkcode'],'template'=>"{label}\n{input}$button1\n{hint}\n{error}"])->textInput(['class'=>'txt']);
				echo $form->field($model,'tel')->textInput(['class'=>'txt']);
				$button2=Html::button('发送短信',['id'=>'sms_button']);
				echo $form->field($model,'tel_code',['options'=>['class'=>'checkcode'],'template'=>"{label}\n{input}$button2\n{hint}\n{error}"])->textInput(['class'=>'txt']);
				echo $form->field($model,'code',['options'=>['class'=>'checkcode']])->widget(\yii\captcha\Captcha::className(),['template'=>'{input}{image}']);
				echo '<li>
							<label for="">&nbsp;</label>
							<input type="submit" value="" class="login_btn">
						</li>';
				echo '</ul>';
				\yii\widgets\ActiveForm::end();

				?>

			</div>
			
			<div class="mobile fl">
				<h3>手机快速注册</h3>			
				<p>中国大陆手机用户，编辑短信 “<strong>XX</strong>”发送到：</p>
				<p><strong>1069099988</strong></p>
			</div>

		</div>
	</div>
	<!-- 登录主体部分end -->
<?php
/*@var $this yii\web\view
 */

	//点击发送短信按钮时发送短信
	$url1=\yii\helpers\Url::to(['user/send']);
	$url2=\yii\helpers\Url::to(['user/mailer']);
	$this->registerJs(new \yii\web\JsExpression(
		<<<JS
			$('#sms_button').click(function(){
				//获取电话号码
				var tel=$('#member-tel').val();
				//ajax请求提交tel发送短信
				$.post('$url1',{'tel':tel},function(data) {
				if(data=='success'){
					alert('短信发送成功');
				}else{
					alert('短信发送失败');
				}
				});

			});

			//点击发送邮件按钮
			$('#email_button').click(function(){

				//获取邮箱地址
				var email=$('#member-email').val();
				//ajax请求提交email发送邮件
				$.post('$url2',{'email':email},function(data) {
				if(data=='success'){
					alert('邮件发送成功');
				}else{
					alert('邮件发送失败');
				}
				});
			})
JS

	));



