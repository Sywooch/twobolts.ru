<?php

use app\components\ArrayHelper;
use app\components\IconHelper;
use app\components\ImageColumn;
use app\models\Body;
use app\models\Manufacturer;
use kartik\dynagrid\DynaGrid;
use kartik\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel app\models\BodySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app/admin', 'Go admin') . ' - ' . Yii::t('app/admin', 'Bodies');
$this->params['breadcrumbs'][] = Yii::t('app/admin', 'Bodies');
?>
<div class="body-index">

    <p class="pull-right"><?= Html::a(IconHelper::show('add') . Yii::t('app/admin', 'Create Body'), '#', [
            'class' => 'btn btn-success',
            'data-id' => 0,
            'data-title' => Yii::t('app/admin', 'Create Body'),
            'data-icon' => 'cubes',
            'data-form' => 'create'
        ]); ?></p>

    <h1><?= Html::encode(Yii::t('app/admin', 'Bodies')) ?></h1>

    <div class="clear"></div>

    <?= DynaGrid::widget([
        'gridOptions' => [
            'id' => 'w_admin_bodies',
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'resizableColumns' => false,
            'striped' => true,
            'bordered' => true,
            'hover' => true,
            'export' => false,
            'pjax' => true,
            'pjaxSettings' => [
                'neverTimeout' => true,
                'options' => [
                    'id' => 'w0_admin_bodies',
                ]
            ],
            'panel' => [
                'type' => GridView::TYPE_DEFAULT,
                'heading' => false,
                'before' => '{summary}',
                'after' => false,
            ],
            'toolbar' =>  [
                [
                    'content' => Html::a(
                        IconHelper::show('delete') . Yii::t('app/admin', 'Delete'),
                        ['#'],
                        [
                            'data-pjax' => 0,
                            'class' => 'btn btn-default deleteSelectedBtn',
                            'title' => Yii::t('app/admin', 'Delete selected')
                        ]
                    )
                ],
                [
                    'content' => Html::a(
                            IconHelper::show('refresh') . Yii::t('app/admin', 'Clear'),
                            ['/admin/body'],
                            [
                                'data-pjax' => 0,
                                'class' => 'btn btn-default',
                                'title' => Yii::t('app/admin', 'Reset data')
                            ]
                        ) . '{dynagridFilter}{dynagridSort}{dynagrid}{toggleData}'
                ]
            ]
        ],
        'showSort' => true,
        'showFilter' => true,
        'enableMultiSort' => false,
        'showPersonalize' => true,
        'allowThemeSetting' => false,
        'theme' => 'simple-bordered',
        'storage'=>'cookie',
        'options' =>  [
            'id' => 'admin_bodies'
        ],
        'columns' => [
            [
                'class' => 'kartik\grid\CheckboxColumn',
                'order' => DynaGrid::ORDER_FIX_LEFT
            ],
            [
                'attribute' => 'body_name',
                'width' => '80%'
            ],
            [
                'label' => 'Моделей',
                'content' => function ($model) {
                    /** @var \app\models\Body $model */
                    return count($model->models);
                },
                'contentOptions' => [
                    'class' => 'text-center'
                ],
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'order' => DynaGrid::ORDER_FIX_RIGHT,
                'template' => '{update}{delete}',
                'buttons' => [
                    'update' => function ($url, $model, $key) {
                        /** @var \app\models\Body $model */
                        return Html::a(
                            '<span class="glyphicon glyphicon-pencil"></span>',
                            '#',
                            [
                                'data-id' => $model->body_id,
                                'data-title' => Yii::t('app/admin', 'Update Body'),
                                'data-icon' => 'cubes',
                                'data-form' => 'update',
                                'data-pjax' => 0
                            ]
                        );
                    }
                ],
                'contentOptions' => [
                    'class' => 'action-cell text-center',
                    'width' => '100px'
                ],
            ],
        ],
    ]); ?>
</div>

<?php
$js = <<<EOD
    admin.gridElem = $('#w_admin_bodies');
    admin.gridController = 'body';
EOD;

$this->registerJs($js, \yii\web\View::POS_READY);