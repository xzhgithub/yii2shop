<?php

use yii\db\Migration;

/**
 * Handles the creation of table `goods_category`.
 */
class m170610_012100_create_goods_category_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('goods_category', [
            'id' => $this->primaryKey(),

            'tree'=>$this->integer()->comment('树id'),
            'lft'=>$this->integer()->comment('左值ֵ'),
            'rgt'=>$this->integer()->comment('右值ֵ'),
            'depth'=>$this->integer()->comment('深度'),
            'name'=>$this->string('50')->comment('名称'),
            'parent_id'=>$this->integer()->comment('父id'),
            'intro'=>$this->text()->comment('详情'),

        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('goods_category');
    }
}
