<?php

use yii\db\Migration;

/**
 * Handles the creation of table `address`.
 */
class m170621_024402_create_address_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('address', [
            'id' => $this->primaryKey(),
            'username'=>$this->string(50)->comment('收货人'),
            'member_id'=>$this->integer()->comment('用户id'),
            'province'=>$this->string(50)->comment('省份'),
            'city'=>$this->string(50)->comment('市'),
            'county'=>$this->string(50)->comment('县'),
            'address'=>$this->string(255)->comment('详细地址'),
            'tel'=>$this->char(11)->comment('电话'),
            'status'=>$this->integer(1)->comment('状态'),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('address');
    }
}
