<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Model */

$this->title = Yii::t('app/admin', 'Go admin') . ' - ' . Yii::t('app/admin', 'Create Model');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app/admin', 'Models'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('app/admin', 'Create Model');
?>
<div class="form-container model-create">

    <h1><?= Html::encode(Yii::t('app/admin', 'Create Model')) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>