<?php
/** @var \app\models\User $user */
/** @var \app\models\Comparison $model */

use yii\helpers\BaseUrl;
use yii\helpers\Url;
?>

<p><?= Yii::t(
        'app/email',
        'This e-mail is notification of the following recent change to your {app_name} account',
        ['app_name' => Yii::$app->name]
    ); ?></p>

<h4><?= Yii::t('app/email', 'New comparison added'); ?></h4>

<p><?= Yii::t('app/email', 'User {username} had added new comparison', ['username' => $user->username]); ?></p>

<p><a href="<?= Url::to(BaseUrl::home(true).'comparison/view/' . $model->url_title); ?>"><?= $model->getFullName(); ?></a>.</p>

<p>&nbsp;</p>

<p><?= Yii::t('app/email', 'If you did not make this change or authorize someone else to make the change, contact us immediately'); ?></p>
Перейти в <a href="<?= Url::to(BaseUrl::home(true) . 'admin/comparisons'); ?>">администрирование</a> сравнений.
<p>&nbsp;</p>

Приятного дня!
<p><?= Yii::t('app/email', 'Thank you {app_name}', ['app_name' => Yii::$app->name]); ?></p>

<p>&nbsp;</p>

<p style="font-size: 11px;"><?= Yii::t(
        'app/email',
        'This email was automatically generated by {app_name}. Please do not reply to this message',
        ['app_name' => Yii::$app->name]
    );?></p>