<?php
/** @var User[] $activeUsers */
/** @var array $topComparisons */
/** @var News[] $lastNews */

use app\components\IconHelper;
use app\components\UrlHelper;
use app\components\widgets\UserLink;
use app\models\Car;
use app\models\News;
use app\models\User;
use yii\bootstrap\Html;
use yii\helpers\StringHelper;
use yii\helpers\Url;

?>
<div class="home-3clmn-active">
    <div class="bx_top_camp">
        <h2>Топ сравниваемых</h2>
        <?php if ($topComparisons): ?>
            <?php foreach ($topComparisons as $oneCompare): ?>
                <?php $grade = number_format($oneCompare['compares_value'], 1); ?>
                <?php $gradePercent = $grade * 10; ?>
                <p>
                    <img src="<?= Car::getDefaultImage($oneCompare['model_image']); ?>" class="top_list_photo">
                    <img src="/images/orange_grdnt_line.png" width="<?= $gradePercent?>%" height="1" class="top_list_scale">
                    <?= Html::a(
                        $oneCompare['manufacturer_name'].' '.$oneCompare['model_name'] . ($oneCompare['body_name'] ? ' '.$oneCompare['body_name'] : ''),
                        Url::to('/comparison/model/'.($oneCompare['url_title'] ? $oneCompare['url_title'] : $oneCompare['model_id']))
                    ); ?>
                    <span>
						<?= $oneCompare['model_compares']; ?> /
						<strong><?= $grade; ?></strong>
					</span>
                </p>
            <?php endforeach ?>
        <?php endif ?>
    </div>

    <div class="bx_act_users">
        <h2>Активные</h2>
        <?php foreach ($activeUsers as $activeUser): ?>
            <div class="avat_pls_nm">
                <div class="comparison-author-avatar-32">
                    <?= Html::img(User::getDefaultAvatar($activeUser->avatar), ['width' => 32]); ?>
                </div>
                <div class="comparison-author-name-link">
                    <?= UserLink::widget(['user' => $activeUser, 'showAvatar' => false]); ?>
                    <br>
                    <span class="users-rating"><?= number_format($activeUser->rating, 0); ?></span>
                </div>
                <div class="clear"></div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="clear"></div>

    <h2 style="margin-left: 10px;">Автоновости</h2>

    <div class="news-list-wrapper">
        <?php foreach ($lastNews as $news): ?>
            <div class="news-list-item news-list-item-<?= $news->id; ?> small">
                <?php if ($news->featured_image): ?>
                    <div class="news-list-item-image">
                        <?= $news->getThumbnailImage(); ?>
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
        <?php endforeach ?>
    </div>

    <div class="clear"></div>
</div>