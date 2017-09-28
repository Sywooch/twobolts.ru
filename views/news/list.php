<?php
/** @var News[] $models */
/** @var int $page */

use app\components\IconHelper;
use app\components\UrlHelper;
use app\models\News;
use yii\helpers\Html;
?>

<?php foreach ($models as $key => $news): ?>
    <div class="news-list-item news-list-item-<?= $news->id; ?> <?= $key < 3 && $page == 1 ? 'large' : 'small'; ?>">
        <?php if ($news->featured_image): ?>
            <div class="news-list-item-image">
                <?= $news->getThumbnailImage('large'); ?>
            </div>
        <?php endif; ?>

        <div class="news-list-item-excerpt-wrapper">
            <p class="news-list-item-details">
                <?= Yii::$app->formatter->asDate($news->created); ?>

                <?= IconHelper::show('eye') . $news->num_views; ?>

                <?= IconHelper::show('comment') . $news->getCommentsCount(); ?>
            </p>

            <h2><?= Html::a($news->title, $news->getUrl()); ?></h2>

            <p class="news-list-item-excerpt"><?= $news->getExcerpt(); ?></p>
        </div>
    </div>
<?php endforeach; ?>