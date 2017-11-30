<?php
use app\models\User;
use yii\helpers\Html;
use yii\helpers\Url;

$user = User::identity();
var_dump(count(User::identity()->notifications));
?>

<div class="login-link">
	<div class="comparison-author-avatar-32 in_header">
	    <?= Html::img(
            User::getDefaultAvatar($user->avatar),
            ['width' => 32]
        ); ?>
</div>
<div style="float:left; margin-left:40px; color:#fff;">
    Привет, <span class="logged-user-name"><?= Html::a($user->username, '/profile'); ?></span>
</div>
<span style="float:left; padding:0 5px;">|</span>
<a href="#" id="signOff">Выход</a>
<?= $user->isAdmin() ? Html::a(Yii::t('app', 'Admin link'), Url::to('/admin')) : ''; ?>
</div>