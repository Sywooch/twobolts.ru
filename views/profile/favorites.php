<?php
/** @var yii\web\View $this */
/** @var User $user */

use app\components\widgets\ComparisonList;
use app\models\User;

$this->title = $user->username . ': ' . Yii::t('app', 'User Favorites') . ' — ' . Yii::$app->params['siteTitle'];
?>

<div class="profile-index inside height-wrapper">
    <h1><?= $user->username . ': ' . Yii::t('app', 'User Favorites'); ?> <sup><?= count($user->favorites); ?></sup></h1>

    <div class="comparison-list-wrap">
        <?= ComparisonList::widget([
            'items' => $user->favorites,
            'loadMore' => false,
            'showUser' => true,
            'showAvatar' => true
        ]); ?>
    </div>
</div>