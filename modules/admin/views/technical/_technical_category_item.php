<?php
/* @var \app\models\TechnicalCategory $category */

use yii\helpers\Html;
?>

<span class="category-nest"><?= Html::a('<i class="fa fa-plus-square-o fa-2x"></i>', '#', ['class' => 'btn-toggle-nested', 'data-target' => 'nested-' . $category->id]); ?></span>

<span class="category-name" data-order="<?= $category->id; ?>"><?= $category->category_name; ?> <span class="badge"><?= count($category->options); ?></span></span>

<span class="action-cell pull-right">
    <?= Html::a('<i class="glyphicon glyphicon-pencil"></i>', '#', ['class' => 'edit-technical-category-item', 'data-ajax' => $category->id]); ?>
    <?= Html::a(
        '<i class="glyphicon glyphicon-trash"></i>',
        '#',
        [
            'data-confirmation' => Yii::t('app/admin', 'Are you sure to delete technical category?'),
            'data-callback' => '/admin/technical/delete-category/?id=' . $category->id
        ]
    ); ?>
</span>