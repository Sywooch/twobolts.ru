<?php
/** @var \yii\web\View $this */
/** @var Car $car */
/** @var string $itemType */

use app\components\ArrayHelper;
use app\models\Car;
use yii\bootstrap\Collapse;
use yii\helpers\Html;

$items = [];
$counter = 0;
$itemType = isset($itemType) ? $itemType : 'input';
$widgetId = isset($id) ? $id : 'w_car_0';

foreach ($car->getTechnicalOptions() as $categoryKey => $category)
{
    $contentOptions = [];
    if ($counter == 0) {
        $contentOptions = [
            'contentOptions' => [
            'class' => 'in'
            ]
        ];
    }

    $content = [];
    foreach ($category['items'] as $itemKey => $item)
    {
        $name = 'CarTechnical_' . $categoryKey . '_' . $itemKey;

        $itemElement = $itemType == 'input'
            ? Html::input('text', $name, $item['value'], ['id' => $name, 'class' => 'form-control car-tech-input', 'data-id' => $itemKey])
            : Html::tag($itemType, $item['value']) . ' ';

        $content[] = Html::label($item['name'], $name) .
            '<div class="relative">' . $itemElement .
            '<span>' . $item['units'] . '</span></div>';
    }

    $item = [
        'label' => $category['name'],
        'content' => $content,
    ];

    $items[] = ArrayHelper::merge($item, $contentOptions);

    ++$counter;
}

echo Collapse::widget([
    'items' => $items,
    'options' => ['id' => $widgetId]
]);