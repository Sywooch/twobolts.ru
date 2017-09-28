<?php
/** @var View $this */
/** @var News $model */

use app\components\IconHelper;
use app\components\ImageHelper;
use app\components\UrlHelper;
use app\models\News;
use dosamigos\gallery\Gallery;
use yii\helpers\Html;
use yii\web\View;

$this->title = $model->title . ' — ' . Yii::$app->params['siteTitle'];
?>

<div class="news-view inside height-wrapper">
    <h1><?= Yii::t('app', 'Auto News'); ?></h1>


    <div class="news-view-wrapper">
        <p class="news-list-item-details">
            <?= Yii::$app->formatter->asDate($model->created); ?>

            <?= IconHelper::show('eye') . $model->num_views; ?>

            <?= IconHelper::show('comment') . $model->getCommentsCount(); ?>
        </p>

        <?= $model->source ? '<p class="news-view-source"><em>Источник: ' . UrlHelper::autoLink(trim($model->source, '/'), 'both', true) . '</em></p>' : ''; ?>

        <h2><?= $model->title; ?></h2>

        <?php if ($model->include_excerpt): ?>
            <p class="news-view-excerpt"><?= $model->excerpt; ?></p>
        <?php endif; ?>
    </div>

    <?php if ($model->video): ?>
        <div class="news-view-video" data-video="<?= $model->video; ?>"></div>
    <?php elseif ($model->featured_image): ?>
        <div class="news-view-image">
            <?= ImageHelper::getImageTag($model, 'featured_image'); ?>

            <?php if ($model->featured_image_caption): ?>
                <span><?= $model->featured_image_caption; ?></span>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="news-view-wrapper">
        <?= UrlHelper::autoLink(trim($model->content, '/'), 'both', true); ?>
    </div>

    <?= $model->renderGallery(); ?>

    <div class="comments-list-container">
        <div class="comments-wrap">
            <h2>
                <a href="#" id="btn_comments"><?= IconHelper::show('triangle_up'); ?> Комментарии</a>
                <sup class="total-comments"><?= $model->getCommentsCount() ? $model->getCommentsCount() : '0'; ?></sup>
            </h2>

            <div class="comments-list">
                <?= $this->render('../_comments', [
                    'model' => $model
                ]) ?>
            </div>
        </div>
    </div>
</div>

<script>
    commentObject = '<?= addslashes(News::className()); ?>';
</script>

<input type="hidden" id="object_id" name="object_id" value="<?= $model->id; ?>">