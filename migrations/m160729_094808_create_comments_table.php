<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Handles the creation for table `comments` table.
 */
class m160729_094808_create_comments_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->renameTable('comparisons_comments', 'comments');
        $this->addColumn('comments', 'object', Schema::TYPE_STRING);
        $this->renameColumn('comments', 'comparison_id', 'object_id');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        echo "m160729_094808_create_comments_table cannot be reverted.\n";

        return false;
    }
}
