<?php
/** @var ComparisonList $widget */

use app\components\IconHelper;
use app\components\UrlHelper;
use app\components\widgets\ComparisonList;
use app\components\widgets\UserLink;
use app\models\Comparison;
use app\models\User;
use yii\helpers\Html;
use yii\helpers\Url;

$widget = $this->context;
$counter = 0;

if ($widget->items) {
    $more = [];

    for ($i = 1; $i <= $widget->_pageNum; ++$i) {
        $more[(ComparisonList::ITEMS_PER_PAGE * $i) - 8] = 1;
    }

    foreach ($widget->items as $item)
    {
        $mainGrade = $item->getGrade('main');
        $compareGrade = $item->getGrade('compare');
        ++$counter;
        ?>

        <div class="comparison-list-item <?= (isset($more[$counter]) && $widget->loadMore !== false) ? 'fnLoadMore' : ''; ?>">
            <div class="compare-small-fotos">
                <p class="comparison-list-item-point left-point"><?= $mainGrade; ?></p>
                <p class="comparison-list-item-point right-point"><?= $compareGrade; ?></p>
                <img src="/images/<?= $mainGrade >= $compareGrade ? 'orange' : 'green'; ?>_gr_scale_vrt.png" class="comparison-list-item-scale-left" width="34px" height="<?= $mainGrade * Comparison::MAX_CRITERIA; ?>%">
                <img src="/images/<?= $compareGrade >= $mainGrade ? 'orange' : 'green'; ?>_gr_scale_vrt.png" class="comparison-list-item-scale-right" width="34px" height="<?= $compareGrade * Comparison::MAX_CRITERIA; ?>%">
                <?= Html::img(UrlHelper::absolute($item->main_foto), ['class' => 'compare-small-foto']).Html::img(UrlHelper::absolute($item->compare_foto), ['class' => 'compare-small-foto']); ?>
            </div>

            <div class="comparison-list-item-name">
                <?= Html::a($item->getShortName(), '/comparison/view/' . ($item->url_title ? $item->url_title : $item->id)); ?>
            </div>

            <div class="comparison-list-item-user">
                <?php if ($widget->showUser): ?>
                    <?= UserLink::widget(['user' => $item->user, 'showAvatar' => $widget->showAvatar]); ?>
                <?php endif; ?>

                <span>
                    <?php if ($widget->showDate): ?>
                        <span title="<?= Yii::t('app', 'Comparison date')?>"><?= IconHelper::show('calendar') . date('d.m.Y', strtotime($item->date)); ?></span>
                    <?php endif; ?>

                    <?php if ($widget->showComments && count($item->comments)): ?>
                        <span title="<?= Yii::t('app', 'Comparison comments')?>"><?= IconHelper::show('comments') . count($item->comments); ?></span>
                    <?php endif; ?>

                    <?php if ($widget->showRating): ?>
                        <span title="<?= Yii::t('app', 'Comparison rating')?>"><?= IconHelper::show('bar-chart') . number_format($item->calculatedRating, 2); ?></span>
                    <?php endif; ?>
                </span>
            </div>
        </div>

        <?php
    }
}
?>