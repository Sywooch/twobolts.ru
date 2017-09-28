<?php
/** @var $this yii\web\View */
/** @var Comparison $comparison */
/** @var bool $fullView */
/** @var array $carMainOptions */
/** @var array $carCompareOptions */

use app\components\IconHelper;
use app\components\widgets\UserLink;
use app\models\Car;
use app\models\Comparison;
use app\models\User;
use yii\helpers\ArrayHelper;
use yii\helpers\BaseUrl;
use yii\helpers\Url;

/** @var User $user */
$user = Yii::$app->user->identity;

$mainGrade = $comparison->getGrade('main');
$compareGrade = $comparison->getGrade('compare');
$favoriteCount = $comparison->getFavoritesCount();
$thanksCount = $comparison->getThanksCount();
?>

<div class="comparison-lg">
    <div class="comparison-car-main-wrap">
        <img src="<?= Car::getDefaultImage($comparison->main_foto); ?>" class="comparison-image">

        <div class="comparison-car-details">
            <div class="comparison-car-grade-wrap">
                <img src="/images/<?= $mainGrade >= $compareGrade ? 'orange' : 'green'; ?>_gr_scale_vrt.png" class="comparison-car-main-scale" width="20" height="<?= $mainGrade * Comparison::MAX_CRITERIA; ?>%">
                <p><?= $mainGrade; ?></p>
            </div>

            <div class="comparison-car-link-wrap">
                <p>
                    <a href="<?= BaseUrl::home(true) . 'comparison/manufacturer/' . ($comparison->carMain->manufacturer->url_title ? $comparison->carMain->manufacturer->url_title : $comparison->carMain->manufacturer->id); ?>">
                        <?= $comparison->carMain->manufacturer->name; ?>
                    </a>
                </p>
                <p>
                    <a href="<?= BaseUrl::home(true) . 'comparison/model/' . ($comparison->carMain->model->url_title ? $comparison->carMain->model->url_title : $comparison->carMain->model->id); ?>">
                        <?= $comparison->carMain->model->name; ?>
                    </a>
                </p>
                <p class="comparison-car-engine"><?= $comparison->carMain->engine->engine_name . ' ' . Yii::t('app', 'Horse power'); ?></p>
                <p class="comparison-car-link-sm"><?= $comparison->carMain->model->body->body_name; ?></p>
            </div>
        </div>
    </div>

    <div class="comparison-car-compare-wrap">
        <img src="<?= $comparison->compare_foto; ?>" class="comparison-image">

        <div class="comparison-car-details comparison-car-compare-details">
            <div class="comparison-car-grade-wrap">
                <img src="/images/<?php echo $compareGrade >= $mainGrade ? 'orange' : 'green'; ?>_gr_scale_vrt.png" class="comparison-car-compare-scale" width="20" height="<?php echo $compareGrade * Comparison::MAX_CRITERIA; ?>%">
                <p><?php echo $compareGrade; ?></p>
            </div>

            <div class="comparison-car-link-wrap">
                <p>
                    <a href="<?= BaseUrl::home(true) . 'comparison/manufacturer/' . ($comparison->carCompare->manufacturer->url_title ? $comparison->carCompare->manufacturer->url_title : $comparison->carCompare->manufacturer->id); ?>">
                        <?= $comparison->carCompare->manufacturer->name; ?>
                    </a>
                </p>
                <p>
                    <a href="<?= BaseUrl::home(true) . 'comparison/model/' . ($comparison->carCompare->model->url_title ? $comparison->carCompare->model->url_title : $comparison->carCompare->model->id); ?>">
                        <?= $comparison->carCompare->model->name; ?>
                    </a>
                </p>
                <p class="comparison-car-engine"><?= $comparison->carCompare->engine->engine_name . ' ' . Yii::t('app', 'Horse power'); ?></p>
                <p class="comparison-car-link-sm"><?= $comparison->carCompare->model->body->body_name; ?></p>
            </div>
        </div>
    </div>

    <div class="clear"></div>
</div>

