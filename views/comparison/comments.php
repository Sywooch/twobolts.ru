<?php
/** @var Comparison $comparison */
/** @var yii\web\View $this */

use app\components\IconHelper;
use app\models\Comparison;
?>

<div class="comments-wrap">
    <h2>
        <a href="#" id="btn_comments"><?= IconHelper::show('triangle_up'); ?> Комментарии</a>
        <sup class="total-comments"><?= $comparison->calculatedComments ? $comparison->calculatedComments : '0'; ?></sup>
    </h2>

    <div class="comments-list">
        <?= $this->render('../_comments', [
            'model' => $comparison
        ]) ?>
    </div>
</div>

<input type="hidden" name="comment_reply_id" id="comment_reply_id" value="0" />