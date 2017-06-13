<?php

use yii\db\Migration;

/**
 * Handles the creation of table `goods_photo`.
 */
class m170612_105022_create_goods_photo_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('goods_photo', [
            'id' => $this->primaryKey(),
            'goods_id'=>$this->integer()->comment('所属商品id'),
            'img'=>$this->string(100)->comment('图片地址'),
            'status'=>$this->integer(1)->comment('状态'),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('goods_photo');
    }
}
