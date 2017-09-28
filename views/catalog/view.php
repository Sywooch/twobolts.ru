<?php
/** @var Car $car */
/** @var Comparison[] $comparisons */
/** @var User $user */

use app\components\ArrayHelper;
use app\components\ImageHelper;
use app\components\UrlHelper;
use app\components\widgets\UserLink;
use app\models\Car;
use app\models\Comparison;
use app\models\Engine;
use app\models\News;
use app\models\User;
use kartik\select2\Select2;
use yii\helpers\BaseUrl;
use yii\helpers\Html;

$this->title = $car->getFullName() . ' — ' . Yii::$app->params['siteTitle'];
$this->registerMetaTag([
    'description' => $car->getFullName()
]);

$comparisons = $car->getComparisons(
    ['views' => SORT_DESC],
    Car::CATALOG_POPULAR_COMPARISONS_COUNT
);

$comments = $car->getComments();

$user = Yii::$app->user->identity;

$news = $car->getNews();
?>

<div class="catalog-view inside height-wrapper">

    <h1 class="catalog-view-title">
        <?= $car->model->getFullName(); ?>
        <span class="catalog-view-title-engines">
            <?= Select2::widget([
                'name' => 'engine',
                'value' => '/catalog/' . $car->engine->getUrl(),
                'data' => ArrayHelper::map(
                    $car->model->engines,
                    function ($item) {
                        /** @var Engine $item */
                        return '/catalog/' . $item->getUrl();
                    },
                    'name'
                ),
                'hideSearch' => true,
                'options' => [
                    'multiple' => false,
                    'id' => 'catalog_engine_switch'
                ],
                'pluginLoading' => false,
                'pluginEvents' => [
                    "change" => "function(e) { window.location = e.target.value; }"
                ]
            ]); ?>
        </span>
    </h1>

    <div class="catalog-view-content">
        <div class="column-section">
            <div class="catalog-view-content-image">
                <?= Html::img(
                    ImageHelper::createThumbnailFile(
                        $car->getImage(false),
                        ImageHelper::THUMBNAIL_LARGE_WIDTH,
                        ImageHelper::THUMBNAIL_LARGE_HEIGHT,
                        false
                    )
                ); ?>

                <?php $avgComparison = $car->getAvgComparison(); ?>

                <?php if ($avgComparison): ?>
                    <?php $avg = isset($avgComparison['compares_value']) ? $avgComparison['compares_value'] : null; ?>

                    <?php if ($avg): ?>
                        <div class="car-mark-model"><div class="avg-comparison-model"><p class="model-avg-main"><?php echo number_format($avg, 1); ?></p><p class="model-compare-main"></p></div></div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>

            <?php if ($car->hasTechnicalOptions()): ?>
                <h3 class="catalog-view-technical-title">
                    <?= Yii::t('app', 'Technical specifications'); ?>

                    <?php if (!Yii::$app->user->isGuest && $user->role == User::ROLE_ADMIN): ?>
                        <a href="#" class="car-technical-spec-edit btn btn-default btn-noshadow btn-sm" data-id="<?= $car->id; ?>">
                            <i class="fa fa-gear"></i> Редактировать
                        </a>
                    <?php endif ?>
                </h3>

                <div class="catalog-view-technical-wrapper">
                    <?= $this->render('@admin/views/car/_tech_tree', ['car' => $car, 'itemType' => 'span', 'id' => 'w_car_' . $car->id]);?>
                </div>
            <?php else: ?>
                <?php if (!Yii::$app->user->isGuest && $user->role == User::ROLE_ADMIN): ?>
                    <a href="#" class="car-technical-spec-edit btn btn-default btn-noshadow" data-id="<?= $car->id; ?>" style="margin: 10px 0 0 10px;">
                        <i class="fa fa-gear"></i> Редактировать технические характеристики
                    </a>
                <?php endif ?>
            <?php endif; ?>
        </div>

        <div class="column-section">
            <?php if ($comparisons): ?>
                <h3><?= Yii::t('app', 'Popular comparisons'); ?></h3>

                <div class="catalog-vs-list">
                    <?php foreach ($comparisons as $comparison): ?>
                        <div class="catalog-vs-list-item">
                            <div class="catalog-vs-list-item-image">
                                <?php $carType = $car->id == $comparison->car_main_id ? 'main' : 'compare'; ?>
                                <?php $vsType = $car->id == $comparison->car_main_id ? 'compare' : 'main'; ?>

                                <?= Html::img($comparison->getImage($vsType)); ?>

                                <p class="catalog-model-comparison-value" data-grade="<?= $comparison->getGrade($carType); ?>">
                                    <?= $comparison->getGrade($vsType); ?>
                                </p>
                            </div>

                            <div class="catalog-vs-list-item-comparison">
                                <?= Html::a($comparison->getCarName($vsType), $comparison->getUrl(), ['class' => 'dotted']); ?>

                                <div class="comparison-list-item-user">
                                    <?= UserLink::widget(['user' => $comparison->user]); ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <?php if (count($car->comparisons) > Car::CATALOG_POPULAR_COMPARISONS_COUNT): ?>
                    <p style="margin: 0 10px">
                        <?= Html::a(
                            'Все сравнения c ' . $car->model->getFullName(),
                            UrlHelper::absolute('comparison/model/' . $car->model->getUrl()),
                            ['class' => 'btn btn-default wrapped']
                        ); ?>
                    </p>
                <?php endif; ?>
            <?php else: ?>
                <h3><?= Yii::t('app', 'No comparisons yet'); ?></h3>
                <p style="margin:30px 10px;">
                    <a href="<?= BaseUrl::home(true) . 'comparison/add/' . $car->engine->getUrl(); ?>" class="dotted"><?= Yii::t('app', 'Be first!'); ?></a>
                </p>
            <?php endif; ?>

            <?php if (count($comments)): ?>
                <h3><?= Yii::t('app', 'Commenting'); ?></h3>

                <div class="catalog-view-comment-wrapper">
                    <?php foreach ($comments as $comment): ?>
                        <?php /** @var \app\models\Comparison $owner */?>
                        <?php $owner = $comment->owner; ?>

                        <div class="catalog-view-comment">
                            <p><?= Html::a($owner->getFullName(), $owner->getUrl(), ['class' => 'dotted']); ?></p>

                            <div class="user-profile-link">
                                <?= UserLink::widget(['user' => $comment->user]); ?>
                            </div>

                            <div class="catalog-view-comment-text"><?= strip_tags($comment->text); ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if (count($news)): ?>
                <h3><?= Yii::t('app', 'News') . ' ' . $car->manufacturer->name; ?></h3>

                <div class="catalog-view-news-wrapper">
                    <?php foreach ($news as $item): ?>
                    <div class="catalog-view-news-item">
                        <div class="catalog-view-news-image">
                            <?= Html::img($item->featured_image ? UrlHelper::absolute($item->featured_image) : $car->getImage()); ?>
                        </div>

                        <div class="catalog-view-news-text">
                            <h4><?= Html::a($item->title, $item->getUrl()); ?></h4>

                            <p class="catalog-view-news-date"><?= Yii::$app->formatter->asDate($item->created); ?></p>

                            <p><?= $item->getExcerpt(); ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="clear"></div>
    </div>
</div>

<?= $this->render('@admin/views/car/_modal_car'); ?>