<?php
/* @var \app\models\TechnicalOption $option */

use yii\helpers\Html;

?>

<span class="option-name-<?= $option->tech_category_id; ?>" data-order="<?= $option->id; ?>">
    <?= $option->option_name; ?> <?= $option->option_units ? '<i>(' . $option->option_units . ')</i>' : ''; ?>
</span>

<span class="action-cell pull-right">
    <?= Html::a('<i class="glyphicon glyphicon-pencil"></i>', '#', ['class' => 'edit-technical-option-item', 'data-ajax' => $option->id]); ?>
    <?= Html::a(
        '<i class="glyphicon glyphicon-trash"></i>',
        '#',
        [
            'data-confirmation' => Yii::t('app/admin', 'Are you sure to delete?'),
            'data-callback' => '/admin/technical/delete-option/?id=' . $option->id
        ]); ?>
</span>