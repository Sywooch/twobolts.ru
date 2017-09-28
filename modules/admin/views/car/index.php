<?php

use app\components\ArrayHelper;
use app\components\IconHelper;
use app\components\ImageColumn;
use app\models\Body;
use app\models\Manufacturer;
use app\models\Model;
use kartik\dynagrid\DynaGrid;
use kartik\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel app\models\CarSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app/admin', 'Go admin') . ' - ' . Yii::t('app/admin', 'Catalog');
$this->params['breadcrumbs'][] = Yii::t('app/admin', 'Catalog');
?>
<div class="car-index">

    <p class="pull-right"><?= Html::a(IconHelper::show('add') . Yii::t('app/admin', 'Create Car'), ['create'], ['class' => 'btn btn-success']) ?></p>

    <h1><?= Html::encode(Yii::t('app/admin', 'Catalog')) ?></h1>

    <div class="clear"></div>

    <?= DynaGrid::widget([
        'gridOptions' => [
            'id' => 'w_admin_cars',
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
                    'id' => 'w0_admin_cars',
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
                            ['/admin/car'],
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
            'id' => 'admin_cars'
        ],
        'columns' => [
            [
                'class' => 'kartik\grid\CheckboxColumn',
                'order' => DynaGrid::ORDER_FIX_LEFT
            ],
            [
                'attribute' => 'manufacturer_id',
                'content' => function ($model) {
                    /** @var \app\models\Car $model */
                    return $model->manufacturer->name;
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filter' => ArrayHelper::map(Manufacturer::filterData(), 'id', 'name'),
                'filterWidgetOptions' => [
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ],
                'filterInputOptions' => [
                    'placeholder' => Yii::t('app/admin', 'All cars...')
                ],
                'format' => 'raw',
                'width' => '25%'
            ],
            [
                'attribute' => 'model_id',
                'content' => function ($model) {
                    /** @var \app\models\Car $model */
                    return $model->model->getFullName(false);
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filter' => ArrayHelper::map(Model::filterData(null, $searchModel->manufacturer_id), 'id', 'name'),
                'filterWidgetOptions' => [
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ],
                'filterInputOptions' => [
                    'placeholder' => Yii::t('app/admin', 'All cars...')
                ],
                'format' => 'raw',
                'width' => '25%'
            ],
            [
                'attribute' => 'engine_id',
                'content' => function ($model) {
                    /** @var \app\models\Car $model */
                    return $model->engine->engine_name;
                },
                'width' => '25%'
            ],
            [
                'label' => Yii::t('app/admin', 'Technical'),
                'content' => function ($model) {
                    /** @var \app\models\Car $model */
                    return $model->hasTechnicalOptions() ? '<i class="fa fa-check-square-o fa-2x text-success"></i>' : '<i class="fa fa-ban fa-2x text-danger"></i>';
                },
                'contentOptions' => [
                    'class' => 'text-center'
                ],
                'width' => '15%'
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
    admin.gridElem = $('#w_admin_engines');
    admin.gridController = 'engine';
EOD;

$this->registerJs($js, \yii\web\View::POS_READY);