<?php

use yii\db\Migration;

/**
 * Handles the creation of table `brand`.
 */
class m170608_073809_create_brand_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('brand', [
            'id' => $this->primaryKey(),
            'name'=>$this->string(20)->comment('名称'),
            'intro'=>$this->text()->comment('详情'),
            'logo'=>$this->string(100)->comment('LOGO'),
            'sort'=>$this->integer(10)->comment('排序'),
            'status'=>$this->integer(2)->comment('状态'),

        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('brand');
    }
}
