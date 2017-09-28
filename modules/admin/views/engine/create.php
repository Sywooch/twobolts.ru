<?php
/* @var $this yii\web\View */
/* @var $model app\models\Model */

use yii\helpers\Html;

$this->title = Yii::t('app/admin', 'Go admin') . ' - ' . Yii::t('app/admin', 'Create Engine');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app/admin', 'Engines'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('app/admin', 'Create Engine');
?>
<div class="form-container engine-create">

    <h1><?= Html::encode(Yii::t('app/admin', 'Create Engine')) ?></h1>

    <?= $this->render('_form', [
        'model' => $model
    ]) ?>

</div>