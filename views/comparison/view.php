<?php
/** @var yii\web\View $this */
/** @var \app\models\Comparison $comparison */
/** @var array $carMainOptions */
/** @var array $carCompareOptions */

use app\components\IconHelper;
use yii\helpers\StringHelper;

$this->title = Yii::t('app', 'Car compares') . ' ' . $comparison->getFullName() . ' — ' . Yii::$app->params['siteTitle'];
$this->registerMetaTag([
    'description' => Yii::t('app/meta', 'Comments and rating comparison') . ' ' . $comparison->getFullName() . '. ' . StringHelper::truncate($comparison->comment, 10)
]);
?>

<div class="comparison-view height-wrapper">
    <?= $this->render('../_comparison', [
        'comparison' => $comparison,
        'carMainOptions' => $carMainOptions,
        'carCompareOptions' => $carCompareOptions,
        'fullView' => true
    ]); ?>

    <div class="clear"></div>

    <?php if ($comparison->comment): ?>
        <div class="comparison-grey-bg">
            <div class="comparison-resume-comment">
                <h2>Резюме</h2>
                <p><?= $comparison->comment; ?></p>
                <hr />
            </div>
        </div>
    <?php endif; ?>

    <?= $this->render('_thanks', ['comparison' => $comparison]); ?>

    <?php if ($comparison->isLikable()): ?>
        <div class="comparison-grey-bg">
            <div class="comparison-say-thanks">
                <h3>Полезное сравнение?</h3>
                <div class="comparison-thanks-button-group">
                    <button type="button" class="fnThanks">да</button>
                    <button type="button" class="fnDislike">нет</button>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($comparison->canFavorite()): ?>
        <div class="comparison-grey-bg" style="text-align: center;">
            <div class="comparison-add-favorite">
                <button class="fnFavorite">
                    <?= IconHelper::show('star-open'); ?> Добавить в избранное
                </button>
            </div>
        </div>
    <?php elseif ($comparison->isFavorite()): ?>
        <div class="comparison-grey-bg" style="text-align: center;">
            <div class="comparison-add-favorite">
                <span class="fnFavorited">
                    <?= IconHelper::show('star'); ?>  В избранном
                </span>
            </div>
        </div>
    <?php endif; ?>

    <div class="comments-list-container">
        <?= $this->render('comments', [
            'comparison' => $comparison,
        ]); ?>
    </div>
</div>

<?php $this->registerJsFile('/js/jquery/jquery.bxslider/jquery.bxslider.min.js', ['depends' => [\yii\web\JqueryAsset::className()]]); ?>

<?= $this->render('@admin/views/car/_modal_car'); ?>
