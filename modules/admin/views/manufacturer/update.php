<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Manufacturer */

$this->title = Yii::t('app/admin', 'Go admin') . ' - ' . Yii::t('app/admin', 'Update Manufacturer') . ' - ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app/admin', 'Manufacturers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('app/admin', 'Update Manufacturer');
?>
<div class="form-container manufacturer-update">

    <h1><?= Html::encode(Yii::t('app/admin', 'Update Manufacturer') . ': ' . $model->name) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>