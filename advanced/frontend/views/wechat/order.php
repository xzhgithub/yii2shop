<table class="table table-bordered table-striped table-hover">
    <tr>
        <th>ID</th>
        <th>收货人</th>
        <th>收货地址</th>
        <th>电话</th>
        <th>发货方式</th>
        <th>支付方式</th>
        <th>总金额</th>
    </tr>
    <?php foreach($orders as $order):?>
        <tr>
            <td><?=$order->id?></td>
            <td><?=$order->name?></td>
            <td><?=$order->address?></td>
            <td><?=$order->tel?></td>
            <td><?=$order->delivery_name?></td>
            <td><?=$order->payment_name?></td>
            <td><?=$order->total?></td>
        </tr>
    <?php endforeach;?>
</table>