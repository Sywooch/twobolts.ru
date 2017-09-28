<?php

namespace app\components;


use kartik\grid\DataColumn;
use yii\helpers\Html;


class ImageColumn extends DataColumn
{
    protected function renderDataCellContent($model, $key, $index)
    {
        if ($model->{$this->attribute}) {
            $thumbFile = ImageHelper::createThumbnailFile(
                ImageHelper::getImageFile($model, $this->attribute),
                100,
                100,
                false
            );

            return '<span class="thumbnail">' . Html::img($thumbFile) . '</span>';
        }

        return '';
    }
}