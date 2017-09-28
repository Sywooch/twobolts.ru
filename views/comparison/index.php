<?php
/** @var yii\web\View $this */
/** @var \app\models\Comparison[] $items */
/** @var int $itemsCount */
/** @var string $sorting */
/** @var int $pageNum */
/** @var string $title */
/** @var string $metaDescription */
/** @var string $findAction */
/** @var array $params */

use app\components\widgets\ComparisonList;
use app\components\widgets\FooterList;

$this->title = $title . ' — ' . Yii::$app->params['siteTitle'];
$this->registerMetaTag([
    'description' => $metaDescription
]);
?>

<div class="comparison-index inside height-wrapper">
    <h1><?= $title; ?> <sup><?= $itemsCount; ?></sup></h1>

    <div class="catalog-sort" data-get="load_comparisons">
        <a href="#" class="<?= $sorting == 'date' ? 'active' : ''; ?>" data-sorting="date">Новые</a>
        <a href="#" class="<?= $sorting == 'rating' ? 'active' : ''; ?>" data-sorting="rating">По рейтингу</a>
        <a href="#" class="<?= $sorting == 'comments' ? 'active' : ''; ?>" data-sorting="comments">Обсуждаемые</a>
    </div>

    <div class="comparison-list-wrap">
        <?= ComparisonList::widget([
            'items' => $items,
            'itemsCount' => $itemsCount,
            'loadMore' => false,
        ]); ?>
    </div>

    <?php if ($itemsCount > ComparisonList::ITEMS_PER_PAGE): ?>
        <div class="comparison-list-btn-wrap">
            <div class="btn btn-default btn-lg btnLoadMore"><span><?= Yii::t('app', 'Load More')?> <span class="badge"><?= $itemsCount - $pageNum * ComparisonList::ITEMS_PER_PAGE; ?></span></span></div>
        </div>
    <?php endif; ?>

    <?= FooterList::widget(); ?>
</div>

<script type="text/javascript">
    _comparisonListControllerAction = 'comparison/<?= $findAction; ?>';
    _comparisonListSorting = '<?= $sorting; ?>';
    _comparisonListPageNum = <?= $pageNum; ?>;
    _comparisonListOptions = <?= $params; ?>;
</script>