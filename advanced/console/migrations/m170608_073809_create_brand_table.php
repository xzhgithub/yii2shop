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
//            name varchar?50?Ãû³Æ
            'name'=>$this->string(20)->comment('Ãû³Æ'),
            //intro text ¼ò½é
            'intro'=>$this->text()->comment('¼ò½é'),
//            logo varchar?255? LOGOÍ¼Æ¬
            'logo'=>$this->string(100)->comment('LOGO'),
//             sort int?11? ÅÅÐò
            'sort'=>$this->integer(10)->comment('ÅÅÐò'),
//            status int?2? ×´Ì¬?©\1É¾³ý 0Òþ²Ø 1Õý³£?
            'status'=>$this->integer(2)->comment('×´Ì¬'),

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
