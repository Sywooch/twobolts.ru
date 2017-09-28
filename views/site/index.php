<?php

/** @var $this yii\web\View */
/** @var Comparison $comparison */
/** @var Comparison[] $newComparisons */

use app\components\widgets\ComparisonList;
use app\components\widgets\FooterList;
use app\models\Comparison;
use yii\helpers\Url;

$this->title = Yii::$app->params['siteTitle'] . ' — ' . Yii::t('app', 'Car compares');
$this->registerMetaTag([
    'description' => Yii::t('app/meta', 'Index Page Description')
]);

?>

<div class="site-index">
    <div class="body-content">

        <?php if (Yii::$app->user->isGuest): ?>
            <div class="promo">

                <p>Туболтс — рейтинг автомобилей основанный на ваших сравнениях.</p>

                <div class="promo-box">
                    <div class="ob_icon icon_cmpr"><p>Сравнивайте</p></div>

                    <div class="ob_icon icon_share"><p>Делитесь своим опытом</p></div>

                    <div class="ob_icon icon_bbl"><p>Обсуждайте</p></div>
                </div>

                <p class="descr">Для добавления своих сравнений, комментирования и использования других функций сайта — <a href="#" class="authOpen">зарегистрируйтесь</a> или авторизуйтесь через соцсети.</p>

            </div>
        <?php endif; ?>

        <?= $this->render('../_comparison', [
            'comparison' => $comparison,
            'fullView' => false
        ]); ?>

        <div class="clear"></div>

        <p style="text-align:center; margin-bottom:10px;">
            <a href="<?= Url::to('/comparison/view/' . ($comparison->url_title ? $comparison->url_title : $comparison->url_title)); ?>" class="btn_orange" style="margin-top: 0;">
                <?= Yii::t('app', 'Read more'); ?>
            </a>
        </p>

    </div>

    <div class="home-new-compares">
        <h2>Новые сравнения</h2>

        <div class="comparison-list-wrap">
            <?= ComparisonList::widget([
                'items' => $newComparisons,
                'loadMore' => false,
                'showUser' => true,
                'showAvatar' => true,
                'showDate' => false
            ]); ?>
        </div>
    </div>

    <?= FooterList::widget(); ?>

</div>

<?php
$this->registerJsFile('/js/jquery/jquery.bxslider/jquery.bxslider.min.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$js = <<<EOD
$(function() {
    $('.comparison-values-wrap').bxSlider({
        pager: false,
        controls: false,
        auto: true,
        pause: 4000,
        //mode: 'fade',
        speed: 1000
    });
});
EOD;

$this->registerJs($js, \yii\web\View::POS_END);
?>