<?php

use yii\db\Migration;
use yii\db\Schema;

class m161111_085747_20161111_alter_news_add_video extends Migration
{
    public function up()
    {
        $this->addColumn('news', 'video', Schema::TYPE_STRING);
    }

    public function down()
    {
        echo "m161111_085747_20161111_alter_news_add_video cannot be reverted.\n";

        return false;
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
