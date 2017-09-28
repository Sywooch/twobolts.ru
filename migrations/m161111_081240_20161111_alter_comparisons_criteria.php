<?php

use yii\db\Migration;

class m161111_081240_20161111_alter_comparisons_criteria extends Migration
{
    public function up()
    {
        $this->renameColumn('comparisons_criteria', 'criteria_id', 'id');
        $this->renameColumn('comparisons_criteria', 'criteria_name', 'name');
        $this->renameColumn('comparisons_criteria', 'criteria_placeholder', 'placeholder');
        $this->renameColumn('comparisons_criteria', 'criteria_order', 'order');
        $this->renameColumn('comparisons_criteria', 'criteria_show_on_home', 'show_on_home');
        $this->renameColumn('comparisons_criteria', 'criteria_icon', 'icon');
    }

    public function down()
    {
        $this->renameColumn('comparisons_criteria', 'id', 'criteria_id');
        $this->renameColumn('comparisons_criteria', 'name', 'criteria_name');
        $this->renameColumn('comparisons_criteria', 'placeholder', 'criteria_placeholder');
        $this->renameColumn('comparisons_criteria', 'order', 'criteria_order');
        $this->renameColumn('comparisons_criteria', 'show_on_home', 'criteria_show_on_home');
        $this->renameColumn('comparisons_criteria', 'icon', 'criteria_icon');
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
