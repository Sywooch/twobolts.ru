<?php

use yii\db\Migration;

class m171030_151600_create_notification extends Migration
{
    public function safeUp()
    {
	    $this->createTable('notification', [
	    	'id' => $this->primaryKey(11),
		    'user_id' => $this->integer(11),
		    'created' => $this->dateTime(),
		    'message' => $this->text(),
		    'is_new' => $this->boolean()->defaultValue(true)
	    ]);

	    $this->createIndex('user_id', 'notification', ['user_id']);
    }

    public function safeDown()
    {
        echo "m171030_151600_create_notification cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m171030_151600_create_notification cannot be reverted.\n";

        return false;
    }
    */
}
