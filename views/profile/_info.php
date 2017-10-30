<?php
/** @var yii\web\View $this */
/** @var \app\models\Comparison[] $comparisons */
/** @var \app\models\Comparison[] $favorites */
/** @var \app\models\UserCar[] $before */
/** @var \app\models\UserCar[] $garage */
/** @var bool $isOwn */
/** @var int $comparisonsCount */
/** @var int $favoritesCount */
/** @var User $user */

use app\components\widgets\ComparisonList;
use app\models\Car;
use app\models\User;
use yii\helpers\Html;

if ($user->profile) {
    $profile = $user->profile;
} else {
    $profile = $user->createProfile();
}
?>

<div class="body-content">
    <h1><?= $isOwn ? 'Мой профиль' : $user->username; ?></h1>
</div>

<div class="my_profile_allview">
    <div class="left_clmn">
        <div class="avat_pls_nm profile_left_block_avatar ">
            <div class="avat_50">
                <?= Html::img(User::getDefaultAvatar($user->avatar),
                    ['width' => 50]
                ); ?>
            </div>
            <div class="comparison-author-name-link">
                <p style="color: #999"><?= $user->username; ?></p>
                <?= $profile->getFullLocation('p'); ?>
                <span class="users-rating"><?= number_format($user->getRating(), 0); ?></span>
                <p></p>
            </div>
            <div class="clear"></div>
        </div>

        <?php if ($profile->about || $isOwn): ?>
            <div class="profile_left_block">
                <p class="block_title">О себе <?= $isOwn ? '<a href="#" id="edit_profile"><i class="fa fa-pencil"></i></a>' : ''; ?></p>

                <p>
                    <?php if ($profile->about): ?>
                        <?= $profile->about; ?>
                    <?php elseif ($isOwn): ?>
                        <span class="border-bottom-dotted">Расскажите немного о себе!</span>
                    <?php endif ?>
                </p>
            </div>
        <?php endif ?>

        <?php if (count($garage)): ?>
            <div class="profile_left_block">
                <p  class="block_title garage">В гараже</p>

                <?php foreach ($garage as $item): ?>
                    <div class="garage-item">
                        <?php if ($isOwn): ?>
                            <a onclick="return confirm('Удалить выбранный авто?');" href="/profile/delete-car?id=<?= $item->id; ?>">
                                <i class="fa fa-trash fnUserCarDelete"></i>
                            </a>
                        <?php endif ?>

                        <?= Html::img(Car::getDefaultImage($item->car->model->image)); ?>

                        <?= $item->car->getFullName(); ?>
                    </div>

                    <div class="clear" style=""></div>
                <?php endforeach; ?>
            </div>
        <?php endif ?>

        <?php if (count($before)): ?>
            <div class="profile_left_block">
                <p  class="block_title garage">Ездил раньше</p>

                <?php foreach ($before as $item): ?>
                    <div class="garage-item">
                        <?php if ($isOwn): ?>
                            <a onclick="return confirm('Удалить выбранный авто?');" href="/profile/delete-car?id=<?= $item->id; ?>">
                                <i class="fa fa-trash fnUserCarDelete"></i>
                            </a>
                        <?php endif ?>

                        <?= Html::img(Car::getDefaultImage($item->car->model->image)); ?>

                        <?= $item->car->getFullName(); ?>
                    </div>

                    <div class="clear" style=""></div>
                <?php endforeach; ?>
            </div>
        <?php endif ?>

        <div class="clear"></div>
    </div>

    <div class="right_clmn">
        <?php if ($comparisons): ?>
            <h3>Сравнил <sup><?= $comparisonsCount; ?></sup></h3>

            <div class="comparison-list-wrap">
                <?= ComparisonList::widget([
                    'items' => $comparisons,
                    'loadMore' => false,
                    'showUser' => false,
                    'showAvatar' => false
                ]); ?>
            </div>
            
            <?php if ($comparisonsCount > User::PROFILE_COMPARISONS_PER_PAGE): ?>
                <a href="/comparison/user/<?= $user->username; ?>" class="edit_profile_link" style="margin: 0 10px 20px;">Все сравнения</a>
            <?php endif; ?>
        <?php endif; ?>

        <?php if ($favorites): ?>
            <h3>В избранном <sup><?= $favoritesCount; ?></sup></h3>

            <div class="comparison-list-wrap">
                <?= ComparisonList::widget([
                    'items' => $favorites,
                    'loadMore' => false,
                    'showUser' => true,
                ]); ?>
            </div>

            <?php if ($favoritesCount > User::PROFILE_COMPARISONS_PER_PAGE): ?>
                <a href="/profile/favorites/<?= $user->username; ?>" class="edit_profile_link" style="margin: 0 10px 20px;">Все сравнения</a>
            <?php endif; ?>
        <?php endif; ?>

        <div class="clear"></div>
    </div>

    <div class="clear"></div>
</div>

<div class="clear"></div>