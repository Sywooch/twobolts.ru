<?php
/** @var yii\web\View $this */
/** @var Comparison|News $model */

use app\components\IconHelper;
use app\models\Comparison;
use app\models\Comment;
use app\models\News;
use yii\helpers\Html;

$counter = 0;
?>

<?php if ($model->comments): ?>
    <?php foreach ($model->comments as $comment): ?>
        <?php ++$counter; ?>

        <?= $this->render('_comment', ['model' => $comment, 'counter' => $counter])?>
    <?php endforeach; ?>
<?php else: ?>
    <h4 style="margin-top: 20px; text-align: center;">Комментариев пока нет. Будьте первым!</h4>
<?php endif; ?>

<div class="comments-btn-wrap">
    <div class="btn btn-default btn-lg" id="btn_more_comment" style="display: <?= count($model->comments) > Comment::COMMENTS_PER_PAGE ? 'block' : 'none'?>;">
        <span>ПОКАЗАТЬ ЕЩЕ <span class="badge"><?= count($model->comments) - Comment::COMMENTS_PER_PAGE; ?></span></span>
    </div>
</div>

<div class="comment-form">
    <?= Html::textarea('comment_text', '', ['id' => 'comment_text']); ?>

    <?php
    if (Yii::$app->user->isGuest) {
        $btnDisabled = 'disabled';
        $need_auth = '<div class="need-auth alert alert-danger"><h4>' . IconHelper::show('lock') . ' Внимание!</h4>Добавлять комментарии могут только авторизованные пользователи. Воспользуйтесь функцией "Вход на сайт" в меню.</div>';
    } else {
        $btnDisabled = '';
        $need_auth = '';
    }
    ?>

    <div class="comment-button-group">
        <button type="button" class="btn <?= Yii::$app->user->isGuest ? '' : 'btn-lg'; ?> btn-primary <?= $btnDisabled; ?>" id="btn_add_comment"><?= IconHelper::show('commenting') . Yii::t('app', 'Add comment'); ?></button>

        <a href="#" class="<?= $btnDisabled; ?> text-danger text-underline" id="btn_clear_comment"><?= IconHelper::show('delete') . Yii::t('app', 'Clear comment'); ?></a>
    </div>

    <?= $need_auth; ?>

    <div class="clear"></div>
</div>

<?php $this->registerJsFile('/js/ckeditor/ckeditor.js', ['depends' => [\yii\web\JqueryAsset::className()]]); ?>
