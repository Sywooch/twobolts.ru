<?php
/** @var array $models */

use app\components\ImageHelper;
use app\models\Car;
use app\models\Model;
use app\models\News;
use yii\helpers\BaseUrl;
use yii\helpers\Html;

$counter = 0;
foreach ($models as $modelItem)
{
    ++$counter;
    if ($counter < 4) {
        $itemClass = 'catalog-item-lg';
    } else {
        $itemClass = 'catalog-item';
    }
    /** @var Model $model */
    $model = $modelItem['model'];
    $body = $model->getBodyName() ? '<p class="catalog-model-body">' . $model->getBodyName() . '</p>' : '';
    ?>

    <div class="<?= $itemClass; ?>">
        <?= Html::img(
            '/images/lazy_load.png',
            [
                'realsrc' => ImageHelper::createThumbnailFile(
                    $model->getImage(false, false),
                    ImageHelper::THUMBNAIL_LARGE_WIDTH,
                    ImageHelper::THUMBNAIL_LARGE_HEIGHT,
                    false
                )
            ]
        ); ?>

        <?php if ($modelItem['compares_total']): ?>
            <div class="catalog_car_info">
                <strong>
                    <?= number_format(($modelItem['main_value'] + $modelItem['compare_value']) /($modelItem['main_criteria'] + $modelItem['compare_criteria']), 1) ?>
                </strong> / <?= $modelItem['compares_total']; ?>
            </div>
        <?php endif; ?>

        <div class="model-title">
            <ul>
                <li>
                    <p class="catalog-model-title"><a href="#"><?= $model->manufacturer->name; ?> <?= $model->name; ?></a></p>
                    <?= $body; ?>

                    <ul>
                        <?php foreach ($modelItem['cars'] as $car): ?>
                            <?php /** @var Car $car */ ?>
                            <?php $comparisonsCount = $car->main_comparisons_count + $car->compare_comparisons_count; ?>
                            <li>
                                <?= Html::a($car->engine->getName(), BaseUrl::home(true) . 'catalog/' . $car->engine->getUrl()); ?>
                                <?= $comparisonsCount ? '<span><i class="fa fa-sliders"></i> ' . $comparisonsCount . '</span>' : ''; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
    <?php
}