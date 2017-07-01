


	<!-- 主体部分 start -->
	<form action="<?=\yii\helpers\Url::to(['cart2/add'])?>" method="post">
	<div class="fillin w990 bc mt15">
		<div class="fillin_hd">
			<h2>填写并核对订单信息</h2>
		</div>

		<div class="fillin_bd">
			<!-- 收货人信息  start-->
			<div class="address">
				<h3>收货人信息</h3>
				<div class="address_info">
					<?php foreach($address as $addre):?>
				<p><input type="radio" value="<?=$addre->id?>" name="address_id" <?=$addre->status==3?'checked':''?>/><?=$addre->username?>  <?=$addre->tel?>  <?=$addre->province?>  <?=$addre->city?>  <?=$addre->county?></p>
					<?php endforeach;?>
				</div>


			</div>
			<!-- 收货人信息  end-->

			<!-- 配送方式 start -->
			<div class="delivery">
				<h3>送货方式 </h3>


				<div class="delivery_select">
					<table>
						<thead>
							<tr>
								<th class="col1">送货方式</th>
								<th class="col2">运费</th>
<!--								<th class="col3">运费标准</th>-->
							</tr>
						</thead>
						<tbody>
						<?php foreach($deliveris as $delivery):?>
							<tr class="cur">	
								<td>
									<input type="radio" class="pay_delivery" name="delivery_id" value="<?=$delivery['delivery_id']?>"/><?=$delivery['delivery_name']?>

								</td>
								<td class="delivery_price">￥<?=$delivery['delivery_price']?></td>
							</tr>
						<?php endforeach;?>
						</tbody>
					</table>

				</div>
			</div> 
			<!-- 配送方式 end --> 

			<!-- 支付方式  start-->
			<div class="pay">
				<h3>支付方式 </h3>


				<div class="pay_select">
					<table>
						<?php foreach($payments as $payment):?>
						<tr class="cur">
							<td class="col1"><input type="radio" name="pay" value="<?=$payment['payment_id']?>"/><?=$payment['payment_name']?></td>
							<td class="col2"><?=$payment['payment_intro']?></td>
						</tr>
						<?php endforeach;?>
					</table>

				</div>
			</div>
			<!-- 支付方式  end-->

			<!-- 发票信息 start-->
			<div class="receipt none">
				<h3>发票信息 </h3>


				<div class="receipt_select ">
					<form action="">
						<ul>
							<li>
								<label for="">发票抬头：</label>
								<input type="radio" name="type" checked="checked" class="personal" />个人
								<input type="radio" name="type" class="company"/>单位
								<input type="text" class="txt company_input" disabled="disabled" />
							</li>
							<li>
								<label for="">发票内容：</label>
								<input type="radio" name="content" checked="checked" />明细
								<input type="radio" name="content" />办公用品
								<input type="radio" name="content" />体育休闲
								<input type="radio" name="content" />耗材
							</li>
						</ul>						
					</form>

				</div>
			</div>
			<!-- 发票信息 end-->

			<!-- 商品清单 start -->
			<div class="goods">
				<h3>商品清单</h3>
				<table>
					<thead>
						<tr>
							<th class="col1">商品</th>
							<th class="col3">价格</th>
							<th class="col4">数量</th>
							<th class="col5">小计</th>
						</tr>	
					</thead>
					<tbody>
					<?php $count=0;$money=0; foreach($models as $model):?>
						<?php $count+=$model['amount'];$money+=$model['shop_price']*$model['amount'];?>
						<tr>
							<td class="col1"><a href=""><?=\yii\helpers\Html::img($model['logo'])?></a>  <strong><a href=""><?=$model['name']?></a></strong></td>
							<td class="col3">￥<?=$model['shop_price']?></td>
							<td class="col4"> <?=$model['amount']?></td>
							<td class="col5"><span>￥<?=$model['shop_price']*$model['amount']?></span></td>
						</tr>
					<?php endforeach;?>
					</tbody>
					<tfoot>
						<tr>
							<td colspan="5">
								<ul>
									<li class="count">
										<span><?=$count?> 件商品，总商品金额：￥</span>
										<em><?=$money?></em>
									</li>
<!--									<li>-->
<!--										<span>返现：</span>-->
<!--										<em>-￥240.00</em>-->
<!--									</li>-->
									<li class="yunfei">
										<span>运费：￥</span>
										<em>00.00</em>
									</li>
									<li class="total1">
										<span>应付总额：￥</span>
										<em><?=$money?></em>
									</li>
								</ul>
							</td>
						</tr>
					</tfoot>
				</table>
			</div>
			<!-- 商品清单 end -->
		
		</div>

		<div class="fillin_ft">

<!--			<a href=""><span>提交订单</span></a>-->
			<?=\yii\helpers\Html::submitInput('提交订单')?>
			<p>应付总额：￥<strong class="total2"><?=$money?></strong>元</p>

<!--			隐藏域将总金额传过去-->
			<input type="hidden" class="total3" name="money" value=""/>
			<input name="_csrf-frontend" type="hidden" id="_csrf-frontend" value="<?= Yii::$app->request->csrfToken ?>">
		</div>
	</div>
	</form>
	<!-- 主体部分 end -->
<?php
	//通过js，发送请求获取运费，修改应付总金额值
	$url=\yii\helpers\Url::to(['cart2/delivery']);
	$token=Yii::$app->request->csrfToken;
	$this->registerJs(new \yii\web\JsExpression(
		<<<JS
			$('.pay_delivery').click(function(){
			//获取运费价格
			var delivery_id=$(this).attr('value');
			$.post('$url',{'delivery_id':delivery_id,'_csrf-frontend':'$token'},function(response){
				//修改运费值
				$('.yunfei').find('em').text(response);
				//计算商品的总金额加上运费的总和
				var count_money=Number($('.count').find('em').text())+Number(response);
				//修改应付总金额的值
				$('.total1').find('em').text(count_money);
				$('.total2').text(count_money);
				$('.total3').attr('value',count_money);
			});
			});
JS


	));
