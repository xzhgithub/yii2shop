

	<!-- 主体部分 start -->
	<div class="mycart w990 mt10 bc">
		<h2><span>我的购物车</span></h2>
		<table>
			<thead>
				<tr>
					<th class="col1">商品名称</th>
					<th class="col3">单价</th>
					<th class="col4">数量</th>	
					<th class="col5">小计</th>
					<th class="col6">操作</th>
				</tr>
			</thead>
			<tbody>
			<?php $total=0; foreach($models as $model):?>

				<tr goods_id="<?=$model['id']?>">
					<td class="col1"><a href=""><?=\yii\helpers\Html::img($model['logo'])?></a>  <strong><?=\yii\helpers\Html::a($model['name'])?></a></strong></td>
					<td class="col3">￥<span><?=$model['shop_price']?></span></td>
					<td class="col4"> 
						<a href="javascript:;" class="reduce_num"></a>
						<input type="text" name="amount" value="<?=$model['amount']?>" class="amount"/>
						<a href="javascript:;" class="add_num"></a>
					</td>
					<td class="col5">￥<span><?=$model['shop_price']*$model['amount']?></span></td>
					<td class="col6"><a href="javascript:;" class="del">删除</a></td>
				</tr>
				<?php $total+=$model['shop_price']*$model['amount']?>
			<?php endforeach;?>

			</tbody>
			<tfoot>
				<tr>
					<td colspan="6">购物金额总计： <strong>￥ <span id="total"><?=$total?></span></strong></td>
				</tr>
			</tfoot>
		</table>
		<div class="cart_btn w990 bc mt10">
			<a href="<?=\yii\helpers\Url::to(['goodscategory/index'])?>" class="continue">继续购物</a>
			<a href="<?=isset(Yii::$app->user->identity)?\yii\helpers\Url::to(['cart2/flow']):\yii\helpers\Url::to(['user/login'])?>" class="checkout">结 算</a>
		</div>
	</div>
	<!-- 主体部分 end -->
<?php

	//监听+ - 按钮的点击事件,监听加减商品事件
	$token=Yii::$app->request->csrfToken;
	$url=\yii\helpers\Url::to(['cart/update']);
	$this->registerJs(new \yii\web\JsExpression(
		<<<JS
			//商品数量发生改变
			$('.reduce_num,.add_num').click(function(){

			//获取数量，和goods_id
			var amount=$(this).closest('tr').find('.amount').val();
			var goods_id=$(this).closest('tr').attr('goods_id');
			//发送ajax post 请求修改cookie中的数据
			$.post('$url',{'amount':amount,'goods_id':goods_id,'_csrf-frontend':'$token'});

			});

			//监听删除事件
			$('.del').click(function(){
				if(confirm('是否删除该商品')){
					//获取数量，和goods_id
					var goods_id=$(this).closest('tr').attr('goods_id');

					//发送ajax post 请求修改cookie中的数据(当amount=0时删除)
					$.post('$url',{'amount':0,'goods_id':goods_id,'_csrf-frontend':'$token'});

					//删除当前行tr
					$(this).closest('tr').remove();
				}
			});




JS



	));

