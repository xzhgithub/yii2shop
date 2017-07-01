<!-- 页面主体 start -->
<div class="main w1210 bc mt10">
    <div class="crumb w1210">
        <h2><strong>我的XX </strong><span>> 我的订单</span></h2>
    </div>

    <!-- 左侧导航菜单 start -->
    <div class="menu fl">
        <h3>我的XX</h3>
        <div class="menu_wrap">
            <dl>
                <dt>订单中心 <b></b></dt>
                <dd><b>.</b><a href="">我的订单</a></dd>
                <dd><b>.</b><a href="">我的关注</a></dd>
                <dd><b>.</b><a href="">浏览历史</a></dd>
                <dd><b>.</b><a href="">我的团购</a></dd>
            </dl>

            <dl>
                <dt>账户中心 <b></b></dt>
                <dd class="cur"><b>.</b><a href="">账户信息</a></dd>
                <dd><b>.</b><a href="">账户余额</a></dd>
                <dd><b>.</b><a href="">消费记录</a></dd>
                <dd><b>.</b><a href="">我的积分</a></dd>
                <dd><b>.</b><a href="">收货地址</a></dd>
            </dl>

            <dl>
                <dt>订单中心 <b></b></dt>
                <dd><b>.</b><a href="">返修/退换货</a></dd>
                <dd><b>.</b><a href="">取消订单记录</a></dd>
                <dd><b>.</b><a href="">我的投诉</a></dd>
            </dl>
        </div>
    </div>
    <!-- 左侧导航菜单 end -->

    <!-- 右侧内容区域 start -->
    <div class="content fl ml10">
<!--        <div class="address_hd">-->
<!--            <h3>收货地址薄</h3>-->

<!--                </dl>-->
<!--            --><?php //endforeach;?>
<!--            <dl class="last"> <!-- 最后一个dl 加类last -->
<!--                <!--					<dt>2.许坤 四川省 成都市 高新区 仙人跳大街 17002810530 </dt>-->
<!--                <!--					<dd>-->
<!--                <!--						<a href="">修改</a>-->
<!--                <!--						<a href="">删除</a>-->
<!--                <!--						<a href="">设为默认地址</a>-->
<!--                <!--					</dd>-->
<!--            </dl>-->
<!---->
<!--        </div>-->

        <div class="address_bd mt10">
            <h4>新增收货地址</h4>

            <!--						<ul>-->
            <!--							<li>-->

            <?php

            $form=\yii\widgets\ActiveForm::begin();
            echo $form->field($address,'username')->textInput(['class'=>'txt']);
            //									echo $form->field($model,'province')->dropDownList(['prompt'=>'请选择'])->label(false);
            //									echo $form->field($model,'city')->dropDownList(['prompt'=>'请选择'])->label(false);
            //									echo $form->field($model,'county')->dropDownList(['prompt'=>'请选择'])->label(false);

            echo $form->field($address, 'province')->widget(\chenkby\region\Region::className(),[
                'model'=>$address,
                'url'=> \yii\helpers\Url::toRoute(['get-region']),
                'province'=>[
                    'attribute'=>'province',
                    'items'=>\frontend\models\Locations::getRegion(),
                    'options'=>['class'=>'form-control form-control-inline','prompt'=>'选择省份']
                ],
                'city'=>[
                    'attribute'=>'city',
                    'items'=>\frontend\models\Locations::getRegion($address['province']),
                    'options'=>['class'=>'form-control form-control-inline','prompt'=>'选择城市']
                ],
                'district'=>[
                    'attribute'=>'county',
                    'items'=>\frontend\models\Locations::getRegion($address['city']),
                    'options'=>['class'=>'form-control form-control-inline','prompt'=>'选择县/区']
                ]
            ]);



            echo $form->field($address,'address')->textInput(['class'=>'txt']);
            echo $form->field($address,'tel')->textInput(['class'=>'txt']);
            echo $form->field($address,'remember')->checkbox([1=>'设为默认地址']);
            echo \yii\helpers\Html::submitInput('保存');
            \yii\widgets\ActiveForm::end();

            ?>
            <!--								<label for=""><span>*</span>收 货 人：</label>-->
            <!--								<input type="text" name="" class="txt" />-->
            <!--							</li>-->
            <!--							<li>-->
            <!--								<label for=""><span>*</span>所在地区：</label>-->
            <!--								<select name="" id="">-->
            <!--									<option value="">请选择</option>-->
            <!--									<option value="">北京</option>-->
            <!--									<option value="">上海</option>-->
            <!--									<option value="">天津</option>-->
            <!--									<option value="">重庆</option>-->
            <!--									<option value="">武汉</option>-->
            <!--								</select>-->
            <!---->
            <!--								<select name="" id="">-->
            <!--									<option value="">请选择</option>-->
            <!--									<option value="">朝阳区</option>-->
            <!--									<option value="">东城区</option>-->
            <!--									<option value="">西城区</option>-->
            <!--									<option value="">海淀区</option>-->
            <!--									<option value="">昌平区</option>-->
            <!--								</select>-->
            <!---->
            <!--								<select name="" id="">-->
            <!--									<option value="">请选择</option>-->
            <!--									<option value="">西二旗</option>-->
            <!--									<option value="">西三旗</option>-->
            <!--									<option value="">三环以内</option>-->
            <!--								</select>-->
            <!--							</li>-->
            <!--							<li>-->
            <!--								<label for=""><span>*</span>详细地址：</label>-->
            <!--								<input type="text" name="" class="txt address"  />-->
            <!--							</li>-->
            <!--							<li>-->
            <!--								<label for=""><span>*</span>手机号码：</label>-->
            <!--								<input type="text" name="" class="txt" />-->
            <!--							</li>-->
            <!--							<li>-->
            <!--								<label for="">&nbsp;</label>-->
            <!--								<input type="checkbox" name="" class="check" />设为默认地址-->
            <!--							</li>-->
            <!--							<li>-->
            <!--								<label for="">&nbsp;</label>-->
            <!--								<input type="submit" name="" class="btn" value="保存" />-->
            <!--							</li>-->
            <!--						</ul>-->
            <!--					</form>-->
        </div>

    </div>
    <!-- 右侧内容区域 end -->
</div>
<!-- 页面主体 end-->











<?php
//echo '<div class="address_bd mt10" style="margin: 0 auto">';
//echo '<ul>';
//echo '<li>';
//    $form=\yii\widgets\ActiveForm::begin();
//echo '</li>';
//    echo $form->field($model,'username')->textInput(['class'=>'txt']);
//echo '<li>';
//    echo $form->field($model,'province')->dropDownList(['prompt'=>'请选择'])->label(false);
//echo '</li>';
//echo '<li>';
//    echo $form->field($model,'city')->dropDownList(['prompt'=>'请选择'])->label(false);
//echo '</li>';
//echo '<li>';
//    echo $form->field($model,'county')->dropDownList(['prompt'=>'请选择'])->label(false);
//echo '</li>';
//echo '<li>';
//    echo $form->field($model,'address')->textInput(['class'=>'txt']);
//echo '</li>';
//echo '<li>';
//    echo $form->field($model,'tel')->textInput(['class'=>'txt']);
//echo '</li>';
//echo '<li>';
//    echo $form->field($model,'remember')->checkbox([1=>'设为默认地址']);
//echo '</li>';
//echo '<li>';
//    echo \yii\helpers\Html::submitInput('保存',['class'=>'btn btn-primary']);
//echo '</li>';
//    \yii\widgets\ActiveForm::end();
//
//echo '</ul>';
//echo '</div>';




