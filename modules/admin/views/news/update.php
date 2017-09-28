<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\News */
/* @var $manufacturers \app\models\Manufacturer[] */

$this->title = Yii::t('app/admin', 'Go admin') . ' - ' . Yii::t('app/admin', 'Update News') . ' - ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app/admin', 'News'), 'url' => ['index']];
$this->params['breadcrumbs'][] = \yii\helpers\StringHelper::truncateWords($model->title, 5);
?>
<div class="form-container news-update">

    <h1><?= Html::encode(Yii::t('app/admin', 'Update News') . ': ' . $model->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'manufacturers' => $manufacturers
    ]) ?>

</div>