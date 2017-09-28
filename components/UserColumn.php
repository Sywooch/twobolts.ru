<?php

namespace app\components;


use app\components\widgets\UserLink;
use kartik\grid\DataColumn;


class UserColumn extends DataColumn
{
    public $user_attribute = null;

    protected function renderDataCellContent($model, $key, $index)
    {
        $column = $this->user_attribute ? $this->user_attribute : $this->attribute;
        $user = $model->{$column};

        return UserLink::widget([
            'user' => $user,
            'showAvatar' => true,
            'url' => 'admin/user/update?id=',
            'urlAttribute' => 'id',
            'options' => [
                'class' => 'user-column-link',
                'data-pjax' => 0
            ]
        ]);
    }
}