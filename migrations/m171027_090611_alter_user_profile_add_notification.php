<?php

use yii\db\Migration;

class m171027_090611_alter_user_profile_add_notification extends Migration
{
    public function safeUp()
    {
	    $this->addColumn('user_profiles', 'notification', $this->boolean()->defaultValue(true));
    }

    public function safeDown()
    {
        echo "m171027_090611_alter_user_profile_add_notification cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m171027_090611_alter_user_profile_add_notification cannot be reverted.\n";

        return false;
    }
    */
}
