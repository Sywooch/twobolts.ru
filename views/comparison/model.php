<?php
/** @var yii\web\View $this */
/** @var \app\models\Comparison[] $items */
/** @var int $itemsCount */
/** @var string $sorting */
/** @var int $pageNum */

use app\components\widgets\ComparisonList;
use app\components\widgets\FooterList;

$this->title = Yii::t('app', 'Car compares') . ' — ' . Yii::$app->params['siteTitle'];
$this->registerMetaTag([
    'description' => Yii::t('app/meta', 'Comparison Index Page Description')
]);
?>

<div class="profile-index inside height-wrapper">
    <h1><?= Yii::t('app', 'Car compares'); ?></h1>

    <div class="catalog-sort" data-get="load_comparisons">
        <a href="#" class="<?= $sorting == 'date' ? 'active' : ''; ?>" data-sorting="date">Новые</a>
        <a href="#" class="<?= $sorting == 'rating' ? 'active' : ''; ?>" data-sorting="rating">По рейтингу</a>
        <a href="#" class="<?= $sorting == 'comment' ? 'active' : ''; ?>" data-sorting="comments">Обсуждаемые</a>
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
    _comparisonListControllerAction = 'comparison/find-items';
    _comparisonListSorting = '<?= $sorting; ?>';
    _comparisonListPageNum = <?= $pageNum; ?>;
    _comparisonListOptions = <?= json_encode([]); ?>;
</script>