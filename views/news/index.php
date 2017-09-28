<?php
/** @var \yii\web\View $this */
use app\models\News;

/** @var string $metaDescription */
/** @var \app\models\News[] $models */
/** @var int $modelsCount */

$this->title = Yii::t('app', 'Auto News') . ' — ' . Yii::$app->params['siteTitle'];
$this->registerMetaTag([
    'description' => $metaDescription
]);
?>

<div class="news-index inside height-wrapper">
    <h1><?= Yii::t('app', 'Auto News'); ?></h1>

    <div class="news-list-wrapper">
        <?= $this->render('list', [
            'models' => $models,
            'page' => 1
        ]); ?>
    </div>

    <?php if ($modelsCount > News::NEWS_PER_PAGE): ?>
        <div class="news-list-btn-wrap">
            <div class="btn btn-default btn-lg btnLoadMoreNews"><span><?= Yii::t('app', 'Load More')?></span></div>
        </div>
    <?php endif; ?>
</div>

<script>
    newsCount = <?= $modelsCount; ?>;
    newsPerPage = <?= News::NEWS_PER_PAGE; ?>;
    newsPage = 2;
</script>
