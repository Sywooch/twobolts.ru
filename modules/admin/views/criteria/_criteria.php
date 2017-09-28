<?php
/* @var \yii\web\View $this */
/* @var \app\models\ComparisonCriteria[] $criteria */

use app\components\IconHelper;
use kartik\sortable\Sortable;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\web\JsExpression;

$items = [];

foreach ($criteria as $criteriaItem)
{
    $row = [
        'content' => $this->render('_criteria_item', ['criteriaItem' => $criteriaItem])
    ];

    $items[] = $row;
}
?>

<p class="pull-right"><?= Html::a(IconHelper::show('add') . Yii::t('app/admin', 'Create Criteria'), '#', ['class' => 'btn btn-success btnCriteriaDlg']) ?></p>

<div class="clearfix"></div>

<?= Sortable::widget([
    'options' => ['class' => 'settings-sortable criteria-sortable'],
    'items' => $items,
    'pluginEvents' => [
        'sortupdate' =>  new JsExpression('function(e, data) { admin.settings.sortCriteriaItems(); }'),
    ]
]); ?>

<?php Modal::begin([
    'id' => 'criteriaDlg',
    'header' => '<h4 class="modal-title">' . Yii::t('app/admin', 'Comparison Criteria') . '</h4>',
    'size' => Modal::SIZE_DEFAULT,
    'clientOptions' => false
]); ?>

<div class="criteria-form-container"></div>

<?php Modal::end(); ?>
