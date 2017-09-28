<?php

use yii\db\Migration;

class m161111_081831_20161111_alter_comparisons_criteria_sort_order extends Migration
{
    public function up()
    {
        $this->renameColumn('comparisons_criteria', 'order', 'sort_order');
    }

    public function down()
    {
        $this->renameColumn('comparisons_criteria', 'sort_order', 'order');
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
