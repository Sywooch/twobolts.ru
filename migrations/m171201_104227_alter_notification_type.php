<?php

use yii\db\Migration;

class m171201_104227_alter_notification_type extends Migration
{
    public function safeUp()
    {
	    $this->addColumn('notification', 'type', $this->string(32));
    }

    public function safeDown()
    {
        echo "m171201_104227_alter_notification_type cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m171201_104227_alter_notification_type cannot be reverted.\n";

        return false;
    }
    */
}
