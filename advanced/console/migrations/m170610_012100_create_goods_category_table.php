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
//            tree int?? ��id
            'tree'=>$this->integer()->comment('��id'),
//        lft int?? ��ֵ
            'lft'=>$this->integer()->comment('��ֵ'),
//        rgt int?? ��ֵ
            'rgt'=>$this->integer()->comment('��ֵ'),
//        depth int?? �㼶
            'depth'=>$this->integer()->comment('�㼶'),
//        name varchar?50?����
            'name'=>$this->string('50')->comment('����'),
//        parent_id int?? �ϼ�����id
            'parent_id'=>$this->integer()->comment('�ϼ�����id'),
//        intro text?? ���
            'intro'=>$this->text()->comment('���'),

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
