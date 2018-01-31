<?php

use app\models\Notification;
use app\models\User;
use yii\helpers\Html;
use yii\helpers\Url;

$notifications = User::identity()->getUnreadNotificationsCount();
$icons = ['commenting', 'star', 'thumbs-up', 'thumbs-down', 'sliders'];
$last = User::identity()->getLastNotifications();

?>

<div class="login-link">
    <ul class="nav navbar-nav navbar-left">
        <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                <span class="user-avatar-24 in_header">
                    <?= Html::img(
                        User::getDefaultAvatar(User::identity()->avatar),
                        ['width' => 24]
                    ); ?>
                </span>

                <span class="logged-user-name">Привет, <strong style="color: #fff;" class="bordered"><?= User::identity()->username; ?></strong></span>
            </a>

            <ul class="dropdown-menu">
                <li><?= Html::a('<i class="fa fa-user"></i>Профиль', '/profile'); ?></li>

                <?php if (User::identity()->isAdmin()): ?>
                    <li><?= Html::a('<i class="fa fa-cog"></i>' . Yii::t('app', 'Admin link'), Url::to('/admin'))?></li>
                <?php endif; ?>

                <li class="divider"></li>

                <li><a href="#" id="signOff"><i class="fa fa-sign-out"></i>Выйти</a></li>
            </ul>
        </li>

        <li class="user-notification">
            <a href="#" class="dropdown-toggle user-notification-wrapper <?= $notifications ? 'has-notification' : ''; ?>" data-toggle="dropdown">
                <i class="fa fa-bell"></i><sup class="notification-counter"><?= $notifications ? $notifications : ''; ?></sup>
            </a>

            <ul class="dropdown-menu">
                <?php if (count($last)): ?>
                    <?php foreach ($last as $notification): ?>
                        <li class="<?= $notification->is_read ? '' : 'new-notification'; ?>">
                            <p>
                                <span><i class="fa fa-<?= Notification::$icons[$notification->type]; ?>"></i></span>

                                <span>
                                    <span><?= $notification->message; ?></span>
                                    <span class="block" style="padding-top: 3px; color: #999;">
                                        <?= Yii::$app->formatter->asDate($notification->created, 'medium'); ?>
                                    </span>
                                </span>
                            </p>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li><p><?= Yii::t('app', 'No notifications'); ?></p></li>
                <?php endif; ?>
            </ul>
        </li>
    </ul>
</div>