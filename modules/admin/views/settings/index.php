<?php
/* @var \yii\web\View $this */
/* @var \app\models\ComparisonCriteria[] $criteria */
/* @var \app\models\TechnicalCategory[] $categories */

use yii\bootstrap\Modal;
use yii\helpers\Html;

$this->title = Yii::t('app/admin', 'Go admin') . ' - ' . Yii::t('app/admin', 'Settings');
$this->params['breadcrumbs'][] = Yii::t('app/admin', 'Settings');
?>

<div class="settings-index">
    <h1><?= Html::encode(Yii::t('app/admin', 'Settings')) ?></h1>

    <div class="row">
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading"><i class="fa fa-cogs"></i> <?= Yii::t('app/admin', 'Technical options'); ?></div>

                <div class="panel-body">
                    <?= $this->render('../technical/_technical', ['categories' => $categories]); ?>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading"><i class="fa fa-bar-chart"></i> <?= Yii::t('app/admin', 'Comparison Criteria'); ?></div>

                <div class="panel-body">
                    <?= $this->render('../criteria/_criteria', ['criteria' => $criteria]); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php Modal::begin([
    'id' => 'formDialog',
    'header' => '<h3 class="modal-title"></h3>',
    'size' => Modal::SIZE_DEFAULT,
    'clientOptions' => false
]); ?>

<div class="form-container"></div>

<?php Modal::end(); ?>