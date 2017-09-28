<?php

use yii\db\Migration;

class m160729_091046_alter_comparisons_comments_table extends Migration
{
    public function up()
    {
        $this->renameColumn('comparisons_comments', 'comment_id', 'id');
        $this->renameColumn('comparisons_comments', 'comment_comparison_id', 'comparison_id');
        $this->renameColumn('comparisons_comments', 'comment_user_id', 'user_id');
        $this->renameColumn('comparisons_comments', 'comment_text', 'text');
        $this->renameColumn('comparisons_comments', 'comment_reply_id', 'reply_id');
        $this->renameColumn('comparisons_comments', 'comment_status', 'status');
        $this->renameColumn('comparisons_comments', 'comment_user_ip', 'user_ip');
        $this->renameColumn('comparisons_comments', 'comment_created', 'created');
        $this->renameColumn('comparisons_comments', 'comment_modified', 'modified');
    }

    public function down()
    {
        echo "m160729_091046_alter_comparisons_comments_table cannot be reverted.\n";

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
