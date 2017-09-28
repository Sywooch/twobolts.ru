<?php

use app\components\IconHelper;
use kartik\dynagrid\DynaGrid;
use kartik\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ManufacturerSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app/admin', 'Go admin') . ' - ' . Yii::t('app/admin', 'Manufacturers');
$this->params['breadcrumbs'][] = Yii::t('app/admin', 'Manufacturers');
?>
<div class="manufacturer-index">

    <p class="pull-right"><?= Html::a(IconHelper::show('add') . Yii::t('app/admin', 'Create Manufacturer'), ['create'], ['class' => 'btn btn-success']) ?></p>

    <h1><?= Html::encode(Yii::t('app/admin', 'Manufacturers')) ?></h1>

    <div class="clear"></div>

    <?= DynaGrid::widget([
        'gridOptions' => [
            'id' => 'w_admin_manufacturers',
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
                    'id' => 'w0_admin_manufacturers',
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
                            ['/admin/manufacturer'],
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
            'id' => 'admin_manufacturers'
        ],
        'columns' => [
            [
                'class' => 'kartik\grid\CheckboxColumn',
                'order' => DynaGrid::ORDER_FIX_LEFT
            ],
            [
                'attribute' => 'name',
                'width' => '35%',
            ],
            [
                'attribute' => 'url_title',
                'width' => '30%',
            ],
            [
                'attribute' => 'is_popular',
                'content' => function ($model) {
                    /** @var \app\models\Manufacturer $model */
                    return $model->is_popular ? '<i class="fa fa-check-square-o fa-2x text-success"></i>' : '';
                },
                'contentOptions' => [
                    'class' => 'text-center'
                ],
                'filterType' => GridView::FILTER_SELECT2,
                'filter' => ['1' => Yii::t('app/admin', 'Popular'), '0' => Yii::t('app/admin', 'Regular')],
                'filterWidgetOptions' => [
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ],
                'filterInputOptions' => [
                    'placeholder' => Yii::t('app/admin', 'All manufacturers...')
                ],
                'format' => 'raw',
            ],
            [
                'label' => 'Моделей',
                'content' => function ($model) {
                    /** @var \app\models\Manufacturer $model */
                    return count($model->models);
                },
                'contentOptions' => [
                    'class' => 'text-center'
                ],
            ],
            [
                'label' => 'Автомобилей',
                'content' => function ($model) {
                    /** @var \app\models\Manufacturer $model */
                    return count($model->cars);
                },
                'contentOptions' => [
                    'class' => 'text-center'
                ],
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'order' => DynaGrid::ORDER_FIX_RIGHT,
                'template' => '{update}{delete}',
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
    admin.gridElem = $('#w_admin_manufacturers');
    admin.gridController = 'manufacturer';
EOD;

$this->registerJs($js, \yii\web\View::POS_READY);