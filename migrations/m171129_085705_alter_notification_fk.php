<?php

use yii\db\Migration;

class m171129_085705_alter_notification_fk extends Migration
{
    public function safeUp()
    {
	    $this->addForeignKey('notification_ibfk_1', 'notification', 'user_id', 'users', 'id', 'CASCADE', 'CASCADE');
    }

    public function safeDown()
    {
        echo "m171129_085705_alter_notification_fk cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m171129_085705_alter_notification_fk cannot be reverted.\n";

        return false;
    }
    */
}
