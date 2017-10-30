<?php

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception \yii\web\HttpException|Exception */

use app\components\IconHelper;
use yii\helpers\Html;

$this->title = Yii::t('app/error', 'Error code {code}', ['code' => $exception->statusCode]) . ' — ' . Yii::$app->params['siteTitle'];

//echo '<pre>' . print_r($exception, true);

switch ($exception->statusCode) {
    default: $icon = 'recycle'; break;
}

?>
<div class="inside height-wrapper">
    <div class="body-content error-content" style="padding: 20px;">
        <div class="row">
            <div class="col-md-3">
                <?= IconHelper::show($icon); ?>
            </div>

            <div class="col-md-9">
                <h1 style="padding: 0; text-align: left"><?= Html::encode($exception->getMessage()) ?></h1>

                <h3 style="text-align:left;"><?= Yii::t('app/error', 'Error code {code}', ['code' => $exception->statusCode])?></h3>

                <p>&nbsp;</p>

                <p>&nbsp;</p>

                <p style="font-size: 16px;"><?= Yii::t('app/error', 'Error ' . $exception->statusCode)?></p>
            </div>
        </div>
    </div>
</div>
