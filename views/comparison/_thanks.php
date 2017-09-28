<?php
/** @var \app\models\Comparison $comparison */

use app\components\IconHelper;
use app\components\widgets\UserLink;
use app\models\User;
use yii\helpers\BaseUrl;
use yii\helpers\Html;

$thanksCount = count($comparison->thanks);
$dislikeCount = count($comparison->dislikes);

$tooltip = 'Это сравнение понравилось ' . Yii::t('app', '{n,plural,=0{users} one{user} other{users}}', ['n' => $thanksCount]) . ', не понравилось ' . Yii::t('app', '{n,plural,=0{users} one{user} other{users}}', ['n' => $dislikeCount]);
?>

<?php if ($comparison->thanks): ?>
    <div class="comparison-grey-bg">
        <div class="comparison-thanks-wrapper">
            <div id="thanksList">
                <h3 title="<?= $tooltip; ?>">
                    <a href="#" id="btn-thanks"><?= IconHelper::show('triangle_down')?> Нравится</a>
                    <sup>
                        <?= $thanksCount . ($dislikeCount ? ' из ' . ($thanksCount + $dislikeCount) : ''); ?>
                    </sup>
                </h3>
                <?php foreach ($comparison->thanks as $thanks): ?>
                    <p class="user-avatar-link comparison-thanks-user-link"><?= UserLink::widget(['user' => $thanks->user]); ?></p>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
<?php elseif ($comparison->dislikes): ?>
    <div class="comparison-grey-bg">
        <div class="comparison-thanks-wrapper">
            Это сравнение не понравилось <?= Yii::t('app', '{n,plural,=0{users} one{user} other{users}}', ['n' => $dislikeCount])?>
        </div>
    </div>
<?php endif; ?>