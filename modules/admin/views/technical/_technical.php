<?php
/** @var \app\models\TechnicalCategory[] $categories */

use app\components\IconHelper;
use kartik\sortable\Sortable;
use yii\helpers\Html;
use yii\web\JsExpression;

$items = $categoryOptions = [];

foreach ($categories as $category)
{
    $row = [
        'content' => $this->render('_technical_category_item', ['category' => $category])
    ];
    $items[] = $row;

    foreach ($category->options as $option)
    {
        $row = [
            'content' => $this->render('_technical_option_item', ['option' => $option])
        ];
        $categoryOptions[$category->id][] = $row;
    }
}
reset($categoryOptions);
?>

<p class="pull-right">
    <?= Html::a(IconHelper::show('add') . Yii::t('app/admin', 'Create Technical Category'), '#', ['class' => 'btn btn-success btnTechnicalDlg']) ?>
    <?= Html::a(IconHelper::show('add') . Yii::t('app/admin', 'Create Technical Option'), '#', ['class' => 'btn btn-warning btnTechnicalOptionDlg']) ?>
</p>

<div class="clearfix"></div>

<?= Sortable::widget([
    'options' => ['class' => 'settings-sortable technical-category-sortable'],
    'items' => $items,
    'pluginEvents' => [
        'sortupdate' =>  new JsExpression('function(e, data) { admin.settings.sortTechnicalCategoryItems(); }'),
    ]
]); ?>

<?php foreach ($categoryOptions as $id => $items): ?>
    <div class="category-options nested-<?= $id; ?>">
        <?= Sortable::widget([
            'options' => ['class' => 'settings-sortable technical-option-sortable'],
            'items' => $items,
            'pluginEvents' => [
                'sortupdate' =>  new JsExpression('function(e, data) { admin.settings.sortTechnicalOptionItems(' . $id . '); }'),
            ]
        ]); ?>
    </div>
<?php endforeach; ?>