<div class="comparison-lg-points-wrap">
    <div>
        <div class="comparison-author-name">
            <div class="comparison-author-avatar-32"><img src="<?= User::getDefaultAvatar($comparison->user->avatar); ?>" width="32"></div>
            <div class="comparison-author-name-link">
                <?= UserLink::widget(['user' => $comparison->user, 'showAvatar' => false]); ?>
                <br>
                <span class="users-rating"><?= number_format($comparison->user->getRating(), 0); ?></span>
            </div>
            <div class="clear"></div>
        </div>

        <div class="comparison-icons-wrap">
            <?= IconHelper::show('star', ['data-toggle' => 'tooltip', 'data-placement' => 'top', 'title' => Yii::t('app', 'In Favorites')]) ?>
            <?= $favoriteCount ? $favoriteCount : '0';?>
            <?= IconHelper::show('thumb_up', ['data-toggle' => 'tooltip', 'data-placement' => 'top', 'title' => Yii::t('app', 'Thanks Said')]) ?>
            <?= $thanksCount ? $thanksCount : '0'; ?>
            <?= IconHelper::show('comments', ['data-toggle' => 'tooltip', 'data-placement' => 'top', 'title' => Yii::t('app', 'Comparison comments')]) ?>
            <?= $comparison->calculatedComments ? $comparison->calculatedComments : '0';?>
        </div>

        <div class="clear"></div>
    </div>

    <?php if ($fullView): ?>
        <div id="pager">
            <a data-slide-index="0" href="">Пользовательские баллы</a><a data-slide-index="1" href="">Тех. характеристики</a>
        </div>

        <div id="comparison_tabs">
            <div id="blue">
    <?php endif; ?>

    <?= $this->render('_values', [
        'comparison' => $comparison,
        'fullView' => $fullView,
        'mainGrade' => $mainGrade,
        'compareGrade' => $compareGrade
    ]); ?>

    <?php if ($fullView): ?>
            </div> <!-- #blue -->

            <div id="pink">
                <table border="0" class="car-technical-spec-table" cellpadding="0" cellspacing="0">
                    <?php if (!Yii::$app->user->isGuest && $user->role == User::ROLE_ADMIN): ?>
                        <tr class="car-technical-spec-edit-wrap">
                            <td>
                                <a href="#" class="car-technical-spec-edit btn btn-default btn-noshadow" data-id="<?= $comparison->carMain->id; ?>">
                                    <i class="fa fa-gear"></i> Редактировать
                                </a>
                            </td>
                            <td>&nbsp;</td>
                            <td style="text-align: right">
                                <a href="#" class="car-technical-spec-edit btn btn-default btn-noshadow" data-id="<?= $comparison->carCompare->id; ?>">
                                    <i class="fa fa-gear"></i> Редактировать
                                </a>
                            </td>
                        </tr>
                    <?php endif ?>

                    <?php $categoryLimit = 0; ?>

                    <?php foreach ($carMainOptions as $categoryId => $category): ?>
                        <?php if ($categoryLimit < 4 || $fullView): ?>
                            <tr>
                                <td colspan="3" align="center" class="teh_cat">
                                    <p><?= $category['name']; ?></p>
                                </td>
                            </tr>

                            <?php $optionLimit = 0; ?>

                            <?php foreach ($category['items'] as $optionId => $option): ?>
                                <?php if ($optionLimit < 3 || $fullView): ?>
                                    <?php $compareCategory = ArrayHelper::getValue($carCompareOptions, $categoryId); ?>
                                    <?php $compareOption = ArrayHelper::getValue($compareCategory['items'], $optionId); ?>
                                    
                                    <tr>
                                        <td align="center" width="40%">
                                            <?= $option['value'] ? $option['value'] . ($option['units'] ? ' <span>'. $option['units'].'</span>' : '') : '<span class="dash">&mdash;</span>'; ?>
                                        </td>
                                        <td align="center"><?= $option['name']; ?></td>
                                        <td align="center" width="40%">
                                            <?= $compareOption
                                                ? $compareOption['value']
                                                    ? $compareOption['value'] . ($compareOption['units'] ? ' <span>' . $compareOption['units'].'</span>' : '')
                                                    : '<span class="dash">&mdash;</span>'
                                                : '<span class="dash">&mdash;</span>';
                                            ?>
                                        </td>
                                    </tr>
                                <?php endif; ?>

                                <?php ++$optionLimit; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>

                        <?php ++$categoryLimit; ?>
                    <?php endforeach; ?>
                    
                </table>
            </div> <!-- #pink -->

            <div class="clear"></div>
        </div> <!-- #comparison_tabs -->
    <?php endif; ?>
</div>

<script>
    commentObject = '<?= addslashes(Comparison::className()); ?>';
</script>

<input type="hidden" id="object_id" name="object_id" value="<?= $comparison->id; ?>">

<?php
if ($fullView) {
    $js = <<<EOD
        $(function () {
            var comparisonId = $('#object_id').val();
        
            $.ajax({
                url: baseURL + 'comparison/update-views',
                async: true,
                type: 'POST',
                dataType: 'html',
                data: {
                    comparisonId: comparisonId
                }
            });
        });    
EOD;
    $this->registerJs($js, \yii\web\View::POS_END);
}
?>