<table class="table table-bordered table-striped table-hover">
    <tr>
        <th>ID</th>
        <th>收货人</th>
        <th>收货地址</th>
        <th>电话</th>
    </tr>
    <?php foreach($address as $model):?>
        <tr>
            <td><?=$model->id?></td>
            <td><?=$model->username?></td>
            <td><?=$model->address?></td>
            <td><?=$model->tel?></td>
        </tr>
    <?php endforeach;?>
</table>