<?php
/* @var $this yii\web\View */
/* @var $model app\models\Engine */

use yii\helpers\Html;

$this->title = Yii::t('app/admin', 'Go admin') . ' - ' . Yii::t('app/admin', 'Update Engine') . ' - ' . $model->engine_name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app/admin', 'Engines'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('app/admin', 'Update Engine');
?>
<div class="form-container engine-update">

    <h1><?= Html::encode(Yii::t('app/admin', 'Update Engine') . ': ' . $model->engine_name) ?></h1>

    <?= $this->render('_form', [
        'model' => $model
    ]) ?>

</div>