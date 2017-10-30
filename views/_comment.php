<?php
/** @var Comment $model */
/** @var User $user */
/** @var int $counter */

use app\components\TimeZoneHelper;
use app\components\widgets\UserLink;
use app\models\Comment;
use app\models\Comparison;
use app\models\User;

$user = User::identity();

?>

<div class="user-comment-wrapper <?= $counter > Comment::COMMENTS_PER_PAGE ? 'hidden' : ''; ?> <?= $counter == 0 ? 'new-comment' : ''; ?>">
    <div class="user-avatar-link"><?= UserLink::widget(['user' => $model->user]); ?></div>

    <div class="user-comment-text" id="comment_text_<?= $model->id; ?>">
        <?= $model->text; ?>

        <div data-username="<?= $model->user->username; ?>" data-id="<?= $model->id; ?>" data-user="<?= $model->user_id; ?>" data-model="<?= $model->object_id; ?>">
            <span class="user-comment-date">
                <?= Yii::$app->formatter->asDatetime(TimeZoneHelper::timezoneDate($model->created), 'd MMMM yyyy в HH:mm'); ?>
            </span>

            <?php if (!Yii::$app->user->isGuest): ?>
                <?php if ($model->object == Comparison::className()): ?>
                    <?php if ($user->canRateComment($model)): ?>
                        <span class="fnIncreaseUserKarma fnManageUserKarma"><i class="fa fa-thumbs-up"></i></span>
                    <?php endif; ?>

                    <?php $karmaTotal = $model->getTotalKarma(); ?>
                    <?php
                    if ($karmaTotal > 0) {
                        $karmaSign = '+';
                        $karmaClass = 'increased';
                    } else {
                        if ($karmaTotal < 0) {
                            $karmaClass = 'decreased';
                        } else {
                            $karmaClass = '';
                        }
                        $karmaSign = '';
                    }
                    ?>
                    <span class="karma-total <?= $karmaClass; ?>"><?= $karmaSign . $karmaTotal; ?></span>

                    <?php if ($user->canRateComment($model)): ?>
                        <span class="fnDecreaseUserKarma fnManageUserKarma"><i class="fa fa-thumbs-down fa-flip-horizontal"></i></span>
                    <?php endif; ?>
                <?php endif; ?>

                <input type="button" id="btn_comment_quote" value="Цитата" class="fnCommentQuote" />

                <a href="#" id="btn_comment_reply" class="fnCommentReply">Ответить</a>
            <?php endif; ?>
        </div>
    </div>

    <div class="clear"></div>
</div>
