<table class="table table-bordered table-striped table-hover">
   <tr>
       <th>ID</th>
       <th>用户</th>
       <th>收货人</th>
       <th>省份</th>
       <th>城市</th>
       <th>区县</th>
       <th>电话</th>
       <th>送货方式</th>
       <th>支付方式</th>
       <th>总金额</th>
       <th>时间</th>
       <th>状态</th>
       <th>操作</th>
    </tr>
    <?php foreach($orders as $order):?>
    <tr>
        <td><?=$order->id?></td>
        <td><?=$order->member_id?></td>
        <td><?=$order->name?></td>
        <td><?=$order->province?></td>
        <td><?=$order->city?></td>
        <td><?=$order->area?></td>
        <td><?=$order->tel?></td>
        <td><?=$order->delivery_name?></td>
        <td><?=$order->payment_name?></td>
        <td><?=$order->total?></td>
        <td><?=date('Y-m-d H:i:s',$order->create_time)?></td>
        <td class="status"><?=$status[$order->status]?></td>
        <td><?=\yii\bootstrap\Html::button('发货',['class'=>'btn btn-primary btn-xs'])?></td>
    </tr>
    <?php endforeach;?>

</table>

<?php
$url=\yii\helpers\Url::to(['order/deliver']);
$token=Yii::$app->request->csrfToken;
$this->registerJS(new \yii\web\JsExpression(
    <<<JS
        $('.btn').click(function() {
        //获取订单id
          var order_id=$(this).closest('tr').find('td:first').text();
          console.log(order_id);
          //发送ajax请求
          $.post('$url',{'order_id':order_id,'_csrf-frontend':'$token'});
          $(this).closest('tr').find('.status').text('已发货')
          $(this).closest('tr').find('.status').attr('background','blue')

        });
JS

));