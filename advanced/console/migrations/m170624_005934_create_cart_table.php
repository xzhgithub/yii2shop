<?php

use yii\db\Migration;

/**
 * Handles the creation of table `cart`.
 */
class m170624_005934_create_cart_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('cart', [
            'id' => $this->primaryKey(),
            'goods_id' => $this->integer()->comment('商品id'),
            'amount' => $this->integer()->comment('商品数量'),
            'member_id' => $this->integer()->comment('用户id'),
            'status' => $this->integer(1)->comment('状态'),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('cart');
    }
}
