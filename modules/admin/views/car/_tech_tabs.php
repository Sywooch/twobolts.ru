<?php
/** @var \yii\web\View $this */
/** @var Car $model */
/** @var string $itemType */
/** @var TechnicalCategory[] $technicalOptions */

use app\models\Car;
use app\models\TechnicalCategory;
use yii\bootstrap\Collapse;
use yii\helpers\Html;

$items = [];
$itemType = isset($itemType) ? $itemType : 'input';

foreach ($technicalOptions as $category)
{
    $content = [];

    foreach ($category->options as $item)
    {
        $name = 'car_technical[' . $item->id . ']';

        if ($model->isNewRecord) {
            $value = '';
        } else {
            $value = '';

            foreach ($model->technical as $option)
            {
                if ($option->tech_option_id == $item->id) {
                    $value = $option->tech_option_value;
                    break;
                }
            }
        }

        $itemElement = $itemType == 'input'
            ? Html::input('text', $name, $value, ['id' => $name, 'class' => 'form-control car-tech-input', 'data-id' => $item->id])
            : Html::tag($itemType, $value) . ' ';

        $content[] = Html::label($item->option_name, $name) .
            '<div class="relative">' . $itemElement .
            '<span>' . $item->option_units . '</span></div>';
    }

    $item = [
        'label' => $category->category_name,
        'content' => $content,
    ];

    $items[] = $item;
}

echo Collapse::widget([
    'items' => $items,
    'options' => [
        'id' => $model->isNewRecord ? 'w_car_0' : 'w_car_' . $model->id
    ]
]);