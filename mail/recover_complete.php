<h2 style="font: normal 20px/23px Arial, Helvetica, sans-serif; margin: 0; padding: 0 0 18px; color: black;">
    Ваш новый пароль на сайте <?= Yii::$app->name; ?>
</h2>
Вы сменили свой пароль.<br />
Не забывайте свой пароль: он хранится в нашей базе в зашифрованном виде, и мы не сможем вам его выслать. Если вы всё же забудете пароль, то сможете запросить новый.
<br />
<br />
Логин: <?= $user->username; ?><br />
Пароль: <?= $password; ?>
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
