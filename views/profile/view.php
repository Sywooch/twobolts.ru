<?php
/** @var yii\web\View $this */
/** @var \app\models\Comparison[] $comparisons */
/** @var \app\models\Comparison[] $favorites */
/** @var \app\models\UserCar[] $before */
/** @var \app\models\UserCar[] $garage */
/** @var int $comparisonsCount */
/** @var int $favoritesCount */
/** @var User $user */

use app\models\User;

$this->title = Yii::t('app', 'User') . ' ' . $user->username . ' — ' . Yii::$app->params['siteTitle'];
?>

<div class="profile-index inside height-wrapper">
    <?= $this->render('_info', [
        'isOwn' => false,
        'user' => $user,
        'comparisons' => $comparisons,
        'comparisonsCount' => $comparisonsCount,
        'favorites' => $favorites,
        'favoritesCount' => $favoritesCount,
        'before' => $before,
        'garage' => $garage
    ]) ?>
</div>