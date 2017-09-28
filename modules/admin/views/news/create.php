<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\News */
/* @var $manufacturers \app\models\Manufacturer[] */

$this->title = Yii::t('app/admin', 'Go admin') . ' - ' . Yii::t('app/admin', 'Create News');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app/admin', 'News'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('app/admin', 'Create News');
?>
<div class="form-container news-create">

    <h1><?= Yii::t('app/admin', 'Create News'); ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'manufacturers' => $manufacturers
    ]) ?>

</div>