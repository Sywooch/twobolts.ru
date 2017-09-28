<?php

use app\models\Comparison;
use app\models\News;
use yii\db\Migration;

class m161111_092033_20161111_alter_comments_converting extends Migration
{
    public function up()
    {
        $this->delete('comments', 'object_id IS NULL');
        $this->update('comments', ['object' => Comparison::className()]);

        $this->dropForeignKey('comments_ibfk_2', 'comments');

        $sql = "INSERT INTO `comments` (`object_id`, `user_id`, `text`, `reply_id`, `status`, `user_ip`, `created`, `modified`, `object`)
            SELECT `comment_news_id`, `comment_user_id`, `comment_text`, `comment_reply_id`, `comment_status`, `comment_user_ip`, `comment_created`, `comment_modified`, :newsClass 
            FROM `news_comments`";

        Yii::$app->getDb()->createCommand($sql, [':newsClass' => News::className()])->query();

        $this->dropTable('news_comments');
    }

    public function down()
    {
        echo "m161111_092033_20161111_alter_comments_converting cannot be reverted.\n";

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
