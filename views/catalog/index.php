<?php
/** @var string $title */
/** @var string $metaDescription */
/** @var \app\models\Manufacturer[] $manufacturers */
/** @var \app\models\Model[] $models */

use app\components\ImageHelper;
use app\models\Car;
use app\models\Model;
use app\models\News;
use yii\helpers\BaseUrl;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = $title . ' — ' . Yii::$app->params['siteTitle'];
$this->registerMetaTag([
    'description' => $metaDescription
]);
?>

<div class="catalog-index inside height-wrapper">
    <div class="manufacturers-wrapper">
        <h1><?= $title; ?></h1>

        <div class="manufacturers-list-wrapper">
            <?php
            if ($manufacturers) {
                $abcPopular = $abcList = [];
                $popularCount = $listCount = 0;
                foreach ($manufacturers as $manufacturer)
                {
                    $first = mb_substr($manufacturer->name, 0 ,1);
                    if (!array_key_exists($first, $abcList)) {
                        $abcList[$first] = [];
                    }
                    if ($manufacturer->is_popular == 1) {
                        if (!array_key_exists($first, $abcPopular)) {
                            $abcPopular[$first] = [];
                        }
                        ++$popularCount;
                        $abcPopular[$first][] = Html::a($manufacturer->name, Url::to('/catalog/manufacturer/' . $manufacturer->getUrl()));
                    }
                    ++$listCount;
                    $abcList[$first][] = Html::a($manufacturer->name, Url::to('/catalog/manufacturer/' . $manufacturer->getUrl()));
                }
                ?>

                <div class="catalog-popular-list">
                    <div class="catalog-list-column">
                        <?php
                        $cols = 4;
                        $i = 0;
                        $lines = ceil($popularCount / $cols);
                        $split = false;
                        foreach ($abcPopular as $key => $val)
                        {
                            ?>
                            <dt class="catalog-marks-dt"><?= strtoupper($key); ?></dt>
                            <dd class="catalog-marks-dd">
                                <ul>
                                    <?php
                                    foreach ($val as $mark)
                                    {
                                        ++$i;
                                        if ($i >= $lines) {
                                            $split = true;
                                        }
                                        ?>
                                        <li><?= $mark; ?></li>
                                        <?php
                                    }
                                    ?>
                                </ul>
                            </dd>
                            <?php
                            if ($split) {
                                $i = 0;
                                $split = false;
                                ?>
                                </div><div class="catalog-list-column">
                                <?php
                            }
                        }
                        ?>
                    </div>
                </div>

                <div class="clear"></div>

                <div class="catalog-all-list">
                    <div class="catalog-list-column">
                        <?php
                        $cols = 4;
                        $i = 0;
                        $lines = ceil($listCount / $cols);
                        $split = false;
                        foreach ($abcList as $key => $val)
                        {
                            ?>
                            <dt class="catalog-marks-dt"><?= strtoupper($key); ?></dt>
                            <dd class="catalog-marks-dd">
                                <ul>
                                    <?php
                                    foreach ($val as $mark)
                                    {
                                        ++$i;
                                        if ($i >= $lines) {
                                            $split = true;
                                        }
                                        ?>
                                        <li><?= $mark; ?></li>
                                        <?php
                                    }
                                    ?>
                                </ul>
                            </dd>
                            <?php
                            if ($split) {
                                $i = 0;
                                $split = false;
                                ?>
                                </div><div class="catalog-list-column">
                                <?php
                            }
                        }
                        ?>
                    </div>
                </div>

                <div class="clear"></div>

                <?php if ($listCount > $popularCount): ?>
                    <div class="catalog-btn-view-all"><a href="#"><?= Yii::t('app', 'All manufacturers'); ?></a></div>
                <?php endif; ?>

                <?php
            }
            ?>

            <div class="clear"></div>
        </div>
    </div>

    <div class="catalog-models">
        <h3>Добавлены</h3>

        <div class="catalog-models-wrapper">
        <?php
        if ($models) {
            $counter = 0;
            foreach ($models as $model)
            {
                ++$counter;
                if ($counter < 4) {
                    $itemClass = 'catalog-item-lg';
                } else {
                    $itemClass = 'catalog-item';
                }
                $body = $model->getBodyName() ? '<p class="catalog-model-body">' . $model->getBodyName() . '</p>' : '';
                ?>
                <div class="<?= $itemClass; ?>">
                    <?= Html::a(
                        Html::img(
                            ImageHelper::createThumbnailFile(
                                $model->getImage(false, false),
                                ImageHelper::THUMBNAIL_LARGE_WIDTH,
                                ImageHelper::THUMBNAIL_LARGE_HEIGHT,
                                false
                            )
                        ) . '<p class="catalog-model-title">' . $model->manufacturer->name . ' ' . $model->name . '</p>' . $body,
                        Url::to(BaseUrl::home(true) . 'catalog/'. $model->cars[0]->engine->getUrl())
                    ); ?>
                </div>
                <?php
            }
        } else {
            Html::tag('h3', Yii::t('app', 'Empty catalog'));
        }
        ?>
        </div>

        <div class="clear"></div>
    </div>

    <div class="clear"></div>
</div>
