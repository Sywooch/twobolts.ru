<?php
use yii\helpers\BaseUrl;
?>
<h2 style="font: normal 20px/23px Arial, Helvetica, sans-serif; margin: 0; padding: 0 0 18px; color: black;">
    Добро пожаловать на <?= Yii::$app->name; ?>!
</h2>
Спасибо, что присоединились к <?= Yii::$app->name; ?>. Ниже указаны Ваши регистрационные данные, сохранятйие их в безопасности.
<br />
<br />
<br />
Логин: <?= $user->username; ?><br />
Пароль: <?= $password; ?>
<br />
<br />
Чтобы перейти на <?= Yii::$app->name; ?>, кликните по ссылке ниже:<br />
<b><a href="<?= BaseUrl::home(true); ?>" style="color: #3366cc;">Перейти на <?= Yii::$app->name; ?> сейчас!</a></b>
<br />
<br />
Не работает ссылка? Скопируйте данную ссылку в адресную строку Вашего броузера:<br />
<nobr><a href="<?= BaseUrl::home(true); ?>" style="color: #3366cc;"><?= BaseUrl::home(true); ?></a></nobr>
<br />
<br />
Приятного дня!
<p><?= Yii::t('app/email', 'Thank you {app_name}', ['app_name' => Yii::$app->name]); ?></p>

<p>&nbsp;</p>

<p style="font-size: 11px;"><?= Yii::t(
        'app/email',
        'This email was automatically generated by {app_name}. Please do not reply to this message',
        ['app_name' => Yii::$app->name]
    );?></p>