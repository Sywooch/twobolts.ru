<?php

use yii\db\Migration;

class m160727_073451_alter_car_table extends Migration
{
    public function up()
    {
        $this->renameColumn('cars', 'car_id', 'id');
        $this->renameColumn('cars', 'foto', 'image');
    }

    public function down()
    {
        echo "m160727_073451_alter_car_table cannot be reverted.\n";

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
