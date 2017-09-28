<?php
/* @var \app\models\ComparisonCriteria $criteriaItem */

use yii\helpers\Html;

?>

<span class="criteria-icon"><?= $criteriaItem->getIcon(); ?></span>

<span class="criteria-name" data-order="<?= $criteriaItem->id; ?>"><?= $criteriaItem->name; ?></span>

<span class="action-cell pull-right">
    <?= Html::a('<i class="glyphicon glyphicon-pencil"></i>', '#', ['class' => 'edit-criteria-item', 'data-ajax' => $criteriaItem->id]); ?>
    <?= Html::a(
        '<i class="glyphicon glyphicon-trash"></i>',
        '#',
        [
            'data-confirmation' => Yii::t('app/admin', 'Are you sure to delete?'),
            'data-callback' => '/admin/criteria/delete/?id=' . $criteriaItem->id
        ]); ?>
</span>

<span class="pull-right"><?= $criteriaItem->show_on_home ? '<span class="label label-info"><i class="fa fa-check"></i> На главной</span>' : ''; ?></span>