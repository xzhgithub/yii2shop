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
//            id primaryKey
//            name varchar?50?����
            'name'=>$this->string(20)->comment('����'),
            //intro text ���
            'intro'=>$this->text()->comment('���'),
//            logo varchar?255? LOGOͼƬ
            'logo'=>$this->string(100)->comment('LOGO'),
//             sort int?11? ����
            'sort'=>$this->integer(10)->comment('����'),
//            status int?2? ״̬?�\1ɾ�� 0���� 1����?
            'status'=>$this->integer(2)->comment('״̬'),

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
