<?php

use yii\db\Migration;

class m171130_114000_alter_notification_add_author extends Migration
{
    public function safeUp()
    {
	    $this->addColumn('notification', 'author_id', $this->integer(11));
	    $this->createIndex('author_id', 'notification', ['author_id']);
	    $this->addForeignKey('notification_ibfk_2', 'notification', 'author_id', 'users', 'id', 'CASCADE', 'CASCADE');
    }

    public function safeDown()
    {
        echo "m171130_114000_alter_notification_add_author cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m171130_114000_alter_notification_add_author cannot be reverted.\n";

        return false;
    }
    */
}
