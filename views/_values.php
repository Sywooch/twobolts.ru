<?php
/** @var $this yii\web\View */
/** @var Comparison $comparison */
/** @var bool $fullView */
/** @var float $mainGrade */
/** @var float $compareGrade */

use app\models\Comparison;
use yii\helpers\Html;
use yii\helpers\StringHelper;

?>

<div class="comparison-values-wrap">
    <?php
    foreach ($comparison->criteria as $key => $criteria)
    {
        $showCriteria = true;

        if (!$fullView) {
            if (!$criteria->criteria->show_on_home) $showCriteria = false;
        }

        if ($showCriteria) {
            ?>
            <div class="comparison-values-item-wrap">

                <div class="comparison-values-item-title"><?= $criteria->criteria->name; ?></div>

                <div class="comparison-values-main-scale">
                    <img src="/images/<?= $mainGrade >= $compareGrade ? 'orange' : 'green'; ?>_points_scale_l.png" width="<?= $criteria->criteria_main_value; ?>0%">
                </div>

                <div class="comparison-values-item-value">
                    <span><?= $criteria->criteria_main_value; ?></span>
                    <?php
                    if ($criteria->criteria->icon) {
                        echo $criteria->criteria->getIcon();
                    }
                    ?>
                    <span><?= $criteria->criteria_compare_value; ?></span>
                </div>

                <div class="comparison-values-compare-scale">
                    <img src="/images/<?= $mainGrade < $compareGrade ? 'orange' : 'green'; ?>_points_scale_r.png" width="<?= $criteria->criteria_compare_value; ?>0%">
                </div>

                <div class="clear"></div>

                <?php if ($fullView && $criteria->criteria_comment): ?>
                    <div class="comparison-values-item-comment"><?= $criteria->criteria_comment; ?></div>
                <?php elseif ($criteria->criteria_comment): ?>
                    <div class="comparison-values-item-comment">
                        <?= StringHelper::truncateWords($criteria->criteria_comment, 20); ?>
                    </div>
                <?php endif; ?>

                <div class="clear"></div>

            </div>

            <?php
        }
    }
    ?>
</div>

<div class="clear"></div>