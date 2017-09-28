<?php
/** @var \app\models\CarRequest $model */

use yii\helpers\BaseUrl;
?>

<h2 style="font: normal 20px/23px Arial, Helvetica, sans-serif; margin: 0; padding: 0 0 18px; color: black;"><?= Yii::t('app/email', 'New car request'); ?></h2>

<br/>
<br/>

<div style="margin-left:50px; border-left:4px solid #eee; padding-left:20px;">
Марка: <?= $model->manufacturer; ?>
<br/>
<br/>
Модель: <?= $model->model; ?>
</div>
<br/>
<br/>
<?= Yii::t('app/email', 'Go to'); ?> <a href="<?= BaseUrl::home(true).'admin'; ?>"><?= Yii::t('app/email', 'administration')?></a>.