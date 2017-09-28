<?php
/* @var $this yii\web\View */
/* @var $model app\models\Model */

use yii\helpers\Html;

$this->title = Yii::t('app/admin', 'Go admin') . ' - ' . Yii::t('app/admin', 'Update Model') . ' - ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app/admin', 'Models'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('app/admin', 'Update Model');
?>
<div class="form-container model-update">

    <h1><?= Html::encode(Yii::t('app/admin', 'Update Model') . ': ' . $model->name) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>