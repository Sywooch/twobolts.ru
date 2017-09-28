<?php
/* @var $this yii\web\View */
/* @var $model app\models\Car */
/** @var TechnicalCategory[] $technicalOptions */

use app\models\TechnicalCategory;
use yii\helpers\Html;

$this->title = Yii::t('app/admin', 'Go admin') . ' - ' . Yii::t('app/admin', 'Create Car');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app/admin', 'Catalog'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('app/admin', 'Create Car');
?>
<div class="form-container car-create">

    <h1><?= Html::encode(Yii::t('app/admin', 'Create Car')) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'technicalOptions' => $technicalOptions
    ]) ?>

</div>